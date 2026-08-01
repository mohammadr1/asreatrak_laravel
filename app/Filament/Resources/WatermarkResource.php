<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WatermarkResource\Pages;
use App\Filament\Resources\WatermarkResource\RelationManagers;
use App\Filament\Resources\WatermarkResource\Schemas\WatermarkForm;
use App\Filament\Resources\WatermarkResource\Tables\WatermarkTable;
use App\Models\Watermark;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WatermarkResource extends Resource
{
    protected static ?string $model = Watermark::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'مدیریت محتوا';

    protected static ?string $navigationLabel = 'واترمارک‌ها';

    protected static ?string $modelLabel = 'واترمارک';

    protected static ?string $pluralModelLabel = 'واترمارک‌ها';


    public static function form(Form $form): Form
    {
        return WatermarkForm::make($form);
    }

    public static function table(Table $table): Table
    {
        return WatermarkTable::make($table);
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
            'index' => Pages\ListWatermarks::route('/'),
            'create' => Pages\CreateWatermark::route('/create'),
            'edit' => Pages\EditWatermark::route('/{record}/edit'),
        ];
    }
}
