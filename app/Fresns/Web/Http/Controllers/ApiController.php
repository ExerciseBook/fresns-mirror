<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Web\Http\Controllers;

use App\Fresns\Web\Helpers\ApiHelper;
use App\Fresns\Web\Helpers\QueryHelper;
use App\Utilities\ConfigUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\UploadedFile;

class ApiController extends Controller
{
    // top list
    public function topList()
    {
        $userQuery = QueryHelper::configToQuery(QueryHelper::TYPE_USER);
        $groupQuery = QueryHelper::configToQuery(QueryHelper::TYPE_GROUP);
        $hashtagQuery = QueryHelper::configToQuery(QueryHelper::TYPE_HASHTAG);
        $postQuery = QueryHelper::configToQuery(QueryHelper::TYPE_POST);
        $commentQuery = QueryHelper::configToQuery(QueryHelper::TYPE_COMMENT);

        $client = ApiHelper::make();

        $results = $client->handleUnwrap([
            'users' => $client->getAsync('/api/v2/user/list', [
                'query' => $userQuery,
            ]),
            'groups' => $client->getAsync('/api/v2/group/list', [
                'query' => $groupQuery,
            ]),
            'hashtags' => $client->getAsync('/api/v2/hashtag/list', [
                'query' => $hashtagQuery,
            ]),
            'posts' => $client->getAsync('/api/v2/post/list', [
                'query' => $postQuery,
            ]),
            'comments' => $client->getAsync('/api/v2/comment/list', [
                'query' => $commentQuery,
            ]),
        ]);

        $data['users'] = $results['users']['data']['list'];
        $data['groups'] = $results['groups']['data']['list'];
        $data['hashtags'] = $results['hashtags']['data']['list'];
        $data['posts'] = $results['posts']['data']['list'];
        $data['comments'] = $results['comments']['data']['list'];

        return $data;
    }

    public function uploadFile()
    {
        $multipart = [];

        foreach (\request()->file() as $name => $file) {
            if ($file instanceof UploadedFile) {
                /** @var UploadedFile $file */
                $multipart[] = [
                    'name' => $name,
                    'filename' => $file->getClientOriginalName(),
                    'contents' => $file->getContent(),
                    'headers' => ['Content-Type' => $file->getClientMimeType()],
                ];
            }
        }

        foreach (\request()->post() as $name => $contents) {
            $headers = ['Content-Type' => 'application/x-www-form-urlencoded'];
            $multipart[] = compact('name', 'contents', 'headers');
        }

        $response = ApiHelper::make()->post('/api/v2/common/upload-file', [
            'multipart' => $multipart,
        ]);

        return \response()->json($response->toArray());
    }

    public function userEdit()
    {
        $response = ApiHelper::make()->put('/api/v2/account/edit', [
            'json' => \request()->all(),
        ]);

        return \response()->json($response->toArray());
    }

    // url sign
    public function urlSign()
    {
        $headers = Arr::except(ApiHelper::getHeaders(), ['Accept']);

        return urlencode(base64_encode(json_encode($headers)));
    }

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

        if ($result['code'] != 0) {
            return back()->with([
                'code' => $result['code'],
                'failure' => $result['message'],
            ]);
        }

        // api data
        $data = $result['data'];

        // Account Login
        Cookie::queue('fs_aid', $data['detail']['aid']);
        Cookie::queue('fs_aid_token', $data['sessionToken']['token']);

        // Number of users under the account
        $users = $data['detail']['users']->toArray();
        $userCount = count($users);

        // Only one user and no password
        if ($userCount == 1) {
            $user = $users[0];

            if ($user['hasPassword']) {
                // User has password
                // header.blade.php
                return redirect()->intended(fs_route(route('fresns.account.login')));
            } else {
                // User does not have a password
                $userResult = ApiHelper::make()->post('/api/v2/user/auth', [
                    'json' => [
                        'uidOrUsername' => $user['uid'],
                        'password' => null,
                        'deviceToken' => $request->deviceToken ?? null,
                    ],
                ]);

                Cookie::queue('fs_uid', $userResult['data.detail.uid']);
                Cookie::queue('fs_uid_token', $userResult['data.sessionToken.token']);
                Cookie::queue('timezone', $userResult['data.detail.timezone']);

                return redirect()->intended(fs_route(route('fresns.account.index')));
            }
        } elseif ($userCount > 1) {
            // There are more than one user
            // header.blade.php
            return redirect()->intended(fs_route(route('fresns.account.login')));
        }
    }

    // account reset password
    public function resetPassword(Request $request)
    {
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

        Cookie::queue('fs_uid', $result['data.detail.uid']);
        Cookie::queue('fs_uid_token', $result['data.sessionToken.token']);
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
