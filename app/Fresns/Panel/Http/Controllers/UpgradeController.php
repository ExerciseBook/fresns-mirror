<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\User;
use App\Models\Config;
use App\Models\Plugin;
use App\Models\SessionKey;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class UpgradeController extends Controller
{
    public function show()
    {
        return view('panel::manage.upgrade');
    }

    public function upgrade()
    {
        exec('php '.base_path('artisan').' fresns:upgrade > /dev/null &');

        return $this->successResponse('upgrade');
    }
}
