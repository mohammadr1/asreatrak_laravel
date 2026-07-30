<?php

namespace App\Filament\Resources\NewsResource\Schemas;


use Filament\Forms\Form;

use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;

use Filament\Forms\Components\Section;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;

use Filament\Forms\Components\Select;

use Filament\Forms\Components\FileUpload;

use Filament\Forms\Components\DateTimePicker;

use Filament\Forms\Components\Toggle;


use Filament\Forms\Components\Grid;
use Illuminate\Support\Str;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Radio;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use App\Enums\NewsStatus;

class NewsForm
{
    public static function make(Form $form): Form
    {
        return $form
            ->schema([

                Tabs::make('News')
                    ->columnSpanFull()

                    ->tabs([

                        Tab::make('خبر')
                            ->schema([

                                Section::make('اطلاعات اصلی خبر')
                                    ->schema([

                                        Grid::make(2)
                                            ->schema([

                                                TextInput::make('uptitle')
                                                    ->label('روتیتر')
                                                    ->maxLength(255)
                                                    ->columnSpanFull(),


                                                TextInput::make('title')
                                                    ->label('عنوان خبر')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(function ($state, callable $set) {

                                                        if (filled($state)) {
                                                            $set(
                                                                'slug',
                                                                Str::slug($state)
                                                            );
                                                        }

                                                    }),


                                                TextInput::make('news_code')
                                                    ->label('کد خبر')
                                                    ->default(function () {

                                                        return now()->format('YmdHis');

                                                    })
                                                    ->required()
                                                    ->unique(ignoreRecord: true),

                                                Select::make('reporter_id')
                                                    ->label('خبرنگار')
                                                    ->relationship(
                                                        name: 'reporter',
                                                        titleAttribute: 'first_name'
                                                    )
                                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->default(auth()->id())
                                                    ->disabled(fn () => auth()->user()->hasRole('Reporter')),


                                                    // Select::make('categories')
                                                    //     ->relationship('categories', 'name')
                                                    //     ->multiple()
                                                    //     ->preload()
                                                    //     ->searchable(),


                                                    Select::make('categories')
                                                        ->label('دسته‌بندی')
                                                        ->relationship(
                                                            'categories',
                                                            'name'
                                                        )
                                                        ->multiple()
                                                        ->searchable()
                                                        ->preload()
                                                        ->required(),
                                                    

                                                    Select::make('tags')
                                                        ->label('برچسب‌ها')
                                                        ->relationship(
                                                            'tags',
                                                            'name'
                                                        )
                                                        ->multiple()
                                                        ->searchable()
                                                        ->preload(),

                                            ]),


                                        Textarea::make('lead')
                                            ->label('لید خبر')
                                            ->required()
                                            ->rows(4)
                                            ->maxLength(1000)
                                            ->columnSpanFull(),


                                        RichEditor::make('content')
                                            ->label('متن خبر')
                                            ->required()
                                            ->columnSpanFull()
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'underline',
                                                'strike',
                                                'link',
                                                'bulletList',
                                                'orderedList',
                                                'blockquote',
                                                'codeBlock',
                                                'h2',
                                                'h3',
                                            ]),

                                        Placeholder::make('editor_comment')
                                            ->label('پیام سردبیر')

                                            ->content(fn ($record) =>

                                                $record?->rejection_reason
                                                    ?: 'پیامی ثبت نشده است.'

                                            )

                                            ->visible(fn ($record) =>

                                                $record
                                                &&

                                                $record->status === NewsStatus::Rejected

                                            ),

                                    ])
                                    ->columns(1),



                                Section::make('تنظیمات فنی')
                                    ->schema([

                                        TextInput::make('slug')
                                            ->label('اسلاگ')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255),


                                    ])
                                    ->collapsed(),



                    ])
                    ->columnSpanFull(),


                        Tab::make('رسانه')
    ->schema([

        Section::make('رسانه‌های خبر')
            ->columnSpanFull()
            ->schema([

                Repeater::make('media')
                    ->relationship()
                    ->label('لیست رسانه‌ها')
                    ->schema([

                        Select::make('type')
                            ->label('نوع رسانه')
                            ->options([
                                'image' => 'تصویر',
                                'video' => 'ویدئو',
                                'audio' => 'صوت',
                                'document' => 'سند',
                            ])
                            ->required()
                            ->live(),


                        FileUpload::make('path')
                            ->label('فایل رسانه')
                            ->disk('public')
                            ->directory('news/media')
                            ->visible(fn ($get) =>
                                in_array($get('type'), [
                                    'image',
                                    'audio',
                                    'document'
                                ])
                            ),


                        TextInput::make('video_url')
                            ->label('لینک ویدئو')
                            ->placeholder('https://aparat.com/...')
                            ->visible(fn ($get) =>
                                $get('type') === 'video'
                            ),


                        TextInput::make('provider')
                            ->label('سرویس دهنده')
                            ->placeholder('aparat / youtube / upload'),


                        TextInput::make('title')
                            ->label('عنوان رسانه'),


                        Textarea::make('caption')
                            ->label('کپشن')
                            ->rows(3),


                        TextInput::make('sort_order')
                            ->label('ترتیب نمایش')
                            ->numeric()
                            ->default(0),


                        Toggle::make('is_featured')
                            ->label('رسانه شاخص'),


                    ])
                    ->columns(2)
                    ->defaultItems(0)
                    ->addActionLabel('افزودن رسانه'),

            ]),

    ]),

                        Tab::make('تنظیمات')
                            ->schema([

                                Select::make('status')
                                    ->label('وضعیت')
                                    ->options([
                                        NewsStatus::Draft->value => 'پیش‌نویس',
                                        NewsStatus::Scheduled->value => 'زمان‌بندی انتشار',
                                    ])
                                    ->default(NewsStatus::Draft->value)
                                    
                                    ->live(),


                                DateTimePicker::make('published_at')
                                    ->label('زمان انتشار')
                                    ->seconds(false)
                                    ->timezone('Asia/Tehran')
                                    ->disabled(fn () => auth()->user()->hasRole('Reporter'))
                                    ->visible(fn ($get) => $get('status') === NewsStatus::Scheduled->value)
                                    ->required(fn ($get) => $get('status') === NewsStatus::Scheduled->value),

                            ]),

                    ])

            ]);
    }
}
