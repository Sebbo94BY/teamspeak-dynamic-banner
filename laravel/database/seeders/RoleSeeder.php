<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Special role "Super Admin" (has by default all permissions)
        // See laravel\app\Providers\AuthServiceProvider.php
        Role::firstOrCreate(['name' => 'Super Admin']);

        // Assign special role to first existing user, if any exists already
        $initial_user = User::first();
        if (! is_null($initial_user)) {
            $initial_user->assignRole('Super Admin');
        }

        // Administration: Users
        $users_admin = Role::firstOrCreate(['name' => 'Users Admin']);
        $users_admin->syncPermissions([
            'view users',
            'add users',
            'edit users',
            'delete users',
        ]);

        // Administration: Roles
        $roles_admin = Role::firstOrCreate(['name' => 'Roles Admin']);
        $roles_admin->syncPermissions([
            'view roles',
            'add roles',
            'edit roles',
            'delete roles',
        ]);

        // Administration: Fonts
        $fonts_admin = Role::firstOrCreate(['name' => 'Fonts Admin']);
        $fonts_admin->syncPermissions([
            'view fonts',
            'add fonts',
            'edit fonts',
            'delete fonts',
        ]);

        // Administration: Twitch
        $twitch_super_admin = Role::firstOrCreate(['name' => 'Twitch Super Admin']);
        $twitch_super_admin->syncPermissions([
            'view twitch',
            'edit twitch api credentials',
            'delete twitch api credentials',
            'add twitch streamers',
            'edit twitch streamers',
            'delete twitch streamers',
        ]);

        $twitch_api_admin = Role::firstOrCreate(['name' => 'Twitch API Admin']);
        $twitch_api_admin->syncPermissions([
            'view twitch',
            'edit twitch api credentials',
            'delete twitch api credentials',
        ]);

        $twitch_streamer_admin = Role::firstOrCreate(['name' => 'Twitch Streamer Admin']);
        $twitch_streamer_admin->syncPermissions([
            'view twitch',
            'add twitch streamers',
            'edit twitch streamers',
            'delete twitch streamers',
        ]);

        $twitch_viewer = Role::firstOrCreate(['name' => 'Twitch Viewer']);
        $twitch_viewer->syncPermissions([
            'view twitch',
        ]);

        // Administration: System Status
        $system_status_viewer = Role::firstOrCreate(['name' => 'System Status Viewer']);
        $system_status_viewer->syncPermissions([
            'view system status',
        ]);

        // Administration: PHP Info
        $php_info_viewer = Role::firstOrCreate(['name' => 'PHP Info Viewer']);
        $php_info_viewer->syncPermissions([
            'view php info',
        ]);

        // Instances
        $instances_admin = Role::firstOrCreate(['name' => 'Instances Admin']);
        $instances_admin->syncPermissions([
            'view instances',
            'add instances',
            'start instances',
            'stop instances',
            'restart instances',
            'edit instances',
            'delete instances',
        ]);

        // Templates
        $templates_admin = Role::firstOrCreate(['name' => 'Templates Admin']);
        $templates_admin->syncPermissions([
            'view templates',
            'add templates',
            'edit templates',
            'delete templates',
        ]);

        // Banners
        $banners_admin = Role::firstOrCreate(['name' => 'Banners Admin']);
        $banners_admin->syncPermissions([
            'view banners',
            'add banners',
            'edit banners',
            'delete banners',
        ]);
    }
}
