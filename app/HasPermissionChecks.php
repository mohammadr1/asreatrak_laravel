<?php

namespace App;

trait HasPermissionChecks
{
        public static function canViewAny(): bool
    {
        return auth()->user()->can(static::$permissionBase . '.view');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can(static::$permissionBase . '.create');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can(static::$permissionBase . '.update');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can(static::$permissionBase . '.delete');
    }
}
