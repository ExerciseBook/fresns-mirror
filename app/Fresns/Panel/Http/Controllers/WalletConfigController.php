<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Config;
use App\Models\Plugin;
use App\Models\Language;
use App\Models\PluginUsage;
use Illuminate\Http\Request;

class WalletConfigController extends Controller
{
    public function show()
    {
        // config keys
        $configKeys = [
            'wallet_status',
            'wallet_currency_code',
            'wallet_withdraw_close',
            'wallet_withdraw_review',
            'wallet_withdraw_verify',
            'wallet_withdraw_interval_time',
            'wallet_withdraw_rate',
            'wallet_withdraw_min_sum',
            'wallet_withdraw_max_sum',
            'wallet_withdraw_sum_limit',
            'currency_codes',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach($configs as $config) {
            $params[$config->item_key] = $config->item_value;
        }

        return view('panel::system.wallet.config', compact('params'));
    }

    public function update(Request $request)
    {
        $configKeys = [
            'wallet_status',
            'wallet_currency_code',
            'wallet_withdraw_close',
            'wallet_withdraw_review',
            'wallet_withdraw_verify',
            'wallet_withdraw_interval_time',
            'wallet_withdraw_rate',
            'wallet_withdraw_min_sum',
            'wallet_withdraw_max_sum',
            'wallet_withdraw_sum_limit',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach($configKeys as $configKey) {
            $config = $configs->where('item_key', $configKey)->first();
            if (!$config) {
                $continue;
            }

            if (!$request->has($configKey)) {
                $config->setDefaultValue();
            } else {
                $config->item_value = $request->$configKey;
            }

            $config->save();
        }

        return $this->updateSuccess();
    }


    public function payIndex()
    {
        $plugins = Plugin::all();

        $pluginUsages = PluginUsage::where('type', 1)
            ->with('plugin')
            ->get();

        return view('panel::system.wallet.pay', compact('pluginUsages', 'plugins'));
    }

    public function payStore(Request $request)
    {
        $pluginUsage = new PluginUsage;
        $pluginUsage->type = 1;
        $pluginUsage->name = '';
        $pluginUsage->plugin_unikey = $request->plugin_unikey;
        $pluginUsage->parameter = $request->parameter;
        $pluginUsage->is_enable = $request->is_enable;
        $pluginUsage->rank_num = $request->rank_num;
        $pluginUsage->save();

        return $this->createSuccess();
    }

    public function withdrawIndex()
    {
        $pluginUsages = PluginUsage::where('type', 1)
            ->with('plugin')
            ->get();

        return view('panel::system.wallet.withdraw', compact('pluginUsages'));
    }
}
