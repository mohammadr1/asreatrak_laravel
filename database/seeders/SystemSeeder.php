<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                /*
        |--------------------------------------------------------------------------
        | 1. ROLES
        |--------------------------------------------------------------------------
        */

        $roles = [
            'Admin',
            'Editor',
            'Reporter',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 2. PERMISSIONS
        |--------------------------------------------------------------------------
        */

        $permissions = [
            'news.view',
            'news.create',
            'news.update',
            'news.delete',
            'news.approve',
            'news.publish',

            'category.manage',
            'tag.manage',

            'user.manage',
            'settings.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 3. ROLE PERMISSION ASSIGNMENT
        |--------------------------------------------------------------------------
        */

        $admin = Role::findByName('Admin');
        $editor = Role::findByName('Editor');
        $reporter = Role::findByName('Reporter');

        $admin->syncPermissions(Permission::all());

        $editor->syncPermissions([
            'news.view',
            'news.create',
            'news.update',
            'news.approve',
            'news.publish',
            'category.manage',
            'tag.manage',
        ]);

        $reporter->syncPermissions([
            'news.view',
            'news.create',
            'news.update',
        ]);

        /*
        |--------------------------------------------------------------------------
        | 4. DEFAULT ADMIN USER
        |--------------------------------------------------------------------------
        */

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@mohammad.com'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'phone' => '09000000000',
                'password' => Hash::make('123456'),
                'is_active' => true,
            ]
        );

        $adminUser->assignRole('Admin');
    }
}
