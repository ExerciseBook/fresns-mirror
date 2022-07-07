<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Web\Http\Controllers;

use App\Fresns\Web\Helpers\ApiHelper;
use Illuminate\Http\Request;

class EditorController extends Controller
{
    // drafts
    public function drafts(Request $request, string $type)
    {
        return view('editor.drafts');
    }

    // post
    public function post(Request $request, int $draftId)
    {
        $type = 'post';

        $stickersResult = ApiHelper::make()->get('/api/v2/global/stickers');
        $stickers = $stickersResult['data'];

        return view('editor.editor', compact('stickers', 'type', 'draftId'));
    }

    // comment
    public function comment(Request $request, int $draftId)
    {
        $type = 'comment';

        $result = ApiHelper::make()->get('/api/v2/global/stickers');

        $stickers = $result['data'];

        return view('editor.editor', compact('stickers', 'type', 'draftId'));
    }
}
