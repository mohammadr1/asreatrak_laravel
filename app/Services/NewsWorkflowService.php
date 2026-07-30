<?php

namespace App\Services;

use App\Enums\NewsStatus;
use App\Models\News;
use Symfony\Component\HttpKernel\Exception\HttpException;

class NewsWorkflowService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        
    }

    public function submit(News $news)
        {
            if ($news->status !== NewsStatus::Draft) {
                abort(403, 'Only draft can be submitted');
            }

            $news->update([
                'status' => NewsStatus::Pending,
            ]);
        }

        public function approve(News $news, int $userId)
        {
            if ($news->status !== NewsStatus::Pending) {
                abort(403, 'Only pending can be approved');
            }

            $news->update([
                'status' => NewsStatus::Approved,
                'approved_by' => $userId,
                'approved_at' => now(),
            ]);
        }

        public function reject(News $news, string $reason)
        {
            if ($news->status !== NewsStatus::Pending) {
                abort(403, 'Only pending can be rejected');
            }

            $news->update([
                'status' => NewsStatus::Rejected,
                'rejection_reason' => $reason,
            ]);
        }

        public function publish(News $news)
        {
            if ($news->status !== NewsStatus::Approved) {
                abort(403, 'Only approved can be published');
            }

            $news->update([
                'status' => NewsStatus::Published,
                'published_at' => now(),
            ]);
        }

    /**
     * بررسی وضعیت مجاز
     */
        private function ensureStatus(
            News $news,
            NewsStatus ...$allowed
        ): void {
            if (! in_array($news->status, $allowed, true)) {
                throw new HttpException(
                    403,
                    'Operation is not allowed for current status.'
                );
            }
        }

}
