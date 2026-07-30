<?php

namespace App\Filament\Resources\CategoryResource\Tables;

use Filament\Tables\Table;
use Filament\Tables;
use Filament\Notifications\Notification;

class UserTable
{
    public static function make(Table $table): Table
    {
        return $table
            ->columns([

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