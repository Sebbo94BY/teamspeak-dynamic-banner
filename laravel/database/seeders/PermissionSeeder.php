<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Administration: Users
        Permission::firstOrCreate(['name' => 'view users']);
        Permission::firstOrCreate(['name' => 'add users']);
        Permission::firstOrCreate(['name' => 'edit users']);
        Permission::firstOrCreate(['name' => 'delete users']);

        // Administration: Roles
        Permission::firstOrCreate(['name' => 'view roles']);
        Permission::firstOrCreate(['name' => 'add roles']);
        Permission::firstOrCreate(['name' => 'edit roles']);
        Permission::firstOrCreate(['name' => 'delete roles']);

        // Administration: Fonts
        Permission::firstOrCreate(['name' => 'view fonts']);
        Permission::firstOrCreate(['name' => 'add fonts']);
        Permission::firstOrCreate(['name' => 'edit fonts']);
        Permission::firstOrCreate(['name' => 'delete fonts']);

        // Administration: System Status
        Permission::firstOrCreate(['name' => 'view system status']);

        // Administration: PHP Info
        Permission::firstOrCreate(['name' => 'view php info']);

        // Instances
        Permission::firstOrCreate(['name' => 'view instances']);
        Permission::firstOrCreate(['name' => 'add instances']);
        Permission::firstOrCreate(['name' => 'start instances']);
        Permission::firstOrCreate(['name' => 'stop instances']);
        Permission::firstOrCreate(['name' => 'restart instances']);
        Permission::firstOrCreate(['name' => 'edit instances']);
        Permission::firstOrCreate(['name' => 'delete instances']);

        // Templates
        Permission::firstOrCreate(['name' => 'view templates']);
        Permission::firstOrCreate(['name' => 'add templates']);
        Permission::firstOrCreate(['name' => 'edit templates']);
        Permission::firstOrCreate(['name' => 'delete templates']);

        // Banners
        Permission::firstOrCreate(['name' => 'view banners']);
        Permission::firstOrCreate(['name' => 'add banners']);
        Permission::firstOrCreate(['name' => 'edit banners']);
        Permission::firstOrCreate(['name' => 'delete banners']);
    }
}
