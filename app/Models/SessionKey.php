<?php

namespace App\Models;

use Illuminate\Support\Collection;

class SessionKey extends Model
{
    protected $fillable = [
        'platform_id',
        'name',
        'type',
        'is_enable',
        'plugin_unikey',
    ];
    /**
     * get config platform name
     *
     * @param array $platforms
     * @access public
     * @return string
     */
    public function platformName($platforms = []): string
    {
        if (!$platforms instanceof Collection) {
           $platforms = collect($platforms);
        }

        $platform = $platforms->where('id', $this->platform_id)->first();
        if (!$platform) {
            return '';
        }
        return $platform['name'] ?? '';
    }

    public function plugin()
    {
        return $this->belongsTo(Plugin::class, 'plugin_unikey', 'unikey');
    }

}
