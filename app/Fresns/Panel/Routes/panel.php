<?php

use Illuminate\Support\Facades\Route;
use App\Fresns\Panel\Http\Controllers\{
    LoginController,
    AdminController,
    ConfigController,
    DashboardController,
    SessionKeyController,
    LanguageController,
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
        // session key
        Route::resource('sessionKeys', SessionKeyController::class)->only([
            'index', 'store', 'update', 'destroy'
        ]);
        Route::put('sessionKeys/{sessionKey}/reset', [SessionKeyController::class, 'reset'])->name('sessionKeys.reset');

        // config
        Route::get('configs/show', [ConfigController::class, 'show'])->name('configs.show');
        Route::put('configs/update', [ConfigController::class, 'update'])->name('configs.update');

        // admin
        Route::resource('admins', AdminController::class)->only([
            'index', 'store', 'destroy'
        ]);
    });

    Route::prefix('system')->group(function() {
        // set language
        Route::put('languages/status/switch', [LanguageController::class, 'switchStatus'])->name('languages.status.switch');
        Route::put('default/languages/update', [LanguageController::class, 'updateDefaultLanguage'])->name('languages.default.update');
        Route::get('languages', [LanguageController::class, 'index'])->name('languages.index');
        Route::post('languages', [LanguageController::class, 'store'])->name('languages.store');
        Route::put('languages/{langTag}', [LanguageController::class, 'update'])->name('languages.update');
        Route::put('languages/{langTag}/rank', [LanguageController::class, 'updateRank'])->name('languages.rank.update');
        Route::delete('languages/{langTag}', [LanguageController::class, 'destroy'])->name('languages.destroy');

    });
});

