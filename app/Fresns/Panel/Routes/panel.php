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
    StopWordController,
    MemberRoleController,
    GroupController,
	ThemeController,
	AppController,
	RenameConfigController,
	InteractiveConfigController,
    EmojiController,
    EmojiGroupController,
    PublishConfigController,
	PluginController,
	ExpandEditorController,
	ExpandTypeController,
	ExpandPostController,
	ExpandManageController,
	ExpandGroupController,
	ExpandFeatureController,
	ExpandProfileController,
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

        // emoji
        Route::resource('emojis', EmojiController::class)->only([
            'index', 'store', 'update', 'destroy'
        ]);
        // emoji group
        Route::resource('emojiGroups', EmojiGroupController::class)->only([
            'index', 'store', 'update', 'destroy'
        ]);

        // publsh config
        // post
        Route::get('postConfigs', [PublishConfigController::class, 'postShow'])->name('postConfigs.show');
        Route::put('postConfigs', [PublishConfigController::class, 'postUpdate'])->name('postConfigs.update');

        Route::get('commentConfigs', [PublishConfigController::class, 'commentShow'])->name('commentConfigs.show');
        Route::put('commentConfigs', [PublishConfigController::class, 'commentUpdate'])->name('commentConfigs.update');

        // stop words
        Route::resource('stopWords', StopWordController::class)->only([
            'index', 'store', 'update', 'destroy'
        ]);

        Route::resource('memberRoles', MemberRoleController::class)->only([
            'index', 'store', 'update', 'destroy'
        ]);

        Route::resource('groups', GroupController::class)->only([
            'index', 'store', 'update', 'destroy'
        ]);
        Route::get('recommendGroups', [GroupController::class, 'recommendIndex'])->name('recommendGroups.index');
        Route::get('disableGroups', [GroupController::class, 'disableIndex'])->name('disableGroups.index');
        Route::put('groups/{group}/enable', [GroupController::class, 'updateEnable'])->name('groups.enable.update');
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

	//plugin
	Route::prefix('plugin')->group(function() {
		// set plugins
		Route::get('plugins', [PluginController::class, 'index'])->name('plugins.index');
		Route::put('plugins/{id}/status', [PluginController::class, 'status'])->name('plugins.status');
		Route::put('plugins/{id}/uninstall', [PluginController::class, 'uninstall'])->name('plugins.uninstall');
	});

	//expand
	Route::prefix('expand')->group(function() {
		// set editor
		Route::resource('expandEditor', ExpandEditorController::class)->only([
			'index', 'store', 'update', 'destroy'
		]);
		Route::put('expandEditor/{id}/rank', [ExpandEditorController::class, 'updateRank'])->name('expandEditor.rank');
		// set type
		Route::resource('expandType', ExpandTypeController::class)->only([
			'index', 'store', 'update', 'destroy'
		]);
		Route::put('expandType/{id}/rank', [ExpandTypeController::class, 'updateRank'])->name('expandType.rank');
		Route::put('expandType/{id}/source', [ExpandTypeController::class, 'source'])->name('expandType.source');
		// set post
		Route::resource('expandPost', ExpandPostController::class)->only([
			'index', 'update'
		]);
		// set manage
		Route::resource('expandManage', ExpandManageController::class)->only([
			'index', 'store', 'update', 'destroy'
		]);
		Route::put('expandManage/{id}/rank', [ExpandManageController::class, 'updateRank'])->name('expandManage.rank');
		// set group
		Route::resource('expandGroup', ExpandGroupController::class)->only([
			'index', 'store', 'update', 'destroy'
		]);
		Route::put('expandGroup/{id}/rank', [ExpandGroupController::class, 'updateRank'])->name('expandGroup.rank');

		// set userfeature
		Route::resource('expandFeature', ExpandFeatureController::class)->only([
			'index', 'store', 'update', 'destroy'
		]);
		Route::put('expandFeature/{id}/rank', [ExpandFeatureController::class, 'updateRank'])->name('expandFeature.rank');

		// set userprofile
		Route::resource('expandProfile', ExpandProfileController::class)->only([
			'index', 'store', 'update', 'destroy'
		]);
		Route::put('expandProfile/{id}/rank', [ExpandProfileController::class, 'updateRank'])->name('expandProfile.rank');


	});
});
