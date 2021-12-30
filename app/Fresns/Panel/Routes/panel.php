<?php

use Illuminate\Support\Facades\Route;
use App\Fresns\Panel\Http\Controllers\LoginController;
use App\Fresns\Panel\Http\Controllers\DashboardController;
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

    // manage
    Route::get('manage/keys', [ManageController::class, 'keyIndex'])->name('manage.keys');
    Route::get('manage/configs', [ManageController::class, 'configIndex'])->name('manage.configs');
    Route::get('manage/admins', [ManageController::class, 'adminIndex'])->name('manage.admins');

    Route::resources([
    ]);

});

