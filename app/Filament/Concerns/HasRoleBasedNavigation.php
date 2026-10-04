<?php

namespace App\Filament\Concerns;

use Illuminate\Support\Facades\Auth;

trait HasRoleBasedNavigation
{
    /**
     * Roles that can access this resource.
     *
     * @return array<int, string>
     */
    protected static function allowedNavigationRoles(): array
    {
        return [
            'Admin',
            'Editor',
        ];
    }

    /**
     * Determine whether the current user can access this resource.
     */
    public static function canAccess(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        return $user->hasAnyRole(static::allowedNavigationRoles());
    }

    /**
     * Determine whether this resource should appear in navigation.
     */
    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
}