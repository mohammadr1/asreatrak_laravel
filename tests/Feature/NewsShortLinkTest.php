<?php

namespace Tests\Feature;

use App\Models\News;
use App\Support\ShortLink;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * لینک کوتاه خبر: /short/{code}
 *
 * - خبرهای جدید: کد خبر (مثل 20261031124151)
 * - خبرهای سایت قبلی: کد ۵ کاراکتری (مثل eqYkB)
 */
class NewsShortLinkTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'short_link_testing',
            'database.connections.short_link_testing' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
            'session.driver' => 'array',
            'cache.default' => 'array',
            'news.short_link.base' => 'https://asreatrak.ir',
        ]);

        DB::purge('short_link_testing');

        $this->artisan('migrate', ['--database' => 'short_link_testing', '--force' => true])
            ->assertSuccessful();
    }

    private function publishedNews(array $attributes = []): News
    {
        return News::create(array_merge([
            'title' => 'خبر آزمایشی لینک کوتاه',
            'status' => 'published',
            'published_at' => now()->subMinute(),
        ], $attributes));
    }

    public function test_new_news_gets_a_short_link_made_from_its_news_code(): void
    {
        $news = $this->publishedNews(['news_code' => '20261031124151']);

        $this->assertSame('https://asreatrak.ir/short/20261031124151', $news->short_link);
        $this->assertSame('https://asreatrak.ir/short/20261031124151', $news->short_url);

        $this->get('/short/20261031124151')
            ->assertStatus(301)
            ->assertRedirectToRoute('news.show', ['news' => $news->slug]);
    }

    public function test_short_link_follows_news_code_changes(): void
    {
        $news = $this->publishedNews(['news_code' => '20261031124151']);

        $news->news_code = '20261031124152';
        $news->save();

        $this->assertSame('https://asreatrak.ir/short/20261031124152', $news->fresh()->short_link);
    }

    public function test_legacy_five_character_codes_keep_working_and_keep_their_code(): void
    {
        $news = $this->publishedNews([
            'news_code' => 'OLD-0010716',
            'short_link' => 'https://asreatrak.ir/short/eqYkB',
        ]);

        // کد قدیمی نباید به کد خبر تغییر کند
        $this->assertSame('https://asreatrak.ir/short/eqYkB', $news->short_link);

        $this->get('/short/eqYkB')
            ->assertStatus(301)
            ->assertRedirectToRoute('news.show', ['news' => $news->slug]);

        // حتی اگر خبر ویرایش شود، لینک قبلی دست‌نخورده می‌ماند
        $news->update(['title' => 'عنوان تازه برای خبر قدیمی']);
        $this->assertSame('https://asreatrak.ir/short/eqYkB', $news->fresh()->short_link);
    }

    public function test_only_published_and_due_news_is_reachable_by_short_link(): void
    {
        $draft = News::create(['title' => 'پیش‌نویس', 'news_code' => '20260101010101']);
        $this->get('/short/20260101010101')->assertNotFound();

        $scheduled = News::create([
            'title' => 'زمان‌بندی‌شده',
            'news_code' => '20260101010102',
            'status' => 'scheduled',
            'published_at' => now()->addDay(),
        ]);
        $this->get('/short/20260101010102')->assertNotFound();

        $this->get('/short/does-not-exist')->assertNotFound();
        $this->assertSame('https://asreatrak.ir/short/20260101010101', $draft->short_link);
        $this->assertSame('https://asreatrak.ir/short/20260101010102', $scheduled->short_link);
    }

    public function test_command_normalizes_broken_legacy_links_without_changing_their_code(): void
    {
        $now = now();

        DB::table('news')->insert([
            [
                'title' => 'خبر قدیمی با اسلش اضافه', 'slug' => 'old-double-slash', 'news_code' => 'OLD-000001',
                'short_link' => 'https://asreatrak.ir//short/eqK8y', 'status' => 'published',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'title' => 'خبر قدیمی با دامنه لوکال', 'slug' => 'old-local', 'news_code' => 'OLD-000002',
                'short_link' => '127.0.0.1:8000/short/Vm8db', 'status' => 'published',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'title' => 'خبر قدیمی بدون لینک', 'slug' => 'old-empty', 'news_code' => 'OLD-000003',
                'short_link' => null, 'status' => 'published',
                'created_at' => $now, 'updated_at' => $now,
            ],
        ]);

        $this->artisan('news:sync-short-links')->assertSuccessful();

        $this->assertSame('https://asreatrak.ir/short/eqK8y', News::where('slug', 'old-double-slash')->value('short_link'));
        $this->assertSame('https://asreatrak.ir/short/Vm8db', News::where('slug', 'old-local')->value('short_link'));
        $this->assertSame('https://asreatrak.ir/short/OLD-000003', News::where('slug', 'old-empty')->value('short_link'));
    }

    public function test_short_link_helper_accepts_every_stored_format(): void
    {
        $this->assertSame('eqYkB', ShortLink::codeFromUrl('https://asreatrak.ir/short/eqYkB'));
        $this->assertSame('V0kmX', ShortLink::codeFromUrl('https://asreatrak.ir//short/V0kmX'));
        $this->assertSame('Vm8db', ShortLink::codeFromUrl('127.0.0.1:8000/short/Vm8db'));
        $this->assertSame('eqYkB', ShortLink::codeFromUrl('https://asreatrak.ir/short/eqYkB/?ref=1'));
        $this->assertSame('ABC123', ShortLink::codeFromUrl('ABC123'));
        $this->assertNull(ShortLink::codeFromUrl('https://asreatrak.ir/news/یک-خبر'));
        $this->assertNull(ShortLink::make('bad code/with slash?'));
        $this->assertTrue(ShortLink::isTimestampCode('20261031124151'));
        $this->assertFalse(ShortLink::isTimestampCode('OLD-000001'));
    }
}
