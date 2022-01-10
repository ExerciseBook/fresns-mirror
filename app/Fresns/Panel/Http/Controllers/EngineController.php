<?php

namespace App\Fresns\Panel\Http\Controllers;

use Illuminate\Http\Request;

class EngineController extends Controller
{
    public function index()
    {
        return view('panel::client.engines');
    }

    public function status($id)
    {
        return $this->updateSuccess();
    }

    public function relation($id)
    {
        return $this->updateSuccess();
    }

    public function uninstall($id)
    {
        return $this->updateSuccess();
    }
}
