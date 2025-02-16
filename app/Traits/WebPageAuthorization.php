<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait WebPageAuthorization
{
    /**
     * Check if the current user has permission for a specific resource and action
     *
     * @param string $resourceType The type of resource (e.g., 'web_pages')
     * @param string $resourceValue The resource value (e.g., 'admin.web_pages.php')
     * @param string $permission The permission to check (e.g., 'view', 'edit')
     * @param int|null $resourceId Specific resource ID if checking a single resource
     * @return bool
     */
    public function checkResourcePermission(string $resourceType, string $resourceValue, string $permission): bool
    {
        $cacheKey = "auth_{$resourceType}_{$resourceValue}_{$permission}_" . auth()->id();

        return Cache::remember($cacheKey, now()->addMinutes(0), function() use ($resourceType, $resourceValue, $permission) {
            try {
                return DB::table('auth.vw_user_authorizations')
                    ->where('user_id', auth()->id())
                    ->where('resource_type', $resourceType)
                    ->where('resource_value', $resourceValue)
                    ->where('permission_name', $permission)
                    ->exists();
            } catch (\Exception $e) {
                Log::error('Authorization check failed', [
                    'error' => $e->getMessage(),
                    'user_id' => auth()->id(),
                    'resource_type' => $resourceType,
                    'resource_value' => $resourceValue,
                    'permission' => $permission
                ]);
                return false;
            }
        });
    }


    /**
     * Get all resources of a specific type that the user has permission to access
     *
     * @param string $resourceType
     * @param string $resourceValue
     * @param string $permission
     * @return array
     */
    public function getAuthorizedResourceIds(string $resourceType, string $resourceValue, string $permission): array
    {
        try {
            return DB::table('auth.vw_user_authorizations')
                ->where('user_id', auth()->id())
                ->where('resource_type', $resourceType)
                ->where('resource_value', $resourceValue)
                ->where('permission_name', $permission)
                ->pluck('resource_id')
                ->filter()
                ->toArray();

        } catch (\Exception $e) {
            Log::error('Failed to get authorized resource IDs', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'resource_type' => $resourceType,
                'resource_value' => $resourceValue,
                'permission' => $permission
            ]);

            return [];
        }
    }
}
