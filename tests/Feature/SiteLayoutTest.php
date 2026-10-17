<?php

namespace Tests\Feature;

use App\Models\News;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SiteLayoutTest extends TestCase
{
    public function test_home_and_article_render_the_same_shared_theme_once(): void
    {
        config([
            'database.default' => 'layout_testing',
            'database.connections.layout_testing' => [
                'driver' => 'sqlite', 'database' => ':memory:',
                'prefix' => '', 'foreign_key_constraints' => true,
            ],
            'session.driver' => 'array',
            'cache.default' => 'array',
        ]);
        DB::purge('layout_testing');
        $this->artisan('migrate', ['--database' => 'layout_testing', '--force' => true])->assertSuccessful();
        $this->withoutVite();

        $news = News::create([
            'title' => 'Shared theme article',
            'slug' => 'shared-theme-article',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        foreach (['/', route('news.show', $news)] as $url) {
            $response = $this->get($url)->assertOk();
            $html = preg_replace('/<!--.*?-->/s', '', $response->getContent());
            $this->assertSame(1, substr_count($html, 'assets/css/home.css'));
            $this->assertSame(1, substr_count($html, '<header class="site-header">'));
            $this->assertSame(1, substr_count($html, '<title>'));
            $this->assertSame(1, substr_count($html, '<footer'));
        }

        $response->assertSee('Shared theme article');
        $this->assertTrue(strpos($html, 'assets/css/home.css') < strpos($html, 'assets/css/style_single.css'));
    }
}
