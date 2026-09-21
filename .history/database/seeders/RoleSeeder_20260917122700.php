<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Create permissions
        $permissions = [
            'view courses',
            'create courses',
            'edit courses',
            'delete courses',
            'manage users',
            'manage terms',
            'view reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles (independent of LDAP roles)
        $roles = [
            'Student' => ['view courses'],
            'Mentor' => ['view courses', 'view reports', 'edit courses'],
            'Admin' => ['view courses', 'create courses', 'edit courses', 'manage users', 'manage terms', 'view reports'],
            'Super-Admin' => ['*'], // All permissions
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            
            if (in_array('*', $rolePermissions)) {
                $role->syncPermissions(Permission::all());
            } else {
                $role->syncPermissions($rolePermissions);
            }
        }
    }
}