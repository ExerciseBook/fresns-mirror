<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Config;
use App\Models\Plugin;
use App\Models\MemberRole;
use Illuminate\Http\Request;
use App\Fresns\Panel\Http\Requests\UpdateUserConfigRequest;

class UserConfigController extends Controller
{
    public function show()
    {
        // config keys
        $configKeys = [
            'account_connect_services',
            'account_prove_service',
            'member_multiple',
            'multi_member_service',
            'multi_member_roles',
            'default_role',
            'default_avatar',
            'anonymous_avatar',
            'deactivate_avatar',
            'password_length',
            'password_strength',
            'mname_min',
            'mname_max',
            'mname_edit',
            'nickname_edit',
            'connects',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        $params = [];
        foreach ($configs as $config) {
            $value = $config->item_value;
            if ($config->item_key == 'password_strength') {
                $value = explode(',', $value);
            }
            $params[$config->item_key] = $value;
        }

        $pluginScenes = [
            'connect',
            'prove',
            'multiple',
        ];

        $plugins = Plugin::all();

        $pluginParams = [];
        foreach ($pluginScenes as $scene) {
            $pluginParams[$scene] = $plugins->filter(function ($plugin) use ($scene) {
                return in_array($scene, $plugin->scene);
            });
        }

        $memberRoles = MemberRole::all();

        return view('panel::system.user', compact('params', 'pluginParams', 'memberRoles'));
    }

    public function update(UpdateUserConfigRequest $request)
    {
        $configKeys = [
            'account_prove_service',
            'member_multiple',
            'multi_member_service',
            'multi_member_roles',
            'default_role',
            'default_avatar',
            'anonymous_avatar',
            'deactivate_avatar',
            'password_length',
            'password_strength',
            'mname_min',
            'mname_max',
            'mname_edit',
            'nickname_edit',
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

            $value = $request->$configKey;

            if ($configKey == 'password_strength') {
                $value = join(',', $request->$configKey);
            }

            $config->item_value = $value;
            $config->save();
        }

        return $this->updateSuccess();
    }
}
