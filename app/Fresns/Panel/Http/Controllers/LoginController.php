<?php

namespace App\Fresns\Panel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function showLoginForm()
    {
        return view('panel::auth.login');
    }

    public function login()
    {
    }
}
