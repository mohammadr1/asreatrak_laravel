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
    private function publishedRelatedIds(int $count = 3): array
    {
        return collect(range(1, $count))->map(fn ($number) => News::create([
            'title' => 'خبر مرتبط آزمایشی '.$number,
            'status' => 'published', 'published_at' => now()->subMinutes($number),
        ])->id)->all();
    }

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

    public function test_role_based_publication_options_and_server_side_protection(): void
    {
        $reporter = auth()->user();
        $this->assertSame(['draft', 'pending'], array_keys(\App\Support\NewsFormPublication::options($reporter)));
        foreach (['published', 'scheduled', 'approved'] as $status) {
            Livewire::test(CreateNews::class)->set('data.status', $status)->call('create')->assertHasFormErrors(['status']);
            try {
                \App\Support\NewsFormPublication::prepare(['status' => $status], $reporter);
                $this->fail('Unauthorized publication accepted');
            } catch (\Illuminate\Validation\ValidationException $exception) {
                $this->assertArrayHasKey('data.status', $exception->errors());
            }
        }
        $pending = \App\Support\NewsFormPublication::prepare(['status' => 'pending', 'published_at' => now()], $reporter);
        $this->assertNull($pending['published_at']);
        $this->assertSame('pending', $pending['status']);
        $reporter->syncRoles(Role::firstOrCreate(['name' => 'Editor', 'guard_name' => 'web']));
        $this->assertArrayHasKey('published', \App\Support\NewsFormPublication::options($reporter));
        $this->travelTo(now()->startOfSecond());
        $data = \App\Support\NewsFormPublication::prepare(['status' => 'published', 'published_at' => now()->subDay()], $reporter);
        $this->assertTrue(now()->equalTo($data['published_at']));
        $this->assertSame($reporter->id, $data['approved_by']);
        $record = News::create(['title' => 'خبر منتشرشده', 'status' => 'published', 'published_at' => now()->subDay()]);
        $data = \App\Support\NewsFormPublication::prepare(['status' => 'published'], $reporter, $record);
        $this->assertTrue($record->published_at->equalTo($data['published_at']));
        $this->travelBack();
    }

    public function test_required_news_messages_are_clear_persian_even_with_english_locale(): void
    {
        app()->setLocale('en');
        foreach ([CreateNews::class, CreateVideoNews::class] as $pageClass) {
            $page = Livewire::test($pageClass)->call('create');
            $errors = $page->instance()->getErrorBag();
            foreach ([
                'title' => 'عنوان خبر را وارد کنید.',
                'lead' => 'لید خبر را وارد کنید.',
                'content' => 'متن خبر را وارد کنید.',
                'categories' => 'حداقل یک دسته‌بندی برای خبر انتخاب کنید.',
                'tags' => 'حداقل ۵ برچسب برای خبر انتخاب کنید.',
                'report_type' => 'نوع مطلب را انتخاب کنید.',
                'production_method' => 'نحوه تولید خبر را انتخاب کنید.',
                'relatedNews' => 'حداقل ۳ خبر مرتبط انتخاب کنید.',
            ] as $field => $message) {
                $this->assertSame($message, $errors->first('data.'.$field));
            }
            $page->set('data.tags', ['new-tag:یک', 'new-tag:دو'])->call('create');
            $this->assertSame(['حداقل ۵ برچسب برای خبر انتخاب کنید.'], $page->instance()->getErrorBag()->get('data.tags'));
        }
    }

    public function test_news_text_rules_counters_and_optional_seo_fields(): void
    {
        foreach ([CreateNews::class, CreateVideoNews::class] as $pageClass) {
            Livewire::test($pageClass)
                ->assertSee('0 / 255 کاراکتر')->assertSee('0 / 1000 کاراکتر')
                ->assertFormFieldExists('meta_title')->assertFormFieldExists('meta_description')
                ->set('data.title', str_repeat('آ', 256))
                ->set('data.uptitle', '<b>روتیتر</b>')
                ->set('data.lead', str_repeat('آ', 1001))
                ->set('data.content', '<p>&nbsp;<br></p>')
                ->set('data.slug', 'invalid/path')
                ->call('create')
                ->assertHasFormErrors(['title' => 'max', 'uptitle', 'lead' => 'max', 'content', 'slug' => 'regex']);
        }
        foreach (["\u{200C}\u{200B}", '<p><br></p>', '&nbsp;', '!!!'] as $text) {
            $this->assertTrue(\Illuminate\Support\Facades\Validator::make(['text' => $text], ['text' => [new \App\Rules\NewsText(rich: true)]])->fails());
        }
        $this->assertTrue(\Illuminate\Support\Facades\Validator::make(['text' => 'خبر کوتاه'], ['text' => [new \App\Rules\NewsText]])->passes());
    }

    public function test_published_news_has_single_title_canonical_and_safe_article_schema(): void
    {
        $news = News::create([
            'title' => 'عنوان خبر', 'slug' => 'seo-news', 'status' => 'published',
            'published_at' => now()->subMinute(), 'lead' => 'خلاصه خبر',
            'meta_title' => 'عنوان اختصاصی سئو', 'meta_description' => 'توضیحات اختصاصی خبر',
            'content' => '<p>متن خبر</p>',
        ]);
        $response = $this->get(route('news.show', $news))->assertOk()
            ->assertSee('عنوان اختصاصی سئو')->assertSee('توضیحات اختصاصی خبر')
            ->assertSee('rel="canonical" href="'.route('news.show', $news).'"', false);
        $this->assertSame(1, substr_count($response->getContent(), '<title>'));
        preg_match('~<script type="application/ld\+json">(.*?)</script>~s', $response->getContent(), $matches);
        $schema = json_decode($matches[1], true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame('NewsArticle', $schema['@type']);
        $this->assertSame($news->title, $schema['headline']);
        $this->assertSame($news->reporter->name, $schema['author']['name']);
        $this->assertSame($news->published_at->toIso8601String(), $schema['datePublished']);
        $this->assertSame(route('news.show', $news), $schema['mainEntityOfPage']['@id']);
    }

    public function test_news_placement_settings_are_validated_saved_loaded_and_cleared_for_both_forms(): void
    {
        $relatedIds = $this->publishedRelatedIds();
        $media = Media::create(['uuid' => 'placement-image', 'filename' => 'cover.jpg', 'extension' => 'jpg', 'mime_type' => 'image/jpeg', 'type' => 'image', 'original_path' => 'cover.jpg', 'is_active' => true]);
        $first = Category::create(['name' => 'فرهنگی', 'slug' => 'placement-culture']);
        $second = Category::create(['name' => 'ورزشی', 'slug' => 'placement-sport']);
        $unrelated = Category::create(['name' => 'دیگر', 'slug' => 'placement-other']);
        $tags = collect(range(1, 5))->map(fn ($number) => Tag::create(['name' => 'خبر '.$number, 'slug' => 'placement-tag-'.$number]));
        $type = ReportType::create(['name' => 'گزارش']);

        foreach ([CreateNews::class, CreateVideoNews::class] as $index => $pageClass) {
            $page = Livewire::test($pageClass)
                ->assertSet('data.featured', false)->assertSet('data.slider', false)
                ->assertFormFieldIsHidden('top_category_id')
                ->fillForm([
                    'title' => 'خبر جایگاه '.$index, 'slug' => 'placement-'.$index, 'news_code' => 'PLACE-'.$index,
                    'lead' => 'لید', 'content' => '<p>متن خبر</p>', 'reporter_id' => auth()->id(),
                    'featured_media_id' => $media->id, 'featured_media_variant' => 'original',
                    'categories' => [$first->id, $second->id], 'tags' => $tags->pluck('id')->all(), 'report_type' => $type->id,
                    'relatedNews' => $relatedIds,
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
            $this->assertEqualsCanonicalizing($relatedIds, $news->relatedNews()->pluck('news.id')->all());
            $this->assertTrue($news->slider);
            $this->assertSame('draft', $news->status->value);
            $this->assertSame([$first->id], $news->categories()->wherePivot('is_top', true)->pluck('categories.id')->all());

            $edit = Livewire::test(EditNews::class, ['record' => $news->getRouteKey()])
                ->assertSet('data.featured', true)->assertSet('data.slider', true)
                ->assertSet('data.mark_as_top', true)->assertSet('data.top_category_id', $first->id);
            $this->assertEqualsCanonicalizing($relatedIds, $edit->get('data.relatedNews'));
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
        $relatedIds = $this->publishedRelatedIds();
        $media = Media::create(['uuid' => 'video-test-image', 'filename' => 'cover.jpg', 'extension' => 'jpg', 'mime_type' => 'image/jpeg', 'type' => 'image', 'original_path' => 'cover.jpg', 'is_active' => true]);
        $category = Category::create(['name' => 'ویدیو', 'slug' => 'video']);
        $tags = collect(range(1, 5))->map(fn ($number) => Tag::create(['name' => 'خبر '.$number, 'slug' => 'news-'.$number]));
        $type = ReportType::create(['name' => 'گزارش']);
        $form = [
            'title' => 'خبر ویدیویی آزمایشی', 'slug' => 'video-test', 'news_code' => 'VIDEO-TEST',
            'lead' => 'لید خبر', 'content' => '<p>متن خبر</p>', 'reporter_id' => auth()->id(),
            'featured_media_id' => $media->id, 'featured_media_variant' => 'original',
            'categories' => [$category->id], 'tags' => $tags->pluck('id')->all(), 'report_type' => $type->id,
            'relatedNews' => $relatedIds,
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
        $this->assertCount(5, $news->tags);
        $this->assertCount(1, $news->categories);
        Livewire::test(EditNews::class, ['record' => $news->getRouteKey()])
            ->assertSet('data.aparat_video_id', 'k93yms3')
            ->set('data.content', '<p>قبل</p><img src="https://example.com/photo.jpg"><p><a href="https://www.aparat.com/v/inside123">ویدیوی آپارات: inside123</a></p><p>بعد</p>')
            ->set('data.status', 'pending')
            ->set('data.aparat_video_id', 'new123')->call('save')->assertHasNoFormErrors();
        $this->assertSame('new123', $news->refresh()->aparat_video_id);
        $this->assertStringContainsString('https://example.com/photo.jpg', $news->content);
        $this->assertStringContainsString(AparatVideo::embedUrl('inside123'), \App\Support\NewsContent::render($news->content));
        $this->assertSame('video', $news->post_type);
        $this->assertSame('pending', $news->status->value);
        $this->assertNull($news->published_at);
        auth()->user()->syncRoles(Role::firstOrCreate(['name' => 'Editor', 'guard_name' => 'web']));
        Livewire::test(EditNews::class, ['record' => $news->getRouteKey()])
            ->set('data.status', 'published')->call('save')->assertHasNoFormErrors();
        $this->assertSame('published', $news->refresh()->status->value);
        $this->assertNotNull($news->published_at);
        $this->assertSame(auth()->id(), $news->approved_by);
        foreach ([CreateNews::class, CreateVideoNews::class] as $index => $class) {
            $direct = array_merge($form, ['title' => 'انتشار مستقیم '.$index, 'slug' => 'direct-'.$index, 'news_code' => 'DIRECT-'.$index, 'status' => 'published', 'aparat_video_id' => 'abc123']);
            Livewire::test($class)->fillForm($direct)->call('create')->assertHasNoFormErrors();
            $created = News::where('slug', 'direct-'.$index)->firstOrFail();
            $this->assertSame('published', $created->status->value);
            $this->assertNotNull($created->published_at);
        }
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

    public function test_related_news_boundaries_visibility_and_self_selection(): void
    {
        $ids = $this->publishedRelatedIds(11);
        $draft = News::create(['title' => 'پیش‌نویس خصوصی']);
        $future = News::create(['title' => 'خبر آینده', 'status' => 'published', 'published_at' => now()->addDay()]);
        foreach ([CreateNews::class, CreateVideoNews::class] as $pageClass) {
            $page = Livewire::test($pageClass);
            $options = $page->instance()->getFormSelectSearchResults('data.relatedNews', 'خصوصی');
            $this->assertEmpty($options);
            foreach ([[], array_slice($ids, 0, 2), $ids, [$ids[0], $ids[0], $ids[1]], [$ids[0], $ids[1], $draft->id], [$ids[0], $ids[1], $future->id], [$ids[0], $ids[1], 999999]] as $invalid) {
                $page->set('data.relatedNews', $invalid)->call('create')->assertHasFormErrors(['relatedNews']);
            }
            foreach ([3, 10] as $count) {
                $page->set('data.relatedNews', array_slice($ids, 0, $count))->call('create')->assertHasNoFormErrors(['relatedNews']);
            }
        }
        $news = News::findOrFail($ids[0]);
        Livewire::test(EditNews::class, ['record' => $news->id])
            ->set('data.relatedNews', array_slice($ids, 0, 3))->call('save')->assertHasFormErrors(['relatedNews']);

        $news->relatedNews()->sync([$ids[1], $ids[2], $draft->id, $future->id]);
        News::findOrFail($ids[2])->delete();
        $this->get(route('news.show', $news))->assertOk()
            ->assertSee(News::findOrFail($ids[1])->title)
            ->assertDontSee($draft->title)->assertDontSee($future->title)
            ->assertDontSee('خبر مرتبط آزمایشی 3');
    }
}
