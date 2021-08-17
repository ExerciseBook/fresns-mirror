<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsPanel;

use App\Base\Controllers\BaseFrontendController;
use App\Helpers\HttpHelper;
use App\Http\Center\Base\PluginConst;
use App\Http\Share\Common\LogService;
use Illuminate\Http\Request;
use App\Helpers\StrHelper;
use App\Http\Fresns\FresnsPanel\Resource\PluginResource;
use App\Http\Fresns\FresnsApi\Helpers\ApiCommonHelper;
use App\Http\Fresns\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\Fresns\FresnsComments\FresnsComments;
use App\Http\Fresns\FresnsGroups\FresnsGroups;
use App\Http\Fresns\FresnsHashtags\FresnsHashtags;
use App\Http\Fresns\FresnsPosts\FresnsPosts;

use Illuminate\Support\Facades\Auth;
use App\Http\Fresns\FresnsPanel\Resource\KeysResource;
use App\Http\Fresns\FresnsSessionKeys\FresnsSessionKeys;
use App\Http\Fresns\FresnsSessionKeys\FresnsSessionKeysService;
use App\Http\Center\Helper\InstallHelper;
use App\Http\Center\Helper\PluginHelper;
use App\Http\Fresns\FresnsConfigs\FresnsConfigs;
use App\Http\Fresns\FresnsMembers\FresnsMembers;
use App\Http\Fresns\FresnsPlugin\FresnsPlugin;
use App\Http\Fresns\FresnsPlugin\FresnsPluginService as FresnsPluginFresnsPluginService;
use App\Http\Fresns\FresnsUsers\FresnsUsers;
use App\Http\Share\Common\ErrorCodeService;
use App\Http\Auth\User;
use App\Http\Fresns\FresnsConfigs\FresnsConfigsConfig;
use App\Http\Fresns\FresnsSessionLogs\FresnsSessionLogs;
use App\Http\Fresns\FresnsSessionLogs\FresnsSessionLogsConfig;
use App\Http\Fresns\FresnsSessionLogs\FresnsSessionLogsService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
class AmControllerWeb extends BaseFrontendController
{

    public function __construct()
    {
        $fresnsVersion = ApiConfigHelper::getConfigByItemKey('fresns_version');

        View::share("version", $fresnsVersion ?? '');

        request()->offsetSet("is_control_api", 1);
    }

    public function index()
    {
        $lang = request()->input('lang','zh-Hans');
        $data = [
            'lang' => $lang,
            'location' => 'action',
            'choose' => 'index',
            'title' => 'Home',
        ];

        // dd($data);
        return $this->display('index', $data);
    }

    public function loginIndex()
    {
        $lang = request()->input('lang','zh-Hans');
        $data = [
            'lang' => $lang,
        ];

        return $this->display('login', $data);

    }

    public function loginAcc(Request $request)
    {
        $account = $request->input('account');
        $password = $request->input('password');
        

        $user = FresnsUsers::where('is_enable', 1)->where('user_type', AmConfig::USER_TYPE_ADMIN)->where('phone', $account)->first();
        if (empty($user)) {
            $user = FresnsUsers::where('is_enable', 1)->where('user_type', AmConfig::USER_TYPE_ADMIN)->where('email', $account)->first();
        }

        if (empty($user)) {
            $this->error(ErrorCodeService::CODE_LOGIN_ERROR);

        }

        $password = base64_decode($password,true);
        $credentials = [
            'login_name' => $account,
            'password' => $password,
        ];

        $result = $this->attemptLogin($credentials);
        if ($result == false) {
            $this->error(ErrorCodeService::CODE_LOGIN_ERROR);
        }

        $user = User::find($user['id']);
        // dd($user);
        Auth::login($user);

        $lang = $request->input('lang','zh-Hans');
        if(empty($lang)){
            $lang = 'zh-Hans';
        }
        Cache::forever('lang_tag_' . $user['id'],$lang);

        App::setLocale($lang);


        return redirect('/fresns/dashboard');
    }

