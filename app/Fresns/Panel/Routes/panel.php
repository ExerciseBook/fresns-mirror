<?php

use Illuminate\Support\Facades\Route;
use App\Fresns\Panel\Http\Controllers\LoginController;

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
    Route::get('logout', [LoginController::class, 'logout'])->name('logout');

    Route::resources([
    ]);

    Route::get('dashboard', function() {
        dd(request()->user());
    })->name('dashboard');
});

