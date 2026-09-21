<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Roles
        $roles = ['admin', 'supervisor', 'mentor', 'student'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Create a local admin for emergency access (optional)
        $admin = User::firstOrCreate(
            ['username' => 'local_admin'],
            [
                'email' => 'admin@local.com',
                'password' => Hash::make('password'),
                'is_ldap_user' => false,
            ]
        );
        $admin->assignRole('admin');

        echo "Roles created: " . implode(', ', $roles) . "\n";
        echo "Local admin created: local_admin / password\n";
    }
}