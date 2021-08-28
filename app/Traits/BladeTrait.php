<?php
/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Traits;

// 模版设置
use App\Helpers\CommonHelper;
use Illuminate\Support\Facades\Route;

trait BladeTrait
{
    public function display($viewName, $assignData)
    {
        $route_arr = explode('@', Route::currentRouteAction());

        // 为插件控制器
        $data = [];
        $data['route_name'] = Route::currentRouteName();
        $data['controller_name'] = $route_arr[0];
        $data['action_name'] = strtolower($route_arr[1]);
        // set theme
        $isMobile = CommonHelper::isMobile();
        $defaultTheme = 'theme1';
        $currentTheme = env('THEME_WEB') ?? $defaultTheme;
        if ($isMobile) {
            $currentTheme = env('THEME_MOBILE') ?? $defaultTheme;
        }

        if (empty($currentTheme)) {
            $currentTheme = $defaultTheme;
        }

        // 同时获取分享数据
        $domain = CommonHelper::domain();
        $shareData = [];
        $shareData['theme_static'] = $domain.'/assets/';
        $shareData['global_static'] = $domain.'/assets/';
        view()->share($shareData);

        return view($viewName, $assignData);
    }

    /**
     * $viewPath = "plugins/example/$currentTheme/$viewName";
     * return view("plugins/example/theme1/a", $data);.
     * @param $viewName
     * @param $assignData
     * @return string
     */
    public function ajaxBlade($viewName, $assignData)
    {
        $route_arr = explode('@', Route::currentRouteAction());

        // 为插件控制器
        $data = [];
        $data['route_name'] = Route::currentRouteName();
        $data['controller_name'] = $route_arr[0];
        $data['action_name'] = strtolower($route_arr[1]);
        // set theme
        $isMobile = CommonHelper::isMobile();
        $defaultTheme = 'theme1';
        $currentTheme = env('THEME_WEB') ?? $defaultTheme;
        if ($isMobile) {
            $currentTheme = env('THEME_MOBILE') ?? $defaultTheme;
        }

        if (empty($currentTheme)) {
            $currentTheme = $defaultTheme;
        }

        // 同时获取分享数据
        $domain = CommonHelper::domain();
        $shareData = [];
        $shareData['theme_static'] = $domain.'/assets/';
        $shareData['global_static'] = $domain.'/assets/';
        view()->share($shareData);

        // 视图路径
        $view_path_name = 'themes/'.$currentTheme.'/'.$viewName;

        return view($view_path_name, $assignData)->render();
    }

    // 插件设置页面
    public function displayPluginSetting($viewName, $assignData)
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
