<?php

namespace App\Fresns\Panel\Http\Controllers;

use Illuminate\Http\Request;

class ClientMenuController extends Controller
{
    public function index()
    {
        return view('panel::client.menus');
    }

    public function update($id)
    {
        return $this->updateSuccess();
    }
}
