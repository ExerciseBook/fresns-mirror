<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Services;

use App\Helpers\AppHelper;
use App\Helpers\InteractiveHelper;
use App\Utilities\ExtendUtility;
use App\Models\Account;

class AccountService
{
    public function accountDetail(int $accountId)
    {
        $headers = AppHelper::getApiHeaders();
        $account = Account::whereId($accountId)->first();

        $userArr = $account->users;
        $userList = null;
        foreach ($userArr as $user) {
            $userProfile = $user->getUserProfile($headers['langTag'], $headers['timezone']);
            $userMainRole = $user->getUserMainRole($headers['langTag'], $headers['timezone']);
            $userList[] = array_merge($userProfile, $userMainRole);
        }

        $accountInfo = $account->getAccountInfo($headers['langTag'], $headers['timezone']);

        $item['connects'] = $account->getAccountConnects();
        $item['wallet'] = $account->getAccountWallet($headers['langTag']);
        $item['users'] = $userList;

        $userInteractive = InteractiveHelper::fresnsUserInteractive($headers['langTag']);

        $detail = array_merge($accountInfo, $item, $userInteractive);

        return $detail;
    }

    public function accountData(int $accountId)
    {
        $headers = AppHelper::getApiHeaders();

        $common['walletRecharges'] = ExtendUtility::getPluginExtends(1, null, null, $accountId, $headers['langTag']);
        $common['walletWithdraws'] = ExtendUtility::getPluginExtends(2, null, null, $accountId, $headers['langTag']);
        $data['commons'] = $common;

        $token['token'] = null;
        $token['expiredTime'] = null;
        $data['sessionToken'] = $token;

        $service = new AccountService();
        $data['detail'] = $service->accountDetail($accountId);

        return $data;
    }
}
