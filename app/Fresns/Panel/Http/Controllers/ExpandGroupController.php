<?php

namespace App\Fresns\Panel\Http\Controllers;

use Illuminate\Http\Request;

class ExpandGroupController extends Controller
{
    public function index()
    {
        return view('panel::expand.group');
    }

    public function store(Request $request)
    {
        return $this->createSuccess();
    }

    public function update()
    {
        return $this->updateSuccess();
    }

    public function destroy()
    {
        return $this->deleteSuccess();
    }

    public function updateRank()
    {
        return $this->updateSuccess();
    }
}
