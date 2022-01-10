<?php

namespace App\Fresns\Panel\Http\Controllers;

use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function index()
    {
        return view('panel::client.themes');
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
