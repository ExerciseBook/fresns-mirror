<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Config;
use App\Models\Plugin;
use App\Models\SessionKey;
use App\Helpers\StrHelper;
use Illuminate\Http\Request;
use App\Fresns\Panel\Http\Requests\UpdateSessionKeyRequest;

class SessionKeyController extends Controller
{
    public function index()
    {
        $platformConfig = Config::platform()->firstOrFail();
        $platforms = $platformConfig['item_value'];

        $sessionKeys = SessionKey::all();

        $typeLabels = [
            1 => __('panel::panel.mainApi'),
            2 => __('panel::panel.manageApi'),
            3 => __('panel::panel.pluginApi'),
        ];

        $plugins = Plugin::all();

        return view('panel::manage.keys', compact('platforms', 'sessionKeys', 'typeLabels', 'plugins'));
    }

    public function store(UpdateSessionKeyRequest $request)
    {
        $key = new SessionKey;
        $key->fill($request->all());
        $key->app_id = strtolower('tw'.StrHelper::randString(14));
        $key->app_secret = strtolower(StrHelper::randString(32));
        $key->save();

        return $this->createSuccess();
    }

    public function update(UpdateSessionKeyRequest $request, SessionKey $sessionKey)
    {
        $attributes = $request->all();
        if ($request->type != 3) {
            $attributes['plugin_unikey'] = null;
        }
        $sessionKey->update($attributes);

        return $this->updateSuccess();
    }

    public function reset(SessionKey $sessionKey)
    {
        $sessionKey->app_id = strtolower('tw'.StrHelper::randString(14));
        $sessionKey->app_secret = strtolower(StrHelper::randString(32));
        $sessionKey->save();

        return $this->updateSuccess();
    }

    public function destroy(SessionKey $sessionKey)
    {
        $sessionKey->delete();

        return $this->deleteSuccess();
    }
}
