<?php

namespace App\Filament\Resources\UserResource\Tables;

use Filament\Tables\Table;
use Filament\Tables;
use Filament\Notifications\Notification;

class UserTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\ImageColumn::make('profile_image')
                    ->label('تصویر')
                    ->disk('public')
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->label('نام')
                    ->formatStateUsing(fn ($record) => $record->name)
                    ->searchable([
                        'first_name',
                        'last_name',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('ایمیل')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('موبایل')
                    ->searchable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('نقش')
                    ->badge(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'active' => 'فعال',
                        'banned' => 'مسدود',
                        'suspended' => 'تعلیق',
                        'deleted_by_admin' => 'حذف شده',
                    })
                    ->color(fn ($state) => match ($state) {
                        'active' => 'success',
                        'banned' => 'danger',
                        'suspended' => 'warning',
                        'deleted_by_admin' => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('reported_news_count')
                    ->label('تعداد خبر')
                    ->counts('reportedNews'),

                // Tables\Columns\TextColumn::make('last_login_at')
                //     ->label('آخرین ورود')
                //     ->since(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ عضویت')
                    ->dateTime()
                    ->sortable(),

            ])



            ->filters([

                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->label('نقش'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('فعال'),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'فعال',
                        'banned' => 'مسدود',
                        'suspended' => 'تعلیق',
                        'deleted_by_admin' => 'حذف شده',
                    ]),

            ])


            ->actions([

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),

                    Tables\Actions\DeleteAction::make(),

                    // Tables\Actions\EditAction::make(),

                    Tables\Actions\Action::make('toggle_status')
                        ->label(fn ($record) =>
                            $record->is_active ? 'غیرفعال کردن' : 'فعال کردن'
                        )
                        ->icon(fn ($record) =>
                            $record->is_active
                                ? 'heroicon-o-lock-closed'
                                : 'heroicon-o-lock-open'
                        )
                        ->color(fn ($record) =>
                            $record->is_active ? 'danger' : 'success'
                        )
                        ->requiresConfirmation()
                        ->action(function ($record) {

                            $record->update([
                                'is_active' => ! $record->is_active,
                            ]);

                            Notification::make()
                                ->title('وضعیت کاربر تغییر کرد.')
                                ->success()
                                ->send();
                        })



                    // Tables\Actions\DeleteAction::make(),

                ])

            ])


            ->bulkActions([

                Tables\Actions\BulkActionGroup::make([

                    Tables\Actions\DeleteBulkAction::make(),

                ]),

            ]);
    }
}