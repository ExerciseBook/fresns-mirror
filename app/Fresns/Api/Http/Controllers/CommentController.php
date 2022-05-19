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
use App\Utilities\ConfigUtility;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function detail(string $cid, Request $request)
    {
        $headers = AppHelper::getApiHeaders();

        $comment = Comment::with('creator')->whereCid($cid)->first();
        if (empty($comment)) {
            return $this->failure(
                37400,
                ConfigUtility::getCodeMessage(37400, 'Fresns', $headers['langTag'])
            );
        }
    }
}
