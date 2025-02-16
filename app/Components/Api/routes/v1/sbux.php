<?php
use Illuminate\Support\Facades\Route;
use App\Components\Api\Http\Controllers\v1\Sbux\SbuxController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('sbux')->group(function () {
        Route::get('/hello', [SbuxController::class, 'hello'])->name('sbux.hello');
    });
});
