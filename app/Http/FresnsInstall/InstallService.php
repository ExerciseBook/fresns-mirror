<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsInstall;

class InstallService
{
    const INSTALL_EXTENSIONS = ['fileinfo'];
    const INSTALL_FUNCTIONS  = ['putenv', 'symlink', 'readlink', 'proc_open'];

    // Get the current setting language
    public static function getLanguage($lang)
    {
        $map = FsConfig::LANGUAGE_MAP;
        return $map[$lang] ?? 'English - English';
    }

    /**
     * 环境检测
     */
    public static function envDetect($name = '')
    {
        try {
            switch ($name) {
                case 'php_version':
                    $value = PHP_VERSION;
                    if ($value !== '' && version_compare(PHP_VERSION, '8.0', '>=')) {
                        $html = '<span class="badge bg-success rounded-pill">'.trans('install.step2CheckStatusSuccess').'</span>';
                        return ['code' => '000000', 'message' => '检测成功','result'=>$html];
                    } else {
                        $html = '<span class="badge bg-danger rounded-pill">'.trans('install.step2CheckStatusFailure').'</span>';
                        return ['code' => '100000', 'message' => '检测失败','result'=>$html];
                    }
                    break;
                case 'https':
                    $value = (new \Illuminate\Http\Request)->server('REQUEST_SCHEME','http');
                    if($value == 'https'){
                        $html = '<span class="badge bg-success rounded-pill">'.trans('install.step2CheckStatusSuccess').'</span>';
                        return ['code' => '000000', 'message' => '检测成功','result'=>$html];
                    }else{
                        $html = '<span class="badge bg-warning rounded-pill">'.trans('install.step2CheckStatusWarning').'</span>';
                        return ['code' => '100000', 'message' => '检测失败','result'=>$html];
                    }
                    break;
                case 'folder':
                    $value = [];
                    $items = [
                        app_path('Plugins'),
                        public_path('assets'),
                        resource_path('views'),
                        storage_path('logs'),
                        database_path('migrations'),
                    ];
                    foreach ($items as $v) {
                        if(!is_writable($v)) {
                            $value[] = $v;
                        }
                    }
                    if (empty($value)) {
                        $html = '<span class="badge bg-success rounded-pill">'.trans('install.step2CheckStatusSuccess').'</span>';
                        return ['code' => '000000', 'message' => '检测成功','result'=>$html];
                    } else {
                        //$disabled = implode('&nbsp;&nbsp;', $value);
                        //$html = '<span><small class="text-muted">'.trans('install.step2StatusNotEnabled').': '.$disabled.'</small></span>';
                        $html = '<span class="badge bg-danger rounded-pill">'.trans('install.step2CheckStatusFailure').'</span>';
                        return ['code' => '100000', 'message' => '检测失败','result'=>$html];
                    }
                    break;
                case 'extensions':
                    $value = [];
                    $extensions = get_loaded_extensions();
                    foreach (self::INSTALL_EXTENSIONS as $v) {
                        if(!in_array($v,$extensions)){
                            $value[] = $v;
                        }
                    }
                    if (empty($value)) {
                        $html = '<span class="badge bg-success rounded-pill">'.trans('install.step2CheckStatusSuccess').'</span>';
                        return ['code' => '000000', 'message' => '检测成功','result'=>$html];
                    } else {
                        $disabled = implode('&nbsp;&nbsp;', $value);
                        $html = '<span><small class="text-muted">'.trans('install.step2StatusNotEnabled').': '.$disabled.'</small></span>';
                        $html .= '<span class="badge bg-danger rounded-pill">'.trans('install.step2CheckStatusFailure').'</span>';
                        return ['code' => '100000', 'message' => '检测失败','result'=>$html];
                    }
                    break;
                case 'functions':
                    $value = [];
                    $disable = get_cfg_var("disable_functions");
                    $disable = explode(',', $disable);
                    foreach ($disable as $v) {
                        if(in_array($v,self::INSTALL_FUNCTIONS)){
                            $value[] = $v;
                        }
                    }
                    if (empty($value)) {
                        $html = '<span class="badge bg-success rounded-pill">'.trans('install.step2CheckStatusSuccess').'</span>';
                        return ['code' => '000000', 'message' => '检测成功','result'=>$html];
                    } else {
                        $disabled = implode('&nbsp;&nbsp;', $value);
                        $html = '<span><small class="text-muted">'.trans('install.step2StatusNotEnabled').': '.$disabled.'</small></span>';
                        $html .= '<span class="badge bg-danger rounded-pill">'.trans('install.step2CheckStatusFailure').'</span>';
                        return ['code' => '100000', 'message' => '检测失败','result'=>$html];
                    }
                    break;
                default:
                    return ['code' => '200000', 'message' => 'name参数错误'];
            }
        } catch (\Exception $e) {
            return ['code' => '999999', 'message' => '服务失败'];
        }
    }

    /**
     * @param $db_host
     * @param $db_port
     * @param $db_name
     * @param $db_user
     * @param $db_pwd
     * @param $db_prefix
     * @return string
     */
    public static function mysqlDetect($db_host,$db_port,$db_name,$db_user,$db_pwd,$db_prefix){

    }

    /**
     * set env mysql Configuration
     */
    public static function envUpdate($key,$val){
        $envFilePath = base_path('.env');
        switch ($key){
            case 'db_host';
                $escaped = preg_quote('='.config('database.connections.mysql.host'), '/');
                $pattern = "/^DB_HOST{$escaped}/m";
                $replacement = 'DB_HOST='.$val;
            break;
            case 'db_port';
                $escaped = preg_quote('='.config('database.connections.mysql.port'), '/');
                $pattern = "/^DB_PORT{$escaped}/m";
                $replacement = 'DB_PORT='.$val;
                break;
            case 'db_name';
                $escaped = preg_quote('='.config('database.connections.mysql.database'), '/');
                $pattern = "/^DB_DATABASE{$escaped}/m";
                $replacement = 'DB_DATABASE='.$val;
                break;
            case 'db_user';
                $escaped = preg_quote('='.config('database.connections.mysql.username'), '/');
                $pattern = "/^DB_USERNAME{$escaped}/m";
                $replacement = 'DB_USERNAME='.$val;
                break;
            case 'db_pwd';
                $escaped = preg_quote('='.config('database.connections.mysql.password'), '/');
                $pattern = "/^DB_PASSWORD{$escaped}/m";
                $replacement = 'DB_PASSWORD='.$val;
                break;
            case 'db_prefix';
                $escaped = preg_quote('='.config('database.connections.mysql.prefix'), '/');
                $pattern = "/^DB_PREFIX{$escaped}/m";
                $replacement = 'DB_PREFIX='.$val;
                break;
            default:
                $pattern = "";
                $replacement = "";
                break;
        }
        if($pattern && $replacement){
            file_put_contents($envFilePath, preg_replace($pattern, $replacement, file_get_contents($envFilePath)));
        }
    }


    /**
     * insert init manager user
     */
    public static function initManager(){

    }
}
