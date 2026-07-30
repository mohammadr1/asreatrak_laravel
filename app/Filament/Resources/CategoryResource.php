<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
    return $form
        ->schema([
            Forms\Components\TextInput::make('name')
                ->label('نام دسته')
                ->required()
                ->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $set('slug', \Illuminate\Support\Str::slug($state));
                    }
                }),

            Forms\Components\TextInput::make('slug')
                ->label('اسلاگ')
                ->required()
                ->unique(ignoreRecord: true),

            Forms\Components\Textarea::make('meta_description')
                ->label('توضیحات سئو'),

            Forms\Components\TextInput::make('meta_keywords')
                ->label('کلمات کلیدی'),

            Forms\Components\TextInput::make('sort_order')
                ->label('ترتیب')
                ->numeric()
                ->default(0),

            Forms\Components\Toggle::make('is_featured')
                ->label('دسته ویژه'),

            Forms\Components\TextInput::make('language')
                ->default('fa')
                ->required(),
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

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('ویژه')
                    ->boolean(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('ترتیب'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('ایجاد')
                    ->dateTime(),
            ])
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
