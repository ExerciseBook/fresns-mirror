<?php

namespace App\Fresns\Panel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = 'panel/dashboard';

    public function username()
    {
        return 'username';
    }

    protected function credentials(Request $request)
    {
        $username = $request->username;

        filter_var($username, FILTER_VALIDATE_EMAIL) ?
            $credentials['email'] = $username :
            $credentials['phone'] = $username;

        $credentials['password'] = $request->password;

        return $credentials;
    }


    public function showLoginForm()
    {
        $langs = config('panel.langs');
        $locale = \App::getLocale();
        return view('panel::auth.login', compact('langs', 'locale'));
    }
}
