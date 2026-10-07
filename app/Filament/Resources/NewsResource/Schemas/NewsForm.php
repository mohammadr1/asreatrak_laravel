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
    private static function counter($component, int $maximum, int $recommended, string $guidance)
    {
        return view('filament.forms.text-counter', [
            'id' => $component->getId(), 'maximum' => $maximum,
            'recommended' => $recommended, 'guidance' => $guidance,
        ]);
    }

    public static function make(Form $form): Form
    {
        return $form
            ->schema([

            Section::make('ویدیوی آپارات')
                ->description('ویدیو را در آپارات بارگذاری کنید و لینک یا شناسه آن را اینجا وارد کنید.')
                ->visible(fn ($livewire, $record): bool => $livewire instanceof \App\Filament\Resources\NewsResource\Pages\CreateVideoNews || $record?->post_type === 'video')
                ->schema([
                    TextInput::make('aparat_video_id')
                        ->validationMessages(['required' => 'لینک یا شناسه ویدیوی آپارات را وارد کنید.', 'max' => 'لینک آپارات نباید بیشتر از ۲۰۴۸ کاراکتر باشد.'])
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
                                                ->validationMessages(['max' => 'روتیتر نباید بیشتر از ۲۵۵ کاراکتر باشد.'])
                                                ->label('روتیتر')
                                                ->maxLength(255)
                                                ->rules([new \App\Rules\NewsText])
                                                ->helperText(fn (TextInput $component) => self::counter($component, 255, 50,''))
                                                ->columnSpanFull(),

                                            TextInput::make('title')
                                                ->validationMessages(['required' => 'عنوان خبر را وارد کنید.', 'unique' => 'این عنوان قبلاً ثبت شده است؛ عنوان دیگری انتخاب کنید.', 'max' => 'عنوان خبر نباید بیشتر از ۲۵۵ کاراکتر باشد.'])
                                                ->label('عنوان خبر')
                                                ->rules([new \App\Rules\NewsText])
                                                ->helperText(fn (TextInput $component) => self::counter($component, 255, 80, ''))
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

                                                    $newSlug = preg_replace('/[^\p{L}\p{N}\p{M}\x{200C}_-]+/u', '', $newSlug);

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
                                                ->validationMessages(['required' => 'کد خبر ایجاد نشده است؛ صفحه را تازه‌سازی کنید.', 'unique' => 'کد خبر تکراری است؛ صفحه را تازه‌سازی کنید و دوباره ذخیره کنید.'])
                                                ->label('کد خبر')
                                                ->default(fn () => now()->format('YmdHis'))
                                                ->required()
                                                ->unique(ignoreRecord: true)
                                                ->disabled()
                                                ->dehydrated(),

                                            Select::make('reporter_id')
                                                ->validationMessages(['required' => 'خبرنگار خبر را انتخاب کنید.', 'exists' => 'خبرنگار انتخاب‌شده معتبر نیست؛ دوباره انتخاب کنید.', 'in' => 'خبرنگار انتخاب‌شده معتبر نیست؛ دوباره انتخاب کنید.'])
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
                                                ->validationMessages(['required' => 'لید خبر را وارد کنید.', 'max' => 'لید خبر نباید بیشتر از ۱۰۰۰ کاراکتر باشد.'])
                                                ->label('لید خبر')
                                                ->rules([new \App\Rules\NewsText])
                                                ->helperText(fn (Textarea $component) => self::counter($component, 255, 300, 'پیشنهاد تحریریه: حدود ۱۲۰ تا ۳۰۰ کاراکتر؛ مهم‌ترین اتفاق و اطلاعات اصلی را خلاصه کنید.'))
                                                ->required()
                                                ->rows(4)
                                                ->maxLength(1000)
                                                ->columnSpanFull(),

                                            RichEditor::make('content')
                                                ->validationMessages(['required' => 'متن خبر را وارد کنید.'])
                                                ->id('news-content')
                                                ->rules([new \App\Rules\NewsText(rich: true)])
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
                                                                ->validationMessages(['required' => 'لینک یا شناسه ویدیوی آپارات را وارد کنید.', 'max' => 'لینک آپارات نباید بیشتر از ۲۰۴۸ کاراکتر باشد.'])
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
                                                ->regex('/\A[\p{L}\p{N}\p{M}\x{200C}_-]+\z/u')
                                                ->validationMessages(['required' => 'اسلاگ خبر را وارد کنید.', 'unique' => 'این اسلاگ قبلاً استفاده شده است؛ اسلاگ دیگری وارد کنید.', 'max' => 'اسلاگ نباید بیشتر از ۲۵۵ کاراکتر باشد.', 'regex' => 'اسلاگ را بدون فاصله و علامت‌هایی مثل / و ؟ وارد کنید؛ از خط تیره بین کلمات استفاده کنید.'])
                                                ->required()
                                                ->unique(ignoreRecord: true)
                                                ->maxLength(255),

                                            TextInput::make('meta_title')
                                                ->validationMessages(['max' => 'عنوان سئو نباید بیشتر از ۲۵۵ کاراکتر باشد.'])
                                                ->label('عنوان سئو (اختیاری)')
                                                ->maxLength(255)
                                                ->rules([new \App\Rules\NewsText])
                                                ->helperText(fn (TextInput $component) => self::counter($component, 255, 60, 'پیشنهاد: حدود ۳۰ تا ۶۰ کاراکتر. اگر خالی باشد عنوان خبر استفاده می‌شود؛ نمایش کامل در گوگل تضمین نمی‌شود.')),

                                            Textarea::make('meta_description')
                                                ->validationMessages(['max' => 'توضیحات سئو نباید بیشتر از ۱۰۰۰ کاراکتر باشد.'])
                                                ->label('توضیحات سئو (اختیاری)')
                                                ->rows(3)->maxLength(1000)
                                                ->rules([new \App\Rules\NewsText])
                                                ->helperText(fn (Textarea $component) => self::counter($component, 1000, 160, 'پیشنهاد: حدود ۱۲۰ تا ۱۶۰ کاراکتر؛ خلاصه خاص همین خبر. اگر خالی باشد لید استفاده می‌شود.')),

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
                                                ->validationMessages(['required' => 'وضعیت خبر را انتخاب کنید.', 'in' => 'اجازه انتخاب این وضعیت انتشار را ندارید.'])
                                                ->label('وضعیت')
                                                ->options(fn ($record) => \App\Support\NewsFormPublication::options(auth()->user(), $record))
                                                ->rules(fn ($record) => [\Illuminate\Validation\Rule::in(array_keys(\App\Support\NewsFormPublication::options(auth()->user(), $record)))])
                                                ->required()
                                                ->helperText('با ثبت یا ذخیره خبر، وضعیت انتخاب‌شده اعمال می‌شود.')
                                                ->default(NewsStatus::Draft->value)
                                                ->live(),

                                            JalaliDateTimePicker::make('published_at')
                                                ->validationMessages(['required' => 'برای انتشار زمان‌بندی‌شده، تاریخ و ساعت انتشار را وارد کنید.'])
                                                ->label('زمان انتشار')
                                                ->disabled(
                                                    fn () => ! \App\Support\NewsFormPublication::canPublish(auth()->user())
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
                                                ->validationMessages(['required' => 'حداقل یک دسته‌بندی برای خبر انتخاب کنید.', 'exists' => 'دسته‌بندی انتخاب‌شده معتبر نیست؛ دوباره انتخاب کنید.', 'in' => 'دسته‌بندی انتخاب‌شده معتبر نیست؛ دوباره انتخاب کنید.'])
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
                                            ->validationMessages(['required' => 'نوع مطلب را انتخاب کنید.', 'exists' => 'نوع مطلب انتخاب‌شده معتبر نیست.', 'in' => 'نوع مطلب انتخاب‌شده معتبر نیست.'])
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
                                            ->validationMessages(['required' => 'نحوه تولید خبر را انتخاب کنید.', 'in' => 'نحوه تولید انتخاب‌شده معتبر نیست.'])
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
                                            ->validationMessages(['required' => 'دسته‌بندی موردنظر برای خبر تاپ را انتخاب کنید.', 'exists' => 'دسته‌بندی خبر تاپ دیگر موجود نیست؛ دوباره انتخاب کنید.', 'in' => 'دسته‌بندی خبر تاپ معتبر نیست؛ دوباره انتخاب کنید.'])
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

                Section::make('خبرهای مرتبط')
                    ->description('حداقل ۳ و حداکثر ۱۰ خبر منتشرشده مرتبط با موضوع این خبر انتخاب کنید.')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('relatedNews')
                            ->label('انتخاب خبرهای مرتبط')
                            ->relationship('relatedNews', 'title',
                                modifyQueryUsing: fn (\Illuminate\Database\Eloquent\Builder $query) => $query->publiclyVisible()->orderByDesc('news.published_at')->orderByDesc('news.id'),
                                ignoreRecord: true)
                            ->multiple()->searchable(['title', 'news_code'])->preload()->optionsLimit(30)
                            ->getOptionLabelFromRecordUsing(fn (\App\Models\News $record) => $record->title.' — '.$record->news_code)
                            ->required()->minItems(3)->maxItems(10)
                            ->validationMessages([
                                'required' => 'حداقل ۳ خبر مرتبط انتخاب کنید.',
                                'min' => 'حداقل ۳ خبر مرتبط انتخاب کنید.',
                                'max' => 'حداکثر ۱۰ خبر مرتبط قابل انتخاب است.',
                            ])
                            ->rules([fn ($record) => function (string $attribute, $value, \Closure $fail) use ($record): void {
                                $ids = array_map('strval', (array) $value);
                                if (count(array_unique($ids)) !== count($ids)
                                    || ($record && in_array((string) $record->id, $ids, true))
                                    || \App\Models\News::query()->publiclyVisible()->whereKey($ids)->count() !== count($ids)) {
                                    $fail('خبرهای مرتبط باید متمایز، منتشرشده و غیر از خود این خبر باشند.');
                                }
                            }])
                            ->helperText('با عنوان یا کد خبر جستجو کنید. انتخاب‌ها پس از ذخیره خبر ثبت می‌شوند.'),
                    ]),
            ]);
    }
}