    public function checkLogin(Request $request)
    {
        $account = $request->input('account');
        $password = $request->input('password');

        $password = base64_decode($password,true);

        $user = FresnsUsers::where('is_enable', 1)->where('user_type', AmConfig::USER_TYPE_ADMIN)->where('phone', $account)->first();
        // dd($user);
        if (empty($user)) {
            $user = FresnsUsers::where('is_enable', 1)->where('user_type', AmConfig::USER_TYPE_ADMIN)->where('email', $account)->first();
        }

        if (empty($user)) {
            $this->error(ErrorCodeService::CODE_LOGIN_ERROR);
        }
        
        $sessionLogId = FresnsSessionLogsService::addConsoleSessionLogs(3,'控制台登录校验',$user->id);

        if($sessionLogId){
            $sessionInput = [
                'object_order_id' => $user->id,
                'user_id' => $user->id,
            ];
            FresnsSessionLogs::where('id',$sessionLogId)->update($sessionInput);
        }

        //查询该邮箱或手机号所属用户，近 1 小时内登录密码错误次数，达到 5 次，则限制登录。
        //session_logs 3-登陆 情况
        $startTime = date('Y-m-d H:i:s',strtotime("-1 hour"));
        $sessionCount = FresnsSessionLogs::where('created_at','>=',$startTime)
        ->where('user_id',$user->id)
        ->where('object_result',FresnsSessionLogsConfig::OBJECT_RESULT_ERROR)
        ->where('object_type',FresnsSessionLogsConfig::OBJECT_TYPE_USER_LOGIN)
        ->count();

        if($sessionCount >= 5){
            FresnsSessionLogsService::updateSessionLogs($sessionLogId,1);
            $this->error(ErrorCodeService::LOGIN_ERROR);
        }

        $credentials = [
            'login_name' => $account,
            'password' => $password,
        ];
        
        $result = $this->attemptLogin($credentials);
        // dd($result);
        if ($result == false) {
            FresnsSessionLogsService::updateSessionLogs($sessionLogId,1);
            $this->error(ErrorCodeService::CODE_LOGIN_ERROR);
        }

        FresnsSessionLogsService::updateSessionLogs($sessionLogId,2);

        return $this->success();
    }

    // 退出
    public function login_out(Request $request)
    {
        $userId = Auth::id();

        Auth::logout();
        $request->session()->flush();
        $adminPath = ApiConfigHelper::getConfigByItemKey(FresnsConfigsConfig::BACKEND_PATH) ?? 'admin';
        $lang = Cache::get('lang_tag_' . $userId);

        $adminPath = "/fresns"."/$adminPath" . "?lang=$lang";
        return redirect("$adminPath");
    }

    //设置多语言
    public function setLanguage(Request $request)
    {
        $lang = $request->input('lang','zh-Hans');
        $userId = Auth::id();

        Cache::forever('lang_tag_' . $userId,$lang);

        $this->success();
    }

    public function settings()
    {
        $userArr = FresnsUsers::where('is_enable', 1)->where('user_type', AmConfig::USER_TYPE_ADMIN)->get([
            'id',
            'uuid',
            'phone',
            'email',
            'country_code',
            'pure_phone'
        ])->toArray();
        foreach ($userArr as &$v) {
            $v['phone_desc'] = 'null';
            $v['email_desc'] = 'null';
            if (!empty($v['pure_phone'])) {
                $v['phone_desc'] = '+'.$v['country_code'].ApiCommonHelper::encryptPhone($v['pure_phone']);
            }
            if (!empty($v['email'])) {
                $v['email_desc'] = ApiCommonHelper::encryptPhone($v['email']);
            }
        }

        $backend_url = ApiConfigHelper::getConfigByItemKey(FresnsConfigsConfig::BACKEND_DOMAIN);

        $admin_path = ApiConfigHelper::getConfigByItemKey(FresnsConfigsConfig::BACKEND_PATH) ?? 'admin';
        $site_url = ApiConfigHelper::getConfigByItemKey(FresnsConfigsConfig::SITE_DOMAIN);
        $path = '';
        if ($backend_url) {
            $path = $backend_url."/fresns"."/$admin_path";
        }

        $userId = Auth::id();
        $lang = Cache::get('lang_tag_' . $userId);
        
        App::setLocale($lang);

        $data = [
            'lang' => $lang,
            'choose' => 'settings',
            'location' => 'settings',
            'title' => 'Settings',
            'user_arr' => $userArr,
            'backend_url' => $backend_url,
            'admin_path' => $admin_path,
            'site_url' => $site_url,
            'path' => $path,
            'lang_desc' => AmService::getLanguage($lang),
        ];

        return $this->display('settings', $data);
    }

