<?php

namespace App\Components\Admin\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait ResourceAuthorization
{
    /**
     * Check if the current user has permission for a specific resource and action
     *
     * @param string $resourceType The type of resource (e.g., 'web_pages')
     * @param string $permission The permission to check (e.g., 'view', 'edit')
     * @param int|null $resourceId Specific resource ID if checking a single resource
     * @return bool
     */
    public function checkResourcePermission(string $resourceType, string $permission, ?int $resourceId = null): bool
    {
        try {
            // Base query to check permissions
            $query = DB::table('auth.vw_user_authorizations')
                ->where('user_id', auth()->id())
                ->where('resource_type', $resourceType)
                ->where('permission_name', $permission);

            // If resourceId is provided, check specific resource or null (all resources)
            if ($resourceId !== null) {
                $query->where(function($q) use ($resourceId) {
                    $q->where('resource_id', $resourceId)
                        ->orWhereNull('resource_id');
                });
            }

            $hasPermission = $query->exists();

            // Log authorization check
            Log::debug('Authorization check', [
                'user_id' => auth()->id(),
                'resource_type' => $resourceType,
                'permission' => $permission,
                'resource_id' => $resourceId,
                'granted' => $hasPermission
            ]);

            return $hasPermission;

        } catch (\Exception $e) {
            Log::error('Authorization check failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'resource_type' => $resourceType,
                'permission' => $permission,
                'resource_id' => $resourceId
            ]);

            return false;
        }
    }

    /**
     * Get all resources of a specific type that the user has permission to access
     *
     * @param string $resourceType
     * @param string $permission
     * @return array
     */
    public function getAuthorizedResourceIds(string $resourceType, string $permission): array
    {
        try {
            return DB::table('auth.vw_user_authorizations')
                ->where('user_id', auth()->id())
                ->where('resource_type', $resourceType)
                ->where('permission_name', $permission)
                ->pluck('resource_id')
                ->filter()
                ->toArray();

        } catch (\Exception $e) {
            Log::error('Failed to get authorized resource IDs', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'resource_type' => $resourceType,
                'permission' => $permission
            ]);

            return [];
        }
    }
}
