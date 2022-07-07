<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Web\Http\Controllers;

use Illuminate\Http\Request;

class AccountController extends Controller
{
    // register
    public function register(Request $request)
    {
        return view('account.register');
    }

    // login
    public function login(Request $request)
    {
        return view('account.login');
    }

    // reset password
    public function resetPassword(Request $request)
    {
        return view('account.reset-password');
    }

    // index
    public function index()
    {
        return view('account.index');
    }

    // wallet
    public function wallet()
    {
        return view('account.wallet');
    }

    // users
    public function users()
    {
        return view('account.users');
    }

    // settings
    public function settings()
    {
        return view('account.settings');
    }
}
