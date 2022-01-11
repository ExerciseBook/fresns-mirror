<?php

namespace App\Fresns\Panel\Http\Controllers;

use Illuminate\Http\Request;

class ExpandPostController extends Controller
{
    public function index()
    {
        return view('panel::expand.post');
    }

    public function update()
    {
        return $this->updateSuccess();
    }
}
