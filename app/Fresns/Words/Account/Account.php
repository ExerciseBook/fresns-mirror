<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Words\Account;

use App\Fresns\Words\Account\DTO\AddAccountDTO;
use App\Fresns\Words\Account\DTO\CreateSessionTokenDTO;
use App\Fresns\Words\Account\DTO\LogicalDeletionAccountDTO;
use App\Fresns\Words\Account\DTO\VerifyAccountDTO;
use App\Fresns\Words\Account\DTO\VerifySessionTokenDTO;
use App\Helpers\PrimaryHelper;
use App\Models\Account as AccountModel;
use App\Models\AccountConnect;
use App\Models\AccountWallet;
use App\Models\SessionToken;
use App\Models\VerifyCode;
use App\Utilities\ConfigUtility;
use App\Utilities\PermissionUtility;
use Fresns\CmdWordManager\Exceptions\Constants\ExceptionConstant;
use Fresns\CmdWordManager\Traits\CmdWordResponseTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Account
{
    use CmdWordResponseTrait;

    /**
     * @param $wordBody
     * @return array
     *
     * @throws \Throwable
     */
    public function addAccount($wordBody)
    {
        $dtoWordBody = new AddAccountDTO($wordBody);
        $langTag = \request()->header('langTag', config('app.locale'));

        if ($dtoWordBody->type == 1) {
            $checkAccount = AccountModel::where('email', $dtoWordBody->account)->first();
        } else {
            $checkAccount = AccountModel::where('phone', $dtoWordBody->countryCode.$dtoWordBody->account)->first();
        }

        if (! empty($checkAccount)) {
            return $this->failure(
                34204,
                ConfigUtility::getCodeMessage(34204, 'Fresns', $langTag)
            );
        }

        $connectInfoArr = [];
        if (isset($dtoWordBody->connectInfo)) {
            $connectInfoArr = json_decode($dtoWordBody->connectInfo, true);
            $connectTokenArr = [];
            foreach ($connectInfoArr as $v) {
                $connectTokenArr[] = $v['connectToken'];
            }
            $count = AccountConnect::whereIn('connect_token', $connectTokenArr)->count();
            if ($count > 0) {
                ExceptionConstant::getHandleClassByCode(ExceptionConstant::CMD_WORD_DATA_ERROR)::throw();
            }
        }

        $inputArr = [];
        $inputArr = match ($dtoWordBody->type) {
            1 => ['email' => $dtoWordBody->account],
            2 => [
                'country_code' => $dtoWordBody->countryCode,
                'pure_phone' => $dtoWordBody->account,
                'phone' => $dtoWordBody->countryCode.$dtoWordBody->account,
            ],
            default => [],
        };
        $inputArr['aid'] = \Str::random(12);
        $inputArr['password'] = isset($dtoWordBody->password) ? Hash::make($dtoWordBody->password) : null;

        $accountId = AccountModel::insertGetId($inputArr);

        // Account Wallet Table
        $accountWalletsInput = [
            'account_id' => $accountId,
        ];
        AccountWallet::insert($accountWalletsInput);

        // Account Connects Table
        if ($connectInfoArr) {
            $itemArr = [];
            foreach ($connectInfoArr as $info) {
                $item['account_id'] = $accountId;
                $item['connect_id'] = $info['connectId'];
                $item['connect_token'] = $info['connectToken'];
                $item['connect_name'] = $info['connectName'];
                $item['connect_nickname'] = $info['connectNickname'];
                $item['connect_avatar'] = $info['connectAvatar'];
                $item['plugin_unikey'] = 'fresns_cmd_user_register';
                $itemArr[] = $item;
            }

            AccountConnect::insert($itemArr);
        }

        return $this->success([
            'aid' => $inputArr['aid'],
            'type' => $dtoWordBody->type,
        ]);
    }

    /**
     * @param $wordBody
     * @return array
     *
     * @throws \Throwable
     */
    public function verifyAccount($wordBody)
    {
        $dtoWordBody = new VerifyAccountDTO($wordBody);
        $langTag = \request()->header('langTag', config('app.locale'));

        if ($dtoWordBody->type == 1) {
            $accountName = $dtoWordBody->account;
            $account = AccountModel::where('email', $accountName)->first();
        } else {
            $accountName = $dtoWordBody->countryCode.$dtoWordBody->account;
            $account = AccountModel::where('phone', $accountName)->first();
        }

        if (empty($dtoWordBody->password)) {
            $checkCode = [
                'type' => $dtoWordBody->type,
                'account' => $accountName,
                'code' => $dtoWordBody->verifyCode,
                'is_enable' => 1,
            ];

            $verifyCode = VerifyCode::where($checkCode)->where('expired_at', '>', date('Y-m-d H:i:s'))->first();

            if ($verifyCode) {
                VerifyCode::where('id', $verifyCode->id)->update([
                    'is_enable' => 0,
                ]);

                return $this->success();
            } else {
                return $this->failure(
                    33103,
                    ConfigUtility::getCodeMessage(33103, 'Fresns', $langTag),
                );
            }
        }

        if (! Hash::check($dtoWordBody->password, $account->password)) {
            return $this->failure(
                34304,
                ConfigUtility::getCodeMessage(34304, 'Fresns', $langTag),
            );
        }

        return $this->success();
    }

    /**
     * @param $wordBody
     * @return array
     *
     * @throws \Throwable
     */
    public function createSessionToken($wordBody)
    {
        $dtoWordBody = new CreateSessionTokenDTO($wordBody);

        $accountId = PrimaryHelper::fresnsAccountIdByAid($dtoWordBody->aid);
        $userId = PrimaryHelper::fresnsAccountIdByUid($dtoWordBody->uid);

        $condition = [
            'platform_id' => $dtoWordBody->platformId,
            'account_id' => $accountId,
            'user_id' => $userId,
        ];

        $tokenInfo = SessionToken::where($condition)->first();
        if (! empty($tokenInfo)) {
            SessionToken::where($condition)->delete();
        }

        $token = \Str::random(32);
        $expiredAt = null;
        if (isset($dtoWordBody->expiredTime)) {
            $now = time();
            $time = $dtoWordBody->expiredTime * 3600;
            $expiredTime = $now + $time;
            $expiredAt = date('Y-m-d H:i:s', $expiredTime);
        }

        $condition['token'] = $token;
        $condition['expired_at'] = $expiredAt;

        SessionToken::insert($condition);

        return $this->success([
            'aid' => $dtoWordBody->aid,
            'uid' => $dtoWordBody->uid,
            'token' => $token,
            'expiredTime' => $expiredAt,
        ]);
    }

    /**
     * @param $wordBody
     * @return array
     *
     * @throws \Throwable
     */
    public function verifySessionToken($wordBody)
    {
        $dtoWordBody = new VerifySessionTokenDTO($wordBody);
        $langTag = \request()->header('langTag', config('app.locale'));

        $condition = [
            'platform_id' => $dtoWordBody->platformId,
            'account_id' => $dtoWordBody->aid,
            'user_id' => $dtoWordBody-> uid ?? null,
        ];
        $session = SessionToken::where($condition)->first();

        if ($session->token != $dtoWordBody->token) {
            return $this->failure(
                31505,
                ConfigUtility::getCodeMessage(31505, 'Fresns', $langTag)
            );
        }

        if ($session->expired_at < date('Y-m-d H:i:s', time())) {
            return $this->failure(
                31303,
                ConfigUtility::getCodeMessage(31303, 'Fresns', $langTag)
            );
        }

        $accountId = PrimaryHelper::fresnsAccountIdByAid($dtoWordBody->aid);
        $userId = PrimaryHelper::fresnsUserIdByUid($dtoWordBody->uid);

        if (! empty($dtoWordBody->uid)) {
            $userAffiliation = PermissionUtility::checkUserAffiliation($userId, $accountId);
            if (! $userAffiliation) {
                return $this->failure(
                    35201,
                    ConfigUtility::getCodeMessage(35201, 'Fresns', $langTag)
                );
            }
        }

        return $this->success();
    }

    /**
     * @param $wordBody
     * @return array
     *
     * @throws \Throwable
     */
    public function logicalDeletionAccount($wordBody)
    {
        $dtoWordBody = new LogicalDeletionAccountDTO($wordBody);

        $accountId = PrimaryHelper::fresnsAccountIdByAid($dtoWordBody->aid);
        $dateTime = 'deleted#'.date('YmdHis').'#';
        AccountModel::where('id', $accountId)->update([
            'phone' => DB::raw("concat('$dateTime','phone')"),
            'email' => DB::raw("concat('$dateTime','email')"),
            'deleted_at' => now(), ]
        );
        AccountConnect::where('account_id', $accountId)->forceDelete();

        return $this->success();
    }
}
