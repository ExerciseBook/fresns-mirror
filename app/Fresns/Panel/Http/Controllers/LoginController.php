<?php

namespace App\Fresns\Panel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        \View::share('langs', config('panel.langs'));
        $this->redirectTo = route('panel.dashboard');
    }

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

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        // check user_type
        $user = $this->guard()->getProvider()->retrieveByCredentials($this->credentials($request));
        if (!$user || $user->user_type != 1) {
            return false;
        }

        return $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        );
    }

    public function showLoginForm()
    {
        return view('panel::auth.login');
    }

    public function loggedOut(Request $request)
    {
        return redirect(route('panel.login.form'));
    }
}
