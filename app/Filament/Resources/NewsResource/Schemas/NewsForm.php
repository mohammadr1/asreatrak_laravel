<?php

namespace App\Filament\Resources\NewsResource\Schemas;

use App\Enums\NewsStatus;
use Filament\Forms\Form;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use App\Forms\Components\JalaliDateTimePicker;
use Filament\Forms\Components\Hidden;
use App\Models\ReportType;
use Filament\Forms\Components\View;

class NewsForm
{
    public static function make(Form $form): Form
    {
        return $form
            ->schema([

            Section::make('ویدیوی آپارات')
                ->description('ویدیو را در آپارات بارگذاری کنید و لینک یا شناسه آن را اینجا وارد کنید.')
                ->visible(fn ($livewire, $record): bool => $livewire instanceof \App\Filament\Resources\NewsResource\Pages\CreateVideoNews || $record?->post_type === 'video')
                ->schema([
                    TextInput::make('aparat_video_id')
                        ->label('لینک یا شناسه ویدیوی آپارات')
                        ->placeholder('https://www.aparat.com/v/k93yms3')
                        ->helperText('مثال: k93yms3 — تنها شناسه ذخیره می‌شود؛ کد iframe یا HTML وارد نکنید.')
                        ->extraInputAttributes(['dir' => 'ltr', 'autocapitalize' => 'none', 'spellcheck' => 'false'])
                        ->required()
                        ->maxLength(2048)
                        ->rules([fn () => function (string $attribute, $value, \Closure $fail): void {
                            if (\App\Support\AparatVideo::id($value) === null) {
                                $fail('لینک معتبر آپارات یا شناسه ویدیو را وارد کنید.');
                            }
                        }])
                        ->dehydrateStateUsing(fn ($state) => \App\Support\AparatVideo::id($state)),
                ]),

            View::make('filament.forms.validation-summary')
                ->columnSpanFull(),
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
                                                ->unique(ignoreRecord: true)
                                                ->live(onBlur: true)
                                                ->maxLength(255)
                                                ->afterStateUpdated(function (
                                                    $state,
                                                    $old,
                                                    callable $set,
                                                    callable $get
                                                ) {

                                                    if (! filled($state)) {
                                                        return;
                                                    }

                                                    $currentSlug = trim(
                                                        (string) $get('slug')
                                                    );

                                                    /*
                                                    |--------------------------------------------------------------------------
                                                    | ساخت اسلاگ قبلی بر اساس عنوان قبلی
                                                    |--------------------------------------------------------------------------
                                                    */

                                                    $oldSlug = trim(
                                                        (string) $old
                                                    );

                                                    $oldSlug = preg_replace(
                                                        '/\s+/u',
                                                        '-',
                                                        $oldSlug
                                                    );

                                                    $oldSlug = preg_replace(
                                                        '/-+/u',
                                                        '-',
                                                        $oldSlug
                                                    );

                                                    $oldSlug = trim(
                                                        $oldSlug,
                                                        '-'
                                                    );

                                                    /*
                                                    |--------------------------------------------------------------------------
                                                    | ساخت اسلاگ جدید
                                                    |--------------------------------------------------------------------------
                                                    */

                                                    $newSlug = trim(
                                                        (string) $state
                                                    );

                                                    $newSlug = preg_replace(
                                                        '/\s+/u',
                                                        '-',
                                                        $newSlug
                                                    );

                                                    $newSlug = preg_replace(
                                                        '/-+/u',
                                                        '-',
                                                        $newSlug
                                                    );

                                                    $newSlug = trim(
                                                        $newSlug,
                                                        '-'
                                                    );

                                                    /*
                                                    |--------------------------------------------------------------------------
                                                    | تعیین اینکه اسلاگ هنوز خودکار است یا دستی
                                                    |--------------------------------------------------------------------------
                                                    |
                                                    | اگر اسلاگ خالی باشد:
                                                    |     خودکار بساز.
                                                    |
                                                    | اگر اسلاگ فعلی همان اسلاگ عنوان قبلی باشد:
                                                    |     یعنی هنوز دستی تغییر نکرده → آپدیت کن.
                                                    |
                                                    | اگر متفاوت باشد:
                                                    |     یعنی کاربر دستی تغییر داده → دست نزن.
                                                    |
                                                    */

                                                    if (
                                                        $currentSlug === '' ||
                                                        $currentSlug === $oldSlug
                                                    ) {
                                                        $set(
                                                            'slug',
                                                            $newSlug
                                                        );
                                                    }
                                                }),

                                            TextInput::make('news_code')
                                                ->label('کد خبر')
                                                ->default(fn () => now()->format('YmdHis'))
                                                ->required()
                                                ->unique(ignoreRecord: true)
                                                ->disabled()
                                                ->dehydrated(),

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
                                                ->visible(
                                                    fn () => auth()->user()?->hasRole('Admin')
                                                ),

                                            Textarea::make('lead')
                                                ->label('لید خبر')
                                                ->required()
                                                ->rows(4)
                                                ->maxLength(1000)
                                                ->columnSpanFull(),

                                            RichEditor::make('content')
                                                ->id('news-content')
                                                ->label('متن خبر')
                                                ->helperText('ابتدا محل درج را در متن انتخاب کنید، سپس «درج تصویر» یا «درج ویدیوی آپارات» را بزنید. ویدیو در ویرایشگر به شکل لینک و در سایت به شکل پخش‌کننده نمایش داده می‌شود.')
                                                ->hintActions([
                                                    Action::make('insertImage')
                                                        ->label('درج تصویر')->icon('heroicon-o-photo')->button()
                                                        ->modalHeading('درج تصویر در متن خبر')->modalWidth('7xl')
                                                        ->modalSubmitAction(false)->modalCancelActionLabel('بستن')
                                                        ->modalContent(fn () => view('filament.media.editor-picker-wrapper')),
                                                    Action::make('insertAparat')
                                                        ->label('درج ویدیوی آپارات')->icon('heroicon-o-video-camera')->button()
                                                        ->modalHeading('درج ویدیو در محل نشانگر')->modalSubmitActionLabel('درج ویدیو')
                                                        ->form([
                                                            TextInput::make('video')->label('لینک یا شناسه آپارات')->required()->maxLength(2048)
                                                                ->placeholder('https://www.aparat.com/v/k93yms3')
                                                                ->extraInputAttributes(['dir' => 'ltr'])
                                                                ->rules([fn () => function (string $attribute, $value, \Closure $fail): void {
                                                                    if (\App\Support\AparatVideo::id($value) === null) $fail('لینک یا شناسه آپارات معتبر نیست.');
                                                                }]),
                                                        ])
                                                        ->action(function (array $data, $livewire): void {
                                                            $livewire->dispatch('news-editor-video', id: \App\Support\AparatVideo::id($data['video']));
                                                        }),
                                                ])
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
                                                    'undo',
                                                    'redo',
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




                                                
                                                    Hidden::make('gallery_media')
                                                        ->default([])
                                                        ->live()
                                                        ->dehydrated(true),

                                                    Actions::make([

                                                        Action::make('selectGallery')
                                                            ->label('انتخاب تصاویر گزارش تصویری')
                                                            ->icon('heroicon-o-photo')
                                                            ->color('primary')
                                                            ->modalHeading('انتخاب تصاویر گزارش تصویری')
                                                            ->modalDescription(
                                                                'حداکثر ۱۵ تصویر انتخاب کنید. تصاویر جدید نیز می‌توانید آپلود و پردازش کنید.'
                                                            )
                                                            ->modalWidth('7xl')
                                                            ->modalSubmitAction(false)
                                                            ->modalCancelActionLabel('بستن')
                                                            ->modalContent(
                                                                fn () => view(
                                                                    'filament.media.gallery-picker-wrapper'
                                                                )
                                                            ),

                                                    ])
                                                        ->visible(
                                                            fn ($get) =>
                                                                filled($get('report_type')) &&
                                                                ReportType::whereKey($get('report_type'))
                                                                    ->where('name', 'گزارش تصویری')
                                                                    ->exists()
                                                        ),


                                                        Placeholder::make('gallery_preview')
                                                            ->hiddenLabel()
                                                            ->content(function ($get) {

                                                                $gallery = $get('gallery_media');

                                                                if (is_string($gallery)) {

                                                                    $gallery =
                                                                        json_decode(
                                                                            $gallery,
                                                                            true
                                                                        ) ?: [];
                                                                }

                                                                if (! is_array($gallery) || empty($gallery)) {

                                                                    return 'هنوز تصویری برای گزارش تصویری انتخاب نشده است.';
                                                                }

                                                                return view(
                                                                    'filament.media.news-gallery-preview',
                                                                    [
                                                                        'items' => $gallery,
                                                                    ]
                                                                );
                                                            })
                                                            ->visible(
                                                                fn ($get) =>
                                                                    filled($get('report_type')) &&
                                                                    \App\Models\ReportType::whereKey($get('report_type'))
                                                                        ->where('name', 'گزارش تصویری')
                                                                        ->exists()
                                                            ),

                                                            View::make('filament.forms.field-errors.gallery-media')
                                                                ->columnSpanFull(),

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
                                    /*
                                    |--------------------------------------------------------------------------
                                    | Featured Media ID
                                    |--------------------------------------------------------------------------
                                    */

                                    Hidden::make('featured_media_id')
                                        ->default(null)
                                        ->live()
                                        ->dehydrated(true),

                                    /*
                                    |--------------------------------------------------------------------------
                                    | Featured Media Variant
                                    |--------------------------------------------------------------------------
                                    */

                                    Hidden::make('featured_media_variant')
                                        ->default(null)
                                        ->live()
                                        ->dehydrated(true),

                                    /*
                                    |--------------------------------------------------------------------------
                                    | Select Featured Image
                                    |--------------------------------------------------------------------------
                                    */

                                    Actions::make([
                                        Action::make('selectFeatured')
                                            ->label('انتخاب تصویر')
                                            ->icon('heroicon-o-photo')
                                            ->modalHeading('انتخاب تصویر شاخص')
                                            ->modalWidth('7xl')
                                            ->modalSubmitAction(false)
                                            ->modalCancelActionLabel('بستن')
                                            ->modalContent(
                                                fn () => view(
                                                    'filament.media.featured-picker-wrapper'
                                                )
                                            ),
                                    ]),

                                    /*
                                    |--------------------------------------------------------------------------
                                    | Featured Image Preview
                                    |--------------------------------------------------------------------------
                                    */

                                    Placeholder::make('featured_preview')
                                        ->hiddenLabel()
                                        ->content(function ($get) {

                                            $id = $get('featured_media_id');

                                            $variant = $get('featured_media_variant');

                                            /*
                                            |--------------------------------------------------------------------------
                                            | No media selected
                                            |--------------------------------------------------------------------------
                                            */

                                            if (! $id) {
                                                return 'هنوز تصویری انتخاب نشده است.';
                                            }

                                            /*
                                            |--------------------------------------------------------------------------
                                            | Variant missing
                                            |--------------------------------------------------------------------------
                                            */

                                            if (! $variant) {
                                                return 'نسخه تصویر انتخاب نشده است.';
                                            }

                                            /*
                                            |--------------------------------------------------------------------------
                                            | Find media
                                            |--------------------------------------------------------------------------
                                            */

                                            $media = \App\Models\Media::find($id);

                                            if (! $media) {
                                                return 'تصویر پیدا نشد.';
                                            }

                                            /*
                                            |--------------------------------------------------------------------------
                                            | Get exact selected variant
                                            |--------------------------------------------------------------------------
                                            */

                                            $url = $media->variantUrl($variant);

                                            /*
                                            |--------------------------------------------------------------------------
                                            | Render preview
                                            |--------------------------------------------------------------------------
                                            */

                                            return view(
                                                'filament.media.featured-preview',
                                                [
                                                    'media' => $media,
                                                    'variant' => $variant,
                                                    'url' => $url,
                                                ]
                                            );
                                        }),

                                        View::make('filament.forms.field-errors.featured-media')
                                            ->columnSpanFull(),
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

                                            JalaliDateTimePicker::make('published_at')
                                                ->label('زمان انتشار')
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


                                    Section::make('اقدامات')
                                        ->schema([

                                            Select::make('categories')
                                                ->label('دسته‌بندی')
                                                ->live()
                                                ->relationship('categories', 'name')
                                                ->multiple()
                                                ->searchable()
                                                ->preload()
                                                ->required(),

                                            \App\Forms\Components\NewsTagsSelect::make('tags')
                                                ->label('برچسب‌ها')
                                                ->required(),

                                                
                                        Select::make('report_type')
                                            ->label('نوع مطلب')
                                            ->relationship(
                                                name: 'reportType',
                                                titleAttribute: 'name'
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->live(),

                                        Select::make('production_method')
                                            ->label('نحوه تولید')
                                            ->options([
                                                'بازنشری' => 'بازنشری',
                                                'پوششی' => 'پوششی',
                                                'تولیدی' => 'تولیدی',
                                                'دریافتی' => 'دریافتی',
                                            ])
                                            ->native(false)
                                            ->required(),

                                        \Filament\Forms\Components\Checkbox::make('featured')
                                            ->label('اضافه کردن به خبرهای ویژه')
                                            ->default(false),

                                        \Filament\Forms\Components\Checkbox::make('slider')
                                            ->label('اضافه کردن به اسلایدر صفحه اصلی')
                                            ->default(false),

                                        \Filament\Forms\Components\Checkbox::make('mark_as_top')
                                            ->label('در نظر گرفتن به عنوان خبر تاپ در دسته‌بندی')
                                            ->default(false)
                                            ->dehydrated(false)
                                            ->live()
                                            ->afterStateUpdated(function ($state, \Filament\Forms\Set $set): void {
                                                if (! $state) $set('top_category_id', null);
                                            }),

                                        Select::make('top_category_id')
                                            ->label('دسته‌بندی خبر تاپ')
                                            ->helperText('همه دسته‌بندی‌ها قابل انتخاب‌اند؛ دسته انتخابی به دسته‌بندی‌های خبر نیز اضافه می‌شود.')
                                            ->options(fn () => \App\Models\Category::query()
                                                ->orderBy('name')->pluck('name', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->native(false)
                                            ->visible(fn (\Filament\Forms\Get $get): bool => (bool) $get('mark_as_top'))
                                            ->required(fn (\Filament\Forms\Get $get): bool => (bool) $get('mark_as_top'))
                                            ->rules([
                                                \Illuminate\Validation\Rule::exists('categories', 'id')->whereNull('deleted_at'),
                                            ])
                                            ->dehydrated(false),


                                        ]),



                                ]),

                        ]),

            ]);
    }
}
