<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsInstall;

use App\Helpers\StrHelper;
use App\Http\FresnsApi\Helpers\ApiCommonHelper;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsCmd\FresnsSubPluginService;
use App\Http\FresnsDb\FresnsConfigs\FresnsConfigs;
use App\Http\FresnsDb\FresnsMemberRoleRels\FresnsMemberRoleRels;
use App\Http\FresnsDb\FresnsMembers\FresnsMembers;
use App\Http\FresnsDb\FresnsMembers\FresnsMembersConfig;
use App\Http\FresnsDb\FresnsMemberStats\FresnsMemberStats;
use App\Http\FresnsDb\FresnsUsers\FresnsUsers;
use App\Http\FresnsDb\FresnsUsers\FresnsUsersConfig;
use App\Http\FresnsDb\FresnsUserWallets\FresnsUserWallets;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class InstallService
{
    const INSTALL_EXTENSIONS = ['fileinfo'];
    const INSTALL_FUNCTIONS  = ['putenv', 'symlink', 'readlink', 'proc_open'];


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
                case 'mysql_version':
                    $value = '9.0';
                    if ($value !== '' && version_compare($value, '8.0', '>=')) {
                        $html = '<span class="badge bg-success rounded-pill">'.trans('install.step2CheckStatusSuccess').'</span>';
                        return ['code' => '000000', 'message' => '检测成功','result'=>$html];
                    } else {
                        $html = '<span class="badge bg-danger rounded-pill">'.trans('install.step2CheckStatusFailure').'</span>';
                        return ['code' => '100000', 'message' => '检测失败','result'=>$html];
                    }
                    break;
                case 'mysql_db':
                    Artisan::call('migrate');
                    $value = '9.0';
                    if ($value !== '' && version_compare($value, '8.0', '>=')) {
                        $html = '<span class="badge bg-success rounded-pill">'.trans('install.step2CheckStatusSuccess').'</span>';
                        return ['code' => '000000', 'message' => '检测成功','result'=>$html];
                    } else {
                        $html = '<span class="badge bg-danger rounded-pill">'.trans('install.step2CheckStatusFailure').'</span>';
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
     * init manager user
     */
    public static function registerUser($params=[]){
        try{
            $input = [
                'email' => $params['email'],
                'country_code' => $params['countryCode'],
                'pure_phone' => $params['purePhone'],
                'phone' => $params['countryCode'].$params['purePhone'],
                'api_token' => StrHelper::createToken(),
                'uuid' => StrHelper::createUuid(),
                'last_login_at' => date('Y-m-d H:i:s'),
                'password' => StrHelper::createPassword($params['password']),
            ];
            $uid = FresnsUsers::insertGetId($input);
            FresnsSubPluginService::addSubTablePluginItem(FresnsUsersConfig::CFG_TABLE, $uid);
            $memberInput = [
                'user_id' => $uid,
                'name' => StrHelper::createToken(rand(6, 8)),
                'nickname' => $params['nickname'],
                'uuid' => ApiCommonHelper::createMemberUuid(),
            ];
            $mid = FresnsMembers::insertGetId($memberInput);
            FresnsSubPluginService::addSubTablePluginItem(FresnsMembersConfig::CFG_TABLE, $mid);
            //成员总数
            $userCounts = ApiConfigHelper::getConfigByItemKey('user_counts');
            if ($userCounts === null) {
                $input = [
                    'item_key' => 'user_counts',
                    'item_value' => 1,
                    'item_tag' => 'stats',
                    'item_type' => 'number',
                ];
                FresnsConfigs::insert($input);
            } else {
                FresnsConfigs::where('item_key', 'user_counts')->update(['item_value' => $userCounts + 1]);
            }
            $memberCounts = ApiConfigHelper::getConfigByItemKey('member_counts');
            if ($memberCounts === null) {
                $input = [
                    'item_key' => 'member_counts',
                    'item_value' => 1,
                    'item_tag' => 'stats',
                    'item_type' => 'number',
                ];
                FresnsConfigs::insert($input);
            } else {
                FresnsConfigs::where('item_key', 'member_counts')->update(['item_value' => $memberCounts + 1]);
            }

            // Register successfully to add records to the table
            $memberStatsInput = [
                'member_id' => $mid,
            ];
            FresnsMemberStats::insert($memberStatsInput);
            $userWalletsInput = [
                'user_id' => $uid,
                'balance' => 0,
            ];
            FresnsUserWallets::insert($userWalletsInput);
            $defaultRoleId = ApiConfigHelper::getConfigByItemKey('default_role');
            $memberRoleRelsInput = [
                'member_id' => $mid,
                'role_id' => $defaultRoleId,
                'type' => 2,
            ];
            FresnsMemberRoleRels::insert($memberRoleRelsInput);

            return ['code' => '000000', 'message' => 'success'];
        } catch (\Exception $e) {
            return ['code' => $e->getCode(), 'message' => '服务失败'];
        }
    }

    /**
     * @param $itemKey
     * @param string $itemValue
     * @param string $item_type
     * @param string $item_tag
     */
    public static function insertConfigs($itemKey, $itemValue = '',$item_type='string',$item_tag='backends')
    {
        try{
            $cond = ['item_key'   => $itemKey];
            $upInfo = ['item_value'   => $itemValue,'item_type'=>$item_type,'item_tag'=>$item_tag];
            DB::table('configs')->updateOrInsert($cond, $upInfo);
            return ['code' => '000000', 'message' => 'success'];
        } catch (\Exception $e) {
            return ['code' => $e->getCode(), 'message' => '服务失败'];
        }
    }
}
