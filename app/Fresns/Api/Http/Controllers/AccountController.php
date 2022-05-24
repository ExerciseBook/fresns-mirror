<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\Controllers;

use App\Helpers\AppHelper;
use App\Fresns\Api\Services\AccountService;
use App\Utilities\ExtendUtility;
use App\Exceptions\ApiException;
use App\Helpers\PrimaryHelper;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function detail()
    {
        $headers = AppHelper::getApiHeaders();

        $accountId = PrimaryHelper::fresnsAccountIdByAid($headers['aid']);
        if (empty($account)) {
            throw new ApiException(31502);
        }

        $common['walletRecharges'] = ExtendUtility::getPluginExtends(1, null, null, $accountId, $headers['langTag']);
        $common['walletWithdraws'] = ExtendUtility::getPluginExtends(2, null, null, $accountId, $headers['langTag']);
        $data['commons'] = $common;

        $service = new AccountService();
        $data['detail'] = $service->accountDetail($accountId);

        return $this->success($data);
    }
}
