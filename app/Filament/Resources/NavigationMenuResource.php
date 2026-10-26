<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\HasRoleBasedNavigation;
use App\Filament\Resources\NavigationMenuResource\Pages;
use App\Models\Category;
use App\Models\NavigationMenu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;

class NavigationMenuResource extends Resource
{
    use HasRoleBasedNavigation;

    protected static ?string $model = NavigationMenu::class;
    protected static ?string $navigationIcon = 'heroicon-o-bars-3';
    protected static ?string $navigationLabel = 'منو';
    protected static ?string $modelLabel = 'آیتم منو';
    protected static ?string $pluralModelLabel = 'منو';
    protected static ?string $navigationGroup = 'مدیریت محتوا';
    protected static ?int $navigationSort = 2;

    protected static function allowedNavigationRoles(): array
    {
        return ['Admin', 'Editor'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')
                ->label('عنوان')
                ->required()
                ->maxLength(255),
            Forms\Components\Select::make('category_id')
                ->label('دسته‌بندی مرتبط (اختیاری)')
                ->options(fn () => Category::query()->orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->nullable()
                ->live()
                ->afterStateUpdated(fn ($state, Forms\Set $set) => $state ? $set('url', null) : null),
            Forms\Components\TextInput::make('url')
                ->label('پیوند سفارشی')
                ->maxLength(2048)
                ->visible(fn(Forms\Get $get): bool => ! $get('category_id'))
                ->helperText('برای لینک داخلی یا خارجی؛ اگر دسته‌بندی انتخاب شود، پیوند دسته به‌صورت خودکار استفاده می‌شود.'),
            Forms\Components\Select::make('parent_id')
                ->label('آیتم والد')
                ->options(fn (?NavigationMenu $record) => NavigationMenu::query()
                    ->when($record, fn ($query) => $query->whereKeyNot($record->getKey()))
                    ->orderBy('sort_order')->orderBy('id')->get()
                    ->mapWithKeys(fn (NavigationMenu $item) => [$item->id => str_repeat('— ', min($item->depth, 5)).$item->title]))
                ->searchable()
                ->nullable()
                ->rules([fn (?NavigationMenu $record) => Rule::notIn($record ? [$record->id] : [])]),
            Forms\Components\TextInput::make('sort_order')
                ->label('ترتیب نمایش')
                ->numeric()
                ->default(0)
                ->required(),
            Forms\Components\Toggle::make('is_active')
                ->label('فعال')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with(['parent', 'category']))
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('عنوان')->searchable(),
                Tables\Columns\TextColumn::make('category.name')->label('دسته‌بندی')->placeholder('—'),
                Tables\Columns\TextColumn::make('url')->label('پیوند سفارشی')->placeholder('پیوند دسته')->limit(40),
                Tables\Columns\TextColumn::make('parent.title')->label('آیتم والد')->placeholder('سطح اصلی'),
                Tables\Columns\TextColumn::make('sort_order')->label('ترتیب')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('فعال')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNavigationMenus::route('/'),
            'create' => Pages\CreateNavigationMenu::route('/create'),
            'edit' => Pages\EditNavigationMenu::route('/{record}/edit'),
        ];
    }
}
