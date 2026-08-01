<?php

namespace App\Filament\Resources\MediaResource\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;

class MediaTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->columns([

                ImageColumn::make('filename')
                    ->disk('public')
                    ->square(),

                TextColumn::make('title')
                    ->searchable(),

                TextColumn::make('type'),

                TextColumn::make('size')
                    ->formatStateUsing(fn ($state) =>
                        number_format($state / 1024, 1).' KB'),

                TextColumn::make('uploader.full_name')
                    ->label('آپلودکننده'),

                IconColumn::make('is_active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->since(),

            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'image' => 'تصویر',
                        'video' => 'ویدئو',
                        'audio' => 'صوت',
                        'document' => 'سند',
                    ]),
            ])

            ->defaultSort('id','desc');

    }
}