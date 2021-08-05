<?php
/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Traits;

// 模版设置
use App\Helpers\CommonHelper;
use App\Helpers\ThemeHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

trait BladePluginTrait
{

    public function displayEngine($engineUniKey, $viewName, $assignData){
        $route_arr = explode('@', Route::currentRouteAction());

        // 为插件控制器
        $data = [];
        $data['route_name'] = Route::currentRouteName();
        $data['controller_name'] = $route_arr[0];
        $data['action_name'] = strtolower($route_arr[1]);
        // set theme
        $isMobile = CommonHelper::isMobile();
        $defaultTheme = 'default';

        $configKey = "{$engineUniKey}_Pc";
        if($isMobile){
            $configKey = "{$engineUniKey}_Mobile";
        }

        $currentTheme = DB::table('configs')->where('item_key', $configKey)->where('deleted_at',NULL)->value('item_value');
        if(empty($currentTheme)){
            $currentTheme = $defaultTheme;
        }

       // dd($currentTheme);

        // 同时获取分享数据
        // 插件static目录
        $domain = CommonHelper::domain();
        $shareData = [];
        $shareData['theme_static'] = $domain . "/themes/$currentTheme/";
        $shareData['global_static'] = $domain . "/static/";
        $shareData['cdn_static'] = CommonHelper::getWebCdnStatic();
        $shareData['cdn_static_h5'] = CommonHelper::getWebCdnH5Static();
        view()->share($shareData);
        // 视图路径
        // dd(public_path($templateName));
        $view = app('view')->getFinder();
        $view->prependLocation(public_path("/themes/" . $currentTheme));

        return view($viewName, $assignData);

    }


    // 插件设置页面
    public function displayView($viewName, $assignData){
        $route_arr = explode('@', Route::currentRouteAction());

        // 为插件控制器
        $data = [];
        $data['route_name'] = Route::currentRouteName();
        $data['controller_name'] = $route_arr[0];
        $data['action_name'] = strtolower($route_arr[1]);

        // 同时获取分享数据
        // 插件static目录
        $domain = CommonHelper::domain();
        $shareData = [];
        $shareData['global_static'] = $domain . "/static/";
        $shareData['cdn_static'] = CommonHelper::getWebCdnStatic();
        $shareData['cdn_static_h5'] = CommonHelper::getWebCdnH5Static();
        view()->share($shareData);

        // 视图路径
        // dd(public_path($templateName));
        $view = app('view')->getFinder();
        $view->prependLocation(public_path("/views/"));

        return view($viewName, $assignData);

    }
}
