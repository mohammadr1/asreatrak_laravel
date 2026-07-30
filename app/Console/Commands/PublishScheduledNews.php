<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\News;
use App\Enums\NewsStatus;


class PublishScheduledNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // News::where('status', NewsStatus::Scheduled->value)
        //     ->where('published_at', '<=', now())
        //     ->each(function ($news) {

        //         $news->update([
        //             'status' => NewsStatus::Published,
        //             'published_at' => now(),
        //         ]);

        //     });


        // $this->info('Scheduled news published.');

    News::where('status', NewsStatus::Scheduled->value)
        ->where('published_at', '<=', now())
        ->each(function ($news) {

            $news->update([
                'status' => NewsStatus::Published->value,
            ]);

        });


    $this->info('Scheduled news published.');
    }
}
