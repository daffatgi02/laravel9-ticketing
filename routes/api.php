<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// routes/api.php
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\TicketCategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketMessageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HCController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Tickets
    Route::apiResource('tickets', TicketController::class);
    Route::get('/tickets/{ticket}/messages', [TicketMessageController::class, 'index']);
    Route::post('/tickets/{ticket}/messages', [TicketMessageController::class, 'store']);

    // HC (Human Capital) Routes
    Route::middleware('role:hc')->prefix('hc')->group(function () {
        // Department Management
        Route::apiResource('departments', DepartmentController::class);

        // Ticket Category Management
        Route::apiResource('categories', TicketCategoryController::class);

        // User Management
        Route::apiResource('users', UserController::class);

        // Ticket Assignment
        Route::post('/tickets/{ticket}/assign', [HCController::class, 'assignTicket']);
        Route::get('/new-tickets', [HCController::class, 'getNewTickets']);
        Route::get('/department-stats', [HCController::class, 'getDepartmentStats']);
    });
});
