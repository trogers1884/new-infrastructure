<?php

namespace App\Components\Admin\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Components\Admin\Http\Middleware\AdminAuthentication;
use App\Components\Admin\View\Composers\NavigationComposer;
use Illuminate\Support\Facades\View;

class AdminServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Update route model binding to new Laravel 11 syntax
        Route::bind('resource_type_mapping', function ($value) {
            return \App\Models\ResourceTypeMapping::findOrFail($value);
        });

        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        // Load views with namespace
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'admin');

        // Register middleware using new Laravel 11 method
        $this->app['router']->middlewareGroup('admin', [
            AdminAuthentication::class,
        ]);

        // Register view composer
        View::composer('admin::layouts.admin', NavigationComposer::class);

        // Publishing assets
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/admin'),
            ], 'admin-views');
        }
    }
}
