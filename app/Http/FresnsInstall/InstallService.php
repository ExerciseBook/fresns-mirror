<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsInstall;


class InstallService
{
    // Get the current setting language
    public static function getLanguage($lang)
    {
        $map = FsConfig::LANGUAGE_MAP;

        return $map[$lang] ?? 'English - English';
    }

    /**
     *  path must writeable
     */
    function dirDetect(){
        $items = [
            ['dir',  '可写', 'success', app_path('Plugins')],
            ['dir',  '可写', 'success', public_path('assets')],
            ['dir',  '可写', 'success', resource_path('views')],
            ['dir',  '可写', 'success', storage_path('logs')],
            ['dir',  '可写', 'success', database_path('migrations')],
        ];

        foreach ($items as &$val) {
            if(!is_writable($val[3])) {
                if(is_dir($val[3])) {
                    $val[1] = '可读';
                    $val[2] = 'error';
                    session('error', true);
                } else {
                    $val[1] = '不存在';
                    $val[2] = 'error';
                    session('error', true);
                }
            }
        }
        return $items;
    }

    /**
     * 环境检测
     */
    public static function envDetect($name = '')
    {
        try {
            switch ($name) {
                case 'server_host_name':
                    $os = explode(" ", php_uname());
                    $value = '/' == DIRECTORY_SEPARATOR ? $os[1] : $os[2];
                    if ($value !== '') {
                        return ['code' => '000000', 'message' => '检测成功', 'result' => ['info' => $value, 'status' => '<span class="text-success">√ 正常</span>']];
                    } else {
                        return ['code' => '100000', 'message' => '检测失败', 'result' => ['info' => '<span class="text-muted">-</span>', 'status' => '<span class="text-danger">× 错误</span>']];
                    }
                    break;
                case 'server_name':
                    $value = $_SERVER['SERVER_NAME'];
                    if ($value !== '') {
                        return ['code' => '000000', 'message' => '检测成功', 'result' => ['info' => $value, 'status' => '<span class="text-success">√ 正常</span>']];
                    } else {
                        return ['code' => '100000', 'message' => '检测失败', 'result' => ['info' => '<span class="text-muted">-</span>', 'status' => '<span class="text-danger">× 错误</span>']];
                    }
                    break;
                case 'server_addr':
                    $value = '/' == DIRECTORY_SEPARATOR ? $_SERVER['SERVER_ADDR'] : @gethostbyname($_SERVER['SERVER_NAME']);
                    if ($value !== '') {
                        return ['code' => '000000', 'message' => '检测成功', 'result' => ['info' => $value, 'status' => '<span class="text-success">√ 正常</span>']];
                    } else {
                        return ['code' => '100000', 'message' => '检测失败', 'result' => ['info' => '<span class="text-muted">-</span>', 'status' => '<span class="text-danger">× 错误</span>']];
                    }
                    break;
                case 'document_root':
                    $value = $_SERVER['DOCUMENT_ROOT'] ? str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']) : str_replace('\\', '/', dirname(__FILE__));
                    if ($value !== '') {
                        return ['code' => '000000', 'message' => '检测成功', 'result' => ['info' => $value, 'status' => '<span class="text-success">√ 正常</span>']];
                    } else {
                        return ['code' => '100000', 'message' => '检测失败', 'result' => ['info' => '<span class="text-muted">-</span>', 'status' => '<span class="text-danger">× 错误</span>']];
                    }
                    break;
                case 'server_time':
                    $value = date("Y-m-d H:i:s");
                    if ($value !== '') {
                        return ['code' => '000000', 'message' => '检测成功', 'result' => ['info' => $value, 'status' => '<span class="text-success">√ 正常</span>']];
                    } else {
                        return ['code' => '100000', 'message' => '检测失败', 'result' => ['info' => '<span class="text-muted">-</span>', 'status' => '<span class="text-danger">× 错误</span>']];
                    }
                    break;
                case 'df':
                    $value = round(@disk_free_space(".") / (1024 * 1024 * 1024), 3);
                    if ($value !== '') {
                        if ($value > 10) {
                            return ['code' => '000000', 'message' => '检测成功', 'result' => ['info' => $value . ' GB', 'status' => '<span class="text-success">√ 正常</span>']];
                        } else {
                            return ['code' => '100000', 'message' => '检测失败', 'result' => ['info' => $value . ' GB', 'status' => '<span class="text-danger">× 错误</span>&nbsp;&nbsp;剩余空间需要大于&nbsp;&nbsp;10 GB']];
                        }
                    } else {
                        return ['code' => '100000', 'message' => '检测失败', 'result' => ['info' => '<span class="text-muted">-</span>', 'status' => '<span class="text-danger">× 错误</span>']];
                    }
                    break;
                case 'dns':
                    $value = @gethostbyname('www.huijiaoyun.com');
                    if ($value !== '') {
                        return ['code' => '000000', 'message' => '检测成功', 'result' => ['info' => 'www.huijiaoyun.com(' . $value . ')', 'status' => '<span class="text-success">√ 正常</span>']];
                    } else {
                        return ['code' => '100000', 'message' => '检测失败', 'result' => ['info' => '<span class="text-muted">-</span>', 'status' => '<span class="text-warning">! 警告</span>']];
                    }
                    break;
                case 'php_version':
                    $value = PHP_VERSION;
                    if ($value !== '') {
                        if (version_compare(PHP_VERSION, '7.0', '>=')) {
                            return ['code' => '000000', 'message' => '检测成功', 'result' => ['info' => $value, 'status' => '<span class="text-success">√ 正常</span>']];
                        } else {
                            return ['code' => '100000', 'message' => '检测失败', 'result' => ['info' => $value, 'status' => '<span class="text-danger">× 错误</span>&nbsp;&nbsp;PHP版本需要大于&nbsp;&nbsp;7.0']];
                        }
                    } else {
                        return ['code' => '100000', 'message' => '检测失败', 'result' => ['info' => '<span class="text-muted">-</span>', 'status' => '<span class="text-danger">× 错误</span>']];
                    }
                    break;
                case 'curl':
                    $value = (function_exists('curl_init') !== false) ? true : false;
                    if ($value === true) {
                        return ['code' => '000000', 'message' => '检测成功', 'result' => ['info' => '支持', 'status' => '<span class="text-success">√ 正常</span>']];
                    } else {
                        return ['code' => '100000', 'message' => '检测失败', 'result' => ['info' => '不支持', 'status' => '<span class="text-danger">× 错误</span>&nbsp;&nbsp;需要支持Curl']];
                    }
                    break;
                case 'cookie':
                    if (isset($_COOKIE)) {
                        return ['code' => '000000', 'message' => '检测成功', 'result' => ['info' => '支持', 'status' => '<span class="text-success">√ 正常</span>']];
                    } else {
                        return ['code' => '100000', 'message' => '检测失败', 'result' => ['info' => '不支持', 'status' => '<span class="text-danger">× 错误</span>&nbsp;&nbsp;需要支持Cookie']];
                    }
                    break;
                case 'session':
                    $value = (function_exists('session_start') !== false) ? true : false;
                    if ($value === true) {
                        return ['code' => '000000', 'message' => '检测成功', 'result' => ['info' => '支持', 'status' => '<span class="text-success">√ 正常</span>']];
                    } else {
                        return ['code' => '100000', 'message' => '检测失败', 'result' => ['info' => '不支持', 'status' => '<span class="text-danger">× 错误</span>&nbsp;&nbsp;需要支持Session']];
                    }
                    break;
                case 'all_extensions':
                    $value = [];
                    $able = get_loaded_extensions();
                    foreach ($able as $k => $v) {
                        $value[] = $v;
                    }
                    if ($value) {
                        sort($value);
                        return ['code' => '000000', 'message' => '检测成功', 'result' => ['info' => implode('&nbsp;&nbsp;', $value), 'status' => '<span class="text-success">√ 正常</span>']];
                    } else {
                        return ['code' => '100000', 'message' => '检测失败', 'result' => ['info' => '<span class="text-muted">-</span>', 'status' => '<span class="text-danger">× 错误</span>']];
                    }
                    break;
                case 'disable_functions':
                    $value = [];
                    $disable = get_cfg_var("disable_functions");
                    $disable = explode(',', $disable);
                    foreach ($disable as $k => $v) {
                        $value[] = $v;
                    }
                    if ($value) {
                        sort($value);
                        return ['code' => '000000', 'message' => '检测成功', 'result' => ['info' => implode('&nbsp;&nbsp;', $value), 'status' => '<span class="text-success">√ 正常</span>']];
                    } else {
                        return ['code' => '100000', 'message' => '检测失败', 'result' => ['info' => '<span class="text-muted">-</span>', 'status' => '<span class="text-danger">× 错误</span>']];
                    }
                    break;
                case 'mysql':
                    $dsn = isset(Yii::$app->db->dsn) ? Yii::$app->db->dsn : '';
                    $message = '';
                    try {
                        Yii::$app->db->open();
                    } catch (\Exception $e) {
                        $message = $e->getMessage();
                    }
                    if ($message === '') {
                        return ['code' => '000000', 'message' => '检测成功', 'result' => ['info' => 'dsn ' . $dsn, 'status' => '<span class="text-success">√ 正常</span>']];
                    } else {
                        return ['code' => '100000', 'message' => '检测失败', 'result' => ['info' => 'dsn ' . $dsn, 'status' => '<span class="text-danger">× 错误</span>&nbsp;&nbsp;' . $message]];
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
