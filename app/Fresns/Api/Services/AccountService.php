<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Services;

use App\Helpers\InteractiveHelper;
use App\Utilities\ExtendUtility;
use App\Models\Account;
use App\Models\PluginUsage;

class AccountService
{
    public function accountDetail(Account $account, string $langTag, string $timezone)
    {
        $headers = HeaderService::getHeaders();

        $userArr = $account->users;
        $userList = null;
        foreach ($userArr as $user) {
            $userProfile = $user->getUserProfile($langTag, $timezone);
            $userMainRole = $user->getUserMainRole($langTag, $timezone);
            $userList[] = array_merge($userProfile, $userMainRole);
        }

        $accountInfo = $account->getAccountInfo($langTag, $timezone);

        $item['connects'] = $account->getAccountConnects();
        $item['wallet'] = $account->getAccountWallet($langTag);
        $item['users'] = $userList;

        $userInteractive = InteractiveHelper::fresnsUserInteractive($langTag);

        $detail = array_merge($accountInfo, $item, $userInteractive);

        return $detail;
    }

    public function accountData(Account $account, string $langTag, string $timezone)
    {
        $common['walletRecharges'] = ExtendUtility::getPluginExtends(PluginUsage::TYPE_WALLET_RECHARGE, null, null, $account->id, $langTag);
        $common['walletWithdraws'] = ExtendUtility::getPluginExtends(PluginUsage::TYPE_WALLET_WITHDRAW, null, null, $account->id, $langTag);
        $data['commons'] = $common;

        $service = new AccountService();
        $data['detail'] = $service->accountDetail($account, $langTag, $timezone);

        return $data;
    }
}
