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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class InstallService
{
    const INSTALL_EXTENSIONS = ['fileinfo'];
    const INSTALL_FUNCTIONS  = ['putenv', 'symlink', 'readlink', 'proc_open'];
    const INSTALL_TABLES = [
        'code_messages','configs','languages','session_keys','session_tokens','session_logs','verify_codes','users','user_connects','user_wallets','user_wallet_logs',
        'files','file_appends','file_logs','plugins','plugin_usages','plugin_badges','plugin_callbacks','members','member_stats','member_roles','member_role_rels', 'member_icons',
        'member_likes','member_follows','member_shields','emojis','stop_words','dialogs','dialog_messages','notifies','implants','seo','groups','posts','post_appends',
        'post_allows','post_members','post_logs','comments','comment_appends','comment_logs','extends','extend_linkeds','hashtags','hashtag_linkeds','domains','domain_links','mentions'
    ];

    /**
     * install  mode
     */
    public static function mode(){
        $path = request()->path();
        if(in_array($path,['install/index','install/step1','install/step2','install/step3','install/step4','install/step5'])){
            return true;
        }
        return false;
    }

    /**
     * check install order
     */
    public static function checkPermission(){
        $path = request()->path();
        switch ($path){
            case 'install/step1';
                if(Cache::get('install_index')){
                    return ['code'=>'000000'];
                }else{
                    return ['code'=>'200000','url'=>route('install.index')];
                }
                break;
            case 'install/step2';
                if(Cache::get('install_step1')){
                    return ['code'=>'000000'];
                }else{
                    return ['code'=>'200000','url'=>route('install.step1')];
                }
                break;
            case 'install/step3';
                if(Cache::get('install_step2')){
                    return ['code'=>'000000'];
                }else{
                    return ['code'=>'200000','url'=>route('install.step2')];
                }
                break;
            case 'install/step4';
                if(Cache::get('install_step3')){
                    return ['code'=>'000000'];
                }else{
                    return ['code'=>'200000','url'=>route('install.step3')];
                }
                break;
            case 'install/step5';
                if(Cache::get('install_step4')){
                    return ['code'=>'000000'];
                }else{
                    return ['code'=>'200000','url'=>route('install.step4')];
                }
                break;
            default ;
                    return ['code'=>'000000'];
                break;
        }
    }

    /**
     * check env
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
                        Cache::forget('install_step2');
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
                        return ['code' => '000000', 'message' => '检测失败','result'=>$html];
                    }
                    break;
                case 'folder':
                    $value = self::file_perms(base_path());
                    if ($value >= 755) {
                        $html = '<span class="badge bg-success rounded-pill">'.trans('install.step2CheckStatusSuccess').'</span>';
                        return ['code' => '000000', 'message' => '检测成功','result'=>$html];
                    } else {
                        Cache::forget('install_step2');
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
                        Cache::forget('install_step2');
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
                        Cache::forget('install_step2');
                        $disabled = implode('&nbsp;&nbsp;', $value);
                        $html = '<span><small class="text-muted">'.trans('install.step2StatusNotEnabled').': '.$disabled.'</small></span>';
                        $html .= '<span class="badge bg-danger rounded-pill">'.trans('install.step2CheckStatusFailure').'</span>';
                        return ['code' => '100000', 'message' => '检测失败','result'=>$html];
                    }
                    break;
                case 'mysql_version':
                    $versionObj  = DB::selectOne('select version()  as version;');
                    $value = $versionObj->version;
                    if ($value !== '' && version_compare($value, '5.7', '>=')) {
                        $html = '<span class="badge bg-success rounded-pill">'.trans('install.step2CheckStatusSuccess').'</span>';
                        return ['code' => '000000', 'message' => '检测成功','result'=>$html];
                    } else {
                        Cache::forget('install_step3');
                        $html = '<span class="badge bg-danger rounded-pill">'.trans('install.step2CheckStatusFailure').'</span>';
                        return ['code' => '100000', 'message' => '检测失败','result'=>$html];
                    }
                    break;
                case 'mysql_db':
                    // execute migrate
                    Artisan::call('migrate');
                    // get count tables
                    $db_tables = DB::select('show tables');
                    $db_tables_count = sizeof($db_tables);
                    $sys_tables_count = sizeof(self::INSTALL_TABLES);
                    $value = $db_tables_count >= $sys_tables_count;
                    if ($value) {
                        $html = '<span class="badge bg-success rounded-pill">'.trans('install.step2CheckStatusSuccess').'</span>';
                        return ['code' => '000000', 'message' => '检测成功','result'=>$html];
                    } else {
                        Cache::forget('install_step3');
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
     * init manager user
     */
    public static function registerUser($params=[]){
        try{
            $input = [
                'email' => $params['email'] ?: null,
                'country_code' => $params['purePhone'] ? $params['countryCode'] : null,
                'pure_phone' => $params['purePhone'] ?: null,
                'phone' => $params['purePhone'] ? $params['countryCode'].$params['purePhone'] : null,
                'api_token' => StrHelper::createToken(),
                'uuid' => StrHelper::createUuid(),
                'last_login_at' => date('Y-m-d H:i:s'),
                'user_type' => 1,
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
            $memberStatsInput = ['member_id' => $mid];
            FresnsMemberStats::insert($memberStatsInput);

            $userWalletsInput = ['user_id' => $uid, 'balance' => 0];
            FresnsUserWallets::insert($userWalletsInput);

            $memberRoleRelsInput = ['member_id' => $mid, 'role_id' => 1, 'type' => 2];
            FresnsMemberRoleRels::insert($memberRoleRelsInput);

            return ['code' => '000000', 'message' => 'success'];
        } catch (\Exception $e) {
            return ['code' => $e->getCode(), 'message' => $e->getMessage()];
        }
    }

    /**
     * init config
     */
    public static function updateOrInsertConfig($itemKey, $itemValue = '',$item_type='string',$item_tag='backends')
    {
        try{
            $cond = ['item_key'   => $itemKey];
            $upInfo = ['item_value'   => $itemValue,'item_type'=>$item_type,'item_tag'=>$item_tag];
            DB::table('configs')->updateOrInsert($cond, $upInfo);
            return ['code' => '000000', 'message' => 'success'];
        } catch (\Exception $e) {
            return ['code' => $e->getCode(), 'message' => $e->getMessage()];
        }
    }

    /**
     * permission
     */
    public static function file_perms($file)
    {
        clearstatcache();
        if(!file_exists($file)) return false;
        $perms = fileperms($file);
        return substr(decoct($perms), -3);
    }
}
