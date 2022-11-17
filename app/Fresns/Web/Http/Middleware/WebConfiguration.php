<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Web\Http\Middleware;

use App\Fresns\Web\Helpers\ApiHelper;
use App\Fresns\Web\Helpers\DataHelper;
use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Models\File;
use App\Models\SessionKey;
use Browser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class WebConfiguration
{
    public function handle(Request $request, Closure $next)
    {
        $path = Browser::isMobile() ? fs_db_config('FresnsEngine_Mobile') : fs_db_config('FresnsEngine_Pc');

        if (! $path) {
            return Response::view('error', [
                'message' => Browser::isMobile() ? '<p>'.__('FsWeb::tips.errorMobileTheme').'</p><p>'.__('FsWeb::tips.settingThemeTip').'</p>' : '<p>'.__('FsWeb::tips.errorPcTheme').'</p><p>'.__('FsWeb::tips.settingThemeTip').'</p>',
                'code' => 500,
            ], 500);
        }

        if (fs_db_config('engine_api_type') == 'local') {
            if (! fs_db_config('engine_key_id')) {
                return Response::view('error', [
                    'message' => '<p>'.__('FsWeb::tips.errorKey').'</p><p>'.__('FsWeb::tips.settingApiTip').'</p>',
                    'code' => 500,
                ], 500);
            }

            $keyId = fs_db_config('engine_key_id');
            $cacheKey = "fresns_web_key_{$keyId}";
            $cacheTime = CacheHelper::fresnsCacheTimeByFileType();

            $keyInfo = Cache::remember($cacheKey, $cacheTime, function () use ($keyId) {
                return SessionKey::find($keyId);
            });

            if (! $keyInfo) {
                return Response::view('error', [
                    'message' => '<p>'.__('FsWeb::tips.errorKey').'</p><p>'.__('FsWeb::tips.settingApiTip').'</p>',
                    'code' => 500,
                ], 500);
            }
        }

        if (fs_db_config('engine_api_type') == 'remote') {
            if (! fs_db_config('engine_api_host') || ! fs_db_config('engine_api_app_id') || ! fs_db_config('engine_api_app_secret')) {
                return Response::view('error', [
                    'message' => '<p>'.__('FsWeb::tips.errorApi').'</p><p>'.__('FsWeb::tips.settingApiTip').'</p>',
                    'code' => 500,
                ], 500);
            }
        }

        $this->loadLanguages();
        $finder = app('view')->getFinder();
        $finder->prependLocation(base_path("extensions/themes/{$path}"));
        $this->webLangTag();

        $this->userPanel();
        $this->groupCategories();

        View::share('topList', DataHelper::getTopList());

        $timezone = fs_user('detail.timezone') ?: ConfigHelper::fresnsConfigByItemKey('default_timezone');
        Cookie::queue('timezone', $timezone);

        return $next($request);
    }

    private function userPanel(): void
    {
        if (fs_user()->check()) {
            $langTag = current_lang_tag();
            $uid = fs_user('detail.uid');

            $cacheKey = "fresns_web_user_panel_{$uid}_{$langTag}";

            $userPanel = Cache::remember($cacheKey, now()->addMinutes(), function () {
                $result = ApiHelper::make()->get('/api/v2/user/panel');

                return data_get($result, 'data.list', null);
            });

            View::share('userPanel', $userPanel);
        }
    }

    private function groupCategories(): void
    {
        $langTag = current_lang_tag();

        $cacheKey = "fresns_web_group_categories_{$langTag}";
        $cacheTime = CacheHelper::fresnsCacheTimeByFileType(File::TYPE_IMAGE);

        $groupCategories = Cache::remember($cacheKey, $cacheTime, function () {
            $result = ApiHelper::make()->get('/api/v2/group/categories', [
                'query' => [
                    'pageSize' => 100,
                    'page' => 1,
                ],
            ]);

            return data_get($result, 'data.list', []);
        });

        if (is_null($groupCategories)) {
            Cache::forget($cacheKey);

            $groupCategories = [];
        }

        View::share('groupCategories', $groupCategories);
    }

    public function loadLanguages()
    {
        $menus = fs_api_config('language_menus') ?? [];

        $supportedLocales = [];
        foreach ($menus as $menu) {
            $supportedLocales[$menu['langTag']] = ['name' => $menu['langName']];
        }

        app()->get('laravellocalization')->setSupportedLocales($supportedLocales);

        fs_api_config('language_status') ? Cache::put('supportedLocales', $supportedLocales) : Cache::forget('supportedLocales');
    }

    public function webLangTag()
    {
        $params = explode('/', \request()->getPathInfo());
        array_shift($params);

        $langTag = ConfigHelper::fresnsConfigByItemKey('default_language');
        if (\count($params) > 0) {
            $locale = $params[0];

            if (app('laravellocalization')->checkLocaleInSupportedLocales($locale)) {
                $langTag = $locale;
            }
        }

        Cookie::queue('langTag', $langTag);
    }
}
