<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Web\Http\Controllers;

use App\Fresns\Web\Exceptions\ErrorException;
use App\Fresns\Web\Helpers\ApiHelper;
use App\Fresns\Web\Helpers\QueryHelper;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

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

    // url sign
    public function urlSign()
    {
        $headers = Arr::except(ApiHelper::getHeaders(), ['Accept']);

        $sign = urlencode(base64_encode(json_encode($headers)));

        return \response()->json([
            'code' => 0,
            'message' => 'ok',
            'data' => [
                'sign' => $sign,
            ]
        ]);
    }

    // get input tips
    public function getInputTips(Request $request): JsonResponse
    {
        if ($request->get('type') &&  $request->get('key')) {
            $result = ApiHelper::make()->get("/api/v2/common/input-tips", [
                'query' => [
                    'type' => $request->get('type'),
                    'key' => $request->get('key'),
                ]
            ]);

            if (data_get($result, 'code') !== 0) {
                throw new ErrorException($result['message'], $result['code']);
            }

            return Response::json(data_get($result->toArray(), 'data'));
        }

        return Response::json();
    }

    // send verify code
    public function sendVerifyCode(Request $request)
    {
        if (\request('useType') == 4) {
            \request()->offsetSet('account', 'fresns_random_string:'.uniqid());
        }

        if (empty(\request('countryCode'))) {
            \request()->offsetSet('countryCode', fs_account()->get('detail.countryCode'));
        }

        if (empty(\request('phone'))) {
            \request()->offsetSet('phone', fs_account()->get('detail.phone'));
        }

        $response = ApiHelper::make()->post('/api/v2/common/send-verify-code', [
            'json' => \request()->all(),
        ]);

        return \response()->json($response->toArray());
    }

    // account register
    public function accountRegister(Request $request)
    {
        $response = ApiHelper::make()->post('/api/v2/account/register', [
            'json' => [
                'type' => $request->type,
                'account' => $request->{$request->type},
                'countryCode' => $request->countryCode ?? null,
                'verifyCode' => $request->verifyCode,
                'password' => $request->password,
                'nickname' => $request->nickname,
                'deviceToken' => $request->deviceToken ?? null,
            ],
        ]);

        return \response()->json($response->toArray());
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
                \request()->offsetSet('fs_aid', $data['detail']['aid']);
                \request()->offsetSet('fs_aid_token', $data['sessionToken']['token']);

                $userResult = ApiHelper::make()->post('/api/v2/user/auth', [
                    'json' => [
                        'uidOrUsername' => strval($user['uid']),
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
    public function accountResetPassword(Request $request)
    {
        if (\request('password') !== \request('password_confirmation')) {
            return \response()->json([
                'code' => 30000,
                'message' => fs_api_config('passwordAgainError'),
                'data' => null,
            ]);
        }

        $response = ApiHelper::make()->put('/api/v2/account/reset-password', [
            'json' => [
                'type' => $request->type,
                'account' => $request->{$request->type},
                'countryCode' => $request->countryCode ?? null,
                'verifyCode' => $request->verifyCode ?? null,
                'newPassword' => $request->password ?? null,
            ],
        ]);

        return \response()->json($response->toArray());
    }

    // account verify identity
    public function accountVerifyIdentity(Request $request)
    {
        $response = ApiHelper::make()->post('/api/v2/account/verify-identity', [
            'json' => \request()->all(),
        ]);

        return \response()->json($response->toArray());
    }

    // account edit
    public function accountEdit()
    {
        if ($editType = \request('edit_type')) {
            $editTypeMode = \request($editType.'_mode');

            $codeType = match($editTypeMode) {
                default => null,
                'phone_to_editPassword' => 'sms',
                'email_to_editPassword' => 'email',
            };

            $verifyCode = match($editTypeMode) {
                default => null,
                'phone_to_editPassword' => \request('phone_verifyCode'),
                'email_to_editPassword' => \request('email_verifyCode'),
            };

            \request()->offsetSet('codeType', $codeType);
            \request()->offsetSet('verifyCode', $verifyCode);
        }

        switch ($editType) {
            case 'editPassword':
                \request()->offsetSet('password', \request('now_editPassword'));
                \request()->offsetSet('editPassword', \request('new_editPassword'));
                \request()->offsetSet('editPasswordConfirm', \request('new_editPassword_confirmation'));
                break;
            case 'editWalletPassword':
                \request()->offsetSet('editWalletPassword', \request('new_editWalletPassword'));
                \request()->offsetSet('editWalletPasswordConfirm', \request('new_editWalletPassword_confirmation'));
                break;
        }

        $response = ApiHelper::make()->put('/api/v2/account/edit', [
            'json' => \request()->all(),
        ]);

        return \response()->json($response->toArray());
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

    // user edit
    public function userEdit()
    {
        $response = ApiHelper::make()->put('/api/v2/user/edit', [
            'json' => \request()->all(),
        ]);

        return \response()->json($response->toArray());
    }

    // user mark
    public function userMark(Request $request)
    {
        $response = ApiHelper::make()->post('/api/v2/user/mark', [
            'json' => \request()->all(),
        ]);

        return \response()->json($response->toArray());
    }

    // user mark note
    public function userMarkNote(Request $request)
    {
    }

    // upload file
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

    // download link
    public function downloadLink(Request $request)
    {
    }

    // post delete
    public function postDelete(string $pid)
    {
        $response = ApiHelper::make()->delete("/api/v2/post/{$pid}");

        if ($response['code'] !== 0) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return back()->with('success', $response['message']);
    }

    // comment delete
    public function commentDelete(string $cid)
    {
        $response = ApiHelper::make()->delete("/api/v2/comment/{$cid}");

        if ($response['code'] !== 0) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return back()->with('success', $response['message']);
    }

    /**
     * @param  string  $gid
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function groupList(string $gid):JsonResponse
    {
        $response = ApiHelper::make()->get('/api/v2/group/list', [
            'query' => [
                'gid' => $gid
            ],
        ]);

        return Response::json(data_get($response->toArray(), 'data.list', []));
    }

    // editor direct publish
    public function editorDirectPublish(Request $request)
    {
        $validator = Validator::make($request->post(),
            [
                'type' => 'required',
                'content' => 'required',
                'postGid' => ($request->post('type') === 'post' && fs_api_config('post_editor_group_required')) ? 'required' : 'nullable',
                'postTitle' => ($request->post('type') === 'post' && fs_api_config('post_editor_title_required')) ? 'required' : 'nullable',
            ]
        );

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $multipart = [
            [
                'name' => 'type',
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'contents' => $request->post('type'),
            ],
            [
                'name' => 'postTitle',
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'contents' => $request->post('postTitle'),
            ],
            [
                'name' => 'content',
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'contents' => $request->post('content'),
            ],
            [
                'name' => 'isAnonymous',
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'contents' => (bool) $request->post('anonymous', false),
            ],
            [
                'name' => 'postGid',
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'contents' => $request->post('gid'),
            ],
            [
                'name' => 'commentPid',
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'contents' => $request->post('commentPid'),
            ],
            [
                'name' => 'commentCid',
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'contents' => $request->post('commentCid'),
            ],
        ];
        if ($request->file('file')) {
            $multipart[] = [
                'name' => 'file',
                'filename' => $request->file('file')->getClientOriginalName(),
                'contents' => $request->file('file')->getContent(),
                'headers' => ['Content-Type' => $request->file('file')->getClientMimeType()],
            ];
        }

        $result = ApiHelper::make()->post("/api/v2/editor/direct-publish", [
            'multipart' => array_filter($multipart, fn($val) => isset($val['contents']))
        ]);

        if ($result['code'] !== 0) {
            throw new ErrorException($result['message']);
        }

        return back()->with('success', $result['message']);
    }

    // editor create or edit
    public function editorStore(Request $request, string $type)
    {
        $type = match ($type) {
            'posts' => 'post',
            'comments' => 'comment',
            'post' => 'post',
            'comment' => 'comment',
            default => 'post',
        };
        $fsid = $request->input('fsid');

        if ($fsid) {
            $response = ApiHelper::make()->post("/api/v2/editor/{$type}/generate/{$fsid}")->toArray();
        } else {
            $response = ApiHelper::make()->post("/api/v2/editor/{$type}/create", [
                'json' => [
                    'createType' => 2,
                    'editorUnikey' => $request->input('editorUnikey'),
                    'postGid' => $request->input('postGid'),
                    'postTitle' => $request->input('postTitle'),
                    'postIsComment' => $request->input('postIsComment'),
                    'postIsCommentPublic' => $request->input('postIsCommentPublic'),
                    'content' => $request->input('content'),
                    'isMarkdown' => $request->input('isMarkdown'),
                    'isAnonymous' => $request->input('anonymous'),
                    'mapJson' => $request->input('mapJson'),
                    'eid' => $request->input('eid'),
                ]
            ])->toArray();
        }

        if (data_get($response, 'code') !== 0) {
            throw new ErrorException($response['message']);
        }

        return redirect()->route('fresns.editor.edit', [$type, $response['data']['detail']['id']]);
    }

    // editor upload file
    public function editorUploadFile(Request $request)
    {
        $multipart = [
            [
                'name' => 'type',
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'contents' => $request->post('type'),
            ],
            [
                'name' => 'usageType',
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'contents' => $request->post('usageType'),
            ],
            [
                'name' => 'tableId',
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'contents' => $request->post('tableId'),
            ],
            [
                'name' => 'uploadMode',
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'contents' => $request->post('uploadMode'),
            ],
            [
                'name' => 'tableName',
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'contents' => $request->post('tableName'),
            ],
            [
                'name' => 'tableColumn',
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'contents' => $request->post('tableColumn', 'id'),
            ],
        ];


        if (! $request->file('files')) {
            return Response::json([]);
        }

        $postAsyncs = [];
        foreach ($request->file('files') as $key => $file) {
            $params = $multipart;
            $params[] = [
                'name' => 'file',
                'filename' => $file->getClientOriginalName(),
                'contents' => $file->getContent(),
                'headers' => ['Content-Type' => $file->getClientMimeType()],
            ];
            $postAsyncs[] = ApiHelper::make()->postAsync('/api/v2/common/upload-file', [
                'multipart' => array_filter($params, fn($val) => isset($val['contents']))
            ]);
        }

        $results = ApiHelper::make()->handleUnwrap($postAsyncs)->toArray();

        return Response::json(array_filter(array_map(fn($arr) => data_get($arr, 'data'), $results), fn($arr) => data_get($arr, 'code') !== 0));
    }

    // editor update
    public function editorUpdate(Request $request, string $type, int $draftId)
    {
        $type = match ($type) {
            'posts' => 'post',
            'comments' => 'comment',
            'post' => 'post',
            'comment' => 'comment',
            default => 'post',
        };

        $response = ApiHelper::make()->put("/api/v2/editor/{$type}/{$draftId}", [
            'json' => [
                'postGid' => $request->post('gid'),
                'postTitle' => $request->post('postTitle'),
                'postIsComment' => $request->post('postIsComment'),
                'postIsCommentPublic' => $request->post('postIsCommentPublic'),
                'content' => $request->post('content'),
                'isMarkdown' => $request->post('isMarkdown'),
                'isAnonymous' => $request->post('anonymous'),
                'mapJson' => $request->post('mapJson'),
                'deleteMap' => $request->post('deleteMap'),
                'deleteFile' => $request->post('deleteFile'),
                'deleteExtend' => $request->post('deleteExtend'),
            ]
        ]);

        if ($response['code'] !== 0) {
            throw new ErrorException($response['message'], $response['code']);
        }

        $response = ApiHelper::make()->post("/api/v2/editor/{$type}/{$draftId}");

        if ($response['code'] !== 0) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return redirect()->route('fresns.post.list')->with('success', $response['message']);
    }

    // editor recall
    public function editorRecall(string $type, string $draftId)
    {
        $type = match ($type) {
            'posts' => 'post',
            'comments' => 'comment',
            'post' => 'post',
            'comment' => 'comment',
            default => 'post',
        };

        $response = ApiHelper::make()->patch("/api/v2/editor/{$type}/{$draftId}");

        if ($response['code'] !== 0) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return back()->with('success', $response['message']);
    }

    // editor delete
    public function editorDelete(string $type, string $draftId)
    {
        $type = match ($type) {
            'posts' => 'post',
            'comments' => 'comment',
            'post' => 'post',
            'comment' => 'comment',
            default => 'post',
        };

        $response = ApiHelper::make()->delete("/api/v2/editor/{$type}/{$draftId}");

        if ($response['code'] !== 0) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return back()->with('success', $response['message']);
    }
}
