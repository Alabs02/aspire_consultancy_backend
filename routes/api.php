<?php

use App\Http\Controllers\Api\Admin\AdminAuthController;
use App\Http\Controllers\Api\Admin\AdminManagement;
use App\Http\Controllers\Api\User\UserAuthController;
use App\Http\Controllers\Api\User\UserManagement;
use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'v1', 'middleware' => ['json.response', 'cors']], function () {
    Route::prefix('user')->group(function () {
        Route::post('/register', [UserAuthController::class, 'signup']);
        Route::post('/login', [UserAuthController::class, 'signin']);

        Route::middleware('auth:user')->group(function () {
            // Get Routes
            Route::get('/appointments', [UserManagement::class, 'getUserAppointments']);

            // Post Routes
            Route::post('/create-appointments', [UserManagement::class, 'createAppointment']);
        });
    });

    Route::prefix('admin')->group(function () {
        Route::post('/register', [AdminAuthController::class, 'signup']);
        Route::post('/login', [AdminAuthController::class, 'signin']);

        Route::middleware('auth:admin')->group(function () {
            // Get Routes
            Route::get('/all-appointments', [AdminManagement::class, 'getAllAppointments']);

            // Post Routes
            Route::post('/logout', [AdminAuthController::class, 'logout']);
            Route::post('/update-appointment/{user_appointment}', [AdminManagement::class, 'updateAppointment']);
            Route::post('/delete-appointment/{user_appointment}', [AdminManagement::class, 'deleteAppointment']);
        });
    });
});

// Route::middleware('auth:sanctum')->group(function() {});
