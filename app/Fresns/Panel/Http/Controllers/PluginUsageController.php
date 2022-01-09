<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\PluginUsage;
use Illuminate\Http\Request;

class PluginUsageController extends Controller
{
    public function destroy(PluginUsage $pluginUsage)
    {
        $pluginUsage->delete();
        return $this->deleteSuccess();
    }
}
