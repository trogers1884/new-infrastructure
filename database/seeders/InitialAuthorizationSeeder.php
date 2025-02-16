<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class InitialAuthorizationSeeder extends Seeder
{
    protected $permissions = [
        // Store Management
        ['name' => 'View Stores', 'code' => 'stores.view'],
        ['name' => 'Manage Stores', 'code' => 'stores.manage'],

        // Group Management
        ['name' => 'View Groups', 'code' => 'groups.view'],
        ['name' => 'Manage Groups', 'code' => 'groups.manage'],

        // Group Types Management
        ['name' => 'View Group Types', 'code' => 'group-types.view'],
        ['name' => 'Manage Group Types', 'code' => 'group-types.manage'],

        // User Management
        ['name' => 'View Users', 'code' => 'users.view'],
        ['name' => 'Manage Users', 'code' => 'users.manage'],

        // Role Management
        ['name' => 'View Roles', 'code' => 'roles.view'],
        ['name' => 'Manage Roles', 'code' => 'roles.manage'],

        // Permission Management
        ['name' => 'View Permissions', 'code' => 'permissions.view'],
        ['name' => 'Manage Permissions', 'code' => 'permissions.manage'],
    ];

    protected $roles = [
        [
            'name' => 'System Administrator',
            'code' => 'sys.admin',
            'description' => 'Full system access',
            'permissions' => '*'  // Special case: all permissions
        ],
        [
            'name' => 'Store Manager',
            'code' => 'store.manager',
            'description' => 'Manages individual store operations',
            'permissions' => [
                'stores.view',
                'users.view',
                'groups.view'
            ]
        ],
        [
            'name' => 'Group Manager',
            'code' => 'group.manager',
            'description' => 'Manages group operations',
            'permissions' => [
                'stores.view',
                'groups.view',
                'groups.manage',
                'users.view'
            ]
        ],
        [
            'name' => 'User Manager',
            'code' => 'user.manager',
            'description' => 'Manages user accounts',
            'permissions' => [
                'users.view',
                'users.manage'
            ]
        ]
    ];

    public function run()
    {
        DB::transaction(function () {
            // Create Permissions
            foreach ($this->permissions as $permissionData) {
                Permission::firstOrCreate(
                    ['code' => $permissionData['code']],
                    $permissionData
                );
            }

            // Create Roles and Assign Permissions
            foreach ($this->roles as $roleData) {
                $permissions = $roleData['permissions'];
                unset($roleData['permissions']);

                $role = Role::firstOrCreate(
                    ['code' => $roleData['code']],
                    $roleData
                );

                // Handle permission assignment
                if ($permissions === '*') {
                    // Assign all permissions to system admin
                    $role->permissions()->sync(Permission::all());
                } else {
                    // Assign specific permissions
                    $role->permissions()->sync(
                        Permission::whereIn('code', $permissions)->pluck('id')
                    );
                }
            }
        });
    }
}
