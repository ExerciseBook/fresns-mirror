<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Web\Http\Controllers;

use Illuminate\Http\Request;

class MessageController extends Controller
{
    // index
    public function index(Request $request)
    {
        return view('messages.index');
    }

    // dialog
    public function dialog(Request $request, int $dialogId)
    {
        return view('messages.dialog');
    }

    // notify
    public function notify(Request $request, string $types)
    {
        return view('messages.notify');
    }
}
