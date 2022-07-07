<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Web\Http\Controllers;

use App\Fresns\Web\Helpers\ApiHelper;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    // index
    public function index(Request $request)
    {
        $result = ApiHelper::make()->get('/api/v2/group/tree');

        $groupTree = $result['data'];

        return view('groups.index', compact('groupTree'));
    }

    // list
    public function list(Request $request)
    {
        return view('groups.list');
    }

    // likes
    public function likes(Request $request)
    {
        return view('groups.likes');
    }

    // dislikes
    public function dislikes(Request $request)
    {
        return view('groups.dislikes');
    }

    // following
    public function following(Request $request)
    {
        return view('groups.following');
    }

    // blocking
    public function blocking(Request $request)
    {
        return view('groups.blocking');
    }

    // detail
    public function detail(Request $request, string $gid)
    {
        return view('groups.detail');
    }
}
