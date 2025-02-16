<?php

use Illuminate\Support\Facades\Route;
use App\Components\Admin\Http\Controllers\UsersController;
use App\Components\Admin\Http\Controllers\RolesController;
use App\Components\Admin\Http\Controllers\PermissionsController;
use App\Components\Admin\Http\Controllers\ResourceTypesController;
use App\Components\Admin\Http\Controllers\ResourceTypeMappingsController;
use App\Components\Admin\Http\Controllers\WebPagesController;
use App\Components\Admin\Http\Controllers\MenuTypesController;
use App\Components\Admin\Http\Controllers\NavigationItemsController;
use App\Components\Admin\Http\Controllers\AdminDashboardController;
use App\Components\Admin\Http\Controllers\UserRolesController;
use App\Components\Admin\Http\Controllers\ResourceAssociationsController;
use App\Components\Admin\Http\Controllers\MigrationsController;

/*
|--------------------------------------------------------------------------
| Admin Web Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['web', 'auth'])->group(function () {

    Route::middleware(['resource.access:web_pages,admin.roles.php'])
        ->group(function () {
            // Resource routes with updated naming convention
            Route::resource('roles', RolesController::class)
                ->except(['show'])
                ->names([
                    'index' => 'roles.index',
                    'create' => 'roles.create',
                    'store' => 'roles.store',
                    'edit' => 'roles.edit',
                    'update' => 'roles.update',
                    'destroy' => 'roles.destroy',
                ]);

            // Permission management routes
            Route::get('/roles/{role}/permissions', [RolesController::class, 'managePermissions'])
                ->name('roles.permissions');

            Route::post('/roles/{role}/permissions', [RolesController::class, 'updatePermissions'])
                ->name('roles.permissions.update');
        });

    Route::middleware('resource.access:web_pages,admin.users.php')
        ->group(function () {
            Route::resource('users', UsersController::class)
                ->except(['show'])  // We don't need a show route for users
                ->names([
                    'index' => 'users.index',
                    'create' => 'users.create',
                    'store' => 'users.store',
                    'edit' => 'users.edit',
                    'update' => 'users.update',
                    'destroy' => 'users.destroy',
                ]);
        });

    // Resource Types management
    Route::middleware('resource.access:web_pages,admin.resource-types.php')->group(function () {
        Route::resource('resource-types', ResourceTypesController::class);
    });

// Resource Type Mappings Routes
    Route::middleware('resource.access:web_pages,admin.resource-type-mappings.php')
        ->group(function () {
            // AJAX routes must come BEFORE resource routes to prevent conflicts
            Route::prefix('resource-type-mappings')
                ->name('resource-type-mappings.')
                ->middleware(['resource.access:web_pages,admin.resource-type-mappings.php'])
                ->group(function () {
                    Route::get('/ajax/tables', [ResourceTypeMappingsController::class, 'getTables'])
                        ->name('tables');
                    Route::get('/ajax/columns', [ResourceTypeMappingsController::class, 'getColumns'])
                        ->name('columns');
                });

            // Main resource routes
            Route::resource('resource-type-mappings', ResourceTypeMappingsController::class)
                ->parameters([
                    'resource-type-mappings' => 'resource_type_mapping'
                ])
                ->except(['show'])  // We don't need a show route
                ->names([
                    'index' => 'resource-type-mappings.index',
                    'create' => 'resource-type-mappings.create',
                    'store' => 'resource-type-mappings.store',
                    'edit' => 'resource-type-mappings.edit',
                    'update' => 'resource-type-mappings.update',
                    'destroy' => 'resource-type-mappings.destroy',
                ]);
        });

    Route::middleware('resource.access:web_pages,admin.menu-types.php')
        ->group(function () {
            Route::resource('menu-types', MenuTypesController::class)
                ->except(['show'])  // Since we don't have a show route
                ->names([
                    'index' => 'menu-types.index',
                    'create' => 'menu-types.create',
                    'store' => 'menu-types.store',
                    'edit' => 'menu-types.edit',
                    'update' => 'menu-types.update',
                    'destroy' => 'menu-types.destroy',
                ]);
        });

    Route::middleware('resource.access:web_pages,admin.navigation-items.php')
        ->group(function () {
            Route::resource('navigation-items', NavigationItemsController::class)
                ->except(['show'])  // We don't have a show route
                ->names([
                    'index' => 'navigation-items.index',
                    'create' => 'navigation-items.create',
                    'store' => 'navigation-items.store',
                    'edit' => 'navigation-items.edit',
                    'update' => 'navigation-items.update',
                    'destroy' => 'navigation-items.destroy',
                ]);
        });

//    Route::resource('permissions', PermissionsController::class);
    Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/database-io', [AdminDashboardController::class, 'getDatabaseIO'])
        ->name('database.io');

    Route::middleware('resource.access:web_pages,admin.user-roles.php')
        ->group(function () {
            Route::resource('user-roles', UserRolesController::class)
                ->parameters([
                    'user-roles' => 'userRole'
                ])
                ->except(['show'])  // We don't have a show route
                ->names([
                    'index' => 'user-roles.index',
                    'create' => 'user-roles.create',
                    'store' => 'user-roles.store',
                    'edit' => 'user-roles.edit',
                    'update' => 'user-roles.update',
                    'destroy' => 'user-roles.destroy',
                ]);
        });

    Route::middleware('resource.access:web_pages,admin.web-pages.php')->group(function () {
        Route::resource('web-pages', WebPagesController::class);
    });

    Route::middleware('resource.access:web_pages,admin.permissions.php')->group(function () {
        Route::resource('permissions', PermissionsController::class);
    });


// Resource Associations Routes
    Route::middleware('resource.access:web_pages,admin.resource-associations.php')
        ->group(function () {
            // AJAX routes must come before resource routes to prevent conflicts
            Route::prefix('resource-associations')->name('resource-associations.')->group(function () {
                Route::get('/roles', [ResourceAssociationsController::class, 'getRoles'])
                    ->name('roles');
                Route::get('/resources', [ResourceAssociationsController::class, 'getResources'])
                    ->name('resources');
            });

            // Main resource routes
            Route::resource('resource-associations', ResourceAssociationsController::class)
                ->parameters([
                    'resource-associations' => 'resource_association'
                ])
                ->except(['show'])  // We don't need a show route
                ->names([
                    'index' => 'resource-associations.index',
                    'create' => 'resource-associations.create',
                    'store' => 'resource-associations.store',
                    'edit' => 'resource-associations.edit',
                    'update' => 'resource-associations.update',
                    'destroy' => 'resource-associations.destroy',
                ]);
        });

    Route::middleware('resource.access:web_pages,admin.migrations.php')
        ->group(function () {
            Route::resource('migrations', MigrationsController::class)
                ->only(['index', 'show'])
                ->names([
                    'index' => 'migrations.index',
                    'show' => 'migrations.show',
                ]);
        });
});

