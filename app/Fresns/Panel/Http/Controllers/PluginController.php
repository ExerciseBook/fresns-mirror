<?php

namespace App\Fresns\Panel\Http\Controllers;

use Illuminate\Http\Request;

class PluginController extends Controller
{
    public function index()
    {
        return view('panel::plugin.plugins');
    }

    public function status($id)
    {
        return $this->updateSuccess();
    }
    public function uninstall($id)
    {
        return $this->updateSuccess();
    }
}
