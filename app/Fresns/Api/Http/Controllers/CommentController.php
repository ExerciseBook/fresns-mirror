<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\Controllers;

use App\Helpers\AppHelper;
use App\Helpers\InteractiveHelper;
use App\Models\Comment;
use App\Exceptions\ApiException;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function detail(string $cid, Request $request)
    {
        $headers = AppHelper::getApiHeaders();
        $user = ! empty($headers['uid']) ? User::whereUid($headers['uid'])->first() : null;

        $comment = Comment::with('creator')->whereCid($cid)->first();
        if (empty($comment)) {
            throw new ApiException(37400);
        }
    }
}
