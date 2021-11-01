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
                case 'core_extensions':
                    $value = [];
                    $core_extensions = ['uuid', 'memcached', 'mongodb', 'redis', 'swoole'];
                    $able = get_loaded_extensions();
                    foreach ($able as $k => $v) {
                        if (in_array($v, $core_extensions)) {
                            $value[] = $v;
                        }
                    }
                    if ($value) {
                        sort($value);
                        sort($core_extensions);
                        if ($value === $core_extensions) {
                            return ['code' => '000000', 'message' => '检测成功', 'result' => ['info' => implode('&nbsp;&nbsp;', $value), 'status' => '<span class="text-success">√ 正常</span>']];
                        } else {
                            return ['code' => '100000', 'message' => '检测失败', 'result' => ['info' => implode('&nbsp;&nbsp;', $value), 'status' => '<span class="text-danger">× 错误</span>&nbsp;&nbsp;缺少&nbsp;&nbsp;' . implode('&nbsp;&nbsp;', array_diff($core_extensions, $value))]];
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
                case 'yii2':
                    $fm = new YYFileManager();
                    $info = $fm->info(Yii::getAlias('@runtime'));
                    $mode = isset($info['mode']) ? decoct($info['mode'] & 000777) : '';
                    if ($mode == '777') {
                        return ['code' => '000000', 'message' => '检测成功', 'result' => ['info' => 'runtime ' . $mode, 'status' => '<span class="text-success">√ 正常</span>']];
                    } else {
                        return ['code' => '100000', 'message' => '检测失败', 'result' => ['info' => 'runtime ' . $mode, 'status' => '<span class="text-danger">× 错误</span>&nbsp;&nbsp;runtime目录需要777权限']];
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
                case 'elastic':
                    $hosts = isset(Yii::$app->elastic->hosts) ? Yii::$app->elastic->hosts : '';
                    $hosts_log = isset(Yii::$app->elastic->hosts_log) ? Yii::$app->elastic->hosts_log : '';
                    $message = '';
                    try {
                        Yii::$app->elastic->client->nodes()->stats();
                    } catch (\Exception $e) {
                        $message .= '<p>hosts:' . $e->getMessage() . '</p>';
                    }
                    try {
                        Yii::$app->elastic->client_log->nodes()->stats();
                    } catch (\Exception $e) {
                        $message .= '<p>hosts_log:' . $e->getMessage() . '</p>';
                    }
                    $info = '';
                    foreach ($hosts as $v) {
                        $info .= '<p>hosts ' . $v . '</p>';
                    }
                    foreach ($hosts_log as $v) {
                        $info .= '<p>hosts_log ' . $v . '</p>';
                    }
                    if ($message === '') {
                        return ['code' => '000000', 'message' => '检测成功', 'result' => ['info' => $info, 'status' => '<span class="text-success">√ 正常</span>']];
                    } else {
                        return ['code' => '100000', 'message' => '检测失败', 'result' => ['info' => $info, 'status' => '<span class="text-danger">× 错误</span>&nbsp;&nbsp;' . $message]];
                    }
                    break;
                case 'redis':
                    $hostname = isset(Yii::$app->redis->hostname) ? Yii::$app->redis->hostname : '';
                    $port = isset(Yii::$app->redis->port) ? Yii::$app->redis->port : '';
                    $database = isset(Yii::$app->redis->database) ? Yii::$app->redis->database : '';
                    $message = '';
                    try {
                        Yii::$app->redis->open();
                    } catch (\Exception $e) {
                        $message = $e->getMessage();
                    }
                    $info = '<p>hostname ' . $hostname . '</p><p>port ' . $port . '</p><p>database ' . $database . '</p>';
                    if ($message === '') {
                        return ['code' => '000000', 'message' => '检测成功', 'result' => ['info' => $info, 'status' => '<span class="text-success">√ 正常</span>']];
                    } else {
                        return ['code' => '100000', 'message' => '检测失败', 'result' => ['info' => $info, 'status' => '<span class="text-danger">× 错误</span>&nbsp;&nbsp;' . $message]];
                    }
                    break;
                case 'hjy':
                    $message = '';
                    try {
                        $opengate_api_prefix = isset(Yii::$app->hjy->opengate_api_prefix) ? Yii::$app->hjy->opengate_api_prefix : '';
                        $platform_code = isset(Yii::$app->hjy->platform_code) ? Yii::$app->hjy->platform_code : '';
                        $token_info = isset(Yii::$app->hjy->token_info) ? Yii::$app->hjy->token_info : [];
                    } catch (\Exception $e) {
                        $message = $e->getMessage();
                        $message_arr = json_decode($message, true);
                        $opengate_api_prefix = isset($message_arr['opengate_api_prefix']) ? $message_arr['opengate_api_prefix'] : '';
                        $platform_code = isset($message_arr['platform_code']) ? $message_arr['platform_code'] : '';
                    }
                    $access_token = isset($token_info['accessToken']) ? $token_info['accessToken'] : '';
                    $info = '<p>opengate_api_prefix ' . $opengate_api_prefix . '</p><p>platform_code ' . $platform_code . '</p><p>access_token ' . $access_token . '</p>';
                    if ($message === '') {
                        return ['code' => '000000', 'message' => '检测成功', 'result' => ['info' => $info, 'status' => '<span class="text-success">√ 正常</span>']];
                    } else {
                        return ['code' => '100000', 'message' => '检测失败', 'result' => ['info' => $info, 'status' => '<span class="text-danger">× 错误</span>&nbsp;&nbsp;' . (isset($message_arr['result']) ? $message_arr['result'] : '')]];
                    }
                    break;
                case 'mq':
                    $mq = isset(Yii::$app->mq) ? Yii::$app->mq : [];
                    $class = $mq ? get_class($mq) : '';
                    if ($class == 'app\librarys\mq\cmq\CMQ') {
                        $endpoint = isset($mq->endpoint) ? $mq->endpoint : '';
                        $topicEndpoint = isset($mq->topicEndpoint) ? $mq->topicEndpoint : '';
                        $info = '<p>class ' . $class . '</p><p>endpoint ' . $endpoint . '</p><p>topicEndpoint ' . $topicEndpoint . '</p>';
                    } else {
                        $info = '';
                    }
                    $message = '';
                    try {
                        if ($class == 'app\librarys\mq\cmq\CMQ') {
                            Yii::$app->mq->listTopic();
                            Yii::$app->mq->listQueue();
                        } else {

                        }
                    } catch (\Exception $e) {
                        $info = '';
                        $message = $e->getMessage();
                    }
                    if ($message === '') {
                        return ['code' => '000000', 'message' => '检测成功', 'result' => ['info' => $info, 'status' => '<span class="text-success">√ 正常</span>']];
                    } else {
                        return ['code' => '100000', 'message' => '检测失败', 'result' => ['info' => $info, 'status' => '<span class="text-danger">× 错误</span>&nbsp;&nbsp;' . $message]];
                    }
                    break;
                case 'upload':
                    $upload = isset(Yii::$app->upload) ? Yii::$app->upload : [];
                    $upload_storage = isset(Yii::$app->params['admin']['upload_storage']) ? Yii::$app->params['admin']['upload_storage'] : '';
                    if ($upload_storage == 'cos') {
                        $cos_region = isset(Yii::$app->params['admin']['cos_region']) ? Yii::$app->params['admin']['cos_region'] : '';
                        $cos_bucket = isset(Yii::$app->params['admin']['cos_bucket']) ? Yii::$app->params['admin']['cos_bucket'] : '';
                        $info = '<p>upload_storage ' . $upload_storage . '</p><p>cos_region ' . $cos_region . '</p><p>cos_bucket ' . $cos_bucket . '</p>';
                    } else {
                        $info = '<p>upload_storage ' . $upload_storage . '</p>';
                    }
                    $message = '';
                    try {
                        if ($upload_storage == 'cos') {
                            $file = Yii::getAlias('@webroot') . '/assets/install/frontend/img/avator.png';
                            $options = array(
                                'allowType' => array('gif', 'jpg', 'jpeg', 'png'),
                                'allowSize' => 10240000,
                                'storage' => 'cos',
                                'resource' => file_get_contents($file),
                                'oriName' => strtolower('avator.png')
                            );
                            $upload->init($options);
                            $result = $upload->save('/' . Yii::$app->params['_common']['platform_code'] . '/uploads/tmp/');
                            if ($result && $result['code'] == '000000') {
                                $file_url = $result['fileUrl'];
                                $info .= '<p>upload_test ' . $file_url . '</p>';
                            } elseif ($result && isset($result['message'])) {
                                $message = $result['message'];
                            } else {
                                $message = '其它故障';
                            }
                        } else {

                        }
                    } catch (\Exception $e) {
                        $message = $e->getMessage();
                    }
                    if ($message === '') {
                        return ['code' => '000000', 'message' => '检测成功', 'result' => ['info' => $info, 'status' => '<span class="text-success">√ 正常</span>']];
                    } else {
                        return ['code' => '100000', 'message' => '检测失败', 'result' => ['info' => $info, 'status' => '<span class="text-danger">× 错误</span>&nbsp;&nbsp;' . $message]];
                    }
                    break;
                case 'sms':
                    $config = isset(Yii::$app->sms->config) ? Yii::$app->sms->config : [];
                    $SendApi = isset($config['SendApi']) ? $config['SendApi'] : '';
                    $SignName = isset($config['SignName']) ? $config['SignName'] : '';
                    $platform = isset($config['platform']) ? $config['platform'] : '';
                    $message = '';
                    try {

                    } catch (\Exception $e) {
                        $message = $e->getMessage();
                    }
                    $info = '<p>SendApi ' . $SendApi . '</p><p>SignName ' . $SignName . '</p><p style="margin-bottom: 6px;">platform ' . $platform . '</p>';
                    $info .= '<div class="layui-inline" style="width: 200px;"><input type="text" name="task_host" class="layui-input" placeholder="请输入手机号码" value=""></div><div class="layui-inline"><button class="layui-btn layui-btn-sm">检测</button></div>';
                    if ($message === '') {
                        return ['code' => '000000', 'message' => '检测成功', 'result' => ['info' => $info, 'status' => '<span class="text-muted">-</span>']];
                    } else {
                        return ['code' => '100000', 'message' => '检测失败', 'result' => ['info' => $info, 'status' => '<span class="text-danger">× 错误</span>&nbsp;&nbsp;' . $message]];
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
