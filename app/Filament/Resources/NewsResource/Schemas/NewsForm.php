<?php

namespace App\Filament\Resources\NewsResource\Schemas;

use App\Enums\NewsStatus;
use Filament\Forms\Form;
use Illuminate\Support\Str;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;

class NewsForm
{
    public static function make(Form $form): Form
    {
        return $form
            ->schema([

                Grid::make(12)
                    ->schema([

                        /*
                        |--------------------------------------------------------------------------
                        | ستون اصلی
                        |--------------------------------------------------------------------------
                        */

                        Grid::make()
                            ->columnSpan([
                                'lg' => 8,
                            ])
                            ->schema([

                                Section::make('اطلاعات اصلی خبر')
                                    ->columns(2)
                                    ->schema([

                                        TextInput::make('uptitle')
                                            ->label('روتیتر')
                                            ->maxLength(255)
                                            ->columnSpanFull(),

                                        TextInput::make('title')
                                            ->label('عنوان خبر')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->maxLength(255)
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
                                            ->required()
                                            ->default(fn () => now()->format('YmdHis'))
                                            ->unique(ignoreRecord: true),

                                        Select::make('reporter_id')
                                            ->label('خبرنگار')
                                            ->relationship(
                                                name: 'reporter',
                                                titleAttribute: 'first_name'
                                            )
                                            ->getOptionLabelFromRecordUsing(
                                                fn ($record) => $record->name
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->default(auth()->id())
                                            ->disabled(
                                                fn () => auth()->user()->hasRole('Reporter')
                                            ),

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
                                            ->content(
                                                fn ($record) =>
                                                    $record?->rejection_reason
                                                    ?: 'پیامی ثبت نشده است.'
                                            )
                                            ->visible(
                                                fn ($record) =>
                                                    $record &&
                                                    $record->status === NewsStatus::Rejected
                                            ),

                                    ]),

                                Section::make('تنظیمات فنی')
                                    ->collapsed()
                                    ->schema([

                                        TextInput::make('slug')
                                            ->label('اسلاگ')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255),

                                    ]),

                            ]),

                                                    /*
                        |--------------------------------------------------------------------------
                        | ستون کناری
                        |--------------------------------------------------------------------------
                        */

                        Grid::make()
                            ->columnSpan([
                                'lg' => 4,
                            ])
                            ->schema([

                        Section::make('تصویر شاخص')
                            ->schema([

                                Actions::make([

                                    Action::make('selectFeatured')
                                        ->label('انتخاب تصویر')
                                        ->icon('heroicon-o-photo')
                                        ->modalHeading('انتخاب تصویر شاخص')
                                        ->modalWidth('7xl')
                                        ->modalSubmitAction(false)
                                        ->modalCancelActionLabel('بستن')
                                        ->modalContent(fn () => view(
                                            'filament.media.featured-picker'
                                        )),

                                ]),

                                Placeholder::make('featured_preview')
                                    ->hiddenLabel()
                                    ->content('هنوز تصویری انتخاب نشده است.'),

                            ]),


                                Section::make('انتشار')
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
                                            ->disabled(
                                                fn () => auth()->user()->hasRole('Reporter')
                                            )
                                            ->visible(
                                                fn ($get) =>
                                                    $get('status') === NewsStatus::Scheduled->value
                                            )
                                            ->required(
                                                fn ($get) =>
                                                    $get('status') === NewsStatus::Scheduled->value
                                            ),

                                    ]),


                                Section::make('دسته‌بندی و برچسب')
                                    ->schema([

                                        Select::make('categories')
                                            ->label('دسته‌بندی')
                                            ->relationship('categories', 'name')
                                            ->multiple()
                                            ->searchable()
                                            ->preload()
                                            ->required(),

                                        Select::make('tags')
                                            ->label('برچسب‌ها')
                                            ->relationship('tags', 'name')
                                            ->multiple()
                                            ->searchable()
                                            ->preload(),

                                    ]),

                            ]),

                    ]),

            ]);
    }
}