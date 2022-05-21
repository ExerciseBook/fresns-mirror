<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\Controllers;

use App\Helpers\AppHelper;
use App\Helpers\InteractiveHelper;
use App\Models\Account;
use App\Utilities\ExpandUtility;
use App\Exceptions\ApiException;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function detail()
    {
        $headers = AppHelper::getApiHeaders();

        $account = Account::whereAid($headers['aid'])->first();
        if (empty($account)) {
            throw new ApiException(31502);
        }

        $common['walletRecharges'] = ExpandUtility::getPluginExpands(1, null, null, $account->id, $headers['langTag']);
        $common['walletWithdraws'] = ExpandUtility::getPluginExpands(2, null, null, $account->id, $headers['langTag']);
        $data['commons'] = $common;

        $userArr = $account->users;
        $userList = [];
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

        $data['detail'] = array_merge($accountInfo, $item, $userInteractive);

        return $this->success($data);
    }
}