    public function updateSetting(Request $request)
    {

        $backend_url = $request->input('backend_url');
        $backend_url_end = substr($backend_url, -1);
        if ($backend_url_end == '/') {
            $backend_url = substr($backend_url, 0, -1);
        }

        $admin_path = $request->input('admin_path');

        $pathNot = AmConfig::BACKEND_PATH_NOT;
        if(in_array($admin_path,$pathNot)){
            $this->error(ErrorCodeService::BACKEND_PATH_ERROR);
        }
        $site_url = $request->input('site_url');
        $site_url_end = substr($site_url, -1);
        if ($site_url_end == '/') {
            $site_url = substr($site_url, 0, -1);
        }
        $backend_url_config = FresnsConfigs::where('item_key', FresnsConfigsConfig::BACKEND_DOMAIN)->first();
        if ($backend_url_config) {
            FresnsConfigs::where('item_key', FresnsConfigsConfig::BACKEND_DOMAIN)->update(['item_value' => $backend_url]);
        } else {
            $input = [
                'item_key' => FresnsConfigsConfig::BACKEND_DOMAIN,
                'item_tag' => 'backends',
                'item_value' => $backend_url,
                'item_type' => 'string',
            ];

            FresnsConfigs::insert($input);
        }
        $admin_path_config = FresnsConfigs::where('item_key', FresnsConfigsConfig::BACKEND_PATH)->first();
        if ($admin_path_config) {
            FresnsConfigs::where('item_key', FresnsConfigsConfig::BACKEND_PATH)->update(['item_value' => $admin_path]);
        } else {
            $input = [
                'item_key' => FresnsConfigsConfig::BACKEND_PATH,
                'item_tag' => 'backends',
                'item_value' => $admin_path,
                'item_type' => 'string',

            ];
            FresnsConfigs::insert($input);
        }
        $site_url_config = FresnsConfigs::where('item_key', FresnsConfigsConfig::SITE_DOMAIN)->first();
        if ($site_url_config) {
            FresnsConfigs::where('item_key', FresnsConfigsConfig::SITE_DOMAIN)->update(['item_value' => $site_url]);
        } else {
            $input = [
                'item_key' => FresnsConfigsConfig::SITE_DOMAIN,
                'item_tag' => 'sites',
                'item_value' => $site_url,
                'item_type' => 'string',
            ];
            FresnsConfigs::insert($input);
        }

        return $this->success();
    }

    public function addAdmin(Request $request)
    {
        // 校验参数
        // $rule = [
        //     'account' => 'required',
        // ];
        // ValidateService::validateRule($request, $rule);
        $account = $request->input('account');
        if(empty($account)){
            $this->error(ErrorCodeService::ACCOUNT_ERROR);
        }
        
        $user = FresnsUsers::where('is_enable', 1)->where('user_type', '!=',AmConfig::USER_TYPE_ADMIN)->where(function ($query) {
            $account = request()->input('account');
            $query->where('phone', $account)->orWhere('email', $account);
        })->first();

        if (empty($user)) {
            $this->error(ErrorCodeService::ADMIN_ACCOUNT_ERROR);
        }

        FresnsUsers::where('id', $user['id'])->update(['user_type' => AmConfig::USER_TYPE_ADMIN]);

        $this->success();
    }

    public function delAdmin(Request $request)
    {
        $uuid = $request->input('uuid');
        $user = Auth::user();
        if($uuid == $user['uuid']){
            $this->error(ErrorCodeService::DELETE_ADMIN);
        }
        FresnsUsers::where('uuid', $uuid)->update(['user_type' => AmConfig::USER_TYPE_USER]);

        $this->success();
    }

    public function admins(Request $request)
    {
        $current = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 50);
        $FresnsPluginService = new FresnsPluginFresnsPluginService();
        $request->offsetSet('type', AmConfig::PLUGIN_TYPE4);
        $request->offsetSet('currentPage', $current);
        $request->offsetSet('pageSize', $pageSize);
        $pluginList = $FresnsPluginService->searchData();
        $pluginArr = PluginResource::collection($pluginList['list'])->toArray($pluginList['list']);

        $userId = Auth::id();
        $lang = Cache::get('lang_tag_' . $userId);

        App::setLocale($lang);
        // dd($pluginList);
        // 类型为控制面板的插件
        // $pluginList = FresnsPlugin::where('type',AmConfig::PLUGIN_TYPE4)->get();
        $data = [
            'lang' => $lang,
            'choose' => 'admins',
            'location' => $pluginArr,
            'title' => 'Admins',
            'lang_desc' => AmService::getLanguage($lang),

        ];

