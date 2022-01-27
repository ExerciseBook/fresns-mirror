<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Fresns\Panel\Http\Requests\StoreAdminRequest;

class AdminController extends Controller
{
    public function index()
    {
        $admins = User::ofAdmin()->get();

        return view('panel::manage.admins', compact('admins'));
    }

    public function store(StoreAdminRequest $request)
    {
        $username = $request->username;

        filter_var($username, FILTER_VALIDATE_EMAIL) ?
            $credentials['email'] = $username :
            $credentials['phone'] = $username;

        $admin = User::where($credentials)->first();

        if (!$admin) {
            return back()->with('failure', __('panel::error.userNotFound'));
        }

        $admin->user_type = 1;
        $admin->save();

        return $this->createSuccess();
    }

    public function destroy(Request $request, User $admin)
    {
        $admin->user_type = 2;
        $admin->save();

        return $this->deleteSuccess();
    }
}
