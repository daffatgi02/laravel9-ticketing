<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\TicketCategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketMessageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HCController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Redirect based on role
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect('/admin/dashboard');
        } elseif ($user->role === 'it' || $user->role === 'ga') {
            return redirect('/support/dashboard');
        } else {
            return redirect('/user/dashboard');
        }
    });

    // User routes
    Route::middleware(['role:user'])->prefix('user')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'userDashboard'])->name('user.dashboard');
        Route::get('/tickets', [TicketController::class, 'index'])->name('user.tickets.index');
        Route::get('/tickets/create', [TicketController::class, 'create'])->name('user.tickets.create');
        Route::post('/tickets', [TicketController::class, 'store'])->name('user.tickets.store');
        Route::get('/tickets/{ticket}', [TicketController::class, 'userShow'])->name('user.tickets.show');
        Route::post('/tickets/{ticket}/messages', [TicketMessageController::class, 'store'])->name('user.tickets.messages.store');
    });

    // Support staff routes (IT/GA)
    Route::middleware(['role:it,ga'])->prefix('support')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'supportDashboard'])->name('support.dashboard');
        Route::get('/tickets', [TicketController::class, 'supportIndex'])->name('support.tickets.index');
        Route::get('/tickets/assigned', [TicketController::class, 'assignedIndex'])->name('support.tickets.assigned');
        Route::get('/tickets/{ticket}', [TicketController::class, 'supportShow'])->name('support.tickets.show');
        Route::post('/tickets/{ticket}/messages', [TicketMessageController::class, 'store'])->name('support.tickets.messages.store');
        Route::post('/tickets/{ticket}/take', [TicketController::class, 'takeOwnership'])->name('support.tickets.take');
        Route::post('/tickets/{ticket}/in-progress', [TicketController::class, 'markInProgress'])->name('support.tickets.progress');
        Route::post('/tickets/{ticket}/resolve', [TicketController::class, 'resolve'])->name('support.tickets.resolve');
    });

    // Admin routes
    Route::middleware(['role:hc'])->prefix('admin')->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');

        // Department management
        Route::get('/departments', [DepartmentController::class, 'index'])->name('admin.departments.index');
        Route::post('/departments', [DepartmentController::class, 'store'])->name('admin.departments.store');
        Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('admin.departments.update');
        Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('admin.departments.destroy');

        // Category management
        Route::get('/categories', [TicketCategoryController::class, 'index'])->name('admin.categories.index');
        Route::post('/categories', [TicketCategoryController::class, 'store'])->name('admin.categories.store');
        Route::put('/categories/{category}', [TicketCategoryController::class, 'update'])->name('admin.categories.update');
        Route::delete('/categories/{category}', [TicketCategoryController::class, 'destroy'])->name('admin.categories.destroy');

        // User management
        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

        // Ticket management
        Route::get('/tickets', [TicketController::class, 'index'])->name('admin.tickets.index');
        Route::get('/tickets/{ticket}', [TicketController::class, 'adminShow'])->name('admin.tickets.show');
        Route::get('/tickets/{ticket}/assign', [HCController::class, 'showAssignForm'])->name('admin.tickets.assign.form');
        Route::post('/tickets/{ticket}/assign', [HCController::class, 'assignTicket'])->name('admin.tickets.assign');
        Route::patch('/tickets/{ticket}/close', [TicketController::class, 'closeTicket'])->name('admin.tickets.close');

        // Reports
        Route::get('/reports', [DashboardController::class, 'reports'])->name('admin.reports');
        Route::get('/reports/export', [DashboardController::class, 'exportReport'])->name('admin.reports.export');
    });
});
