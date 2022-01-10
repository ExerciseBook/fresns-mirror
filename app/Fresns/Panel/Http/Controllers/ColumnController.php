<?php

namespace App\Fresns\Panel\Http\Controllers;

use Illuminate\Http\Request;

class ColumnController extends Controller
{
    public function index()
    {
        return view('panel::client.columns');
    }

    public function update($id)
    {
        return $this->updateSuccess();
    }
}
