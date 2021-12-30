<?php

namespace App\Fresns\Panel\Http\Controllers;

use Illuminate\Http\Request;

class ManageController extends Controller
{
    public function keyIndex()
    {
        return view('panel::manage.keys');
    }

    public function configIndex()
    {
        return view('panel::manage.configs');
    }

    public function adminIndex()
    {
        return view('panel::manage.admins');
    }
}
