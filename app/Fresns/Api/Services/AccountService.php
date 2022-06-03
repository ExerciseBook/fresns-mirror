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
use App\Models\PluginUsage;

class AccountService
{
    public function accountDetail(Account $account)
    {
        $headers = AppHelper::getApiHeaders();

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

    public function accountData(Account $account)
    {
        $headers = AppHelper::getApiHeaders();

        $common['walletRecharges'] = ExtendUtility::getPluginExtends(PluginUsage::TYPE_WALLET_RECHARGE, null, null, $account->id, $headers['langTag']);
        $common['walletWithdraws'] = ExtendUtility::getPluginExtends(PluginUsage::TYPE_WALLET_WITHDRAW, null, null, $account->id, $headers['langTag']);
        $data['commons'] = $common;

        $service = new AccountService();
        $data['detail'] = $service->accountDetail($account);

        return $data;
    }
}
