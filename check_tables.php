<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
dump(Illuminate\Support\Facades\Schema::getColumnListing('news'));
$n = App\Models\News::latest('id')->first();
dump($n ? $n->toArray() : 'no news');
echo "news count: " . App\Models\News::count() . PHP_EOL;
echo "media count: " . App\Models\Media::count() . PHP_EOL;
echo "news with media: " . App\Models\News::query()->whereHas('media')->count() . PHP_EOL;
$nm = App\Models\News::query()->whereHas('media')->latest('id')->first();
dump($nm->only('id', 'title', 'news_code', 'featured_media_id'));
dump($nm->media->map->only('id', 'directory', 'filename', 'watermarked_path', 'original_path')->toArray());

// Scan many OLD news for image src patterns
$map = [];
foreach (glob(public_path('images/2026/*/*_original.*')) as $file) {
    $base = basename($file);
    if (preg_match('/^([a-f0-9-]{36})_original\./', $base, $m)) {
        $map[$m[1]] = ltrim(str_replace([public_path(), '\\'], ['', '/'], $file), '/');
    }
}
echo "uuid originals found: " . count($map) . PHP_EOL;
echo "news mentioning images/2026: " . App\Models\News::where('content', 'like', '%images/2026/%')->count() . PHP_EOL;
echo "media uuids matching images map: " . App\Models\Media::whereIn('uuid', array_keys($map))->count() . PHP_EOL;
echo "featured set total: " . App\Models\News::whereNotNull('featured_media_id')->count() . PHP_EOL;
echo "news with featured_media_id IN media ids: " . App\Models\News::whereIn('featured_media_id', App\Models\Media::select('id'))->count() . PHP_EOL;
// sample urls of media that ARE attached
$m2 = App\Models\Media::whereNotNull('watermarked_path')->latest('id')->first();
echo "sample: /storage/" . $m2->watermarked_path . ' exists=' . (file_exists(public_path('storage/' . $m2->watermarked_path)) ? 'YES' : 'NO') . PHP_EOL;
die;

$srcs = [];
App\Models\News::where('news_code', 'like', 'OLD%')->where('content', 'like', '%<img%')->select('id','content')->chunk(500, function ($chunk) use (&$srcs) {
    foreach ($chunk as $nn) {
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/', $nn->content, $mm);
        foreach ($mm[1] as $s) { $srcs[] = $s; }
    }
});
echo "total img srcs: " . count($srcs) . PHP_EOL;
$patterns = [];
foreach ($srcs as $s) {
    $p = preg_replace('/[a-f0-9-]{36}/', '{uuid}', $s);
    $p = preg_replace('/\d+/', '{n}', $p);
    $patterns[$p] = ($patterns[$p] ?? 0) + 1;
}
arsort($patterns);
print_r(array_slice($patterns, 0, 10, true));
foreach (array_slice($srcs, 0, 5) as $s) {
    $p = ltrim(parse_url($s, PHP_URL_PATH), '/');
    echo $s . ' => ' . (file_exists(public_path($p)) ? 'EXISTS' : 'MISSING') . PHP_EOL;
}
