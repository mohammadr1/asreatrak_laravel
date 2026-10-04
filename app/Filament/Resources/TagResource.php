<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagResource\Pages;
use App\Filament\Resources\TagResource\RelationManagers;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Concerns\HasRoleBasedNavigation;

class TagResource extends Resource
{
    use HasRoleBasedNavigation;

    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'برچسب‌ها';

    protected static ?string $modelLabel = 'برچسب';

    protected static ?string $pluralModelLabel = 'برچسب‌ها';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'مدیریت محتوا';

    protected static function allowedNavigationRoles(): array
    {
        return [
            'Admin',
            'Editor',
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasAnyRole([
            'Super Admin',
            'Admin',
            'Editor',
        ]) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('نام برچسب')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set) {

                        if ($state) {
                            $set('slug', \Illuminate\Support\Str::slug($state));
                        }

                    }),

                Forms\Components\TextInput::make('slug')
                    ->label('نام مستعار')
                    ->required()
                    ->unique(ignoreRecord: true),

                // نمایش تعداد استفاده در فرم به صورت فقط‌خواندنی (در حالت ویرایش)
                Forms\Components\Placeholder::make('usage_count_display')
                    ->label('تعداد استفاده در اخبار')
                    ->content(fn (?Tag $record): int => $record ? $record->news()->count() : 0)
                    ->hidden(fn (?Tag $record) => $record === null),

                Forms\Components\Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('نام')
                    ->searchable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('اسلاگ'),

                Tables\Columns\TextColumn::make('news_count')
                    ->counts('news')
                    ->label('تعداد استفاده')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTags::route('/'),
            'create' => Pages\CreateTag::route('/create'),
            'edit' => Pages\EditTag::route('/{record}/edit'),
        ];
    }
}
