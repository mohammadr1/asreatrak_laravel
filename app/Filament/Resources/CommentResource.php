<?php

namespace App\Filament\Resources;

use App\Enums\CommentStatus;
use App\Filament\Resources\CommentResource\Pages;
use App\Mail\CommentReplied;
use App\Models\Comment;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Morilog\Jalali\Jalalian;


class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'نظرات کاربران';
    protected static ?string $modelLabel = 'نظر';
    protected static ?string $pluralModelLabel = 'نظرات';
    protected static ?int $navigationSort = 20;

    /* ---------- دسترسی: ادمین و سردبیر ---------- */
    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return $user && ($user->hasRole('Admin') || $user->can('news.approve'));
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    /* ---------- شمارنده‌ی نظرهای در انتظار کنار منو ---------- */
    public static function getNavigationBadge(): ?string
    {
        $count = Comment::query()
            ->whereNull('parent_id')
            ->where('status', CommentStatus::Pending)
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    /* ---------- فقط نظرهای اصلی؛ پاسخ‌ها داخل همان نظر می‌آیند ---------- */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereNull('parent_id')
            ->with('news')
            ->withCount('replies');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('نام')
                    ->searchable(),

                Tables\Columns\TextColumn::make('body')
                    ->label('متن نظر')
                    ->limit(90)
                    ->wrap()
                    ->searchable(),

                Tables\Columns\TextColumn::make('news.title')
                    ->label('خبر')
                    ->limit(40)
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge(),

                Tables\Columns\IconColumn::make('replied')
                    ->label('پاسخ داده شده')
                    ->boolean()
                    ->getStateUsing(fn (Comment $record): bool => $record->replies_count > 0),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ثبت')
                    ->formatStateUsing(fn ($state) => $state
                        ? Jalalian::fromDateTime($state)->format('Y/m/d H:i')
                        : '-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('emailOrMobile')
                    ->label('ایمیل / موبایل')
                    ->searchable()
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options(CommentStatus::class)
                    ->default(CommentStatus::Pending->value), // پیش‌فرض: فقط در انتظار تأیید
            ])
            ->actions([

                Tables\Actions\Action::make('approve')
                    ->label('تأیید')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Comment $record) => $record->status !== CommentStatus::Approved)
                    ->action(function (Comment $record) {
                        $record->update([
                            'status'      => CommentStatus::Approved,
                            'approved_at' => now(),
                        ]);

                        Notification::make()->title('نظر تأیید شد.')->success()->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('رد')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Comment $record) => $record->status !== CommentStatus::Rejected)
                    ->action(function (Comment $record) {
                        $record->update(['status' => CommentStatus::Rejected]);

                        Notification::make()->title('نظر رد شد.')->success()->send();
                    }),

                Tables\Actions\Action::make('reply')
                    ->label('پاسخ')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('primary')
                    ->visible(fn (Comment $record) => $record->status !== CommentStatus::Rejected)
                    ->modalHeading('پاسخ به نظر')
                    ->modalDescription(fn (Comment $record) => $record->name . ': ' . $record->body)
                    ->modalSubmitActionLabel('ارسال پاسخ')
                    ->form([
                        Forms\Components\Textarea::make('body')
                            ->label('متن پاسخ')
                            ->required()
                            ->rows(5)
                            ->maxLength(2000),
                    ])
                    ->action(function (Comment $record, array $data) {
                        // اگر نظر هنوز تأیید نشده بود، با پاسخ دادن تأیید می‌شود تا پاسخ دیده شود
                        if ($record->status !== CommentStatus::Approved) {
                            $record->update([
                                'status'      => CommentStatus::Approved,
                                'approved_at' => now(),
                            ]);
                        }

                        $reply = $record->replies()->create([
                            'news_id'     => $record->news_id,
                            'user_id'     => auth()->id(),
                            'name'        => 'مدیریت سایت',
                            'body'        => $data['body'],
                            'status'      => CommentStatus::Approved,
                            'approved_at' => now(),
                        ]);

                        Notification::make()->title('پاسخ ثبت شد.')->success()->send();

                        // اطلاع‌رسانی ایمیلی، فقط اگر کاربر درخواستش را داده باشد
                        if ($record->notify_me && $record->contact_is_email) {
                            try {
                                Mail::to($record->emailOrMobile)->queue(new CommentReplied($record, $reply));
                            } catch (\Throwable $e) {
                                report($e);

                                Notification::make()
                                    ->title('پاسخ ثبت شد، ولی ارسال ایمیل ناموفق بود.')
                                    ->warning()
                                    ->send();
                            }
                        }
                    }),

                Tables\Actions\Action::make('open')
                    ->label('نمایش در سایت')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->visible(fn (Comment $record) => $record->status === CommentStatus::Approved)
                    ->url(fn (Comment $record) => route('news.show', $record->news) . '#comment-' . $record->id)
                    ->openUrlInNewTab(),

                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->label('تأیید انتخاب‌شده‌ها')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(fn (Collection $records) => $records->each->update([
                            'status'      => CommentStatus::Approved,
                            'approved_at' => now(),
                        ])),

                    Tables\Actions\BulkAction::make('reject')
                        ->label('رد انتخاب‌شده‌ها')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion()
                        ->action(fn (Collection $records) => $records->each->update([
                            'status' => CommentStatus::Rejected,
                        ])),

                    Tables\Actions\DeleteBulkAction::make()->label('حذف انتخاب‌شده‌ها'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageComments::route('/'),
        ];
    }
}
