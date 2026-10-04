<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Enums\NewsStatus;
use App\Filament\Resources\NewsResource;
use App\Models\News;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Morilog\Jalali\Jalalian;

class PendingNews extends ListRecords
{
    protected static string $resource = NewsResource::class;

    protected static ?string $title = 'اخبار در انتظار تأیید';

    public static function canAccess(array $parameters = []): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->hasAnyRole([
            'Admin',
            'Editor',
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                News::query()
                    ->where('status', NewsStatus::Pending)
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('news_code')
                    ->label('کد خبر')
                    ->searchable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('عنوان خبر')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('reporter.name')
                    ->label('خبرنگار')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ثبت خبر')
                    ->formatStateUsing(fn ($state) => 
                        $state 
                            ? Jalalian::fromDateTime($state)->format('Y/m/d H:i') 
                            : '-'
                    )
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(
                        fn ($state) => match ($state) {
                            NewsStatus::Pending => 'در انتظار تأیید',
                            default => $state?->value ?? $state,
                        }
                    ),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('تأیید')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('تأیید خبر')
                    ->modalDescription(
                        'آیا از تأیید این خبر مطمئن هستید؟'
                    )
                    ->action(function (News $record): void {
                        $record->transitionTo(
                            NewsStatus::Approved,
                            auth()->user()
                        );
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('رد خبر')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('رد خبر')
                    ->modalDescription(
                        'دلیل رد خبر را وارد کنید.'
                    )
                    ->form([
                        \Filament\Forms\Components\Textarea::make(
                            'rejection_reason'
                        )
                            ->label('دلیل رد')
                            ->required()
                            ->rows(4),
                    ])
                    ->action(function (
                        News $record,
                        array $data
                    ): void {
                        $record->transitionTo(
                            NewsStatus::Rejected,
                            auth()->user(),
                            [
                                'rejection_reason' => $data['rejection_reason'],
                            ]
                        );
                    }),

                Tables\Actions\EditAction::make()
                    ->label('مشاهده / ویرایش'),
            ]);
    }
}