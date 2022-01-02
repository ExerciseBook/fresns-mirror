<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Config;
use App\Models\Plugin;
use App\Models\SessionKey;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('panel::manage.admins');
    }
}
