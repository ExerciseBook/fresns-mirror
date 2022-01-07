<?php

use Illuminate\Support\Facades\Route;
use App\Fresns\Panel\Http\Controllers\{
    LoginController,
    AdminController,
    ConfigController,
    DashboardController,
    SessionKeyController,
    LanguageController,
    LanguageMenuController,
    SiteController,
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
        Route::put('languageMenus/status/switch', [LanguageMenuController::class, 'switchStatus'])->name('languageMenus.status.switch');
        Route::put('default/languages/update', [LanguageMenuController::class, 'updateDefaultLanguage'])->name('languageMenus.default.update');
        Route::get('languageMenus', [LanguageMenuController::class, 'index'])->name('languageMenus.index');
        Route::post('languageMenus', [LanguageMenuController::class, 'store'])->name('languageMenus.store');
        Route::put('languageMenus/{langTag}', [LanguageMenuController::class, 'update'])->name('languageMenus.update');
        Route::put('languageMeus/{langTag}/rank', [LanguageMenuController::class, 'updateRank'])->name('languageMenus.rank.update');
        Route::delete('languageMenus/{langTag}', [LanguageMenuController::class, 'destroy'])->name('languageMenus.destroy');


        // site
        Route::get('site', [SiteController::class, 'show'])->name('site.show');
        Route::put('site', [SiteController::class, 'update'])->name('site.update');
        Route::put('batch/languages/{itemKey}', [LanguageController::class, 'batchUpdate'])->name('language.batch.update');

    });
});

