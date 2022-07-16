<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Web\Http\Controllers;

use App\Fresns\Web\Helpers\ApiHelper;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    // send verify code
    public function sendVerifyCode(Request $request)
    {
    }

    // download link
    public function downloadLink(Request $request)
    {
    }

    // account register
    public function accountRegister(Request $request)
    {
        return ApiHelper::make()->post('/api/v2/account/register', [
            'json' => [
                'type' => $request->type,
                'account' => $request->account,
                'countryCode' => $request->countryCode ?? null,
                'verifyCode' => $request->verifyCode,
                'password' => $request->password,
                'nickname' => $request->nickname,
                'deviceToken' => $request->deviceToken ?? null,
            ],
        ]);
    }

    // account login
    public function accountLogin(Request $request)
    {
        return ApiHelper::make()->post('/api/v2/account/login', [
            'json' => [
                'type' => $request->type,
                'account' => $request->{$request->type},
                'countryCode' => $request->countryCode ?? null,
                'password' => $request->password ?? null,
                'verifyCode' => $request->verifyCode ?? null,
                'deviceToken' => $request->deviceToken ?? null,
            ],
        ]);
    }

    // account reset password
    public function resetPassword(Request $request)
    {
    }

    // account logout
    public function logout(Request $request)
    {
        fs_account()->logout();

        return ApiHelper::make()->delete('/api/v2/account/logout');
    }

    // user auth
    public function userAuth(Request $request)
    {
        return ApiHelper::make()->post('/api/v2/user/auth', [
            'json' => [
                'uidOrUsername' => $request->uidOrUsername,
                'password' => $request->password ?? null,
                'deviceToken' => $request->deviceToken ?? null,
            ],
        ]);
    }

    // user mark
    public function userMark(Request $request)
    {
    }

    // user mark note
    public function userMarkNote(Request $request)
    {
    }

    // post delete
    public function postDelete(string $pid)
    {
        return ApiHelper::make()->delete("/api/v2/post/{$pid}");
    }

    // comment delete
    public function commentDelete(string $cid)
    {
        return ApiHelper::make()->delete("/api/v2/comment/{$cid}");
    }
}
