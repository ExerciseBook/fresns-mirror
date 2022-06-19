<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Models\Traits;

use App\Helpers\FileHelper;
use App\Helpers\LanguageHelper;
use App\Helpers\PluginHelper;
use App\Models\PluginBadge;

trait PluginUseServiceTrait
{
    public function getUseInfo(?string $langTag = null, ?int $userId = null)
    {
        $useData = $this;

        $info['plugin'] = $useData->plugin_unikey;
        $info['name'] = LanguageHelper::fresnsLanguageByTableId('plugin_uses', 'name', $useData->id, $langTag);
        $info['icon'] = FileHelper::fresnsFileUrlByTableColumn($useData->icon_file_id, $useData->icon_file_url);
        $info['url'] = PluginHelper::fresnsPluginUseUrl($useData->plugin_unikey, $useData->parameter);

        $info['badgesType'] = null;
        $info['badgesValue'] = null;
        $info['editorNumber'] = $useData->editor_number;

        if (! empty($userId)) {
            $badge = PluginBadge::where('plugin_unikey', $useData->plugin_unikey)->where('user_id', $userId)->first();
            $info['badgesType'] = $badge->display_type;
            $info['badgesValue'] = match ($badge->display_type) {
                default => null,
                1 => $badge->value_number,
                2 => $badge->value_text,
            };
        }

        $pluginRating['postByAll'] = PluginHelper::pluginDataRatingHandle('postByAll', $useData->data_sources, $langTag);
        $pluginRating['postByFollow'] = PluginHelper::pluginDataRatingHandle('postByFollow', $useData->data_sources, $langTag);
        $pluginRating['postByNearby'] = PluginHelper::pluginDataRatingHandle('postByNearby', $useData->data_sources, $langTag);
        $info['pluginRating'] = $pluginRating;

        return $info;
    }

    public function getIconUrl()
    {
        return FileHelper::fresnsFileUrlByTableColumn($this->icon_file_id, $this->icon_file_url);
    }
}
