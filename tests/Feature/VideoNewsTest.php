<?php

namespace Tests\Feature;

use App\Filament\Resources\NewsResource;
use App\Filament\Resources\NewsResource\Pages\CreateNews;
use App\Filament\Resources\NewsResource\Pages\CreateVideoNews;
use App\Filament\Resources\NewsResource\Pages\EditNews;
use App\Models\{Category, Media, News, ReportType, Tag, User};
use App\Support\AparatVideo;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Spatie\Permission\Models\{Permission, Role};
use Tests\TestCase;

class VideoNewsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'video_testing',
            'database.connections.video_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true],
            'session.driver' => 'array', 'cache.default' => 'array',
        ]);
        DB::purge('video_testing');
        $this->artisan('migrate', ['--database' => 'video_testing', '--force' => true])->assertSuccessful();
        $this->withoutVite();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $user = User::create(['first_name' => 'خبرنگار', 'last_name' => 'آزمایشی', 'slug' => 'video-reporter']);
        $user->assignRole(Role::create(['name' => 'Reporter', 'guard_name' => 'web']));
        foreach (['news.view', 'news.create', 'news.update'] as $name) {
            $user->givePermissionTo(Permission::create(['name' => $name, 'guard_name' => 'web']));
        }
        $this->actingAs($user);
    }

    public function test_aparat_input_accepts_only_ids_and_trusted_video_urls(): void
    {
        foreach (['k93yms3', ' https://www.aparat.com/v/k93yms3 ', 'https://aparat.com/v/k93yms3?source=share'] as $input) {
            $this->assertSame('k93yms3', AparatVideo::id($input));
        }
        foreach (['', '<iframe src="x">', 'javascript:alert(1)', 'https://aparat.com.evil.test/v/id', 'https://evil.test/v/id', 'https://user@aparat.com/v/id', 'https://aparat.com/v/a%22b', 'https://aparat.com:8888/v/id', '../secret'] as $input) {
            $this->assertNull(AparatVideo::id($input));
            $this->assertNull(AparatVideo::embedUrl($input));
        }
    }

    public function test_news_placement_settings_are_validated_saved_loaded_and_cleared_for_both_forms(): void
    {
        $media = Media::create(['uuid' => 'placement-image', 'filename' => 'cover.jpg', 'extension' => 'jpg', 'mime_type' => 'image/jpeg', 'type' => 'image', 'original_path' => 'cover.jpg', 'is_active' => true]);
        $first = Category::create(['name' => 'فرهنگی', 'slug' => 'placement-culture']);
        $second = Category::create(['name' => 'ورزشی', 'slug' => 'placement-sport']);
        $unrelated = Category::create(['name' => 'دیگر', 'slug' => 'placement-other']);
        $tag = Tag::create(['name' => 'خبر', 'slug' => 'placement-tag']);
        $type = ReportType::create(['name' => 'گزارش']);

        foreach ([CreateNews::class, CreateVideoNews::class] as $index => $pageClass) {
            $page = Livewire::test($pageClass)
                ->assertSet('data.featured', false)->assertSet('data.slider', false)
                ->assertFormFieldIsHidden('top_category_id')
                ->fillForm([
                    'title' => 'خبر جایگاه '.$index, 'slug' => 'placement-'.$index, 'news_code' => 'PLACE-'.$index,
                    'lead' => 'لید', 'content' => '<p>متن خبر</p>', 'reporter_id' => auth()->id(),
                    'featured_media_id' => $media->id, 'featured_media_variant' => 'original',
                    'categories' => [$first->id, $second->id], 'tags' => [$tag->id], 'report_type' => $type->id,
                    'production_method' => 'تولیدی', 'status' => 'draft', 'aparat_video_id' => 'abc123',
                    'featured' => true, 'slider' => true, 'mark_as_top' => true, 'top_category_id' => null,
                ])
                ->call('create')->assertHasFormErrors(['top_category_id' => 'required']);
            $options = $page->instance()->form->getFlatFields()['top_category_id']->getOptions();
            $this->assertArrayHasKey($unrelated->id, $options);
            $page->set('data.top_category_id', 999999)->call('create')->assertHasFormErrors(['top_category_id']);
            $page->set('data.top_category_id', $first->id)->call('create')->assertHasNoFormErrors();
            $news = News::where('slug', 'placement-'.$index)->firstOrFail();
            $this->assertTrue($news->featured);
            $this->assertTrue($news->slider);
            $this->assertSame('draft', $news->status->value);
            $this->assertSame([$first->id], $news->categories()->wherePivot('is_top', true)->pluck('categories.id')->all());

            $edit = Livewire::test(EditNews::class, ['record' => $news->getRouteKey()])
                ->assertSet('data.featured', true)->assertSet('data.slider', true)
                ->assertSet('data.mark_as_top', true)->assertSet('data.top_category_id', $first->id);
            $edit->set('data.top_category_id', $second->id)->call('save')->assertHasNoFormErrors();
            $this->assertSame([$second->id], $news->categories()->wherePivot('is_top', true)->pluck('categories.id')->all());
            $edit->set('data.categories', [$first->id])->set('data.top_category_id', $unrelated->id)
                ->call('save')->assertHasNoFormErrors();
            $this->assertSame([$unrelated->id], $news->categories()->wherePivot('is_top', true)->pluck('categories.id')->all());
            $this->assertEqualsCanonicalizing([$first->id, $unrelated->id], $news->categories()->pluck('categories.id')->all());
            $edit->set('data.mark_as_top', false)->set('data.featured', false)->set('data.slider', false)
                ->call('save')->assertHasNoFormErrors();
            $this->assertFalse($news->refresh()->featured);
            $this->assertFalse($news->slider);
            $this->assertSame(0, $news->categories()->wherePivot('is_top', true)->count());
            $this->assertSame([$first->id], $news->categories()->pluck('categories.id')->all());
        }
    }

    public function test_reporter_can_create_and_edit_video_using_the_shared_news_form(): void
    {
        $media = Media::create(['uuid' => 'video-test-image', 'filename' => 'cover.jpg', 'extension' => 'jpg', 'mime_type' => 'image/jpeg', 'type' => 'image', 'original_path' => 'cover.jpg', 'is_active' => true]);
        $category = Category::create(['name' => 'ویدیو', 'slug' => 'video']);
        $tag = Tag::create(['name' => 'خبر', 'slug' => 'news']);
        $type = ReportType::create(['name' => 'گزارش']);
        $form = [
            'title' => 'خبر ویدیویی آزمایشی', 'slug' => 'video-test', 'news_code' => 'VIDEO-TEST',
            'lead' => 'لید خبر', 'content' => '<p>متن خبر</p>', 'reporter_id' => auth()->id(),
            'featured_media_id' => $media->id, 'featured_media_variant' => 'original',
            'categories' => [$category->id], 'tags' => [$tag->id], 'report_type' => $type->id,
            'production_method' => 'تولیدی', 'status' => 'draft', 'aparat_video_id' => '',
        ];
        $page = Livewire::test(CreateVideoNews::class)->fillForm($form)
            ->call('create')->assertHasFormErrors(['aparat_video_id' => 'required']);
        $page->set('data.aparat_video_id', 'https://evil.test/v/id')->call('create')
            ->assertHasFormErrors(['aparat_video_id']);
        $page->set('data.aparat_video_id', 'https://www.aparat.com/v/k93yms3')
            ->call('create')->assertHasNoFormErrors();
        $news = News::where('slug', 'video-test')->firstOrFail();
        $this->assertSame('video', $news->post_type);
        $this->assertSame('k93yms3', $news->aparat_video_id);
        $this->assertSame(auth()->id(), $news->reporter_id);
        $this->assertSame('draft', $news->status->value);
        $this->assertCount(1, $news->tags);
        $this->assertCount(1, $news->categories);
        Livewire::test(EditNews::class, ['record' => $news->getRouteKey()])
            ->assertSet('data.aparat_video_id', 'k93yms3')
            ->set('data.content', '<p>قبل</p><img src="https://example.com/photo.jpg"><p><a href="https://www.aparat.com/v/inside123">ویدیوی آپارات: inside123</a></p><p>بعد</p>')
            ->set('data.aparat_video_id', 'new123')->call('save')->assertHasNoFormErrors();
        $this->assertSame('new123', $news->refresh()->aparat_video_id);
        $this->assertStringContainsString('https://example.com/photo.jpg', $news->content);
        $this->assertStringContainsString(AparatVideo::embedUrl('inside123'), \App\Support\NewsContent::render($news->content));
        $this->assertSame('video', $news->post_type);
        Livewire::test(CreateNews::class)->assertFormFieldIsHidden('aparat_video_id');
        $this->assertContains('افزودن خبر ویدیویی', array_map(fn ($item) => $item->getLabel(), NewsResource::getNavigationItems()));
    }

    public function test_only_published_video_news_is_public_and_content_is_sanitized(): void
    {
        $news = News::create(['title' => 'عنوان ویدیو', 'post_type' => 'video', 'aparat_video_id' => 'k93yms3', 'status' => 'draft', 'content' => '<p>متن اصلی</p><script>alert(123)</script>']);
        $this->get(route('news.show', $news))->assertNotFound();
        $news->update(['status' => 'scheduled', 'published_at' => now()->subDay()]);
        $this->get(route('news.show', $news))->assertNotFound();
        $news->update(['status' => 'published', 'published_at' => now()->addDay()]);
        $this->get(route('news.show', $news))->assertNotFound();
        $news->update(['published_at' => now()->subMinute()]);
        $this->get(route('news.show', $news))->assertOk()->assertSee('عنوان ویدیو')
            ->assertSee(AparatVideo::embedUrl('k93yms3'), false)
            ->assertSee('متن اصلی')->assertDontSee('<script>alert(123)</script>', false);
    }

    public function test_inline_media_is_rendered_in_order_in_an_article_and_unsafe_html_is_removed(): void
    {
        $content = '<p>قبل از تصویر</p><img src="https://example.com/photo.jpg" alt="تصویر نمونه" onerror="alert(1)">'
            .'<p>بین تصویر و ویدیو</p><p><a href="https://www.aparat.com/v/k93yms3">ویدیوی آپارات: k93yms3</a></p>'
            .'<p>بعد از ویدیو</p><iframe src="https://evil.test"></iframe><script>alert(1)</script>';
        $news = News::create(['title' => 'خبر دارای رسانه', 'post_type' => 'article', 'status' => 'published', 'content' => $content]);
        $this->get(route('news.show', $news))->assertOk()
            ->assertSeeInOrder(['قبل از تصویر', 'https://example.com/photo.jpg', 'بین تصویر و ویدیو', AparatVideo::embedUrl('k93yms3'), 'بعد از ویدیو'], false)
            ->assertDontSee('onerror=', false)->assertDontSee('https://evil.test', false)->assertDontSee('<script>alert(1)', false);
        $html = \App\Support\NewsContent::render('<a href="https://evil.test/v/abc">ویدیوی آپارات: abc</a><a href="https://www.aparat.com/v/abc">لینک معمولی</a>');
        $this->assertStringNotContainsString('<iframe', $html);
    }

    public function test_inline_video_action_validates_input_and_dispatches_normalized_id(): void
    {
        Livewire::test(CreateNews::class)
            ->callFormComponentAction('content', 'insertAparat', data: ['video' => 'https://www.aparat.com/v/k93yms3'])
            ->assertDispatched('news-editor-video', id: 'k93yms3');
        Livewire::test(CreateNews::class)
            ->callFormComponentAction('content', 'insertAparat', data: ['video' => 'https://evil.test/v/abc'])
            ->assertNotDispatched('news-editor-video');
        Livewire::test(CreateNews::class)->mountFormComponentAction('content', 'insertImage')
            ->assertSeeLivewire(\App\Livewire\Media\FeaturedPicker::class);
    }
}
