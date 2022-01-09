<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Config;
use App\Models\Language;
use App\Models\Plugin;
use Illuminate\Http\Request;

class StorageController extends Controller
{
    public function imageShow()
    {
        // config keys
        $configKeys = [
            'images_service',
            'images_secret_id',
            'images_secret_key',
            'images_bucket_name',
            'images_bucket_area',
            'images_bucket_domain',
            'images_ext',
            'images_max_size',
            'images_url_status',
            'images_url_key',
            'images_url_expire',
            'images_thumb_config',
            'images_thumb_avatar',
            'images_thumb_ratio',
            'images_thumb_square',
            'images_thumb_big',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach($configs as $config) {
            $params[$config->item_key] = $config->item_value;
        }

        $pluginScenes = [
            'storage',
        ];

        $plugins = Plugin::all();

        $pluginParams = [];
        foreach($pluginScenes as $scene) {
            $pluginParams[$scene] = $plugins->filter(function($plugin) use ($scene) {
                return in_array($scene, $plugin->scene);
            });
        }

        return view('panel::system.storage.image', compact('params', 'pluginParams'));
    }

    public function imageUpdate(Request $request)
    {
        $configKeys = [
            'images_service',
            'images_secret_id',
            'images_secret_key',
            'images_bucket_name',
            'images_bucket_area',
            'images_bucket_domain',
            'images_ext',
            'images_max_size',
            'images_url_status',
            'images_url_key',
            'images_url_expire',
            'images_thumb_config',
            'images_thumb_avatar',
            'images_thumb_ratio',
            'images_thumb_square',
            'images_thumb_big',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach($configKeys as $configKey) {
            $config = $configs->where('item_key', $configKey)->first();
            if (!$config) {
                $continue;
            }

            $value = $request->$configKey;
            $config->item_value = $value;
            $config->save();
        }

        return $this->updateSuccess();
    }
}
