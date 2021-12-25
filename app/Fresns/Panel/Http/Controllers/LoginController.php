<?php

namespace App\Fresns\Panel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function login()
    {
        return view('admin::foo');
    }
}
