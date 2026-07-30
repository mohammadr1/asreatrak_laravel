<?php

namespace App\Filament\Resources\NewsResource\Tables;

use Filament\Tables\Table;
use App\Enums\NewsStatus;
use Filament\Tables;
use Filament\Notifications\Notification;
use Filament\Forms;
use Morilog\Jalali\Jalalian;


class NewsTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable(),

                Tables\Columns\TextColumn::make('reporter.name')
                    ->label('خبرنگار')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn (NewsStatus $state) => match ($state) {
                        NewsStatus::Draft => 'پیش‌نویس',
                        NewsStatus::Pending => 'در انتظار بررسی',
                        NewsStatus::Approved => 'تأیید شده',
                        NewsStatus::Rejected => 'رد شده',
                        NewsStatus::Published => 'منتشر شده',
                        NewsStatus::Scheduled => 'زمان‌بندی شده',
                    })
                    ->color(fn (NewsStatus $state) => match ($state) {
                        NewsStatus::Draft => 'gray',
                        NewsStatus::Pending => 'warning',
                        NewsStatus::Approved => 'success',
                        NewsStatus::Rejected => 'danger',
                        NewsStatus::Published => 'primary',
                        NewsStatus::Scheduled => 'info',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('زمان انتشار')
                    ->formatStateUsing(fn ($state) =>
                            $state
                                ? Jalalian::fromDateTime($state)->format('Y/m/d H:i')
                                : '-'
                        )
                    // ->dateTime()
                    ->sortable()
                    ->toggleable(),


            ])

            ->actions([

            
                Tables\Actions\Action::make('submit')
                    ->label('ارسال برای بررسی')
                    ->color('warning')

                    ->requiresConfirmation()
                    ->icon('heroicon-o-paper-airplane')
                    ->action(function ($record) {

                        $record->transitionTo(
                            NewsStatus::Pending,
                            auth()->user(),
                            [
                                'rejection_reason' => null,
                            ]
                        );
                        Notification::make()
                            ->title('خبر برای بررسی ارسال شد.')
                            ->success()
                            ->send();
                    })

                    ->visible(fn($record)=> 
                        auth()->check()
                        &&
                        in_array(
                            $record->status,
                            [
                                NewsStatus::Draft,
                                NewsStatus::Rejected,
                            ],
                            true
                        )
                        &&
                        auth()->user()->hasRole('Reporter')
                    ),


                Tables\Actions\Action::make('approve')

                    ->label('تایید خبر')

                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->action(function($record){

                    $record->transitionTo(
                        NewsStatus::Approved,
                        auth()->user(),
                        [
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                            'rejection_reason' => null,
                        ]
                    );
                    Notification::make()
                        ->title('خبر با موفقیت تأیید شد.')
                        ->success()
                        ->send();

                        // $record->update([

                        //     'status'=>NewsStatus::Approved,

                        //     'approved_by'=>auth()->id(),

                        //     'approved_at'=>now(),

                        // ]);

                    })

                    ->visible(fn($record)=>

                        auth()->check()
                        &&
                        $record->status === NewsStatus::Pending
                        &&
                        auth()->user()->can('news.approve')

                    ),


                Tables\Actions\Action::make('reject')
                    ->label('رد خبر')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')

                    ->form([

                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('دلیل رد خبر')
                            ->required()
                            ->rows(5)
                            ->placeholder('دلیل رد خبر را برای خبرنگار بنویسید...'),

                    ])

                    ->action(function ($record, array $data) {

                        $record->transitionTo(
                            NewsStatus::Rejected,
                            auth()->user(),
                            [
                                'rejection_reason' => $data['rejection_reason'],
                            ]
                        );

                        Notification::make()
                            ->title('خبر رد شد.')
                            ->success()
                            ->send();

                    })

                    ->visible(fn ($record) =>

                        auth()->check()

                        &&

                        auth()->user()->can('news.approve')

                        &&

                        $record->status === NewsStatus::Pending

                    ),


                    
                Tables\Actions\Action::make('publish')

                    ->label('انتشار')
                    ->icon('heroicon-o-globe-alt')
                    ->action(function($record){


                        $record->transitionTo(
                            NewsStatus::Published,
                            auth()->user(),
                            [
                                'published_at' => now(),
                            ]
                        );
                        Notification::make()
                            ->title('خبر منتشر شد.')
                            ->success()
                            ->send();

                        // $record->update([

                        //     'status'=>NewsStatus::Published,

                        //     'published_at'=>now(),

                        // ]);

                    })

                    ->visible(fn($record)=>

                    auth()->check()
                    &&
                    $record->status === NewsStatus::Approved

                    &&

                    auth()->user()->can('news.publish')

                    ),


                    Tables\Actions\Action::make('schedule')

                        ->label('زمان‌بندی انتشار')

                        ->icon('heroicon-o-clock')

                        ->color('warning')

                        ->form([

                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('زمان انتشار')
                            ->seconds(false)
                            ->minDate(now())
                            ->required(),


                        


        ])

        ->action(function ($record, array $data) {

            $record->transitionTo(
                NewsStatus::Scheduled,
                auth()->user(),
                [
                    'published_at' => $data['published_at'],
                ]
            );

            Notification::make()
                ->title('خبر زمان‌بندی شد.')
                ->success()
                ->send();

        })

        ->visible(fn ($record) =>

            auth()->check()

            &&

            $record->status === NewsStatus::Approved

            &&

            auth()->user()->can('news.publish')

        ),


        Tables\Actions\Action::make('publish_now')
                            ->label('انتشار فوری')
                            ->icon('heroicon-o-bolt')
                            ->color('success')

                            ->action(function ($record) {

                                // $record->update([
                                //     'status' => NewsStatus::Published,
                                //     'published_at' => now(),
                                // ]);
                                $record->transitionTo(
                                    NewsStatus::Published,
                                    auth()->user(),
                                    [
                                        'published_at' => now(),
                                    ]
                                );
                                
                                Notification::make()
                                    ->title('خبر منتشر شد.')
                                    ->success()
                                    ->send();
                            })

                            ->visible(fn ($record) =>
                                auth()->check()
                                &&
                                $record->status === NewsStatus::Scheduled
                                &&
                                auth()->user()->can('news.publish')
                            ),

                    
            ]);
    }
}
