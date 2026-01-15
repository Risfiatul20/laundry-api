<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LaundryServiceController;
use App\Http\Controllers\Api\LaundryStatusController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/transactions/track/{code}', [TransactionController::class, 'track']);

// Public read-only
Route::get('/services', [LaundryServiceController::class, 'index']);
Route::get('/services/{service}', [LaundryServiceController::class, 'show']);
Route::get('/payment-methods', [PaymentMethodController::class, 'index']);
Route::get('/statuses', [LaundryStatusController::class, 'index']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/profile', [AuthController::class, 'profile']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::put('/auth/change-password', [AuthController::class, 'changePassword']);

    // Transactions - All authenticated users
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show']);

    // Transactions - Admin & Kasir only
    Route::middleware('role:admin,kasir')->group(function () {
        Route::put('/transactions/{transaction}', [TransactionController::class, 'update']);
        Route::patch('/transactions/{transaction}/status', [TransactionController::class, 'updateStatus']);
        Route::patch('/transactions/{transaction}/payment', [TransactionController::class, 'processPayment']);
    });

    // Admin only routes
    Route::middleware('role:admin')->group(function () {
        // Users management
        Route::apiResource('users', UserController::class);

        // Services management
        Route::post('/services', [LaundryServiceController::class, 'store']);
        Route::put('/services/{service}', [LaundryServiceController::class, 'update']);
        Route::delete('/services/{service}', [LaundryServiceController::class, 'destroy']);

        // Payment methods management
        Route::post('/payment-methods', [PaymentMethodController::class, 'store']);
        Route::put('/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'update']);
        Route::delete('/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'destroy']);

        // Statuses management
        Route::post('/statuses', [LaundryStatusController::class, 'store']);
        Route::put('/statuses/{status}', [LaundryStatusController::class, 'update']);
        Route::delete('/statuses/{status}', [LaundryStatusController::class, 'destroy']);

        // Reports
        Route::get('/reports/summary', [ReportController::class, 'summary']);
        Route::get('/reports/transactions', [ReportController::class, 'transactions']);
        Route::get('/reports/revenue', [ReportController::class, 'revenue']);
    });
});
