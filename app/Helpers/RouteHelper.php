<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RouteHelper
{
    /**
     * Get admin named routes for navigation item selection.
     *
     * @return array
     */
    public static function getAdminNamedRoutes(): array
    {
        $routes = Route::getRoutes();
        $namedRoutes = [];

        foreach ($routes as $route) {
            if ($name = $route->getName()) {
                // Only include admin routes and ensure they're appropriate for navigation
                if (!Str::startsWith($name, 'admin.') || !self::shouldIncludeRoute($name, $route)) {
                    continue;
                }

                // Get the section name (e.g., 'users' from 'admin.users.index')
                $segments = explode('.', $name);
                $group = $segments[1] ?? 'other';

                $namedRoutes[$group][] = [
                    'name' => $name,
                    'uri' => $route->uri(),
                    'methods' => $route->methods()[0] ?? '', // Usually GET for navigation
                ];
            }
        }

        // Sort groups and routes alphabetically
        ksort($namedRoutes);
        foreach ($namedRoutes as &$routes) {
            usort($routes, function ($a, $b) {
                return strcmp($a['name'], $b['name']);
            });
        }

        return $namedRoutes;
    }

    /**
     * Determine if a route should be included in the navigation options.
     *
     * @param string $routeName
     * @param \Illuminate\Routing\Route $route
     * @return bool
     */
    private static function shouldIncludeRoute(string $routeName, $route): bool
    {
        // Only include index and show routes
        // Exclude CRUD operation routes that shouldn't be in navigation
        $excludedPatterns = [
            '*.create',
            '*.store',
            '*.edit',
            '*.update',
            '*.destroy',
            '*.io',
            '*.columns',
            '*.tables',
            '*.api.*'
        ];

        // Check if route matches any excluded pattern
        foreach ($excludedPatterns as $pattern) {
            if (Str::is($pattern, $routeName)) {
                return false;
            }
        }

        // Check for route parameters in URI
        $uri = $route->uri();
        if (preg_match('/{.*}/', $uri)) {
            return false;
        }

        // Only allow GET routes for navigation
        if (!in_array('GET', $route->methods())) {
            return false;
        }

        return true;
    }
}
