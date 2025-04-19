<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // Add this line
use App\Http\Controllers\HomeController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Role-based redirects
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect('/admin/dashboard');
        } elseif ($user->isIT()) {
            return redirect('/it/dashboard');
        } elseif ($user->isGA()) {
            return redirect('/ga/dashboard');
        } else {
            return redirect('/user/dashboard');
        }
    });

    // Admin routes
    Route::middleware(['role:hc'])->prefix('admin')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        });

        Route::get('/departments', function () {
            return view('admin.departments.index');
        });

        Route::get('/categories', function () {
            return view('admin.categories.index');
        });

        Route::get('/users', function () {
            return view('admin.users.index');
        });

        Route::get('/tickets', function () {
            return view('admin.tickets.index');
        });
    });

    // IT Support routes
    Route::middleware(['role:it'])->prefix('it')->group(function () {
        Route::get('/dashboard', function () {
            return view('it.dashboard');
        });

        Route::get('/tickets', function () {
            return view('it.tickets.index');
        });
    });

    // GA Support routes
    Route::middleware(['role:ga'])->prefix('ga')->group(function () {
        Route::get('/dashboard', function () {
            return view('ga.dashboard');
        });

        Route::get('/tickets', function () {
            return view('ga.tickets.index');
        });
    });

    // User routes
    Route::middleware(['role:user'])->prefix('user')->group(function () {
        Route::get('/dashboard', function () {
            return view('user.dashboard');
        });

        Route::get('/tickets', function () {
            return view('user.tickets.index');
        });

        Route::get('/tickets/create', function () {
            return view('user.tickets.create');
        });
    });
});
