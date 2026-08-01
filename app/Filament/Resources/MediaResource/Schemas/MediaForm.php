<?php

namespace App\Filament\Resources\MediaResource\Schemas;

use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;

class MediaForm
{
    public static function make(Form $form): Form
    {
        return $form
            ->schema([

                        Section::make('آپلود فایل')
                ->schema([

                    FileUpload::make('filename')
                        ->disk('public')
                        ->directory('media')
                        ->visibility('public')
                        ->required()

                        ->imageEditor()

                        ->imagePreviewHeight('220')

                        ->downloadable()

                        ->openable(),

                ]),

            Section::make('اطلاعات')
                ->schema([

                    TextInput::make('title')
                        ->label('عنوان'),

                    TextInput::make('alt')
                        ->label('Alt'),

                    Textarea::make('caption')
                        ->label('توضیحات'),

                    Toggle::make('is_active')
                        ->default(true),

                    Select::make('visibility')
                        ->options([
                            'public' => 'عمومی',
                            'private' => 'خصوصی',
                        ])
                        ->default('public'),

                ]),

            ]);
    }
}