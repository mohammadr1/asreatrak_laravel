<?php

namespace Tests\Feature;

use App\Filament\Resources\NewsResource\Pages\ListNews;
use App\Models\Category;
use App\Models\News;
use App\Models\ReportType;
use App\Models\Tag;
use App\Models\User;
use App\Support\NewsDateRange;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NewsListFiltersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // A dedicated in-memory connection: never migrate or write the site's database.
        config([
            'database.default' => 'news_testing',
            'database.connections.news_testing' => [
                'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true,
            ],
            'session.driver' => 'array',
            'cache.default' => 'array',
        ]);
        DB::purge('news_testing');
        $this->artisan('migrate', ['--database' => 'news_testing', '--force' => true])->assertSuccessful();
        $this->withoutVite();
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::create(['first_name' => 'مدیر', 'last_name' => 'آزمایشی', 'slug' => 'test-admin']);
        $permission = Permission::create(['name' => 'news.view', 'guard_name' => 'web']);
        $admin->assignRole(Role::create(['name' => 'Admin', 'guard_name' => 'web']));
        $admin->givePermissionTo($permission);
        Role::create(['name' => 'Reporter', 'guard_name' => 'web'])->givePermissionTo($permission);
        $this->actingAs($admin);
    }

    private function news(array $attributes = []): News
    {
        return News::create(array_merge(['title' => 'خبر آزمایشی'], $attributes));
    }

    public function test_combined_filters_and_search_update_full_statistics_without_duplicate_counts(): void
    {
        $reporter = User::create(['first_name' => 'علی', 'last_name' => 'احمدی', 'slug' => 'reporter']);
        $type = ReportType::create(['name' => 'گزارش تصویری']);
        ReportType::create(['name' => 'یادداشت']);
        $category = Category::create(['name' => 'فرهنگی', 'slug' => 'culture']);
        $tag = Tag::create(['name' => 'هنر', 'slug' => 'art']);
        $tag2 = Tag::create(['name' => 'موزه', 'slug' => 'museum']);
        $match = $this->news(['title' => 'گزارش موزه', 'reporter_id' => $reporter->id, 'report_type' => $type->id, 'production_method' => 'تولیدی']);
        $match->categories()->attach($category);
        $match->tags()->attach([$tag->id, $tag2->id]);
        $other = $this->news(['production_method' => 'بازنشری']);

        $page = Livewire::test(ListNews::class)->assertSuccessful()->assertCanSeeTableRecords([$match, $other]);
        $options = $page->instance()->getFormSelectSearchResults('tableDeferredFilters.reporter.values', 'علی احمدی');
        $this->assertSame((string) $reporter->id, (string) $options[0]['value']);
        $page->set('tableDeferredFilters.reporter.values', [$reporter->id])
            ->set('tableDeferredFilters.report_type.values', [$type->id])
            ->set('tableDeferredFilters.production_method.values', ['تولیدی'])
            ->set('tableDeferredFilters.categories.values', [$category->id])
            ->set('tableDeferredFilters.tags.values', [$tag->id, $tag2->id]);
        $this->assertSame(2, $page->instance()->getNewsStatistics()['filtered']['total']);
        $page->call('applyTableFilters')->assertHasNoErrors()->searchTable('علی احمدی')
            ->assertCanSeeTableRecords([$match])->assertCanNotSeeTableRecords([$other]);

        $stats = $page->instance()->getNewsStatistics();
        $this->assertSame(2, $stats['overview']['total']);
        $this->assertSame(1, $stats['filtered']['total']);
        $this->assertSame(1, collect($stats['filtered']['groups'][0]['items'])->firstWhere('label', 'تولیدی')['count']);
        $this->assertSame(0, collect($stats['filtered']['groups'][1]['items'])->firstWhere('label', 'یادداشت')['count']);
        $page->searchTable('بدون نتیجه');
        $this->assertSame(0, $page->instance()->getNewsStatistics()['filtered']['total']);
        $page->call('removeTableFilters')->assertHasNoErrors();
        $this->assertSame(2, $page->instance()->getNewsStatistics()['filtered']['total']);
    }

    public function test_jalali_date_range_includes_both_days_and_supports_publication_date(): void
    {
        $start = NewsDateRange::parse('۱۴۰۵/۰۷/۰۵');
        $first = $this->news();
        $last = $this->news();
        $outside = $this->news();
        foreach ([[$first, $start], [$last, $start->copy()->endOfDay()], [$outside, $start->copy()->addDay()]] as [$news, $date]) {
            DB::table('news')->where('id', $news->id)->update(['created_at' => $date, 'published_at' => $date]);
        }
        $page = Livewire::test(ListNews::class)
            ->set('tableDeferredFilters.date_range.from', '۱۴۰۵/۰۷/۰۵')
            ->set('tableDeferredFilters.date_range.until', '1405/07/05')
            ->call('applyTableFilters')->assertHasNoErrors()
            ->assertCanSeeTableRecords([$first, $last])->assertCanNotSeeTableRecords([$outside]);
        $this->assertSame(2, $page->instance()->getNewsStatistics()['filtered']['total']);
        $page->set('tableDeferredFilters.date_range.basis', 'published_at')
            ->call('applyTableFilters')->assertHasNoErrors()->assertCanSeeTableRecords([$first, $last]);
        $page->set('tableDeferredFilters.date_range.until', '1405/07/04')
            ->call('applyTableFilters')->assertHasErrors(['tableDeferredFilters.date_range.until']);
        $page->set('tableDeferredFilters.date_range.until', '1405/13/01')
            ->call('applyTableFilters')->assertHasErrors(['tableDeferredFilters.date_range.until']);
        $this->assertSame(2, $page->instance()->getNewsStatistics()['filtered']['total']);
        $page->call('resetTableFiltersForm')->assertHasNoErrors();
        $this->assertSame(3, $page->instance()->getNewsStatistics()['filtered']['total']);
    }

    public function test_reporter_cannot_see_statistics_but_can_still_filter_own_news(): void
    {
        $reporter = User::create(['first_name' => 'خبرنگار', 'last_name' => 'دوم', 'slug' => 'second']);
        $reporter->assignRole('Reporter');
        $hidden = $this->news();
        for ($i = 0; $i < 12; $i++) {
            $this->news(['reporter_id' => $reporter->id]);
        }
        $deleted = $this->news(['reporter_id' => $reporter->id]);
        $deleted->delete();
        $this->actingAs($reporter);
        $page = Livewire::test(ListNews::class)->set('tableRecordsPerPage', 5)
            ->assertCanNotSeeTableRecords([$hidden, $deleted])
            ->assertDontSee('نمای کلی تحریریه')->assertDontSee('گزارش نتایج انتخاب‌شده');
        $this->assertFalse($page->instance()->canViewNewsStatistics());
        $this->assertSame(12, $page->instance()->getFilteredTableQuery()->count());
        $page->set('tableDeferredFilters.reporter.values', [$hidden->reporter_id])->call('applyTableFilters');
        $this->assertSame(0, $page->instance()->getFilteredTableQuery()->count());
        $page->call('getNewsStatistics')->assertForbidden();
    }

    public function test_editor_cannot_request_statistics_even_through_a_direct_livewire_call(): void
    {
        $editor = User::create(['first_name' => 'سردبیر', 'slug' => 'editor']);
        $editor->assignRole(Role::create(['name' => 'Editor', 'guard_name' => 'web']));
        $editor->givePermissionTo('news.view');
        $this->actingAs($editor);
        $this->mock(\App\Services\NewsStatistics::class)->shouldNotReceive('summarize');

        Livewire::test(ListNews::class)->assertSuccessful()
            ->assertDontSee('نمای کلی تحریریه')->assertDontSee('گزارش نتایج انتخاب‌شده')
            ->call('getNewsStatistics')->assertForbidden();
    }

    public function test_admin_sees_independent_accessible_toggles_and_full_statistics(): void
    {
        for ($i = 0; $i < 12; $i++) {
            $this->news();
        }
        $this->news()->delete();
        $page = Livewire::test(ListNews::class)->set('tableRecordsPerPage', 5)
            ->assertSee('نمای کلی تحریریه')->assertSee('گزارش نتایج انتخاب‌شده')
            ->assertSeeHtml('aria-controls="news-overview-content"')
            ->assertSeeHtml('aria-controls="news-filtered-content"')
            ->assertSee('باز و بسته کردن فیلترها');
        $this->assertSame(\Filament\Tables\Enums\FiltersLayout::AboveContentCollapsible, $page->instance()->getTable()->getFiltersLayout());
        $this->assertSame(12, $page->instance()->getNewsStatistics()['overview']['total']);
        $this->assertSame(12, $page->instance()->getNewsStatistics()['filtered']['total']);
    }

    public function test_date_filters_render_the_jalali_picker_hook(): void
    {
        Livewire::test(ListNews::class)
            ->assertSeeHtml('data-jalali-date-input="true"')
            ->assertSeeHtml('data-jalali-time="true"')
            ->assertSeeHtml('data-jalali-role="from"')
            ->assertSeeHtml('data-jalali-role="until"');

    }

    public function test_panel_loads_one_versioned_calendar_script_in_its_layout(): void
    {
        auth()->logout();
        $response = $this->get('/backroom-entry/login')->assertOk();
        $response->assertSee('admin-news-jalali-picker.js?v='.filemtime(public_path('assets/js/admin-news-jalali-picker.js')), false);
        $this->assertSame(1, substr_count($response->getContent(), 'admin-news-jalali-picker.js?v='));
    }

    public function test_time_range_preserves_minutes_and_includes_the_entire_last_minute(): void
    {
        $date = NewsDateRange::parse('۱۴۰۵/۰۷/۰۵ ۱۴:۳۰');
        $this->assertSame('14:30:00', $date->format('H:i:s'));
        $records = [];
        foreach (['14:29:59', '14:30:00', '14:30:59', '14:31:00'] as $time) {
            $news = $this->news();
            DB::table('news')->where('id', $news->id)->update(['created_at' => $date->toDateString().' '.$time]);
            $records[] = $news;
        }
        $page = Livewire::test(ListNews::class)
            ->set('tableDeferredFilters.date_range.from', '۱۴۰۵/۰۷/۰۵ ۱۴:۳۰')
            ->set('tableDeferredFilters.date_range.until', '1405/07/05 14:30')
            ->call('applyTableFilters')->assertHasNoErrors()
            ->assertCanSeeTableRecords([$records[1], $records[2]])
            ->assertCanNotSeeTableRecords([$records[0], $records[3]]);
        $this->assertSame(2, $page->instance()->getNewsStatistics()['filtered']['total']);
        $page->set('tableDeferredFilters.date_range.until', '1405/07/05 14:29')
            ->call('applyTableFilters')->assertHasErrors(['tableDeferredFilters.date_range.until']);
        $page->set('tableDeferredFilters.date_range.until', '1405/07/05 24:00')
            ->call('applyTableFilters')->assertHasErrors(['tableDeferredFilters.date_range.until']);
        $page->set('tableDeferredFilters.date_range.until', '1405/07/05')
            ->call('applyTableFilters')->assertHasNoErrors();
        $this->assertSame(3, $page->instance()->getNewsStatistics()['filtered']['total']);
    }

    public function test_create_and_edit_use_jalali_publication_field_with_tehran_time_conversion(): void
    {
        config(['app.timezone' => 'UTC']);
        $originalTimezone = date_default_timezone_get();
        date_default_timezone_set('UTC');
        $this->beforeApplicationDestroyed(fn () => date_default_timezone_set($originalTimezone));
        foreach (['news.create', 'news.update'] as $permission) {
            auth()->user()->givePermissionTo(Permission::create(['name' => $permission, 'guard_name' => 'web']));
        }
        $create = Livewire::test(\App\Filament\Resources\NewsResource\Pages\CreateNews::class)
            ->set('data.status', 'scheduled')
            ->assertSeeHtml('data-jalali-time-required="true"')
            ->set('data.published_at', '1405/07/07 14:30');
        $field = $create->instance()->form->getFlatFields()['published_at'];
        $this->assertInstanceOf(\App\Forms\Components\JalaliDateTimePicker::class, $field);
        $storage = $field->getStateToDehydrate()['data.published_at'];
        $this->assertSame('2026-09-29 11:00:00', $storage);

        $news = $this->news(['status' => 'scheduled', 'published_at' => $storage]);
        $edit = Livewire::test(\App\Filament\Resources\NewsResource\Pages\EditNews::class, ['record' => $news->getRouteKey()])
            ->assertSet('data.published_at', '1405/07/07 14:30')
            ->assertSeeHtml('data-jalali-time-required="true"')
            ->set('data.published_at', '۱۴۰۵/۰۷/۰۸ ۱۶:۴۵');
        $field = $edit->instance()->form->getFlatFields()['published_at'];
        $this->assertSame('2026-09-30 13:15:00', $field->getStateToDehydrate()['data.published_at']);
    }

    public function test_publication_time_is_required_when_a_date_is_provided(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        \App\Forms\Components\JalaliDateTimePicker::storageValue('1405/07/07');
    }

    public function test_inline_tags_are_staged_then_saved_and_reused_on_edit(): void
    {
        foreach (['news.create', 'news.update'] as $permission) {
            auth()->user()->givePermissionTo(Permission::create(['name' => $permission, 'guard_name' => 'web']));
        }
        $existing = Tag::create(['name' => 'فرهنگ', 'slug' => 'culture']);
        $create = Livewire::test(\App\Filament\Resources\NewsResource\Pages\CreateNews::class);
        $results = $create->instance()->getFormSelectSearchResults('data.tags', 'تگ تازه');
        $token = $results[0]['value'];
        $this->assertSame('new-tag:تگ تازه', $token);
        $this->assertSame(1, Tag::count());
        $create->set('data.tags', [(string) $existing->id, $token]);
        $this->assertSame(1, Tag::count());

        $news = $this->news();
        $field = $create->instance()->form->getFlatFields()['tags'];
        $field->model($news)->saveRelationships();
        $this->assertSame(2, Tag::count());
        $this->assertSame(2, $news->tags()->count());
        $new = Tag::where('name', 'تگ تازه')->firstOrFail();
        $this->assertNotEmpty($new->slug);

        $edit = Livewire::test(\App\Filament\Resources\NewsResource\Pages\EditNews::class, ['record' => $news->getRouteKey()]);
        $this->assertEqualsCanonicalizing([$existing->id, $new->id], $edit->get('data.tags'));
        $results = $edit->instance()->getFormSelectSearchResults('data.tags', 'تگ تازه');
        $this->assertSame((string) $new->id, (string) $results[0]['value']);
        $edit->set('data.tags', ['new-tag:تگ تازه', 'new-tag:  تگ   تازه  ']);
        $edit->instance()->form->getFlatFields()['tags']->saveRelationships();
        $this->assertSame(2, Tag::count());
        $this->assertSame([$new->id], $news->tags()->pluck('tags.id')->all());
    }
}
