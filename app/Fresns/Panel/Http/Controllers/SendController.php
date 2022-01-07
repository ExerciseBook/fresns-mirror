<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Config;
use App\Models\Language;
use App\Models\Plugin;
use Illuminate\Http\Request;

class SendController extends Controller
{
    public function show()
    {
        // config keys
        $configKeys = [
            'send_email_service',
            'send_sms_service',
            'send_sms_code',
            'send_sms_code_more',
            'send_ios_service',
            'send_android_service',
            'send_wechat_service',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach($configs as $config) {
            $params[$config->item_key] = $config->item_value;
        }

        $params['send_sms_code_more'] = join(PHP_EOL, $params['send_sms_code_more']);

        $pluginScenes = [
            'email',
            'sms',
            'ios',
            'android',
            'wechat',
        ];

        $plugins = Plugin::all();

        $pluginParams = [];
        foreach($pluginScenes as $scene) {
            $pluginParams[$scene] = $plugins->filter(function($plugin) use ($scene) {
                return in_array($scene, $plugin->scene);
            });
        }

        return view('panel::system.send', compact('params', 'pluginParams'));
    }

    public function update(Request $request)
    {
        $configKeys = [
            'send_email_service',
            'send_sms_service',
            'send_sms_code',
            'send_sms_code_more',
            'send_ios_service',
            'send_android_service',
            'send_wechat_service',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach($configKeys as $configKey) {
            $config = $configs->where('item_key', $configKey)->first();
            if (!$config) {
                $continue;
            }

            $value = $request->$configKey;
            if ($configKey == 'send_sms_code_more') {
                $value = explode("\r\n", $request->send_sms_code_more);
            }

            $config->item_value = $value;
            $config->save();
        }

        return $this->updateSuccess();
    }
}
