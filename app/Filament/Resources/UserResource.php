<?php

namespace App\Filament\Resources;

use App\Models\User;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\Schemas\UserForm;
use App\Filament\Resources\UserResource\Tables\UserTable;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class UserResource extends Resource
{

    protected static ?string $model = User::class;


    protected static ?string $navigationIcon = 'heroicon-o-users';


    protected static ?string $navigationLabel = 'کاربران';


    protected static ?string $modelLabel = 'کاربر';


    protected static ?string $pluralModelLabel = 'کاربران';


    protected static ?string $navigationGroup = 'مدیریت سیستم';



    public static function form(Form $form): Form
    {
        return UserForm::make($form);
    }



    public static function table(Table $table): Table
    {
        return UserTable::make(
            $table->modifyQueryUsing(
                fn ($query) => $query->withCount('reportedNews')
                )
            );
        // return UserTable::make($table);
    }



    public static function getPages(): array
    {
        return [

            'index' => Pages\ListUsers::route('/'),

            'create' => Pages\CreateUser::route('/create'),

            'edit' => Pages\EditUser::route('/{record}/edit'),

        ];
    }
}