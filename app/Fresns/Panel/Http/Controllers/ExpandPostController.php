<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\PluginUsage;
use App\Models\Plugin;
use Illuminate\Http\Request;

class ExpandPostController extends Controller
{
    public function index()
    {
        $pluginUsage = PluginUsage::where('name', 'post_detail_service')->where('type', 10)->with('plugin')->first();

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

        return view('panel::expand.post', compact('pluginParams', 'pluginUsage'));
    }

    public function update($itemKey, Request $request)
    {
        $pluginUsage = PluginUsage::where('name', $itemKey)->where('type', 10)->with('plugin')->first();
        if (!$pluginUsage) {
            $pluginUsage = new PluginUsage();
            $pluginUsage->type =10;
            $pluginUsage->name =$itemKey;
        }
        $pluginUsage->plugin_unikey =$request->get('plugin_unikey');
        $pluginUsage->save();

        return $this->updateSuccess();
    }
}
