<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Config;
use App\Models\Plugin;
use App\Models\SessionKey;
use Illuminate\Http\Request;
use App\Fresns\Panel\Http\Requests\UpdateConfigRequest;

class ManageConfigController extends Controller
{
    public function show()
    {
        $domainConfig = Config::where('item_key', 'backend_domain')->first();
        $domain = optional($domainConfig)->item_value;

        $pathConfig = Config::where('item_key', 'backend_path')->first();
        $path = optional($pathConfig)->item_value;

        return view('panel::manage.configs', compact('domain', 'path'));
    }

    public function update(UpdateConfigRequest $request)
    {
        if ($request->domain) {
            $domainConfig = Config::where('item_key', 'backend_domain')->firstOrNew();
            $domainConfig->item_value = $request->domain;
            $domainConfig->save();
        }

        if ($request->path) {
            $pathConfig = Config::where('item_key', 'backend_path')->firstOrNew();
            $pathConfig->item_value = $request->path;
            $pathConfig->save();
        }

        return $this->updatSuccess();
    }
}
