<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Config;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Fresns\Panel\Http\Requests\UpdatePolicyRequest;

class PolicyController extends Controller
{
    public function show()
    {
        // config keys
        $configKeys = [
            'account_terms_close',
            'account_privacy_close',
            'account_cookie_close',
            'account_delete_close',
            'delete_account',
            'delete_account_todo'
        ];

        // language keys
        $langKeys = [
            'account_terms',
            'account_privacy',
            'account_cookie',
            'account_delete',
        ];
        $configs = Config::whereIn('item_key', $configKeys)->get();

        $languages = Language::ofConfig()->whereIn('table_key', $langKeys)->get();

        foreach($configs as $config) {
            $params[$config->item_key] = $config->item_value;
        }

        $langParams = [];
        foreach($langKeys as $langKey) {
            $langParams[$langKey] = $languages->where('table_key', $langKey)->pluck('lang_content', 'lang_tag')->toArray();
        }

        return view('panel::system.policy', compact('params', 'langParams'));
    }

    public function update(UpdatePolicyRequest $request)
    {
        $configKeys = [
            'account_terms_close',
            'account_privacy_close',
            'account_cookie_close',
            'account_delete_close',
            'delete_account',
            'delete_account_todo',
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
                    $config->item_value = 'false';
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
