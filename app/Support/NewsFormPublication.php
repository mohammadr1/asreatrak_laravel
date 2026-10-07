<?php

namespace App\Support;

use App\Enums\NewsStatus;
use App\Models\News;
use App\Models\User;
use Illuminate\Validation\ValidationException;

final class NewsFormPublication
{
    public static function canPublish(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Editor']);
    }

    public static function options(User $user, ?News $record = null): array
    {
        $options = [NewsStatus::Draft->value => 'پیش‌نویس'];
        if (self::canPublish($user)) {
            $options += [NewsStatus::Published->value => 'انتشار', NewsStatus::Scheduled->value => 'زمان‌بندی انتشار'];
        } elseif ($user->hasRole('Reporter') && (! $record || in_array($record->status, [NewsStatus::Draft, NewsStatus::Rejected, NewsStatus::Pending], true))) {
            $options[NewsStatus::Pending->value] = 'ارسال برای سردبیر';
        } else {
            $options = [];
        }
        if ($record) {
            $options[$record->status->value] ??= match ($record->status) {
                NewsStatus::Draft => 'پیش‌نویس', NewsStatus::Pending => 'در انتظار بررسی سردبیر',
                NewsStatus::Approved => 'تأیید شده', NewsStatus::Rejected => 'رد شده',
                NewsStatus::Scheduled => 'زمان‌بندی انتشار', NewsStatus::Published => 'منتشر شده',
            };
        }
        return $options;
    }

    public static function prepare(array $data, User $user, ?News $record = null): array
    {
        $status = $data['status'] ?? $record?->status->value ?? NewsStatus::Draft->value;
        if (! array_key_exists($status, self::options($user, $record))) {
            throw ValidationException::withMessages(['data.status' => 'اجازه انتخاب این وضعیت انتشار را ندارید.']);
        }
        $data['status'] = $status;
        if (! self::canPublish($user)) {
            unset($data['published_at'], $data['approved_at'], $data['approved_by']);
            if ($status === NewsStatus::Pending->value && $record?->status !== NewsStatus::Pending) {
                $data['published_at'] = null;
                $data['approved_at'] = null;
                $data['approved_by'] = null;
                $data['rejection_reason'] = null;
            }
            return $data;
        }
        if ($status === NewsStatus::Published->value) {
            // Editing a published article must not reset its original publication time.
            $data['published_at'] = $record?->status === NewsStatus::Published ? ($record->published_at ?? now()) : now();
            $data['approved_by'] = $record?->approved_by ?? $user->id;
            $data['approved_at'] = $record?->approved_at ?? now();
            $data['rejection_reason'] = null;
        } elseif ($status === NewsStatus::Scheduled->value) {
            if (blank($data['published_at'] ?? null)) {
                throw ValidationException::withMessages(['data.published_at' => 'برای انتشار زمان‌بندی‌شده، تاریخ و ساعت انتشار را وارد کنید.']);
            }
        } else {
            $data['published_at'] = null;
        }
        return $data;
    }
}
