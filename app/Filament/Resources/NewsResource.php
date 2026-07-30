<?php

namespace App\Filament\Resources;

use App\Enums\NewsStatus;
use App\Filament\Resources\NewsResource\Pages;
use App\Filament\Resources\NewsResource\RelationManagers;
use App\Filament\Resources\NewsResource\Schemas\NewsForm;
use App\Filament\Resources\NewsResource\Tables\NewsTable;
use App\HasPermissionChecks;
use App\Models\News;
use Filament\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;



class NewsResource extends Resource
{
    use HasPermissionChecks;

    protected static ?string $model = News::class;

    protected static string $permissionBase = 'news';

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    public static function form(Form $form): Form
    {
        return NewsForm::make($form);
    }

    public static function table(Table $table): Table
    {
        return NewsTable::make($table);
    }


    
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = auth()->user();


        if ($user->hasRole('Reporter')) {

            return $query->where(
                'reporter_id',
                $user->id
            );

        }


        return $query;
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
    
}
