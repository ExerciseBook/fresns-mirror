<?php

use Illuminate\Support\Facades\Route;
use App\Fresns\Panel\Http\Controllers\{
    LoginController,
    AdminController,
    ConfigController,
    DashboardController,
    SessionKeyController,
};
use App\Fresns\Panel\Http\Controllers\ManageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login.form');
Route::post('login', [LoginController::class, 'login'])->name('login');

Route::middleware(['panelAuth'])->group(function() {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // dashboard
    Route::get('dashboard', [DashboardController::class, 'show'])->name('dashboard');

    Route::prefix('manage')->group(function() {
        Route::resource('sessionKeys', SessionKeyController::class)->only([
            'index', 'store', 'update', 'destroy'
        ]);
        Route::put('sessionKeys/{sessionKey}/reset', [SessionKeyController::class, 'reset'])->name('sessionKeys.reset');

        Route::resource('configs', ConfigController::class)->only([
            'index', 'store', 'update', 'destroy'
        ]);
        Route::resource('admins', AdminController::class)->only([
            'index', 'store', 'update', 'destroy'
        ]);
    });

});

