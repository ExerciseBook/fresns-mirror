<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Base;

use App\Base\Controllers\BaseApiController;
use App\Helpers\SignHelper;
use App\Helpers\StrHelper;
use App\Http\Center\Helper\PluginRpcHelper;
use App\Http\Fresns\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\Fresns\FresnsCmds\FresnsPlugin;
use App\Http\Fresns\FresnsCmds\FresnsPluginConfig;
use App\Http\Share\Common\LogService;
use App\Http\Fresns\FresnsConfigs\FresnsConfigs;
use App\Http\Fresns\FresnsMemberRoleRels\FresnsMemberRoleRels;
use App\Http\Fresns\FresnsMemberRoleRels\FresnsMemberRoleRelsService;
use App\Http\Fresns\FresnsMemberRoles\FresnsMemberRoles;
use App\Http\Fresns\FresnsMemberRoles\FresnsMemberRolesService;
use App\Http\Fresns\FresnsMembers\FresnsMembers;
use App\Http\Fresns\FresnsSessionKeys\FresnsSessionKeys;
use App\Http\Fresns\FresnsSessionTokens\FresnsSessionTokensConfig;
use App\Http\Fresns\FresnsUsers\FresnsUsers;
use App\Http\Fresns\FresnsUsers\FresnsUsersConfig;
use App\Http\Share\Common\ErrorCodeService;
use App\Http\Share\Common\ValidateService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class FresnsBaseApiController extends BaseApiController
{
    public $appId;

    public $langTag;

    public $sign;

    public $platform;
    public $version;
    public $versionInt;
    public $uid;
    public $mid;
    public $token;

    // 浏览模式默认私有
    public $viewMode = AmConfig::VIEW_MODE_PRIVATE;

    public $checkHeader = true;

    //是否开启签名验证: true 开启，false 关闭
    public $checkSign = false;

    public function __construct()
    {
        $this->checkRequest();
        $this->initData();

    }

    public function initData()
    {
        // header 数据初始化, 参数

        $this->mid = request()->header("mid");
        $this->uid = request()->header("uid");
        $this->langTag = request()->header("langTag");
        $this->platform = request()->header("platform");

    }

    public function checkRequest()
    {

        $uri = Request::getRequestUri();

        if ($this->checkHeader) {
            $this->checkHeaderParams();
        }

        if ($this->checkSign) {
            $this->checkSign();
        }

        $this->checkAccessPerm();

        $this->checkPagination();

        return true;
    }

    public function checkAccessPerm()
    {
        $uri = Request::getRequestUri();
        //是否私有化
        $siteMode = FresnsConfigs::where('item_key', 'site_mode')->value('item_value');
        $uid = request()->header('uid');
        $mid = request()->header('mid');
        $token = request()->header('token');
        $deviceInfo = request()->header('deviceInfo');
        $platform = request()->header('platform');
        if ($siteMode == 'public') {
            if (empty($uid)) {
                if (in_array($uri, AmConfig::PUBLIC_UID_URI_ARR)) {
                    $info = [
                        'missing header' => 'uid',
                    ];
                    $this->error(ErrorCodeService::HEADER_ERROR, $info);

                }
            }

            if (empty($mid)) {
                if (in_array($uri, AmConfig::PUBLIC_MID_URI_ARR)) {
                    $info = [
                        'missing header' => 'mid',
                    ];
                    $this->error(ErrorCodeService::HEADER_ERROR, $info);

                }
            }
            if (empty($token)) {
                if (in_array($uri, AmConfig::PUBLIC_TOKEN_URI_ARR)) {
                    $info = [
                        'missing header' => 'token',
                    ];

                    $this->error(ErrorCodeService::HEADER_ERROR, $info);

                }
            }
            if (empty($deviceInfo)) {
                if (in_array($uri, AmConfig::PUBLIC_DEVICEINFO_URI_ARR)) {
                    $info = [
                        'missing header' => 'deviceInfo',
                    ];
                    $this->error(ErrorCodeService::HEADER_ERROR, $info);

                }
            }

        } else {

            if (empty($uid)) {
                if (in_array($uri, AmConfig::PRIVATE_UID_URI_ARR)) {
                    $info = [
                        'missing header' => 'uid',
                    ];

                    $this->error(ErrorCodeService::HEADER_ERROR, $info);

                }
            }

            if (empty($mid)) {
                if (in_array($uri, AmConfig::PRIVATE_MID_URI_ARR)) {
                    $info = [
                        'missing header' => 'mid',
                    ];

                    $this->error(ErrorCodeService::HEADER_ERROR, $info);

                }
            }
            if (empty($token)) {
                if (in_array($uri, AmConfig::PRIVATE_TOKEN_URI_ARR)) {
                    $info = [
                        'missing header' => 'token',
                    ];

                    $this->error(ErrorCodeService::HEADER_ERROR, $info);

                }
            }
            if (empty($deviceInfo)) {
                if (in_array($uri, AmConfig::PRIVATE_DEVICEINFO_URI_ARR)) {
                    $info = [
                        'missing header' => 'deviceInfo',
                    ];

                    $this->error(ErrorCodeService::HEADER_ERROR, $info);

                }
            }
        }

        if ($deviceInfo) {
            //校验是否是json
            $isJson = StrHelper::isJson($deviceInfo);
            if ($isJson === false) {
                $info = [
                    'deviceInfo' => '请输入json类型'
                ];
                $this->error(ErrorCodeService::HEADER_TYPE_ERROR, $info);

            }
        }

        $time = date('Y-m-d H:i:s', time());
        //如果uid不为空则token必传，如果mid不为空，则三个参数都必传
        if (empty($mid)) {
            if (!empty($uid)) {
                if (empty($token)) {
                    $info = [
                        'missing header' => 'token',
                    ];

                    $this->error(ErrorCodeService::HEADER_ERROR, $info);

                }
                if (in_array($uri, AmConfig::CHECK_USER_DELETE_URI)) {
                    $user = DB::table(FresnsUsersConfig::CFG_TABLE)->where('uuid', $uid)->first();
                } else {
                    $user = FresnsUsers::where('uuid', $uid)->first();
                }

                if (empty($user)) {
                    $info = [
                        'null user' => 'uid',
                    ];
                    $this->error(ErrorCodeService::UID_EXIST_ERROR, $info);
                }
                //校验是否存在deleted
                if (!empty($user->phone)) {
                    $str = strstr($user->phone, 'deleted');
                    if ($str != false) {
                        $info = [
                            'null user' => 'uid',
                        ];
                        $this->error(ErrorCodeService::NO_RECORD, $info);
                    }
                }
                if (!empty($user->email)) {
                    $str = strstr($user->phone, 'deleted');
                    if ($str != false) {
                        $info = [
                            'null user' => 'uid',
                        ];
                        $this->error(ErrorCodeService::NO_RECORD, $info);
                    }
                }
                if ($user->is_enable == 0) {
                    if (!in_array($uri, AmConfig::CHECK_USER_IS_ENABLE_URI)) {
                        $this->error(ErrorCodeService::HEADER_IS_ENABLE_ERROR);
                    }
                }
                $userId = $user->id;

                //校验token
                $cmd = FresnsPluginConfig::PLG_CMD_VERIFY_SESSION_TOKEN;
                $input = [];
                $input['uid'] = request()->header('uid');
                $input['platform'] = request()->header('platform');
                $input['token'] = $token;
                $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);
                if (PluginRpcHelper::isErrorPluginResp($resp)) {
                    $this->errorCheckInfo($resp);
                }
            }
        } else {
            if (empty($uid) || empty($mid) || empty($token)) {
                $info = [
                    'missing header' => 'uid或mid或token',
                ];

                $this->error(ErrorCodeService::CODE_PARAM_ERROR, $info);

                $this->error(ErrorCodeService::HEADER_ERROR, $info);

            }
            //校验mid是否属于uid
            if (in_array($uri, AmConfig::CHECK_USER_DELETE_URI)) {
                $user = DB::table(FresnsUsersConfig::CFG_TABLE)->where('uuid', $uid)->first();
            } else {
                $user = FresnsUsers::where('uuid', $uid)->first();
            }
            if (empty($user)) {
                $info = [
                    'null user' => 'uid',
                ];
                $this->error(ErrorCodeService::UID_EXIST_ERROR, $info);
            }
            //校验是否存在deleted
            if (!empty($user->phone)) {
                $str = strstr($user->phone, 'deleted');
                if ($str != false) {
                    $info = [
                        'null user' => 'uid',
                    ];
                    $this->error(ErrorCodeService::NO_RECORD, $info);
                }
            }
            if (!empty($user->email)) {
                $str = strstr($user->phone, 'deleted');
                if ($str != false) {
                    $info = [
                        'null user' => 'uid',
                    ];
                    $this->error(ErrorCodeService::NO_RECORD, $info);
                }
            }

            if ($user->is_enable == 0) {
                if (!in_array($uri, AmConfig::CHECK_USER_IS_ENABLE_URI)) {
                    $this->error(ErrorCodeService::HEADER_IS_ENABLE_ERROR);
                }
            }

            $userId = $user->id;
            $member = FresnsMembers::where('uuid', $mid)->first();

            if (empty($member)) {
                $info = [
                    'null member' => 'mid',
                ];
                $this->error(ErrorCodeService::HEADER_EXSIT_MEMBER, $info);
            }
            if ($member['is_enable'] == 0) {
                $this->error(ErrorCodeService::HEADER_IS_ENABLE_ERROR);

            }
            $memberId = $member['id'];

            $count = FresnsMembers::where('user_id', $userId)->where('id', $memberId)->count();
            if ($count == 0) {
                $this->error(ErrorCodeService::CODE_FAIL);
            }

            //校验token
            $cmd = FresnsPluginConfig::PLG_CMD_VERIFY_SESSION_TOKEN;
            $input = [];
            $input['uid'] = request()->header('uid');
            $input['platform'] = request()->header('platform');
            $input['mid'] = request()->header('mid');
            $input['token'] = $token;

            $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);

            if (PluginRpcHelper::isErrorPluginResp($resp)) {
                $this->errorCheckInfo($resp);
            }
            //查询角色权限
            if (in_array($uri, AmConfig::NOTICE_CONTENT_URI)) {
                //成员主角色权限 member_roles > permission > content_view 是否允许浏览，如果禁止浏览，不可请求「内容类」和「消息类」接口；
                //如果角色有过期时间，并且已经过期，则以继承角色权限为主；
                //如果无继承角色，则以配置表 default_role 键名键值的角色权限为准；如果配置表键值为空，则当无权处理。
                $roleId = FresnsMemberRoleRelsService::getMemberRoleRels($memberId);

                if (empty($roleId)) {
                    $this->error(ErrorCodeService::USERS_NOT_AUTHORITY_ERROR);
                }

                $memberRole = FresnsMemberRoles::where('id', $roleId)->first();
                if (!empty($memberRole)) {
                    $permission = $memberRole['permission'];
                    $permissionArr = json_decode($permission, true);
                    if (!empty($permissionArr)) {
                        $permissionMap = FresnsMemberRolesService::getPermissionMap($permissionArr);
                        if (empty($permissionMap)) {
                            $this->error(ErrorCodeService::USERS_NOT_AUTHORITY_ERROR);
                        }
                        if ($permissionMap['content_view'] == false) {
                            $this->error(ErrorCodeService::USERS_NOT_AUTHORITY_ERROR);

                        }

                    }
                }
            }

        }

        return true;
    }

    public function checkPagination()
    {
        $request = request();
        $rule = [
            'pageSize' => "numeric",
            'page' => "numeric",
        ];
        ValidateService::validateRule($request, $rule);
    }

    public function checkHeaderParams()
    {
        if ($this->viewMode == AmConfig::VIEW_MODE_PRIVATE) {
            return $this->checkPrivateModeHeaders();
        } else {
            return $this->checkPublicModeHeaders();
        }

        return true;
    }

    // 公开模式 header 校验
    public function checkPublicModeHeaders()
    {

        return true;
    }

    // 私有模式 header 校验
    public function checkPrivateModeHeaders()
    {
        $headerFieldArr = AmConfig::HEADER_FIELD_ARR;
        foreach ($headerFieldArr as $headerField) {
            $headerContent = request()->header($headerField);
            if (empty($headerContent)) {
                $info = [
                    'missing header' => $headerField,
                ];

                $this->error(ErrorCodeService::HEADER_ERROR, $info);

            }
        }

        return true;
    }

    // 验证签名
    // 第一步，设所有发送或者接收到的数据为集合M，将集合M内非空参数值的参数按照参数名ASCII码从小到大排序（字典序），使用URL键值对的格式（即key1=value1&key2=value2…）拼接成字符串stringA。
    // 第二步，在stringA最后拼接上key得到stringSignTemp字符串，并对stringSignTemp进行MD5运算，再将得到的字符串所有字符转换为大写，得到sign值signValue。
    public function checkSign()
    {
        $appId = request()->header('appId');
        $platform = request()->header('platform');
        $versionInt = request()->header('versionInt');
        if (!is_numeric($platform)) {
            $info = [
                'platform' => '请输入整数'
            ];
            $this->error(ErrorCodeService::HEADER_TYPE_ERROR, $info);

        }
        if (!is_numeric($versionInt)) {
            $info = [
                'versionInt' => '请输入整数'
            ];
            $this->error(ErrorCodeService::HEADER_TYPE_ERROR, $info);


        }

        //1、验证 appId 和 platform 参数
        //1.1、是否存在 session_keys > app_id
        //1.2、是否匹配 platsession_keys > platform_id
        //1.3、是否启用 session_keys > is_enable
        $sessionKeys = FresnsSessionKeys::where('app_id', $appId)->first();
        if (empty($sessionKeys)) {
            $info = [
                'appId' => '无此记录',
            ];

            $this->error(ErrorCodeService::NO_RECORD, $info);
        }
        if ($sessionKeys['platform_id'] != $platform) {
            $info = [
                'platform' => '无此记录',
            ];

            $this->error(ErrorCodeService::NO_RECORD, $info);
        }
        if ($sessionKeys['is_enable'] == 0) {

            $this->error(ErrorCodeService::HEADER_IS_ENABLE_ERROR);
        }
        if ($sessionKeys['type'] == 2) {
            $this->error(ErrorCodeService::USERS_NOT_AUTHORITY_ERROR);

        }
        $signKey = $sessionKeys['app_secret'];
        $dataMap = [];
        foreach (AmConfig::SIGN_FIELD_ARR as $signField) {
            $signFieldValue = request()->header($signField);
            if (!empty($signFieldValue)) {
                $dataMap[$signField] = $signFieldValue;
            }
        }

        $dataMap['sign'] = request()->header('sign');
        LogService::info("验签信息: ", $dataMap);

        $cmd = FresnsPluginConfig::PLG_CMD_VERIFY_SIGN;
        
        $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $dataMap);
        if (PluginRpcHelper::isErrorPluginResp($resp)) {
            $this->errorCheckInfo($resp,[],$resp['output']);
        }

        return true;
    }


}