        return $this->display('admins', $data);
    }

    public function apps(Request $request)
    {
        // 类型为控制面板的插件
        $current = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 50);
        $FresnsPluginService = new FresnsPluginFresnsPluginService();
        $request->offsetSet('type', AmConfig::PLUGIN_TYPE3);
        $request->offsetSet('currentPage', $current);
        $request->offsetSet('pageSize', $pageSize);
        $pluginList = $FresnsPluginService->searchData();
        $pluginArr = PluginResource::collection($pluginList['list'])->toArray($pluginList['list']);
        // $pluginList = FresnsPlugin::where('type',AmConfig::PLUGIN_TYPE3)->get();
        // dd($pluginArr);

        $userId = Auth::id();
        $lang = Cache::get('lang_tag_' . $userId);

        App::setLocale($lang);
        $data = [
            'lang' => $lang,
            'choose' => 'apps',
            'location' => $pluginArr,
            'title' => 'Apps',
            'lang_desc' => AmService::getLanguage($lang),

        ];

        return $this->display('apps', $data);
    }

    public function dashboard(Request $request)
    {
        $userId = Auth::id();
        $langTag = Cache::get('lang_tag_' . $userId);

        // $plugins = app()->call('App\Http\Center\Market\RemoteController@index');
        // //插入plugin表
        // if($plugins){
        //     foreach($plugins as $plugin){
        //         // dd($plugin);
        //         $pluginCount = FresnsPlugin::where('unikey',$plugin['uniKey'])->where('type',AmConfig::PLUGINS_TYPE)->count();
        //         if($pluginCount == 0){
        //             $input = [
        //                 'unikey' => $plugin['uniKey'],
        //                 'name' => $plugin['name'],
        //                 'type' => AmConfig::PLUGINS_TYPE,
        //                 'description' => $plugin['description'],
        //                 'version' => $plugin['version'],
        //                 'version_int' => $plugin['versionInt'],
        //                 'is_enable' => AmConfig::ENABLE_FALSE
        //             ];
        //             (new FresnsPlugin())->store($input);
        //         }
        //     }
        // }
        $FresnsPluginService = new FresnsPluginFresnsPluginService();
        $request->offsetSet('type', AmConfig::PLUGINS_TYPE);
        $pluginList = $FresnsPluginService->searchData();
        $pluginArr = PluginResource::collection($pluginList['list'])->toArray($pluginList['list']);
        // dd($pluginArr);
        $newVision = [];
        if ($pluginArr) {
            foreach ($pluginArr as $key => $p) {
                if ($key == 5) {
                    break;
                }
                $arr = [];
                if ($p['isDownload'] == 1 && $p['isNewVision'] == 1) {
                    $arr = $p;
                    $newVision[] = $arr;
                }
            }
        }
        // dd($newVision);
        //账号总数
        $memberCount = FresnsMembers::count();
        //用户总数
        $userCount = FresnsUsers::count();
        //小组总数
        $groupCount = FresnsGroups::count();
        //话题总数
        $hashtagCount = FresnsHashtags::count();
        //帖子总数
        $postCount = FresnsPosts::count();
        //评论总数
        $commentCount = FresnsComments::count();
        //控制面板
        $plugin5 = FresnsPlugin::where('type', 5)->count();
        //网站主题
        $plugin4 = FresnsPlugin::where('type', 4)->count();
        //移动应用
        $plugin3 = FresnsPlugin::where('type', 3)->count();
        //扩展插件
        $plugin2 = FresnsPlugin::where('type', 2)->count();
        $plugin1 = FresnsPlugin::where('type', 1)->count();
        $keysCount = FresnsSessionKeys::count();


        $total['member_count'] = $memberCount;
        $total['user_count'] = $userCount;
        $total['group_count'] = $groupCount;
        $total['hashtag_count'] = $hashtagCount;
        $total['post_count'] = $postCount;
        $total['comment_count'] = $commentCount;
        $total['keys_count'] = $keysCount;
        $total['plugin_5'] = $plugin5;
        $total['plugin_4'] = $plugin4;
        $total['plugin_3'] = $plugin3;
        $total['plugin_2'] = $plugin2;
        $total['plugin_1'] = $plugin1;
        // dd($newVision);

        //动态获取通知
        $url = AmConfig::NOTICE_URL;

        $userId = Auth::id();

        App::setLocale($langTag);

        $json = HttpHelper::curlRequest($url);
        $noticeArr = [];
        if (!empty($json)) {
            $jsonArr = json_decode($json, true);
            if (!empty($jsonArr)) {
                foreach ($jsonArr as $v) {
                    if ($v['langTag'] == $langTag) {
                        $noticeArr[] = $v['content'];
                        break;
                    }
                }
            }
        }

        // dd($newVision);

        $data = [
            'lang' => $langTag,
            'location' => 'dashboard',
            'choose' => 'dashboard',
            'newVisionPlugin' => $newVision,
            'title' => 'Dashboard',
            'total' => $total,
            'notice_arr' => $noticeArr,
            'lang_desc' => AmService::getLanguage($langTag),

        ];

        return $this->display('dashboard', $data);
    }

    public function iframe(Request $request)
    {
        $url = $request->input('url');
        $userId = Auth::id();
        $lang = Cache::get('lang_tag_' . $userId);

        App::setLocale($lang);
        $data = [
            'lang' => $lang,
            'choose' => 'iframe',
            'location' => $url,
            'title' => 'Setting',
            'lang_desc' => AmService::getLanguage($lang),

        ];
        // dd($data);
        return $this->display('iframe', $data);
    }

    public function keys(Request $request)
    {
        $current = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 10);
        $request->offsetSet('currentPage', $current);
        $request->offsetSet('pageSize', $pageSize);
        $FresnsSessionKeysService = new FresnsSessionKeysService();
        $keyLists = $FresnsSessionKeysService->searchData();
        $pluginArr = KeysResource::collection($keyLists['list'])->toArray($keyLists['list']);
        // 获取密钥数据
        $clientData = FresnsSessionKeys::getByStaticWithCond()->toArray();
        $platforms = FresnsConfigs::where("item_key", "platforms")->first(["item_value"]);
        // // 平台配置数据
        $platforms = json_decode($platforms['item_value'], true);
        // // 插件数据
        $cond = [
            ['type','!=',5]
        ];
        $plugin = FresnsPlugin::getByStaticWithCond($cond)->toArray();
        // // dd($platforms);
        // // 平台编号名称
        // if($clientData){
        //     foreach($clientData as &$c){
        //         $c['platformName'] = "";
        //         foreach($platforms as $p){
        //             if($c['platform_id'] == $p['id']){
        //                 $c['platformName'] = $p['name'];
        //             }
        //         }
        //         $c['typeName'] = $c['type'] == 1 ? "主程API" : "插件API";
        //     }
        // }
        // dd($clientData);

        $userId = Auth::id();
        $lang = Cache::get('lang_tag_' . $userId);

        App::setLocale($lang);
        $data = [
            'lang' => $lang,
            'data' => $keyLists,
            'page' => $current,
            'location' => $pluginArr,
            'choose' => 'keys',
            'platform' => $platforms,
            'plugin' => $plugin,
            'title' => 'Keys',
            'lang_desc' => AmService::getLanguage($lang),

        ];
        // dd($data);
        return $this->display('keys', $data);
    }

    public function plugins(Request $request)
    {
        // $plugins = app()->call('App\Http\Center\Market\RemoteController@index');
        // dump($plugins);
        //插入plugin表
        // if($plugins){
        //     foreach($plugins as $plugin){
        //         // dd($plugin);
        //         $pluginCount = FresnsPlugin::where('unikey',$plugin['uniKey'])->where('type',AmConfig::PLUGINS_TYPE)->count();
        //         if($pluginCount == 0){
        //             $input = [
        //                 'unikey' => $plugin['uniKey'],
        //                 'name' => $plugin['name'],
        //                 'type' => AmConfig::PLUGINS_TYPE,
        //                 'description' => $plugin['description'],
        //                 'version' => $plugin['version'],
        //                 'version_int' => $plugin['versionInt'],
        //                 'is_enable' => AmConfig::ENABLE_FALSE
        //             ];
        //             (new FresnsPlugin())->store($input);
        //         }
        //     }
        // }
        $current = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 20);
        $FresnsPluginService = new FresnsPluginFresnsPluginService();
        $request->offsetSet('type', AmConfig::PLUGINS_TYPE);
        $request->offsetSet('currentPage', $current);
        $request->offsetSet('pageSize', $pageSize);
        $pluginList = $FresnsPluginService->searchData();
        // dd($pluginList);
        $enableCount = 0;
        $unEnableCount = 0;
        $pluginArr = PluginResource::collection($pluginList['list'])->toArray($pluginList['list']);
        // dd($pluginArr);
        foreach ($pluginArr as $p) {
            if ($p['is_enable'] == 0) {
                $unEnableCount++;
            }
            if ($p['is_enable'] == 1) {
                $enableCount++;
            }
        }
        // dd($pluginArr);
        //页面总数
        $pagination = $pluginList['pagination'];
        if ($pagination['total'] != 0) {
            $totalPage = (int)ceil($pagination['total'] / $pageSize);
        } else {
            $totalPage = 1;
        }

        $userId = Auth::id();
        $lang = Cache::get('lang_tag_' . $userId);

        App::setLocale($lang);
        // dd($totalPage);
        $data = [
            'lang' => $lang,
            'location' => $pluginArr,
            'unEnableCount' => $unEnableCount,
            'enableCount' => $enableCount,
            'data' => $pluginList,
            'page' => $current,
            'title' => 'Plugins',
            'choose' => 'plugins',
            'totalPage' => $totalPage,
            'lang_desc' => AmService::getLanguage($lang),

        ];
        // dd($data);
        return $this->display('plugins', $data);
    }

    public function websites(Request $request)
    {
        // 插件表类型为网站引擎
        $current = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 50);
        $FresnsPluginService = new FresnsPluginFresnsPluginService();
        $request->offsetSet('currentPage', $current);
        $request->offsetSet('pageSize', $pageSize);
        $pluginList = $FresnsPluginService->searchData();
        $pluginArr = PluginResource::collection($pluginList['list'])->toArray($pluginList['list']);
        $websitePluginArr = [];
        $subjectPluginArr = [];
        foreach ($pluginArr as $p) {
            if ($p['type'] == 1) {
                $websitePluginArr[] = $p;
            }
            if ($p['type'] == 5) {
                $subjectPluginArr[] = $p;
            }
        }

        $userId = Auth::id();
        $lang = Cache::get('lang_tag_' . $userId);

        App::setLocale($lang);
        // dump($websitePluginArr);
        // dd($subjectPluginArr);
        $data = [
            'lang' => $lang,
            'location' => 'index',
            'choose' => 'websites',
            'websitePluginArr' => $websitePluginArr,
            'subjectPluginArr' => $subjectPluginArr,
            'title' => 'Websites',
            'lang_desc' => AmService::getLanguage($lang),

        ];

        return $this->display('websites', $data);
    }

    // 重置密钥
    public function resetKey(Request $request)
    {
        $id = $request->input('data_id');
        $app_secret = strtolower(StrHelper::randString(32));
        FresnsSessionKeys::where('id', $id)->update(['app_secret' => $app_secret]);
        $this->success();
    }

    // 新增密钥
    public function submitKey(Request $request)
    {
        $platformId = $request->input('platformId');
        $keyName = $request->input('keyName');
        $type = $request->input('type');
        $plugin = $type == 2 ? $request->input('plugin') : null;
        $app_id = strtolower('tw'.StrHelper::randString(14));
        $app_secret = strtolower(StrHelper::randString(32));
        $enAbleStatus = $request->input('enAbleStatus');
        if(!$keyName){
            $this->error(ErrorCodeService::KEYS_NAME_ERROR);
        }
        if($platformId == '选择密钥应用平台'){
            $this->error(ErrorCodeService::KEYS_PLAT_ERROR);
        }
        if($type == 2){
            if(!$plugin || $plugin == "选择密钥用于哪个插件"){
                $this->error(ErrorCodeService::PLUGIN_PLAT_ERROR);
            }
        }
        $input = [
            'platform_id' => $platformId,
            'name' => $keyName,
            'type' => $type,
            'plugin_unikey' => $plugin,
            'app_id' => $app_id,
            'app_secret' => $app_secret,
            'is_enable' => $enAbleStatus,
        ];
        (new FresnsSessionKeys())->store($input);
        $this->success();
    }

    // 编辑密钥
    public function updateKey(Request $request)
    {
        $id = $request->input('id');
        $platformId = $request->input('platformId');
        $keyName = $request->input('keyName');
        $type = $request->input('type');
        $plugin = ($type == 2) ? $request->input('plugin') : null;
        $enAbleStatus = $request->input('enAbleStatus');
        if(!$keyName){
            $this->error(ErrorCodeService::KEYS_NAME_ERROR);
        }
        if($platformId == '选择密钥应用平台'){
            $this->error(ErrorCodeService::KEYS_PLAT_ERROR);
        }
        if($type == 2){
            if(!$plugin || $plugin == "选择密钥用于哪个插件"){
                $this->error(ErrorCodeService::PLUGIN_PLAT_ERROR);
            }
        }
        // dd($plugin);
        $input = [
            'platform_id' => $platformId,
            'name' => $keyName,
            'type' => $type,
            'plugin_unikey' => $plugin,
            'is_enable' => $enAbleStatus,
        ];
        FresnsSessionKeys::where('id', $id)->update($input);
        $this->success();
    }

    // 启用禁用
    public function enableStatus(Request $request)
    {
        $id = $request->input('data_id');
        $is_enable = $request->input('is_enable');
        FresnsSessionKeys::where('id', $id)->update(['is_enable' => $is_enable]);
        $this->success();
    }

    // 删除
    public function delKey(Request $request)
    {
        $id = $request->input('data_id');
        FresnsSessionKeys::where('id', $id)->delete();
        $this->success();
    }

    // 卸载插件
    public function uninstall(Request $request)
    {
        // 主要是让插件去判断是否清除数据的，目前只需要传到后端即可 1 删除数据 删除文件 0 代表不删除，保留数据仅卸载插件文件
        $clear_plugin_data = $request->input('clear_plugin_data');
        $uniKey = $request->input('unikey');
        // 插件配置
        $pluginConfig = PluginHelper::findPluginConfigClass($uniKey);
        // dd($pluginConfig);
        $type = $pluginConfig->type;
        if ($type == PluginConst::PLUGIN_TYPE_THEME) {
            // todo 
            $plugin = FresnsPlugin::where('unikey', $uniKey)->first();
            if (!$plugin) {
                $this->error(ErrorCodeService::PLUGIN_UNIKEY_ERROR);
            }
            if ($plugin['is_enable'] == 1) {
                $this->error(ErrorCodeService::PLUGIN_ENABLE_ERROR);
            }
            $info = PluginHelper::uninstallByUniKey($uniKey);
            // $info = $installer->uninstall();
            InstallHelper::freshSystem();
            // 删除插件数据
            // FresnsPlugin::where('unikey', $uniKey)->delete();
            DB::table('plugins')->where('unikey', $uniKey)->delete();
            $this->success($info);
        } else {
            // 获取安装类
            $installer = InstallHelper::findInstaller($uniKey);
            if (empty($installer)) {
                $this->error(ErrorCodeService::NO_RECORD);
            }
        }

        $plugin = FresnsPlugin::where('unikey', $uniKey)->first();
        if (!$plugin) {
            $this->error(ErrorCodeService::PLUGIN_UNIKEY_ERROR);
        }
        if ($plugin['is_enable'] == 1) {
            $this->error(ErrorCodeService::PLUGIN_ENABLE_ERROR);
        }
        $info = $installer->uninstall();
        InstallHelper::freshSystem();
        // 删除插件数据
        // FresnsPlugin::where('unikey', $uniKey)->delete();
        DB::table('plugins')->where('unikey', $uniKey)->delete();
        $this->success($info);
    }

    // 安装插件
    public function install(Request $request)
    {
        $unikey = $request->input('unikey');
        $pathArr = [
            base_path(),
            'public',
            'storage',
            'plugins',
            $unikey
        ];
        $downloadFileName = implode(DIRECTORY_SEPARATOR, $pathArr);
        if (!file_exists($downloadFileName)) {
            $this->error(ErrorCodeService::FILES_ERROR);
        }
        // 插件目录下的json文件
        $jsonArr = PluginHelper::getPluginJsonFileArrByDirName($unikey);
        if (empty($jsonArr)) {
            $this->error(ErrorCodeService::FILES_JSON_ERROR);
        }
        // dd($jsonArr);
        $options = [];
        $installFileInfo = InstallHelper::installLocalPluginFile($jsonArr['uniKey'], $unikey, $downloadFileName,
            $options);
        $info = [];
        $info['downloadFileName'] = $downloadFileName;
        $info['installFileInfo'] = $installFileInfo;
        // dd($info);
        // 2. 执行插件本身的安装函数
        $installer = InstallHelper::findInstaller($unikey);
        // dd($info);
        // 2. 执行插件本身的安装函数
        $installer = InstallHelper::findInstaller($unikey);
        // dd($installer);
        if (empty($installer)) {
            $this->error(ErrorCodeService::NO_RECORD);
        }

        $installInfo = $installer->install();
        $info['installInfo'] = $installInfo;

        // 3. 模版和前端文件的安装
        InstallHelper::pushPluginResourcesFiles($unikey);

        $this->success($info);
    }

    // 本地安装, 直接覆盖，无升级操作
    public function localInstall(Request $request)
    {
        $dirName = $request->input('dirName');
        if(empty($dirName)){
            $this->error(ErrorCodeService::FILES_EMPTY_ERROR);
        }
        // dd($dirName);
        $downloadFileName = InstallHelper::getPluginExtensionPath($dirName);
        if (!file_exists($downloadFileName)) {
            $this->error(ErrorCodeService::FILES_ERROR);
        }

        // todo 检查一下文件信息是否全

        // dd($jsonArr);
        $uniKey = $dirName;
        $options = [];
        $installFileInfo = InstallHelper::installLocalPluginFile($uniKey, $dirName, $downloadFileName, $options);
        // dd($installFileInfo);
        $info = [];
        $info['downloadFileName'] = $downloadFileName;
        $info['installFileInfo'] = $installFileInfo;
        // dd($info);

        // 1. 分发文件
        InstallHelper::pushPluginResourcesFiles($uniKey);

        // 插件配置
        $pluginConfig = PluginHelper::findPluginConfigClass($uniKey);
        $type = $pluginConfig->type;

        // 2. 执行插件本身的安装函数, 主题模版不需要执行该步骤
        if ($type != PluginConst::PLUGIN_TYPE_THEME) {
            $installer = InstallHelper::findInstaller($uniKey);
            if (empty($installer)) {
                $this->error(ErrorCodeService::NO_RECORD);
            }

            $installInfo = $installer->install();
            $info['installInfo'] = $installInfo;
        }

        LogService::info("install info : ", $info);

        // 插件入库

        // dd($type);
        $image = PluginHelper::getPluginImageUrl($pluginConfig);

        $scene = $pluginConfig->sceneArr;
        $input = [
            'unikey' => $uniKey,
            'type' => $type,
            'name' => $pluginConfig->name,
            'image' => $image,
            'description' => $pluginConfig->description,
            'version' => $pluginConfig->currVersion,
            'version_int' => $pluginConfig->currVersionInt,
            'scene' => empty($scene) ? null : json_encode($scene),
            'author' => $pluginConfig->author,
            'author_link' => $pluginConfig->authorLink,
            'plugin_domain' => $pluginConfig->pluginUrl,
            'access_path' => $pluginConfig->accessPath,
            'setting_path' => $pluginConfig->settingPath,
        ];
        $plugin = FresnsPlugin::where('unikey', $uniKey)->first();
        // dump($plugin);
        if (empty($plugin)) {
            // dump($input);
            $res = (new FresnsPlugin())->store($input);
            // dd($res);
        } else {
            FresnsPlugin::where('unikey', $uniKey)->update($input);
        }
        $this->success($info);
    }

    // 更新插件
    public function uploadPlugin(Request $request)
    {
        $dowmLoadUrl = "https://apps.fresns.cn/releases/fresns.zip";
    }

    // 插件启用禁用
    public function enableUnikeyStatus(Request $request)
    {
        $id = $request->input('data_id');
        $is_enable = $request->input('is_enable');
        FresnsPlugin::where('id', $id)->update(['is_enable' => $is_enable]);
        $this->success();
    }

    // 引擎关联主题模板
    public function websiteLinkSubject(Request $request)
    {
        $websiteUnikey = $request->input('websiteUnikey');
        $subjectUnikeyPc = $request->input('subjectUnikeyPc');
        $subjectUnikeyMobile = $request->input('subjectUnikeyMobile');
        if ($subjectUnikeyPc) {
            $websitePc = ApiConfigHelper::getConfigByItemKey($websiteUnikey.'_Pc');
            if ($websitePc) {
                FresnsConfigs::where('item_key', $websiteUnikey.'_Pc')->update(['item_value' => $subjectUnikeyPc]);
            } else {
                $input = [
                    'item_key' => $websiteUnikey.'_Pc',
                    'item_tag' => 'themes',
                    'item_value' => $subjectUnikeyPc,
                    'item_type' => 'plugin',

                ];
                FresnsConfigs::insert($input);
            }
        } else {
            FresnsConfigs::where('item_key', $websiteUnikey.'_Pc')->delete();
        }
        if ($subjectUnikeyMobile) {
            $websiteMobile = ApiConfigHelper::getConfigByItemKey($websiteUnikey.'_Mobile');
            if ($websiteMobile) {
                FresnsConfigs::where('item_key',
                    $websiteUnikey.'_Mobile')->update(['item_value' => $subjectUnikeyMobile]);
            } else {
                $input = [
                    'item_key' => $websiteUnikey.'_Mobile',
                    'item_tag' => 'themes',
                    'item_value' => $subjectUnikeyMobile,
                    'item_type' => 'plugin',
                ];
                FresnsConfigs::insert($input);
            }
        } else {
            FresnsConfigs::where('item_key', $websiteUnikey.'_Mobile')->delete();
        }
        $this->success();
    }
    
    public function getPostPage(Request $request){
        $current = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 10);
        $request->offsetSet('currentPage', $current);
        $request->offsetSet('pageSize', $pageSize);
        // request()->offsetSet('pageSize',5);

        $cmsPostService = new FresnsSessionKeysService();
        $data = $cmsPostService->searchData();
        $postArr = KeysResource::collection($data['list'])->toArray($data['list']);

        $post['list_arr'] = $postArr;
        $bladeData = $this->ajaxBlade("keys", $post);

        $data = [
            'bladeData' => $bladeData,
        ];

        $this->success($data);
    }
}
