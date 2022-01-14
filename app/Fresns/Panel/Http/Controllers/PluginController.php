<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Plugin;
use Illuminate\Http\Request;

class PluginController extends Controller
{
    public function index(Request $request)
    {
        $isEnable = $request->is_enable;

        $plugins = Plugin::query();

        if ($request->has('is_enable')) {
            $plugins->where('is_enable', $request->is_enable);
        }

        $plugins = $plugins->get();

        // sidebar 显示
        $enablePlugins = Plugin::where('is_enable', 1)->get();

        $enableCount = $enablePlugins->count();
        $disableCount = Plugin::where('is_enable', 0)->count();

        return view('panel::plugin.plugins', compact('plugins', 'enableCount', 'disableCount', 'isEnable', 'enablePlugins'));
    }

    public function update(Plugin $plugin, Request $request)
    {
        if ($request->has('is_enable')) {
            $plugin->is_enable = $request->is_enable;
        }
        $plugin->save();
        return $this->updateSuccess();
    }

    public function destroy(Plugin $plugin)
    {
        $plugin->delete();
        return $this->deleteSuccess();
    }
}
