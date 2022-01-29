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

        foreach ($configs as $config) {
            $params[$config->item_key] = $config->item_value;
        }

        $pluginScenes = [
            'storage',
        ];

        $plugins = Plugin::all();

        $pluginParams = [];
        foreach ($pluginScenes as $scene) {
            $pluginParams[$scene] = $plugins->filter(function ($plugin) use ($scene) {
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

        foreach ($configKeys as $configKey) {
            $config = $configs->where('item_key', $configKey)->first();
            if (!$config) {
                continue;
            }

            $value = $request->$configKey;
            $config->item_value = $value;
            $config->save();
        }

        return $this->updateSuccess();
    }


    public function videoShow()
    {
        // config keys
        $configKeys = [
            'videos_service',
            'videos_secret_id',
            'videos_secret_key',
            'videos_bucket_name',
            'videos_bucket_area',
            'videos_bucket_domain',
            'videos_ext',
            'videos_max_size',
            'videos_max_time',
            'videos_url_status',
            'videos_url_key',
            'videos_url_expire',
            'videos_transcode',
            'videos_watermark',
            'videos_screenshot',
            'videos_gift',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach ($configs as $config) {
            $params[$config->item_key] = $config->item_value;
        }

        $pluginScenes = [
            'storage',
        ];

        $plugins = Plugin::all();

        $pluginParams = [];
        foreach ($pluginScenes as $scene) {
            $pluginParams[$scene] = $plugins->filter(function ($plugin) use ($scene) {
                return in_array($scene, $plugin->scene);
            });
        }

        return view('panel::system.storage.video', compact('params', 'pluginParams'));
    }

    public function videoUpdate(Request $request)
    {
        $configKeys = [
            'videos_service',
            'videos_secret_id',
            'videos_secret_key',
            'videos_bucket_name',
            'videos_bucket_area',
            'videos_bucket_domain',
            'videos_ext',
            'videos_max_size',
            'videos_max_time',
            'videos_url_status',
            'videos_url_key',
            'videos_url_expire',
            'videos_transcode',
            'videos_watermark',
            'videos_screenshot',
            'videos_gift',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach ($configKeys as $configKey) {
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


    public function audioShow()
    {
        // config keys
        $configKeys = [
            'audios_service',
            'audios_secret_id',
            'audios_secret_key',
            'audios_bucket_name',
            'audios_bucket_area',
            'audios_bucket_domain',
            'audios_ext',
            'audios_max_size',
            'audios_max_time',
            'audios_url_status',
            'audios_url_key',
            'audios_url_expire',
            'audios_transcode',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach ($configs as $config) {
            $params[$config->item_key] = $config->item_value;
        }

        $pluginScenes = [
            'storage',
        ];

        $plugins = Plugin::all();

        $pluginParams = [];
        foreach ($pluginScenes as $scene) {
            $pluginParams[$scene] = $plugins->filter(function ($plugin) use ($scene) {
                return in_array($scene, $plugin->scene);
            });
        }

        return view('panel::system.storage.audio', compact('params', 'pluginParams'));
    }

    public function audioUpdate(Request $request)
    {
        $configKeys = [
            'audios_service',
            'audios_secret_id',
            'audios_secret_key',
            'audios_bucket_name',
            'audios_bucket_area',
            'audios_bucket_domain',
            'audios_ext',
            'audios_max_size',
            'audios_max_time',
            'audios_url_status',
            'audios_url_key',
            'audios_url_expire',
            'audios_transcode',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach ($configKeys as $configKey) {
            $config = $configs->where('item_key', $configKey)->first();
            if (!$config) {
                $config = new Config();
                $config->item_key = $configKey;
                $config->item_type = 'number';
                $config->item_tag = 'storageAudios';
                $config->is_enable = 1;
                $config->is_restful = 1;
            }

            $value = $request->$configKey;
            $config->item_value = $value;
            $config->save();
        }

        return $this->updateSuccess();
    }


    public function docShow()
    {
        // config keys
        $configKeys = [
            'docs_service',
            'docs_secret_id',
            'docs_secret_key',
            'docs_bucket_name',
            'docs_bucket_area',
            'docs_bucket_domain',
            'docs_ext',
            'docs_max_size',
            'docs_url_status',
            'docs_url_key',
            'docs_url_expire',
            'docs_online_preview',
            'docs_preview_ext',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();
        foreach ($configs as $config) {
            $params[$config->item_key] = $config->item_value;
        }
        $pluginScenes = [
            'storage',
        ];

        $plugins = Plugin::all();

        $pluginParams = [];
        foreach ($pluginScenes as $scene) {
            $pluginParams[$scene] = $plugins->filter(function ($plugin) use ($scene) {
                return in_array($scene, $plugin->scene);
            });
        }

        return view('panel::system.storage.doc', compact('params', 'pluginParams'));
    }

    public function docUpdate(Request $request)
    {
        $configKeys = [
            'docs_service',
            'docs_secret_id',
            'docs_secret_key',
            'docs_bucket_name',
            'docs_bucket_area',
            'docs_bucket_domain',
            'docs_ext',
            'docs_max_size',
            'docs_url_status',
            'docs_url_key',
            'docs_url_expire',
            'docs_online_preview',
            'docs_preview_ext',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach ($configKeys as $configKey) {
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


    public function repairShow()
    {
        // config keys
        $configKeys = [
            'repair_image',
            'repair_video',
            'repair_audio',
            'repair_doc',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach ($configs as $config) {
            $params[$config->item_key] = $config->item_value;
        }

        return view('panel::system.storage.repair', compact('params'));
    }

    public function repairUpdate(Request $request)
    {
        $configKeys = [
            'repair_image',
            'repair_video',
            'repair_audio',
            'repair_doc',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach ($configKeys as $configKey) {
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
