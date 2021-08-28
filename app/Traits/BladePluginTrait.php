<?php
/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Traits;

// 模版设置
use App\Helpers\CommonHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

trait BladePluginTrait
{

    /**
     * 主题视图渲染
     */
    public function displayEngine($engineUniKey, $viewName, $assignData)
    {
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
        if ($isMobile) {
            $configKey = "{$engineUniKey}_Mobile";
        }

        $currentTheme = DB::table('configs')->where('item_key', $configKey)->where('deleted_at', null)->value('item_value');
        if (empty($currentTheme)) {
            $currentTheme = $defaultTheme;
        }
        // 同时获取分享数据
        // 插件static目录
        $domain = CommonHelper::domain();
        $shareData = [];
        $shareData['theme_static'] = $domain."/assets/";
        $shareData['global_static'] = $domain.'/assets/';
        view()->share($shareData);
        // 视图路径
        $view_path_name = 'themes/'.$currentTheme.'/'.$viewName;
        return view($view_path_name, $assignData);
    }

    /**
     * 基础视图渲染
     */
    public function displayView($viewName, $assignData)
    {
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
        $shareData['global_static'] = $domain.'/assets/';
        view()->share($shareData);

        return view($viewName, $assignData);
    }
}
