<?php

namespace App\Filament\Resources;

use App\Models\User;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\Schemas\UserForm;
use App\Filament\Resources\UserResource\Tables\UserTable;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use App\Filament\Concerns\HasRoleBasedNavigation;

class UserResource extends Resource
{
    use HasRoleBasedNavigation;
    
    protected static ?string $model = User::class;



    protected static ?string $navigationGroup = 'مدیریت کاربران';

    protected static ?string $navigationLabel = 'کاربران';

    protected static ?string $modelLabel = 'کاربر';

    protected static ?string $pluralModelLabel = 'کاربران';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?int $navigationSort = 2;


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