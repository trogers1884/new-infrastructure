<?php

namespace App\Helpers;

class IconHelper
{
    /**
     * Common icons mapped to their Font Awesome equivalents
     */
    private static $commonIcons = [
        'dashboard' => 'gauge',
        'users' => 'users',
        'settings' => 'cog',
        'reports' => 'chart-bar',
        'menu' => 'bars',
        'resources' => 'folder',
        'navigation' => 'compass',
        'roles' => 'user-shield',
        'permissions' => 'key',
        'stores' => 'store',
        'pages' => 'file',
        'types' => 'tags',
        'list' => 'list',
        'home' => 'home',
        'database' => 'database',
        'server' => 'server',
        'business' => 'globe',
    ];

    /**
     * Get the full icon class string
     */
    public static function getIconClasses(?string $icon = null): string
    {
        if (empty($icon)) {
            return '';
        }

        // Check if it's a common icon name
        if (isset(self::$commonIcons[$icon])) {
            return 'fas fa-' . self::$commonIcons[$icon];
        }

        // If it's a direct FA icon name, use it
        return 'fas fa-' . $icon;
    }

    /**
     * Get list of common icons for the form selection
     */
    public static function getCommonIcons(): array
    {
        $icons = [];
        foreach (self::$commonIcons as $name => $icon) {
            $icons[$name] = [
                'name' => ucfirst($name),
                'value' => $name,
                'classes' => self::getIconClasses($name)
            ];
        }
        return $icons;
    }

    /**
     * Check if an icon name is valid
     */
    public static function isValidIcon(?string $icon): bool
    {
        if (empty($icon)) {
            return true;
        }

        return isset(self::$commonIcons[$icon]);
    }
}
