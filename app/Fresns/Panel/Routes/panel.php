<?php

use App\Fresns\Panel\Http\Controllers\{
    SiteController,
    SendController,
    LoginController,
    AdminController,
    ConfigController,
    PolicyController,
    StorageController,
    LanguageController,
    DashboardController,
    MapConfigController,
    SessionKeyController,
    VerifyCodeController,
    UserConfigController,
    LanguageMenuController,
    WalletConfigController,
	PluginUsageController,
	ClientMenuController,
	ColumnController,
	LanguagePackController,
	EngineController,
	ThemeController,
	AppController,
	RenameConfigController,
	InteractiveConfigController,
};
use Illuminate\Support\Facades\Route;
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

    // plugin usage
    Route::resource('pluginUsages', PluginUsageController::class)->only([
        'store', 'update', 'destroy'
    ]);

    // update language
    Route::put('batch/languages/{itemKey}', [LanguageController::class, 'batchUpdate'])->name('languages.batch.update');
    Route::put('languages/{itemKey}', [LanguageController::class, 'update'])->name('languages.update');

    Route::prefix('system')->group(function() {
        // set language menus
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
        // policy
        Route::get('policy', [PolicyController::class, 'show'])->name('policy.show');
        Route::put('policy', [PolicyController::class, 'update'])->name('policy.update');
        // send
        Route::get('send', [SendController::class, 'show'])->name('send.show');
        Route::put('send', [SendController::class, 'update'])->name('send.update');
        // verify code
        Route::get('verifyCodes', [VerifyCodeController::class, 'index'])->name('verifyCodes.index');
        Route::get('verifyCodes/{itemKey}/edit', [VerifyCodeController::class, 'edit'])->name('verifyCodes.edit');
        Route::put('verifyCodes/{itemKey}', [VerifyCodeController::class, 'update'])->name('verifyCodes.update');
        // user
        Route::get('userConfigs', [UserConfigController::class, 'show'])->name('userConfigs.show');
        Route::put('userConfigs', [UserConfigController::class, 'update'])->name('userConfigs.update');
        // wallet
        Route::get('walletConfigs', [WalletConfigController::class, 'show'])->name('walletConfigs.show');
        Route::put('walletConfigs', [WalletConfigController::class, 'update'])->name('walletConfigs.update');

        Route::get('walletPayConfigs', [WalletConfigController::class, 'payIndex'])->name('walletPayConfigs.index');
        Route::post('walletPayConfigs', [WalletConfigController::class, 'payStore'])->name('walletPayConfigs.payStore');
        Route::put('walletPayConfigs/{id}', [WalletConfigController::class, 'payUpdate'])->name('walletPayConfigs.update');

        Route::get('walletWithdrawConfigs', [WalletConfigController::class, 'withdrawIndex'])->name('walletWithdrawConfigs.index');
        Route::put('walletWithdrawConfigs/{id}', [WalletConfigController::class, 'withdrawUpdate'])->name('walletWithdrawConfigs.update');

        // sotrage
        // image
        Route::get('storage/image', [StorageController::class, 'imageShow'])->name('storage.image.show');
        Route::put('storage/image', [StorageController::class, 'imageUpdate'])->name('storage.image.update');

        // video


        // map
        //Route::get('mapConfigs', [MapConfigController::class, 'index'])->name('mapConfigs.index');
        Route::resource('mapConfigs', MapConfigController::class)->only([
            'index', 'store', 'update'
        ]);
    });

    // operating
    Route::prefix('operation')->group(function() {
        // rename config
        Route::get('renameConfigs', [RenameConfigController::class, 'show'])->name('renameConfigs.show');

        // interactive config
        Route::get('interactiveConfigs', [InteractiveConfigController::class, 'show'])->name('interactiveConfigs.show');
        Route::put('interactiveConfigs', [InteractiveConfigController::class, 'update'])->name('interactiveConfigs.update');
    });

    // client
	Route::prefix('client')->group(function() {
		// set meuns
		Route::get('clientMenus', [ClientMenuController::class, 'index'])->name('clientMenus.index');
		Route::put('clientMenus/{id}', [ClientMenuController::class, 'update'])->name('clientMenus.update');
		// set columns
		Route::get('columns', [ColumnController::class, 'index'])->name('columns.index');
		Route::put('columns/{id}', [ColumnController::class, 'update'])->name('columns.update');
		//set language pack
		Route::get('languagePack', [LanguagePackController::class, 'index'])->name('languagePack.index');
		Route::get('languagePack/{id}/config', [LanguagePackController::class, 'show'])->name('languagePack.show');
		Route::post('languagePack/{id}/config', [LanguagePackController::class, 'store'])->name('languagePack.store');
		Route::delete('languagePack/{id}/config/{config_id}', [LanguagePackController::class, 'destroy'])->name('languagePack.destroy');
		//set engines
		Route::get('engines', [EngineController::class, 'index'])->name('engines.index');
		Route::put('engines/{id}/status', [EngineController::class, 'status'])->name('engines.status');
        Route::put('engines/{id}/relation', [EngineController::class, 'relation'])->name('engines.relation');
		Route::put('engines/{id}/uninstall', [EngineController::class, 'uninstall'])->name('engines.uninstall');
		// set themes
		Route::get('themes', [ThemeController::class, 'index'])->name('themes.index');
		Route::put('themes/{id}/status', [ThemeController::class, 'status'])->name('themes.status');
		Route::put('themes/{id}/uninstall', [ThemeController::class, 'uninstall'])->name('themes.uninstall');
		// set apps
		Route::get('apps', [AppController::class, 'index'])->name('apps.index');
		Route::put('apps/{id}/status', [AppController::class, 'status'])->name('apps.status');
		Route::put('apps/{id}/uninstall', [AppController::class, 'uninstall'])->name('apps.uninstall');
	});
});
