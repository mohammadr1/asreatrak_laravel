<?php

namespace App\Filament\Resources\WatermarkResource\Schemas;


use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

use Illuminate\Support\Str;

use Spatie\Permission\Models\Role;

use Filament\Forms\Components\Grid;


class WatermarkForm
{

    public static function make(Form $form): Form
    {

        return $form
            ->schema([

                Section::make('واترمارک')
                    ->schema([

                        TextInput::make('title')
                            ->required(),

                        FileUpload::make('image')
                            ->disk('public')
                            ->directory('watermarks')
                            ->image()
                            ->required(),

                        Select::make('type')
                            ->options([
                                'system' => 'خبرگزاری',
                                'user' => 'اختصاصی خبرنگار',
                            ])
                            ->live()
                            ->required(),

                        Select::make('user_id')
                            ->relationship('user', 'first_name')
                            ->searchable()
                            ->preload()
                            ->visible(fn ($get) => $get('type') === 'user'),

                        Toggle::make('is_active')
                            ->default(true),


                    ])

            ]);

    }

}