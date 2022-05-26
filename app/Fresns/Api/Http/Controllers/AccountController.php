<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\Controllers;

use App\Fresns\Api\Http\DTO\AccountLoginDTO;
use App\Fresns\Api\Http\DTO\AccountRegisterDTO;
use App\Fresns\Api\Http\DTO\AccountResetPasswordDTO;
use App\Fresns\Api\Http\DTO\AccountWalletLogsDTO;
use App\Helpers\AppHelper;
use App\Helpers\DateHelper;
use App\Fresns\Api\Services\AccountService;
use App\Exceptions\ApiException;
use App\Helpers\ConfigHelper;
use App\Helpers\PrimaryHelper;
use App\Models\Account;
use App\Models\AccountWalletLog;
use App\Models\SessionToken;
use App\Utilities\ValidationUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    // register
    public function register(Request $request)
    {
        $dtoRequest = new AccountRegisterDTO($request->all());
        $headers = AppHelper::getApiHeaders();

        $configs = ConfigHelper::fresnsConfigByItemKeys([
            'site_mode',
            'site_public_status',
            'site_public_service',
            'site_register_email',
            'site_register_phone',
        ]);

        if ($configs['site_mode'] == 'private' || ! $configs['site_public_status'] || ! empty($configs['site_public_service'])) {
            throw new ApiException(34201);
        }

        if ($dtoRequest->type == 1 && ! $configs['site_register_email']) {
            throw new ApiException(34202);
        }

        if ($dtoRequest->type == 2 && ! $configs['site_register_phone']) {
            throw new ApiException(34203);
        }

        // check code
        $checkCodeWordBody = [
            'type' => $dtoRequest->type,
            'account' => $dtoRequest->account,
            'countryCode' => $dtoRequest->countryCode,
            'verifyCode' => $dtoRequest->verifyCode,
        ];

        $fresnsResp = \FresnsCmdWord::plugin('Fresns')->checkCode($checkCodeWordBody);

        if ($fresnsResp->isErrorResponse()) {
            return $fresnsResp->errorResponse();
        }

        // add account
        $addAccountWordBody = [
            'type' => $dtoRequest->type,
            'account' => $dtoRequest->account,
            'countryCode' => $dtoRequest->countryCode,
            'connectInfo' => null,
            'password' => $dtoRequest->password,
        ];

        $fresnsAccountResp = \FresnsCmdWord::plugin('Fresns')->addAccount($addAccountWordBody);

        if ($fresnsAccountResp->isErrorResponse()) {
            return $fresnsAccountResp->errorResponse();
        }

        // add user
        $addUserWordBody = [
            'aid' => $fresnsAccountResp->getData('aid'),
            'nickname' => $dtoRequest->nickname,
            'username' => null,
            'password' => null,
            'avatarFid' => null,
            'avatarUrl' => null,
            'gender' => null,
            'birthday' => null,
            'timezone' => null,
            'language' => null,
        ];
        $fresnsUserResp = \FresnsCmdWord::plugin('Fresns')->addUser($addUserWordBody);

        if ($fresnsUserResp->isErrorResponse()) {
            return $fresnsUserResp->errorResponse();
        }

        // create token
        $createTokenWordBody = [
            'platformId' => $headers['platformId'],
            'aid' => $fresnsUserResp->getData('aid'),
            'uid' => $fresnsUserResp->getData('uid'),
            'expiredTime' => null,
        ];
        $fresnsTokenResponse = \FresnsCmdWord::plugin('Fresns')->createSessionToken($createTokenWordBody);

        if ($fresnsTokenResponse->isErrorResponse()) {
            return $fresnsTokenResponse->errorResponse();
        }

        // get account data
        $accountId = PrimaryHelper::fresnsAccountIdByAid($fresnsTokenResponse->getData('aid'));

        $service = new AccountService();
        $data = $service->accountData($accountId);

        $token['token'] = $fresnsTokenResponse->getData('token');
        $token['expiredTime'] = $fresnsTokenResponse->getData('expiredTime');
        $data['sessionToken'] = $token;

        return $this->success($data);
    }

    // login
    public function login(Request $request)
    {
        $dtoRequest = new AccountLoginDTO($request->all());
        $headers = AppHelper::getApiHeaders();

        // login
        $wordBody = [
            'type' => $dtoRequest->type,
            'account' => $dtoRequest->account,
            'countryCode' => $dtoRequest->countryCode,
            'password' => $dtoRequest->password,
            'verifyCode' => $dtoRequest->verifyCode,
        ];
        $fresnsResponse = \FresnsCmdWord::plugin('Fresns')->verifyAccount($wordBody);

        if ($fresnsResponse->isErrorResponse()) {
            return $fresnsResponse->errorResponse();
        }

        // create token
        $createTokenWordBody = [
            'platformId' => $headers['platformId'],
            'aid' => $fresnsResponse->getData('aid'),
            'uid' => null,
            'expiredTime' => null,
        ];
        $fresnsTokenResponse = \FresnsCmdWord::plugin('Fresns')->createSessionToken($createTokenWordBody);

        if ($fresnsTokenResponse->isErrorResponse()) {
            return $fresnsTokenResponse->errorResponse();
        }

        // get account data
        $accountId = PrimaryHelper::fresnsAccountIdByAid($fresnsTokenResponse->getData('aid'));

        $service = new AccountService();
        $data = $service->accountData($accountId);

        $token['token'] = $fresnsTokenResponse->getData('token');
        $token['expiredTime'] = $fresnsTokenResponse->getData('expiredTime');
        $data['sessionToken'] = $token;

        return $this->success($data);
    }

    // reset password
    public function resetPassword(Request $request)
    {
        $dtoRequest = new AccountResetPasswordDTO($request->all());
        $headers = AppHelper::getApiHeaders();

        // check code
        $checkCodeWordBody = [
            'type' => $dtoRequest->type,
            'account' => $dtoRequest->account,
            'countryCode' => $dtoRequest->countryCode,
            'verifyCode' => $dtoRequest->verifyCode,
        ];

        $fresnsResp = \FresnsCmdWord::plugin('Fresns')->checkCode($checkCodeWordBody);

        if ($fresnsResp->isErrorResponse()) {
            return $fresnsResp->errorResponse();
        }

        if ($dtoRequest->type == 1) {
            $account = Account::where('email', $dtoRequest->account)->first();
        } else {
            $account = Account::where('phone', $dtoRequest->countryCode.$dtoRequest->account)->first();
        }

        if (empty($account)) {
            throw new ApiException(34301);
        }

        $validPassword = ValidationUtility::validPassword($dtoRequest->newPassword);

        if (! $validPassword['length']) {
            throw new ApiException(34104);
        }

        if (! $validPassword['number']) {
            throw new ApiException(34105);
        }

        if (! $validPassword['lowercase']) {
            throw new ApiException(34106);
        }

        if (! $validPassword['uppercase']) {
            throw new ApiException(34107);
        }

        if (! $validPassword['symbols']) {
            throw new ApiException(34108);
        }

        $dataPassword = Hash::make($dtoRequest->newPassword);

        $account->update([
            'password' => $dataPassword,
        ]);

        return $this->success();
    }

    // detail
    public function detail()
    {
        $headers = AppHelper::getApiHeaders();

        $accountId = PrimaryHelper::fresnsAccountIdByAid($headers['aid']);
        if (empty($accountId)) {
            throw new ApiException(31502);
        }

        $service = new AccountService();
        $data = $service->accountData($accountId);

        return $this->success($data);
    }

    // walletLogs
    public function walletLogs(Request $request)
    {
        $dtoRequest = new AccountWalletLogsDTO($request->all());
        $headers = AppHelper::getApiHeaders();

        $accountId = PrimaryHelper::fresnsAccountIdByAid($headers['aid']);
        $status = $dtoRequest->status ?? 1;

        $walletLogQuery = AccountWalletLog::where('account_id', $accountId)->where('is_enable', $status)->orderBy('created_at', 'desc');

        if (!empty($dtoRequest->type)) {
            $walletLogQuery->where('object_type', $dtoRequest->type);
        }

        $walletLogs = $walletLogQuery->paginate($request->get('pageSize', 20));

        $logList = null;
        foreach ($walletLogs as $log) {
            $item['type'] = $log->object_type;
            $item['amount'] = $log->amount;
            $item['transactionAmount'] = $log->transaction_amount;
            $item['systemFee'] = $log->system_fee;
            $item['openingBalance'] = $log->opening_balance;
            $item['closingBalance'] = $log->closing_balance;
            $info['createTime'] = DateHelper::fresnsFormatDateTime($log->created_at, $headers['timezone'], $headers['langTag']);
            $info['createTimeFormat'] = DateHelper::fresnsFormatTime($log->created_at, $headers['langTag']);
            $item['remark'] = $log->remark;
            $item['pluginUnikey'] = $log->object_unikey;
            $item['status'] = (bool) $log->is_enable;
            $logList[] = $item;
        }

        return $this->fresnsPaginate($logList, $walletLogs->total(), $walletLogs->perPage());
    }

    // logout
    public function logout()
    {
        $headers = AppHelper::getApiHeaders();

        $accountId = PrimaryHelper::fresnsAccountIdByAid($headers['aid']);
        $userId = PrimaryHelper::fresnsAccountIdByUid($headers['uid']);

        $condition = [
            'platform_id' => $headers['platformId'],
            'account_id' => $accountId,
            'user_id' => $userId,
        ];
        SessionToken::where($condition)->delete();

        return $this->success();
    }
}
