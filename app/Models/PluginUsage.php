<?php

namespace App\Models;

use Illuminate\Support\Collection;

class PluginUsage extends Model
{
    public function plugin()
    {
        return $this->belongsTo(Plugin::class, 'plugin_unikey', 'unikey');
    }
}
