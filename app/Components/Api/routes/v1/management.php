<?php
use Illuminate\Support\Facades\Route;
use App\Components\Api\Http\Controllers\v1\Management\ReportingController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('management')->group(function () {
        Route::get('/hello', [ReportingController::class, 'hello'])->name('management.hello');
    });
});
