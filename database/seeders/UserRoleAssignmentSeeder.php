<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\UserAccessRole;
use App\Models\Store;
use App\Models\Group;
use Illuminate\Support\Facades\DB;

class UserRoleAssignmentSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            // Clear existing assignments to prevent duplicates
            UserAccessRole::truncate();

            // 1. System Administrator Assignment
            // For user.id = 2 (test users), assign sys.admin role at corporate group level
            $sysAdminRole = Role::where('code', 'sys.admin')->first();
            UserAccessRole::create([
                'user_id' => 2,
                'role_id' => $sysAdminRole->id,
                'group_id' => 1  // Murgado Automotive (Corporate)
            ]);

            // 2. Store Manager Example
            // Assign store manager role for Motor Werks (store_id: 1)
            $storeManagerRole = Role::where('code', 'store.manager')->first();
            UserAccessRole::create([
                'user_id' => 3,  // tom
                'role_id' => $storeManagerRole->id,
                'store_id' => 1  // Motor Werks
            ]);

            // 3. Group Manager Example
            // Assign group manager role for Chicago Campus
            $groupManagerRole = Role::where('code', 'group.manager')->first();
            UserAccessRole::create([
                'user_id' => 5,  // dimitar
                'role_id' => $groupManagerRole->id,
                'group_id' => 4  // Chicago Campus
            ]);

            // Additional example: Give tom access to Honda brand group
            UserAccessRole::create([
                'user_id' => 3,  // tom
                'role_id' => $storeManagerRole->id,
                'group_id' => 5  // Honda brand group
            ]);
        });
    }
}
