<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PlannerController as AdminPlannerController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;

use App\Http\Controllers\Auth\ClientLoginController;
use App\Http\Controllers\Auth\ClientRegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Client\HistoryController;
use App\Http\Controllers\Client\PaymentController;
use App\Http\Controllers\Client\PlannerController;
use App\Http\Controllers\Client\ProfileController as ClientProfileController;

/*
|--------------------------------------------------------------------------
| Rute Umum
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| Rute Admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    // Login Admin (guest)
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AdminLoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [AdminLoginController::class, 'login'])->name('login.submit')->middleware('throttle:5,1');
    });

    // Logout Admin
    Route::post('logout', [AdminLoginController::class, 'logout'])
        ->name('logout')
        ->middleware('auth:admin');

    // Admin yang sudah login
    Route::middleware('auth:admin')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Order langsung dari dashboard
        Route::patch('orders/{id}/status', [DashboardController::class, 'updateStatus'])->name('orders.update');
        Route::delete('orders/delete/{id}', [DashboardController::class, 'destroy'])->name('orders.destroy');
        Route::get('orders/{id}/bukti', [DashboardController::class, 'bukti'])->name('orders.bukti');

        // Manajemen Kategori (resource, tanpa show)
        Route::resource('categories', CategoryController::class)
            ->except(['show'])
            ->parameter('categories', 'id')
            ->names([
                'index'   => 'categories.index',
                'create'  => 'categories.create',
                'store'   => 'categories.store',
                'edit'    => 'categories.edit',
                'update'  => 'categories.update',
                'destroy' => 'categories.destroy',
            ]);

        // Manajemen Paket (resource, tanpa show)
        Route::resource('packages', PackageController::class)
            ->except(['show'])
            ->parameter('packages', 'id')
            ->names([
                'index'   => 'packages.index',
                'create'  => 'packages.create',
                'store'   => 'packages.store',
                'edit'    => 'packages.edit',
                'update'  => 'packages.update',
                'destroy' => 'packages.destroy',
            ]);

        // Manajemen Pengguna (Admin & Client)
        // Wedding Planner client (lihat saja)
        Route::get('planners', [AdminPlannerController::class, 'index'])->name('planners.index');
        Route::get('planners/{id}', [AdminPlannerController::class, 'show'])->name('planners.show');

        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::put('admin/{id}', [UserController::class, 'updateAdmin'])->name('admin.update');
            Route::put('client/{id}', [UserController::class, 'updateClient'])->name('client.update');
            Route::delete('{type}/{id}', [UserController::class, 'destroy'])->name('destroy');
        });

        // Profil Admin
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [AdminProfileController::class, 'index'])->name('index');
            Route::put('/', [AdminProfileController::class, 'update'])->name('update');
        });
    });
});

/*
|--------------------------------------------------------------------------
| Rute Client
|--------------------------------------------------------------------------
*/
Route::prefix('client')->name('client.')->group(function () {
    // Login/Register Client (guest)
    Route::middleware('guest:client')->group(function () {
        Route::get('login', [ClientLoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [ClientLoginController::class, 'login'])->name('login.submit')->middleware('throttle:5,1');

        Route::get('register', [ClientRegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('register', [ClientRegisterController::class, 'register'])->name('register.submit');
    });

    // Logout Client
    Route::post('logout', [ClientLoginController::class, 'logout'])
        ->name('logout')
        ->middleware('auth:client');

    // Client yang sudah login
    Route::middleware('auth:client')->group(function () {
        // Riwayat Pesanan
        Route::get('orders/history', [HistoryController::class, 'index'])->name('orders.history');
        Route::get('orders/{id}/bukti', [HistoryController::class, 'bukti'])->name('orders.bukti');

        // Pembayaran
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::get('{package}', [PaymentController::class, 'showForm'])->name('show');
            Route::post('{package}', [PaymentController::class, 'processOrder'])->name('process');
        });

        // Profil Client
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ClientProfileController::class, 'index'])->name('index');
            Route::put('/', [ClientProfileController::class, 'update'])->name('update');
        });

        // Wedding Planner
        Route::prefix('planner')->name('planner')->controller(PlannerController::class)->group(function () {
            Route::get('/', 'show');
            Route::put('/', 'updateInfo')->name('.info');
            Route::delete('/', 'reset')->name('.reset');

            Route::post('tasks', 'storeTask')->name('.tasks.store');
            Route::put('tasks/{id}', 'updateTask')->name('.tasks.update');
            Route::patch('tasks/{id}/toggle', 'toggleTask')->name('.tasks.toggle');
            Route::delete('tasks/{id}', 'destroyTask')->name('.tasks.destroy');

            Route::post('budget', 'storeBudget')->name('.budget.store');
            Route::put('budget/{id}', 'updateBudget')->name('.budget.update');
            Route::delete('budget/{id}', 'destroyBudget')->name('.budget.destroy');

            Route::post('guests', 'storeGuest')->name('.guests.store');
            Route::put('guests/{id}', 'updateGuest')->name('.guests.update');
            Route::patch('guests/{id}/rsvp', 'rsvpGuest')->name('.guests.rsvp');
            Route::delete('guests/{id}', 'destroyGuest')->name('.guests.destroy');
        });
    });
});