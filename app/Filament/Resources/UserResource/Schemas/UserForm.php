<?php

namespace App\Filament\Resources\UserResource\Schemas;


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


class UserForm
{

    public static function make(Form $form): Form
    {

return $form
    ->schema([

        Section::make('اطلاعات کاربر')
            ->schema([

                Grid::make(2)
                    ->schema([

                        TextInput::make('first_name')
                            ->label('نام')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {

                                $set(
                                    'slug',
                                    Str::slug(
                                        trim($state . ' ' . $get('last_name'))
                                    )
                                );

                            }),

                        TextInput::make('last_name')
                            ->label('نام خانوادگی')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {

                                $set(
                                    'slug',
                                    Str::slug(
                                        trim($get('first_name') . ' ' . $state)
                                    )
                                );

                            }),

                        TextInput::make('slug')
                            ->label('اسلاگ')
                            ->required()
                            ->unique(ignoreRecord: true),

                        TextInput::make('email')
                            ->email()
                            ->unique(ignoreRecord: true),

                        TextInput::make('phone')
                            ->label('موبایل')
                            ->unique(ignoreRecord: true),

                        TextInput::make('password')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn ($operation) => $operation === 'create'),

                    ])

            ]),

        Section::make('پروفایل')
            ->schema([

                FileUpload::make('profile_image')
                    ->label('تصویر')
                    ->disk('public')
                    ->directory('users'),

                Textarea::make('bio')
                    ->label('بیوگرافی')
                    ->rows(4),

                // FileUpload::make('watermark')
                //     ->label('واترمارک')
                //     ->image()
                //     ->directory('watermarks/users')
            ]),

        Section::make('دسترسی')
            ->schema([

                Select::make('roles')
                    ->label('نقش')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->searchable(),

                Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true),

                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'banned' => 'مسدود',
                        'suspended' => 'تعلیق',
                        'deleted_by_admin' => 'حذف توسط مدیر',
                    ])
                    ->default('active'),

            ]),

    ]);

    }

}