<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Config;
use App\Models\Plugin;
use Illuminate\Http\Request;

class ExpandPostController extends Controller
{
    public function index()
    {
        // config keys
        $configKeys = [
            'post_detail_service',
        ];
        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach ($configs as $config) {
            $params[$config->item_key] = $config->item_value;
        }

        $pluginScenes = [
            'restful',
        ];
        $plugins = Plugin::all();
        $pluginParams = [];
        foreach ($pluginScenes as $scene) {
            $pluginParams[$scene] = $plugins->filter(function ($plugin) use ($scene) {
                return in_array($scene, $plugin->scene);
            });
        }

        return view('panel::expand.post', compact('pluginParams', 'params'));
    }

    public function update(Request $request)
    {
        // config keys
        $configKeys = [
            'post_detail_service',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach ($configKeys as $configKey) {
            $config = $configs->where('item_key', $configKey)->first();
            if (!$config) {
                $continue;
            }

            if (!$request->has($configKey)) {
                if ($config->item_type == 'boolean') {
                    $config->item_value = 'false';
                } elseif ($config->item_type == 'number') {
                    $config->item_value = 0;
                } else {
                    $config->item_value = null;
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
