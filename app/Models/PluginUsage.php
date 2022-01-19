<?php

namespace App\Models;

use Illuminate\Support\Collection;

class PluginUsage extends Model
{
    public function plugin()
    {
        return $this->belongsTo(Plugin::class, 'plugin_unikey', 'unikey');
    }

    public function names()
    {
        return $this->hasMany(Language::class, 'table_id', 'id')
                    ->where('table_field', 'name')
                    ->where('table_name', 'plugin_usages');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }
}
