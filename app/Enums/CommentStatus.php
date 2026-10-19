<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CommentStatus: string implements HasLabel, HasColor
{
    case Pending  = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending  => 'در انتظار تأیید',
            self::Approved => 'تأیید شده',
            self::Rejected => 'رد شده',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Pending  => 'warning',
            self::Approved => 'success',
            self::Rejected => 'danger',
        };
    }
}