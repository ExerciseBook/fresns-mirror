<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Panel\Http\Controllers;

use Illuminate\Http\Request;

class IframeController extends Controller
{
    public function settings(Request $request)
    {
        $url = $request->url;

        return view('FsView::extensions.iframe', compact('url'));
    }

    public function marketplace(Request $request)
    {
        $url = $request->url;

        return view('FsView::extensions.iframe', compact('url'));
    }
}
