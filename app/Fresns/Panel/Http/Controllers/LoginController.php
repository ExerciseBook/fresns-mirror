<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Panel\Http\Controllers;

use Browser;
use App\Helpers\AppHelper;
use App\Models\SessionLog;
use Illuminate\Http\Request;
use App\Utilities\AppUtility;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $loginLimit = false;

    public function __construct()
    {
        \View::share('langs', config('FsConfig.langs'));
        try {
            $this->redirectTo = route('panel.dashboard');
        } catch (\Exception $e) {
            $this->redirectTo = 'dashboard';
        }
    }

    public function username()
    {
        return 'accountName';
    }

    protected function credentials(Request $request)
    {
        $accountName = $request->accountName;

        filter_var($accountName, FILTER_VALIDATE_EMAIL) ?
            $credentials['email'] = $accountName :
            $credentials['phone'] = $accountName;

        $credentials['password'] = $request->password;

        return $credentials;
    }

    /**
     * Attempt to log the account into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        // check account type
        $account = $this->guard()->getProvider()->retrieveByCredentials($this->credentials($request));

        if (! $account || $account->type != 1) {
            $result = false;
        } else {
            $result = $this->guard()->attempt(
                $this->credentials($request), $request->filled('remember')
            );
        }

        // login session log
        if ($account) {
            $loginCount = SessionLog::where('account_id', $account->id)
                ->where('object_result', 1)
                ->where('created_at', '>=', now()->subHour())
                ->count();

            if ($loginCount >= 5) {
                $this->loginLimit = true;
                return false;
            }

            $langTag = \request()->header('langTag', config('app.locale'));
            $deviceInfo = AppUtility::getDeviceInfo();
            $wordBody = [
                'pluginUnikey' => 'Fresns',
                'platform' => Browser::isMobile() ? 3 : 2,
                'version' => AppHelper::VERSION,
                'langTag' => $langTag,
                'aid' => (string)$account->aid, //凭账号查询到的账号表 aid
                'uid' => null,
                'objectType' => 2,
                'objectName' => self::class,
                'objectAction' => 'Panel Login',
                'objectResult' => $result ? 2 : 1, //登录成功或失败
                'objectOrderId' => null,
                'deviceInfo' => json_encode($deviceInfo),
                'deviceToken' => null,
                'moreJson' => null,
            ];
            \FresnsCmdWord::plugin('Fresns')->uploadSessionLog($wordBody);
        }

        return $result;

    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $error = trans('auth.failed');
        if ($this->loginLimit) {
            $error = trans('FsLang::tips.account_login_limit');
        }
        return back()->withErrors([
            $this->username() => [$error],
        ]);
    }

    public function showLoginForm()
    {
        return view('FsView::auth.login');
    }

    public function loggedOut(Request $request)
    {
        return redirect(route('panel.empty', 'empty'));
    }

    public function emptyPage()
    {
        return view('FsView::auth.empty');
    }
}
