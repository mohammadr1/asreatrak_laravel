<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaResource\Pages;
use App\Filament\Resources\MediaResource\RelationManagers;
use App\Filament\Resources\MediaResource\Schemas\MediaForm;
use App\Filament\Resources\MediaResource\Tables\MediaTable;
use App\Models\Media;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class MediaResource extends Resource
{


    protected static ?string $navigationLabel = 'رسانه‌ها';

    protected static ?string $pluralModelLabel = 'رسانه‌ها';

    protected static ?string $modelLabel = 'رسانه';

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'مدیریت محتوا';

    protected static ?int $navigationSort = 2;

    protected static ?string $model = Media::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return MediaForm::make($form);
    }

    public static function table(Table $table): Table
    {
        return MediaTable::make($table);
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
            'index' => Pages\ListMedia::route('/'),
            'create' => Pages\CreateMedia::route('/create'),
            'edit' => Pages\EditMedia::route('/{record}/edit'),
        ];
    }
}
