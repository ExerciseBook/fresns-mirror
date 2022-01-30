<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Config;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Fresns\Panel\Http\Requests\UpdateSiteRequest;

class RenameConfigController extends Controller
{
    public function show()
    {
        // config keys
        $configKeys = [
            'member_name',
            'member_id_name',
            'member_name_name',
            'member_nickname_name',
            'member_role_name',
            'group_name',
            'hashtag_name',
            'post_name',
            'comment_name',
            'publish_post_name',
            'publish_comment_name',
            'like_member_name',
            'like_group_name',
            'like_hashtag_name',
            'like_post_name',
            'like_comment_name',
            'follow_member_name',
            'follow_group_name',
            'follow_hashtag_name',
            'follow_post_name',
            'follow_comment_name',
            'shield_member_name',
            'shield_group_name',
            'shield_hashtag_name',
            'shield_post_name',
            'shield_comment_name',
        ];

        $configs = Config::whereIn('item_key', $configKeys)
            ->with('languages')
            ->get();

        $configs = $configs->mapWithKeys(function ($config) {
            return [$config->item_key => $config];
        });

        $langKeys = $configKeys;

        $defaultLangParams = Language::ofConfig()
            ->whereIn('table_key', $langKeys)
            ->where('lang_tag', $this->defaultLanguage)
            ->pluck('lang_content', 'table_key');

        return view('panel::operation.rename', compact('configs', 'defaultLangParams'));
    }

    public function update(UpdateSiteRequest $request)
    {
        $configKeys = [
            'member_name',
            'member_id_name',
            'member_name_name',
            'member_nickname_name',
            'member_role_name',
            'group_name',
            'hashtag_name',
            'post_name',
            'comment_name',
            'publish_post_name',
            'publish_comment_name',
            'like_member_name',
            'like_group_name',
            'like_hashtag_name',
            'like_post_name',
            'like_comment_name',
            'follow_member_name',
            'follow_group_name',
            'follow_hashtag_name',
            'follow_post_name',
            'follow_comment_name',
            'shield_member_name',
            'shield_group_name',
            'shield_hashtag_name',
            'shield_post_name',
            'shield_comment_name',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach ($configKeys as $configKey) {
            $config = $configs->where('item_key', $configKey)->first();
            if (!$config) {
                $continue;
            }

            if (!$request->has($configKey)) {
                if ($config->item_type == 'boolean') {
                    $config->item_value = 'false';
                } elseif ($config->item_type == 'number') {
                    $config->item_value = 0;
                } else {
                    $config->item_value = null;
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
