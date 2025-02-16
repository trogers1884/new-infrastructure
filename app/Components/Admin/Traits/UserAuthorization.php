<?php

namespace App\Components\Admin\Traits;

use Illuminate\Support\Facades\DB;

trait UserAuthorization
{
    /**
     * Check if the user has permission for a specific resource and action
     *
     * @param string $resourceType
     * @param string $resourceValue
     * @param string $permission
     * @param int|null $resourceId
     * @return bool
     */
//    public function checkResourcePermission(string $resourceType, string $permission, ?int $resourceId = null): bool
    public function checkResourcePermission(string $resourceType, string $resourceValue, $permission): bool
    {
        $query = DB::table('auth.vw_user_authorizations')
            ->where('user_id', $this->id)
            ->where('resource_type', $resourceType)
            ->where('resource_value', $resourceValue)
            ->where('permission_name', $permission);

//        if ($resourceId !== null) {
//            $query->where(function($q) use ($resourceId) {
//                $q->where('resource_id', $resourceId)
//                    ->orWhereNull('resource_id');
//            });
//        }

        return $query->exists();
    }
}
