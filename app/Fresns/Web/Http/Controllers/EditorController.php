<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Web\Http\Controllers;

use App\Fresns\Web\Exceptions\ErrorException;
use App\Fresns\Web\Helpers\ApiHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class EditorController extends Controller
{
    // drafts
    public function drafts(string $type)
    {
        $type = match ($type) {
            'posts' => 'post',
            'comments' => 'comment',
            'post' => 'post',
            'comment' => 'comment',
            default => 'post',
        };

        $clid = null;

        // 获取草稿列表
        $drafts = Arr::get(ApiHelper::make()->get("/api/v2/editor/{$type}/drafts")->toArray(), 'data.list');

        if (count($drafts) === 0) {
            $response = ApiHelper::make()->post("/api/v2/editor/{$type}/create", [
                'json' => [
                    'createType' => 2,
                ]
            ])->toArray();

            if (data_get($response, 'code') !== 0) {
                throw new ErrorException($response['message']);
            }

            return redirect()->route('fresns.editor.draft.edit', [$type, $response['data']['detail']['id']]);

        }

        $draftInfo = self::getDraft($type);

        $config = $draftInfo['config'];
        $stickers = $draftInfo['stickers'];

        return view('editor.drafts', compact('drafts','type', 'clid', 'config', 'stickers'));

    }


    public function publish(Request $request)
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


    public function storeDraft(string $type, Request $request)
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
                ]
            ])->toArray();
        }

        if (data_get($response, 'code') !== 0) {
            throw new ErrorException($response['message']);
        }

        return redirect()->route('fresns.editor.draft.edit', [$type, $response['data']['detail']['id']]);

    }

    public function editDraft(Request $request, string $type, int $draftId)
    {
        $type = match ($type) {
            'posts' => 'post',
            'comments' => 'comment',
            'post' => 'post',
            'comment' => 'comment',
            default => 'post',
        };

        $clid = null;

        $plid = $draftId;

        $draftInfo = self::getDraft($type, $draftId);

        $config = $draftInfo['config'];
        $stickers = $draftInfo['stickers'];
        $draft = $draftInfo['draft'];

        return view('editor.editor', compact('draft','type', 'clid', 'plid', 'config', 'stickers'));
    }

    public function updateDraft(Request $request, string $type, int $draftId)
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
                'content' => $request->post('content'),
                'isAnonymous' => $request->post('anonymous')
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

    public function destroyDraft(Request $request, string $type, string $draftId)
    {
        $type = match ($type) {
            'posts' => 'post',
            'comments' => 'comment',
            'post' => 'post',
            'comment' => 'comment',
            default => 'post',
        };

        $response = ApiHelper::make()->delete("/api/v2/editor/$type/{$draftId}");

        if ($response['code'] !== 0) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return back()->with('success', $response['message']);
    }

    public function destroy(Request $request, string $type, string $draftId)
    {
        $type = match ($type) {
            'posts' => 'post',
            'comments' => 'comment',
            'post' => 'post',
            'comment' => 'comment',
            default => 'post',
        };

        $response = ApiHelper::make()->delete("/api/v2/$type/{$draftId}");

        if ($response['code'] !== 0) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return back()->with('success', $response['message']);
    }


    public static function getDraft(string $type, ?int $draftId = null)
    {
        $client = ApiHelper::make();

        if (empty($draftId)) {
            $results = $client->handleUnwrap([
                'config' => $client->getAsync("/api/v2/editor/{$type}/config"),
                'stickers' => $client->getAsync('/api/v2/global/stickers'),
            ]);

            $draftInfo['draft'] = null;
        } else {
            $results = $client->handleUnwrap([
                'config' => $client->getAsync("/api/v2/editor/{$type}/config"),
                'stickers' => $client->getAsync('/api/v2/global/stickers'),
                'draft' => $client->getAsync("/api/v2/editor/post/{$draftId}"),
            ]);

            if ($results['draft']['code'] !== 0) {
                throw new ErrorException($results['draft']['message'], $results['draft']['code']);
            }

            $draftInfo['draft'] = $results['draft']['data'];
        }

        $draftInfo['config'] = $results['config']['data'];
        $draftInfo['stickers'] = $results['stickers']['data'];

        return $draftInfo;
    }

}
