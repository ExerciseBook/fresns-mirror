<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\User;

use App\Helpers\DateHelper;
use App\Helpers\StrHelper;
use App\Http\Center\Common\GlobalService;
use App\Http\Center\Common\ErrorCodeService;
use App\Http\Center\Common\ValidateService;
use App\Http\Center\Helper\PluginRpcHelper;
use App\Http\FresnsApi\Base\FresnsBaseApiController;
use App\Http\FresnsApi\Helpers\ApiCommonHelper;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsApi\Helpers\ApiLanguageHelper;
use App\Http\FresnsCmd\FresnsCmdService;
use App\Http\FresnsCmd\FresnsPlugin;
use App\Http\FresnsCmd\FresnsPluginConfig;
use App\Http\FresnsDb\FresnsConfigs\FresnsConfigs;
use App\Http\FresnsDb\FresnsMemberRoleRels\FresnsMemberRoleRels;
use App\Http\FresnsDb\FresnsMembers\FresnsMembers;
use App\Http\FresnsDb\FresnsMembers\FresnsMembersConfig;
use App\Http\FresnsDb\FresnsMemberStats\FresnsMemberStats;
use App\Http\FresnsDb\FresnsSessionLogs\FresnsSessionLogs;
use App\Http\FresnsDb\FresnsSessionLogs\FresnsSessionLogsConfig;
use App\Http\FresnsDb\FresnsSessionLogs\FresnsSessionLogsService;
use App\Http\FresnsDb\FresnsSessionTokens\FresnsSessionTokensConfig;
use App\Http\FresnsDb\FresnsUsers\FresnsUsers;
use App\Http\FresnsDb\FresnsUsers\FresnsUsersConfig;
use App\Http\FresnsDb\FresnsUserWalletLogs\FresnsUserWalletLogsService;
use App\Http\FresnsDb\FresnsUserWallets\FresnsUserWallets;
use App\Http\FresnsDb\FresnsVerifyCodes\FresnsVerifyCodes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FsControllerApi extends FresnsBaseApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->service = new FsService();
        $this->initData();
    }

    // User Register
    public function register(Request $request)
    {
        $rule = [
            'type' => 'required|numeric|in:1,2,3',
            'account' => 'required',
            'nickname' => 'required',
        ];
        // Verify Parameters
        $type = $request->input('type');
        switch ($type) {
            case 1:
                $rule = [
                    'type' => 'required|numeric|in:1,2,3',
                    'account' => 'required|email',
                    'nickname' => 'required',
                ];
                break;

            case 2:
                $rule = [
                    'type' => 'required|numeric|in:1,2,3',
                    'account' => 'required|numeric|regex:/^1[^0-2]\d{9}$/',
                    'nickname' => 'required',
                    'countryCode' => 'required|numeric',
                ];
                break;
        }
        ValidateService::validateRule($request, $rule);

        $account = $request->input('account');
        $countryCode = $request->input('countryCode');
        $verifyCode = $request->input('verifyCode');
        $password = $request->input('password');
        $nickname = $request->input('nickname');

        $langTag = ApiLanguageHelper::getLangTagByHeader();

        $siteMode = ApiConfigHelper::getConfigByItemKey('site_mode');
        if ($siteMode == 'private') {
            $this->error(ErrorCodeService::API_NO_CALL_ERROR);
        }

        $sitePublicClose = ApiConfigHelper::getConfigByItemKey('site_public_close');
        if ($sitePublicClose === false) {
            $this->error(ErrorCodeService::API_NO_CALL_ERROR);
        }
        $sitePublicService = ApiConfigHelper::getConfigByItemKey('site_public_service');
        if (! empty($sitePublicService)) {
            $this->error(ErrorCodeService::API_NO_CALL_ERROR);
        }
        if ($type == 1) {
            $codeAccount = $account;
            $siteRegisterEmail = ApiConfigHelper::getConfigByItemKey('site_register_email');
            if ($siteRegisterEmail === false) {
                $this->error(ErrorCodeService::REGISTER_EMAIL_ERROR);
            }
        }
        if ($type == 2) {
            $codeAccount = $countryCode.$account;
            $siteRegisterPhone = ApiConfigHelper::getConfigByItemKey('site_register_phone');
            if ($siteRegisterPhone === false) {
                $this->error(ErrorCodeService::REGISTER_PHONE_ERROR);
            }
        }

        // Verify Password
        if ($password) {
            $passwordLength = ApiConfigHelper::getConfigByItemKey('password_length');
            if ($passwordLength > 0) {
                if ($passwordLength > strlen($password)) {
                    $this->error(ErrorCodeService::PASSWORD_LENGTH_ERROR);
                }
            }
            $passwordStrength = ApiConfigHelper::getConfigByItemKey('password_strength');

            // Verify Password Rules
            if (! empty($passwordStrength)) {
                $passwordStrengthArr = explode(',', $passwordStrength);

                if (in_array(FsConfig::PASSWORD_NUMBER, $passwordStrengthArr)) {
                    $isError = preg_match('/\d/is', $password);
                    if ($isError == 0) {
                        $this->error(ErrorCodeService::PASSWORD_NUMBER_ERROR);
                    }
                }
                if (in_array(FsConfig::PASSWORD_LOWERCASE_LETTERS, $passwordStrengthArr)) {
                    $isError = preg_match('/[a-z]/', $password);
                    if ($isError == 0) {
                        $this->error(ErrorCodeService::PASSWORD_LOWERCASE_ERROR);
                    }
                }
                if (in_array(FsConfig::PASSWORD_CAPITAL_LETTERS, $passwordStrengthArr)) {
                    $isError = preg_match('/[A-Z]/', $password);
                    if ($isError == 0) {
                        $this->error(ErrorCodeService::PASSWORD_CAPITAL_ERROR);
                    }
                }
                if (in_array(FsConfig::PASSWORD_SYMBOL, $passwordStrengthArr)) {
                    $isError = preg_match('/^[A-Za-z0-9]+$/', $password);
                    if ($isError == 1) {
                        $this->error(ErrorCodeService::PASSWORD_SYMBOL_ERROR);
                    }
                }
            }
        }

        $time = date('Y-m-d H:i:s', time());
        $codeArr = FresnsVerifyCodes::where('type', $type)->where('account', $codeAccount)->where('expired_at', '>',
            $time)->pluck('code')->toArray();
        if (! in_array($verifyCode, $codeArr)) {
            $this->error(ErrorCodeService::SMS_CODE_CHECK_ERROR);
        }

        // Check if a user has registered
        switch ($type) {
            case 1:
                $count = FresnsUsers::where('email', $account)->count();
                if ($count > 0) {
                    $this->error(ErrorCodeService::REGISTER_USER_ERROR);
                }
                break;
            case 2:
                $count = FresnsUsers::where('pure_phone', $account)->count();
                if ($count > 0) {
                    $this->error(ErrorCodeService::REGISTER_USER_ERROR);
                }
                break;
            default:

                break;
        }

        $input = [];
        // Verify successful user creation
        switch ($type) {
            case 1:
                $input = [
                    'email' => $account,
                ];
                break;
            case 2:
                $input = [
                    'country_code' => $countryCode,
                    'pure_phone' => $account,
                    'phone' => $countryCode.$account,
                ];
                break;
            default:
                // code...
                break;
        }

        $userUuid = ApiCommonHelper::createUuid();
        $input['api_token'] = StrHelper::createToken();
        $input['uuid'] = $userUuid;
        $input['last_login_at'] = date('Y-m-d H:i:s');
        if ($password) {
            $input['password'] = StrHelper::createPassword($password);
        }

        $uid = FresnsUsers::insertGetId($input);
        FresnsCmdService::addSubTablePluginItem(FresnsUsersConfig::CFG_TABLE, $uid);

        $memberInput = [
            'user_id' => $uid,
            'name' => StrHelper::createToken(rand(6, 8)),
            'nickname' => $nickname,
            'uuid' => ApiCommonHelper::createMemberUuid(),
        ];

        $mid = FresnsMembers::insertGetId($memberInput);
        FresnsCmdService::addSubTablePluginItem(FresnsMembersConfig::CFG_TABLE, $mid);

        $langTag = $this->langTag;

        if ($type == 1) {
            // Add Counts
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

        $sessionId = GlobalService::getGlobalSessionKey('session_log_id');
        if ($sessionId) {
            FresnsSessionLogsService::updateSessionLogs($sessionId, 2, $uid, $mid, $uid);
        }

        $data = $this->service->getUserDetail($uid, $langTag, $mid);
        if ($data) {
            $cmd = FresnsPluginConfig::PLG_CMD_CREATE_SESSION_TOKEN;
            $input['uid'] = $userUuid;
            $input['platform'] = $request->header('platform');
            $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);
            if (PluginRpcHelper::isErrorPluginResp($resp)) {
                $this->errorCheckInfo($resp);
            }

            $token = $resp['output']['token'] ?? '';

            $data['token'] = $token;
            $data['tokenExpiredTime'] = '';
        }

        $this->success($data);
    }

    // User Login
    public function login(Request $request)
    {
        // Verify Parameters
        $rule = [
            'type' => 'required|numeric|in:1,2,3',
            'account' => 'required',

        ];

        $type = $request->input('type');
        $account = $request->input('account');
        $countryCode = $request->input('countryCode');
        $verifyCode = $request->input('verifyCode');
        $passwordBase64 = $request->input('password');

        if ($passwordBase64) {
            $password = base64_decode($passwordBase64, true);
            if ($password == false) {
                $password = $passwordBase64;
            }
        } else {
            $password = null;
        }

        switch ($type) {
            case 1:
                $rule = [
                    'type' => 'required|numeric|in:1,2,3',
                    'account' => 'required|email',
                ];
                $user = DB::table(FresnsUsersConfig::CFG_TABLE)->where('email', $account)->first();
                if (empty($user)) {
                    $user = DB::table(FresnsUsersConfig::CFG_TABLE)->where('email', $account)->first();
                }
                break;
            case 2:
                $rule = [
                    'type' => 'required|numeric|in:1,2,3',
                    'account' => 'required|numeric|regex:/^1[^0-2]\d{9}$/',
                    'countryCode' => 'required|numeric',
                ];
                $user = DB::table(FresnsUsersConfig::CFG_TABLE)->where('phone', $countryCode.$account)->first();
                if (empty($user)) {
                    $user = DB::table(FresnsUsersConfig::CFG_TABLE)->where('phone', $countryCode.$account)->first();
                }
                break;
            default:
                // code...
                break;
        }

        ValidateService::validateRule($request, $rule);

        if (empty($user)) {
            $this->error(ErrorCodeService::CODE_PHONE_ERROR);
        }

        $sessionLogId = GlobalService::getGlobalSessionKey('session_log_id');
        if ($sessionLogId) {
            $sessionInput = [
                'object_order_id' => $user->id,
                'user_id' => $user->id,
            ];
            FresnsSessionLogs::where('id', $sessionLogId)->update($sessionInput);
        }

        // Check the user of login password errors in the last 1 hour for the user to whom the email or cell phone number belongs.
        // If it reaches 5 times, the login will be restricted.
        // session_logs > object_type=3
        $startTime = date('Y-m-d H:i:s', strtotime('-1 hour'));
        $sessionCount = FresnsSessionLogs::where('created_at', '>=', $startTime)
        ->where('user_id', $user->id)
        ->where('object_result', FresnsSessionLogsConfig::OBJECT_RESULT_ERROR)
        ->where('object_type', FresnsSessionLogsConfig::OBJECT_TYPE_USER_LOGIN)
        ->count();

        if ($sessionCount >= 5) {
            $this->error(ErrorCodeService::LOGIN_ERROR);
        }
        // One of the password or verification code is required
        if (empty($password) && empty($verifyCode)) {
            $this->error(ErrorCodeService::CODE_PARAM_ERROR);
        }

        $time = date('Y-m-d H:i:s', time());
        if ($type != 3) {
            if ($verifyCode) {
                switch ($type) {
                    case 1:
                        $codeArr = FresnsVerifyCodes::where('type', $type)->where('account',
                            $account)->where('expired_at', '>', $time)->pluck('code')->toArray();
                        break;
                    case 2:
                        $codeArr = FresnsVerifyCodes::where('type', $type)->where('account',
                            $countryCode.$account)->where('expired_at', '>', $time)->pluck('code')->toArray();

                        break;

                    default:
                        // code...
                        break;
                }

                if (! in_array($verifyCode, $codeArr)) {
                    $this->error(ErrorCodeService::SMS_CODE_CHECK_ERROR);
                }
            }

            if ($password) {
                if (! Hash::check($password, $user->password)) {
                    $this->error(ErrorCodeService::PASSWORD_INVALID);
                }
            }
        }

        if ($user->is_enable == 0) {
            $this->error(ErrorCodeService::USERS_NOT_AUTHORITY_ERROR);
        }

        $langTag = ApiLanguageHelper::getLangTagByHeader();

        $data = $this->service->getUserDetail($user->id, $langTag);
        if ($data) {
            // Update the last_login_at field in the users table
            FresnsUsers::where('id', $user->id)->update(['last_login_at' => date('Y-m-d H:i:s', time())]);

            $cmd = FresnsPluginConfig::PLG_CMD_CREATE_SESSION_TOKEN;
            $input['uid'] = $user->uuid;
            $input['platform'] = $request->header('platform');
            $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);
            if (PluginRpcHelper::isErrorPluginResp($resp)) {
                $this->errorCheckInfo($resp);
            }

            $token = $resp['output']['token'] ?? '';

            $data['token'] = $token;
            $data['tokenExpiredTime'] = '';
        }
        $sessionId = GlobalService::getGlobalSessionKey('session_log_id');
        if ($sessionId) {
            FresnsSessionLogsService::updateSessionLogs($sessionId, 2, $user->id, null, $user->id);
        }

        $this->success($data);
    }

    // User Logout
    public function logout(Request $request)
    {
        $uid = GlobalService::getGlobalKey('user_id');
        $mid = GlobalService::getGlobalKey('member_id');

        DB::table(FresnsSessionTokensConfig::CFG_TABLE)->where('user_id', $uid)->where('member_id', null)->delete();
        DB::table(FresnsSessionTokensConfig::CFG_TABLE)->where('member_id', $mid)->delete();
        $this->success();
    }

    // Delete User
    public function delete(Request $request)
    {
        $uid = GlobalService::getGlobalKey('user_id');

        $user = FresnsUsers::where('id', $uid)->first();
        if (empty($user)) {
            $this->error(ErrorCodeService::NO_RECORD);
        }

        FresnsUsers::where('id', $user['id'])->delete();
        FresnsMembers::where('user_id', $user['id'])->delete();

        // Return config parameter
        $itemValue = ApiConfigHelper::getConfigByItemKey('delete_account_todo');

        $data['days'] = $itemValue ?? 0;

        $sessionId = GlobalService::getGlobalSessionKey('session_log_id');
        if ($sessionId) {
            FresnsSessionLogsService::updateSessionLogs($sessionId, 2, $user['id'], null, $user['id']);
        }

        DB::table(FresnsSessionTokensConfig::CFG_TABLE)->where('user_id', $uid)->where('member_id', null)->delete();

        $this->success($data);
    }

    // Restore User
    public function restore(Request $request)
    {
        $uid = $request->header('uid');

        $user = FresnsUsers::where('uuid', $uid)->first();
        if ($user) {
            $this->error(ErrorCodeService::NO_RECORD);
        }

        $user = DB::table(FresnsUsersConfig::CFG_TABLE)->where('uuid', $uid)->first();

        if (empty($user)) {
            $this->error(ErrorCodeService::NO_RECORD);
        }

        $input['deleted_at'] = null;

        $memberInput = [
            'deleted_at' => null,
        ];

        DB::table(FresnsUsersConfig::CFG_TABLE)->where('id', $user->id)->update($input);
        DB::table(FresnsMembersConfig::CFG_TABLE)->where('user_id', $user->id)->update($memberInput);

        $langTag = $this->langTag;

        $data = $this->service->getUserDetail($user->id, $langTag);
        $this->success($data);
    }

    // Reset Password
    public function reset(Request $request)
    {
        // Verify Parameters
        $rule = [
            'type' => 'required|numeric|in:1,2',
            'account' => 'required',
            'verifyCode' => 'required',
            'newPassword' => 'required',
        ];

        $type = $request->input('type');
        $account = $request->input('account');
        $verifyCode = $request->input('verifyCode');
        $newPassword = $request->input('newPassword');
        $countryCode = $request->input('countryCode');

        switch ($type) {
            case 1:
                $rule = [
                    'type' => 'required|numeric|in:1,2',
                    'account' => 'required|email',
                    'newPassword' => 'required',
                    'verifyCode' => 'required',
                ];
                break;
            case 2:
                $rule = [
                    'type' => 'required|numeric|in:1,2',
                    'account' => 'required|numeric|regex:/^1[^0-2]\d{9}$/',
                    'countryCode' => 'required|numeric',
                    'newPassword' => 'required',
                    'verifyCode' => 'required',

                ];

                break;
            default:
                // code...
                break;
        }

        ValidateService::validateRule($request, $rule);

        $time = date('Y-m-d H:i:s', time());
        switch ($type) {
            case 1:
                $codeArr = FresnsVerifyCodes::where('type', $type)->where('account', $account)->where('expired_at', '>',
                    $time)->pluck('code')->toArray();
                break;
            case 2:
                $codeArr = FresnsVerifyCodes::where('type', $type)->where('account',
                    $countryCode.$account)->where('expired_at', '>', $time)->pluck('code')->toArray();

                break;

            default:
                // code...
                break;
        }

        if (! in_array($verifyCode, $codeArr)) {
            $this->error(ErrorCodeService::SMS_CODE_CHECK_ERROR);
        }

        switch ($type) {
            case 1:
                $user = FresnsUsers::where('email', $account)->first();
                break;

            default:
                $user = FresnsUsers::where('pure_phone', $account)->first();
                break;
        }

        if (empty($user)) {
            $this->error(ErrorCodeService::CODE_PHONE_ERROR);
        }
        $password = str_replace(' ', '', $newPassword);
        $passwordLength = ApiConfigHelper::getConfigByItemKey('password_length');
        if ($passwordLength > 0) {
            if ($passwordLength > strlen($password)) {
                $this->error(ErrorCodeService::PASSWORD_LENGTH_ERROR);
            }
        }
        $passwordStrength = ApiConfigHelper::getConfigByItemKey('password_strength');

        // Verify Password Rules
        if (! empty($passwordStrength)) {
            $passwordStrengthArr = explode(',', $passwordStrength);

            if (in_array(FsConfig::PASSWORD_NUMBER, $passwordStrengthArr)) {
                $isError = preg_match('/\d/is', $password);
                if ($isError == 0) {
                    $this->error(ErrorCodeService::PASSWORD_NUMBER_ERROR);
                }
            }
            if (in_array(FsConfig::PASSWORD_LOWERCASE_LETTERS, $passwordStrengthArr)) {
                $isError = preg_match('/[a-z]/', $password);
                if ($isError == 0) {
                    $this->error(ErrorCodeService::PASSWORD_LOWERCASE_ERROR);
                }
            }
            if (in_array(FsConfig::PASSWORD_CAPITAL_LETTERS, $passwordStrengthArr)) {
                $isError = preg_match('/[A-Z]/', $password);
                if ($isError == 0) {
                    $this->error(ErrorCodeService::PASSWORD_CAPITAL_ERROR);
                }
            }
            if (in_array(FsConfig::PASSWORD_SYMBOL, $passwordStrengthArr)) {
                $isError = preg_match('/^[A-Za-z0-9]+$/', $password);
                if ($isError == 1) {
                    $this->error(ErrorCodeService::PASSWORD_SYMBOL_ERROR);
                }
            }
        }

        $input = [
            'password' => StrHelper::createPassword($newPassword),
        ];

        FresnsUsers::where('id', $user['id'])->update($input);

        $sessionId = GlobalService::getGlobalSessionKey('session_log_id');
        if ($sessionId) {
            FresnsSessionLogsService::updateSessionLogs($sessionId, 2, $user['id'], null, $user['id']);
        }

        $this->success();
    }

    // User Detail
    public function detail(Request $request)
    {
        $uid = $request->header('uid');
        $mid = $request->header('mid');
        $uid = DB::table(FresnsUsersConfig::CFG_TABLE)->where('uuid', $uid)->value('id');
        $mid = DB::table(FresnsMembersConfig::CFG_TABLE)->where('uuid', $mid)->value('id');

        $langTag = $this->langTag;

        $data = $this->service->getUserDetail($uid, $langTag, $mid);
        $this->success($data);
    }

    public function walletLogs(Request $request)
    {
        // Verify Parameters
        $rule = [
            'type' => 'numeric',
            'status' => 'in:1,0',
            'pageSize' => 'numeric',
            'page' => 'numeric',
        ];
        ValidateService::validateRule($request, $rule);
        $currentPage = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 30);
        if ($currentPage < 0 || $pageSize < 0) {
            $this->error(ErrorCodeService::CODE_PARAM_ERROR);
        }

        $uid = GlobalService::getGlobalKey('user_id');

        $langTag = ApiLanguageHelper::getLangTagByHeader();

        $fresnsUserWalletLogsService = new FresnsUserWalletLogsService();

        $request->offsetSet('currentPage', $currentPage);
        $request->offsetSet('pageSize', $pageSize);
        $request->offsetSet('user_id', $uid);
        $request->offsetSet('langTag', $langTag);

        $fresnsUserWalletLogsService->setResource(FresnsWalletLogsResource::class);
        $data = $fresnsUserWalletLogsService->searchData();

        $this->success($data);
    }

    // Edit User Info
    public function edit(Request $request)
    {
        // Verify Parameters
        $rule = [
            'codeType' => 'numeric|in:1,2',
            'editCountryCode' => 'numeric',
        ];
        ValidateService::validateRule($request, $rule);
        $uid = GlobalService::getGlobalKey('user_id');

        $verifyCode = $request->input('verifyCode');
        $codeType = $request->input('codeType');
        $password = $request->input('password');
        $walletPassword = $request->input('walletPassword');
        $editEmail = $request->input('editEmail');
        $editPhone = $request->input('editPhone');
        $editCountryCode = $request->input('editCountryCode');
        $editPassword = $request->input('editPassword');
        $editWalletPassword = $request->input('editWalletPassword');
        $editLastLoginTime = $request->input('editLastLoginTime');
        $user = FresnsUsers::where('id', $uid)->first();

        $email = $user['email'];

        if ($codeType == 1) {
            $account = $email;
        } else {
            $account = $user['phone'];
        }

        if ($editEmail) {
            if (empty($email)) {
                $account = $editEmail;
            } else {
                $account = $email;
            }
        }

        if ($editPhone) {
            // Verify Parameters
            $rule = [
                'editCountryCode' => 'required|numeric',
            ];
            ValidateService::validateRule($request, $rule);
            if (empty($user['phone'])) {
                $account = $editCountryCode.$editPhone;
            } else {
                $account = $user['phone'];
            }
        }

        $codeArr = null;

        if (empty($editLastLoginTime)) {
            if (empty($password) && empty($walletPassword)) {
                $time = date('Y-m-d H:i:s', time());
                $codeArr = FresnsVerifyCodes::where('account', $account)->where('expired_at', '>',
                    $time)->pluck('code')->toArray();

                if (! in_array($verifyCode, $codeArr)) {
                    $this->error(ErrorCodeService::SMS_CODE_CHECK_ERROR);
                }
            }
        }

        if ($editEmail) {
            FresnsUsers::where('id', $user['id'])->update(['email' => $editEmail]);
        }

        if ($editPhone) {
            $input = [
                'country_code' => $editCountryCode,
                'pure_phone' => $editPhone,
                'phone' => $editCountryCode.$editPhone,
            ];
            FresnsUsers::where('id', $user['id'])->update($input);
        }

        if ($editPassword) {
            if (! empty($password)) {
                if (! Hash::check($password, $user['password'])) {
                    $this->error(ErrorCodeService::PASSWORD_INVALID);
                }
            }

            FresnsUsers::where('id', $user['id'])->update(['password' => bcrypt($editPassword)]);
        }

        if ($editWalletPassword) {
            $wallet = FresnsUserWallets::where('user_id', $user['id'])->first();
            if (empty($codeArr)) {
                if (! Hash::check($password, $wallet['password'])) {
                    $this->error(ErrorCodeService::PASSWORD_INVALID);
                }
            }
            if (empty($wallet)) {
                $this->error(ErrorCodeService::NO_RECORD);
            }
            FresnsUserWallets::where('id', $wallet['id'])->update(['password' => bcrypt($editWalletPassword)]);
        }

        if ($editLastLoginTime) {
            $rule = [
                'editLastLoginTime' => 'date_format:Y-m-d H:i:s',
            ];
            ValidateService::validateRule($request, $rule);
            FresnsUsers::where('id',
                $user['id'])->update(['last_login_at' => DateHelper::fresnsInputTimeToTimezone($editLastLoginTime)]);
        }

        $sessionId = GlobalService::getGlobalSessionKey('session_log_id');
        if ($sessionId) {
            FresnsSessionLogsService::updateSessionLogs($sessionId, 2, $user['id'], null, $user['id']);
        }

        $this->success();
    }
}
