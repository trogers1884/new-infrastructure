<?php

namespace App\Components\Admin\Helpers;

class AuthorizationHelper
{
    /**
     * List of system-critical permissions that require extra protection
     *
     * @var array
     */
    private static array $criticalPermissions = [
        'manage_system_settings',
        'manage_security',
        'manage_roles',
        'manage_permissions',
        'manage_users',
        'manage_authentication',
        'manage_authorization'
    ];

    /**
     * Check if a permission is considered system-critical
     *
     * @param string $permissionName
     * @return bool
     */
    public static function isSystemCriticalPermission(string $permissionName): bool
    {
        return in_array(
            strtolower($permissionName),
            self::$criticalPermissions
        );
    }

    /**
     * Check if the given role name is a system role
     *
     * @param string $roleName
     * @return bool
     */
    public static function isSystemRole(string $roleName): bool
    {
        $systemRoles = ['administrator', 'super admin', 'system admin'];
        return in_array(strtolower($roleName), $systemRoles);
    }
}
