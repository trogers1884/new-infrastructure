<?php
use Illuminate\Support\Facades\Route;
use App\Components\Api\Http\Controllers\v1\Trading\DeskController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('trading')->group(function () {
        Route::get('/hello', [DeskController::class, 'hello'])->name('desk.hello');
    });
});
