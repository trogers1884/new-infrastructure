<?php
use Illuminate\Support\Facades\Route;
use App\Components\Api\Http\Controllers\v1\AuthController;

Route::post('/login', [AuthController::class, 'login'])->name('api.login');
