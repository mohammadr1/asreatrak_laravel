<?php

namespace Tests\Feature;

use App\Filament\Resources\NewsResource\Pages\CreateNews;
use App\Filament\Resources\NewsResource\Pages\EditNews;
use App\Models\News;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * نمایش لینک کوتاه در فرم خبر (پنل مدیریت).
 *
 * این تست رندر فرم را بررسی می‌کند تا وجود/نمایش فیلد لینک کوتاه
 * در صفحه‌های «ایجاد خبر» و «ویرایش خبر» تضمین شود.
 */
class TempShortLinkUiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'short_link_ui_testing',
            'database.connections.short_link_ui_testing' => [
                'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true,
            ],
            'session.driver' => 'array',
            'cache.default' => 'array',
            'news.short_link.base' => 'https://asreatrak.ir',
        ]);

        DB::purge('short_link_ui_testing');

        $this->artisan('migrate', ['--database' => 'short_link_ui_testing', '--force' => true])->assertSuccessful();
        $this->withoutVite();

        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $user = User::create([
            'first_name' => 'ادمین',
            'last_name' => 'آزمایشی',
            'slug' => 'admin-ui-test',
        ]);
        $user->assignRole(Role::create(['name' => 'Admin', 'guard_name' => 'web']));

        foreach (['news.view', 'news.create', 'news.update', 'news.delete', 'news.approve', 'news.publish'] as $name) {
            $user->givePermissionTo(Permission::create(['name' => $name, 'guard_name' => 'web']));
        }

        $this->actingAs($user);
    }

    public function test_edit_page_renders_the_copyable_short_link(): void
    {
        $news = News::create([
            'title' => 'خبر برای نمایش لینک کوتاه',
            'news_code' => '20261031124151',
            'status' => 'draft',
        ]);

        $html = Livewire::test(EditNews::class, ['record' => $news->getRouteKey()])
            ->assertOk()
            ->html();

        $this->assertStringContainsString('لینک کوتاه خبر', $html);
        $this->assertStringContainsString('https://asreatrak.ir/short/20261031124151', $html);
    }

    public function test_create_page_explains_the_short_link_is_built_after_saving(): void
    {
        $html = Livewire::test(CreateNews::class)->assertOk()->html();

        $this->assertStringContainsString('لینک کوتاه خبر', $html);
        $this->assertStringContainsString('پس از ذخیرهٔ خبر', $html);
    }
}
