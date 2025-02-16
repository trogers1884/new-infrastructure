<?php
use Illuminate\Support\Facades\Route;
use App\Components\Api\Http\Controllers\v1\Cafe\MenuController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('cafe')->group(function () {
        Route::get('/hello', [MenuController::class, 'hello'])->name('cafe.hello');
    });
});
