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


    public function publish(Request $request)
    {
        $validator = Validator::make($request->post(),
            [
                'type' => 'required',
                'content' => 'required',
                'postGid' => fs_db_config('post_editor_group_required') ? 'required' : 'nullable',
                'postTitle' => fs_db_config('post_editor_title_required') ? 'required' : 'nullable',
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
        try {
            $result = ApiHelper::make()->post("/api/v2/editor/direct-publish", [
                'multipart' => $multipart
            ]);

            if ($result['code'] != 0) {
                throw new ErrorException($result['message'], $result['code']);
            }

            return back()->with('success', $result['message']);

        } catch (\Exception $exception) {
            return back()->withErrors($exception->getMessage());
        }
    }
}
