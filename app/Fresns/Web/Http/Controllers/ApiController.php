<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Web\Http\Controllers;

use App\Fresns\Web\Helpers\ApiHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;

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
        $result = ApiHelper::make()->post('/api/v2/account/login', [
            'json' => [
                'type' => $request->type,
                'account' => $request->{$request->type},
                'countryCode' => $request->countryCode ?? null,
                'password' => $request->password ?? null,
                'verifyCode' => $request->verifyCode ?? null,
                'deviceToken' => $request->deviceToken ?? null,
            ],
        ]);

        // api data
        $data = $result['data'];

        $users = $data['detail']['users']->toArray();
        // 用户数量
        $userCount = count($users);

        // 账号登录
        Cookie::queue('fs_aid', $data['detail']['aid']);
        Cookie::queue('fs_token', $data['sessionToken']['token']);


        // 用户登录处理


        // 只有一个用户，用户没有密码
        if ($userCount == 1) {
            $user = current($users);

            // 用户没有密码
            if ($user['hasPassword'] === false) {
                // 自动完成用户登录
                Cookie::queue('fs_uid', $user['uid']);
                Cookie::queue('timezone', $user['timezone']);

                return redirect()->intended(fs_route(route('fresns.account.index')));
            }
            // 用户有密码
            else {
                // 用户有密码的操作，自动弹出输入密码
                // 弹窗逻辑写在 header.blade.php
                return redirect()->intended(fs_route(route('fresns.account.login')));
            }
        } 
        // 有 2 个以上用户
        else if($userCount > 1) {
            // 有 2 个以上用户的操作，自动弹出选择用户
            // 弹窗逻辑写在 header.blade.php
            return redirect()->intended(fs_route(route('fresns.account.login')));
        } 
        // 没有用户
        else {
            return back()->with([
                'failure' => '抱歉，您当前没有绑定用户信息',
            ]);
        }
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
        $result = ApiHelper::make()->post('/api/v2/user/auth', [
            'json' => [
                'uidOrUsername' => $request->uidOrUsername,
                'password' => $request->password ?? null,
                'deviceToken' => $request->deviceToken ?? null,
            ],
        ]);

        Cookie::queue('token', $result['data.sessionToken.token']);
        Cookie::queue('uid', $result['data.detail.uid']);
        Cookie::queue('timezone', $result['data.detail.timezone']);

        return redirect()->intended(fs_route(route('fresns.account.index')));
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
