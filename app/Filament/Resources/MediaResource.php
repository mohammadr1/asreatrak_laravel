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
use App\Filament\Concerns\HasRoleBasedNavigation;

class MediaResource extends Resource
{
    use HasRoleBasedNavigation;

    protected static ?string $navigationLabel = 'رسانه‌ها';

    protected static ?string $pluralModelLabel = 'رسانه‌ها';

    protected static ?string $modelLabel = 'رسانه';

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'مدیریت محتوا';

    protected static ?int $navigationSort = 3;



    protected static ?string $model = Media::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
