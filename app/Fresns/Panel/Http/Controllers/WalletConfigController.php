<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Config;
use App\Models\Language;
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
        //dd($params);

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
}
