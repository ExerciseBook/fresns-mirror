<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Web\Http\Controllers;

use Illuminate\Http\Request;

class HashtagController extends Controller
{
    // index
    public function index(Request $request)
    {
        return view('hashtags.index');
    }

    // list
    public function list(Request $request)
    {
        return view('hashtags.list');
    }

    // likes
    public function likes(Request $request)
    {
        return view('hashtags.likes');
    }

    // dislikes
    public function dislikes(Request $request)
    {
        return view('hashtags.dislikes');
    }

    // following
    public function following(Request $request)
    {
        return view('hashtags.following');
    }

    // blocking
    public function blocking(Request $request)
    {
        return view('hashtags.blocking');
    }

    // detail
    public function detail(Request $request, string $hid)
    {
        return view('hashtags.detail');
    }
}
