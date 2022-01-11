<?php

namespace App\Fresns\Panel\Http\Controllers;

use Illuminate\Http\Request;

class ExpandFeatureController extends Controller
{
    public function index()
    {
        return view('panel::expand.feature');
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
