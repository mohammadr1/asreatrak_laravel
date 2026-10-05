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

use App\Filament\Concerns\HasRoleBasedNavigation;
use Morilog\Jalali\Jalalian;

class NewsResource extends Resource
{
    use HasPermissionChecks;

    protected static ?string $model = News::class;

    protected static ?string $navigationGroup = 'اخبار';

    protected static ?string $navigationLabel = 'اخبار';

    protected static ?string $modelLabel = 'خبر';

    protected static ?string $pluralModelLabel = 'اخبار';

    protected static ?int $navigationSort = 1;

    protected static string $permissionBase = 'news';

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';


    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->hasAnyRole([
            'Admin',
            'Editor',
            'Reporter',
        ]);
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->hasAnyRole([
            'Admin',
            'Editor',
            'Reporter',
        ]);
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->hasAnyRole([
            'Admin',
            'Editor',
            'Reporter',
        ]);
    }

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


    public static function getNavigationItems(): array
    {
        $user = auth()->user();

        if (! $user) {
            return [];
        }

        $items = [
            \Filament\Navigation\NavigationItem::make('اخبار')
                ->icon('heroicon-o-newspaper')
                ->group('اخبار')
                ->url(static::getUrl('index'))
                ->sort(1)
                ->isActiveWhen(
                    fn (): bool => request()->routeIs(
                        static::getRouteBaseName() . '.index'
                    )
                ),

            \Filament\Navigation\NavigationItem::make('ایجاد خبر')
                ->icon('heroicon-o-plus-circle')
                ->group('اخبار')
                ->url(static::getUrl('create'))
                ->sort(2)
                ->isActiveWhen(
                    fn (): bool => request()->routeIs(
                        static::getRouteBaseName() . '.create'
                    )
                ),
        ];

        if ($user->hasAnyRole([
            'Admin',
            'Editor',
        ])) {
            $pendingCount = \App\Models\News::query()
                ->where('status', \App\Enums\NewsStatus::Pending)
                ->count();

            $items[] = \Filament\Navigation\NavigationItem::make(
                'در انتظار تأیید'
            )
                ->icon('heroicon-o-clock')
                ->group('اخبار')
                ->url(static::getUrl('pending'))
                ->sort(3)
                ->badge(
                    $pendingCount > 0
                        ? (string) $pendingCount
                        : null
                )
                ->isActiveWhen(
                    fn (): bool => request()->routeIs(
                        static::getRouteBaseName() . '.pending'
                    )
                );
        }

        if (static::canCreate()) {
            $items[] = \Filament\Navigation\NavigationItem::make('افزودن خبر ویدیویی')
                ->icon('heroicon-o-video-camera')
                ->group(static::$navigationGroup)
                ->url(static::getUrl('create-video'))
                ->sort(3)
                ->isActiveWhen(fn (): bool => request()->routeIs(static::getRouteBaseName() . '.create-video'));
        }

        return $items;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNews::route('/'),
            'pending' => Pages\PendingNews::route('/pending'),
            'create' => Pages\CreateNews::route('/create'),
            'create-video' => Pages\CreateVideoNews::route('/create-video'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
    
}
