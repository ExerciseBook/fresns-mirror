<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Config;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Fresns\Panel\Http\Requests\UpdateSiteRequest;

class EmojiController extends Controller
{
    public function show()
    {

        return view('panel::operation.emoji', compact('params', 'langParams'));
    }

    public function update(UpdateSiteRequest $request)
    {
        $configKeys = [
            'site_domain',
            'site_copyright',
            'site_copyright_years',
            'default_timezone',
            'site_mode',
            'site_public_close',
            //'site_public_service',
            'site_register_email',
            'site_register_phone',
            'site_private_close',
            //'site_private_service',
            'site_private_end',
            'site_email',
        ];

        //dd($request->all());
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
