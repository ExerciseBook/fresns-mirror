<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Config;
use App\Models\Plugin;
use App\Models\SessionKey;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function index()
    {
        return view('panel::manage.configs');
    }
}
