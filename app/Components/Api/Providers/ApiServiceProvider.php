<?php
// app/Components/Api/Providers/ApiServiceProvider.php

namespace App\Components\Api\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Register any API services.
     */
    public function register(): void
    {
        // Register config file if we create one later
        $this->mergeConfigFrom(
            __DIR__ . '/../config/api.php', 'api'
        );

        // Register our API routes
        $this->registerRoutes();
    }

    /**
     * Bootstrap any API services.
     */
    public function boot(): void
    {
        // Load routes
        if ($this->app->routesAreCached()) {
            return;
        }

        // Register middleware
        $this->registerMiddleware();

        // Load views if we add any later
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'api');

        // Load translations if we add any later
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'api');

        // Publish configuration if we add it
        $this->publishes([
            __DIR__ . '/../config/api.php' => config_path('api.php'),
        ], 'api-config');
    }

    /**
     * Register the API routes.
     */
    protected function registerRoutes(): void
    {
        Route::group([
            'prefix' => 'api',
            'middleware' => ['api'],
//            'namespace' => 'App\Components\Api\Http\Controllers',
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        });

        // Version-specific routes
        Route::group([
            'prefix' => 'api/v1',
            'middleware' => ['api', 'api.version:v1'],
//            'namespace' => 'App\Components\Api\Http\Controllers\v1',
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/v1/api.php');
        });
    }

    /**
     * Register API middleware.
     */
    protected function registerMiddleware(): void
    {
        $router = $this->app['router'];

        // Add our custom middleware
        $router->aliasMiddleware('api.version', \App\Components\Api\Http\Middleware\ApiVersioning::class);
    }
}
