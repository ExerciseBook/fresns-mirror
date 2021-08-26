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
use Illuminate\Support\Facades\Route;

trait BladeTrait
{

    public function display($viewName, $assignData){
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
        if($isMobile){
            $currentTheme = env('THEME_MOBILE') ?? $defaultTheme;
        }

        if(empty($currentTheme)){
            $currentTheme = $defaultTheme;
        }



        // 同时获取分享数据
        // 插件static目录
        $domain = CommonHelper::domain();
        $shareData = [];
        $shareData['theme_static'] = $domain . "/themes/$currentTheme/";
        $shareData['global_static'] = $domain . "/static/";

        $shareData['theme_static'] = $domain . "/assets/";
        $shareData['global_static'] = $domain . "/assets/";

        $shareData['cdn_static'] = CommonHelper::getWebCdnStatic();
        $shareData['cdn_static_h5'] = CommonHelper::getWebCdnH5Static();
        view()->share($shareData);
        // 视图路径
        // dd(public_path($templateName));
        $view = app('view')->getFinder();
   //     $view->prependLocation(public_path("/themes/" . $currentTheme));

        return view($viewName, $assignData);

    }


    // $viewPath = "plugins/example/$currentTheme/$viewName";
    // return view("plugins/example/theme1/a", $data);

    public function ajaxBlade($viewName, $assignData){
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
        if($isMobile){
            $currentTheme = env('THEME_MOBILE') ?? $defaultTheme;
        }

        if(empty($currentTheme)){
            $currentTheme = $defaultTheme;
        }

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

        return view($viewName, $assignData)->render();
    }


    // 插件设置页面
    public function displayPluginSetting($viewName, $assignData){
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
        $view->prependLocation(resource_path("/views/"));

        return view($viewName, $assignData);

    }
}
