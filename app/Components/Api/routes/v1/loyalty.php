<?php
use Illuminate\Support\Facades\Route;
use App\Components\Api\Http\Controllers\v1\Loyalty\LoyaltyController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('loyalty')->group(function () {
        Route::get('/hello', [LoyaltyController::class, 'hello'])->name('loyalty.hello');
    });
});
