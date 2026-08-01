<?php

namespace App\Filament\Resources\WatermarkResource\Tables;

use Filament\Tables;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Table;


class WatermarkTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->disk('public')
                    ->square(),

                TextColumn::make('title')
                    ->searchable(),

                TextColumn::make('type'),

                TextColumn::make('user.first_name')
                    ->label('خبرنگار'),

                IconColumn::make('is_active')
                    ->boolean(),

            ])



            ->filters([


            ])


            ->actions([

            ])


            ->bulkActions([

                Tables\Actions\BulkActionGroup::make([

                    Tables\Actions\DeleteBulkAction::make(),

                ]),

            ]);
    }
}