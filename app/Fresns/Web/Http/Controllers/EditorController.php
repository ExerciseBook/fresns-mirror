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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EditorController extends Controller
{
    // drafts
    public function drafts(Request $request, string $type)
    {
        $draftType = match ($type) {
            'posts' => 'post',
            'comments' => 'comment',
            'post' => 'post',
            'comment' => 'comment',
            default => 'post',
        };

        $query = $request->all();

        $result = ApiHelper::make()->get("/api/v2/editor/{$draftType}/drafts", [
            'query' => $query,
        ]);

        if ($result['code'] != 0) {
            throw new ErrorException($result['message'], $result['code']);
        }

        $drafts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        return view('editor.drafts', compact('drafts', 'type'));
    }

    // direct publish
    public function directPublish(Request $request)
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
                'name' => 'postGid',
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'contents' => $request->post('postGid'),
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

    // create or edit
    public function store(Request $request, string $type)
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

        return redirect()->to(fs_route(route('fresns.editor.edit', [$type, $response['data']['detail']['id']])));
    }

    // index
    public function index(string $type, ?string $postGid = null, ?string $commentPid = null, ?string $commentCid = null)
    {
        $type = match ($type) {
            'posts' => 'post',
            'comments' => 'comment',
            'post' => 'post',
            'comment' => 'comment',
            default => 'post',
        };

        $client = ApiHelper::make();

        $results = $client->handleUnwrap([
            'config' => $client->getAsync("/api/v2/editor/{$type}/config"),
            'drafts' => $client->getAsync("/api/v2/editor/{$type}/drafts"),
        ]);

        $config = $results['config']['data'];
        $drafts = $results['drafts']['data']['list'];

        if (count($drafts) === 0) {
            $response = ApiHelper::make()->post("/api/v2/editor/{$type}/create", [
                'json' => [
                    'createType' => 2,
                    'postGid' => $postGid,
                    'commentPid' => $commentPid,
                    'commentCid' => $commentCid,
                ]
            ])->toArray();

            if (data_get($response, 'code') !== 0) {
                throw new ErrorException($response['message']);
            }

            return redirect()->to(fs_route(route('fresns.editor.edit', [$type, $response['data']['detail']['id']])));
        }

        return view('editor.index', compact('type', 'config', 'drafts'));
    }

    // edit
    public function edit(Request $request, string $type, int $draftId)
    {
        $type = match ($type) {
            'posts' => 'post',
            'comments' => 'comment',
            'post' => 'post',
            'comment' => 'comment',
            default => 'post',
        };

        $draftInfo = self::getDraft($type, $draftId);

        $config = $draftInfo['config'];
        $stickers = $draftInfo['stickers'];
        $draft = $draftInfo['draft'];
        $group = data_get($draftInfo, 'group.detail') ?? data_get($draft,'detail.group.0');

        $plid = null; // post log id
        $clid = null; // comment log id
        if ($type == 'post') {
            $plid = $draftId;
        } else {
            $clid = $draftId;
        }

        return view('editor.edit', compact('type', 'plid', 'clid', 'config', 'stickers', 'draft', 'group'));
    }

    // publish
    public function publish(Request $request, string $type, int $draftId)
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
                'postGid' => $request->post('postGid'),
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

        if ($response['code'] == 38200) {
            return redirect()->to(fs_route(route('fresns.post.list')))->with('success', $response['message']);
        }

        if ($response['code'] !== 0) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return redirect()->to(fs_route(route('fresns.post.list')))->with('success', $response['message']);
    }

    // get draft
    public static function getDraft(string $type, ?int $draftId = null)
    {
        $client = ApiHelper::make();

        $params = [
            'config' => $client->getAsync("/api/v2/editor/{$type}/config"),
            'stickers' => $client->getAsync('/api/v2/global/stickers'),
        ];

        if ($draftId) {
            $params['draft'] = $client->getAsync("/api/v2/editor/{$type}/{$draftId}");
        }

        if ($gid = request('gid')) {
            $params['group'] = $client->getAsync("/api/v2/group/{$gid}/detail");
        }

        $results = $client->handleUnwrap($params);

        $draftInfo['config'] = data_get($results, 'config.data');
        $draftInfo['stickers'] = data_get($results, 'stickers.data');
        $draftInfo['draft'] = data_get($results, 'draft.data');
        $draftInfo['group'] = data_get($results, 'group.data');

        return $draftInfo;
    }

    public function delete(Request $request, string $type, string $draftId)
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

        if ($type === 'post') {
            return redirect()->to(fs_route(route('fresns.post.list')))->with('success', $response['message']);
        }

        if ($type === 'comment') {
            return redirect()->to(fs_route(route('fresns.comment.list')))->with('success', $response['message']);
        }
    }
}
