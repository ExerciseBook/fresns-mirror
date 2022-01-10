<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Config;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Fresns\Panel\Http\Requests\UpdateSiteRequest;

class InteractiveConfigController extends Controller
{
    public function show()
    {
        // config keys
        $configKeys = [
            'hashtag_show',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach($configs as $config) {
            $params[$config->item_key] = $config->item_value;
        }

        return view('panel::operation.interactive', compact('params'));
    }

    public function update(UpdateSiteRequest $request)
    {
        $configKeys = [
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach($configKeys as $configKey) {
            $config = $configs->where('item_key', $configKey)->first();
            if (!$config) {
                $continue;
            }

            if (!$request->has($configKey)) {
                if ($config->item_type == 'boolean') {
                    $config->item_value = 'false';
                }  else if ($config->item_type == 'number') {
                    $config->item_value = 0;
                } else {
                    $config->item_value = NULl;
                }
                $config->save();
                continue;
            }

            $config->item_value = $request->$configKey;
            $config->save();
        }

        return $this->updateSuccess();
    }
}
