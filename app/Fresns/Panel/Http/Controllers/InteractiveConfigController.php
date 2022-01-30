<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Config;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Fresns\Panel\Http\Requests\UpdateSiteRequest;

class InteractiveConfigController extends Controller
{
    public function show()
    {
        // config keys
        $configKeys = [
            'hashtag_show',
            'post_hot',
            'comment_preview',
            'nearby_length',
            'dialog_status',
            'dialog_files',
            'like_member_setting',
            'like_group_setting',
            'like_hashtag_setting',
            'like_post_setting',
            'like_comment_setting',
            'follow_member_setting',
            'follow_group_setting',
            'follow_hashtag_setting',
            'follow_post_setting',
            'follow_comment_setting',
            'shield_member_setting',
            'shield_group_setting',
            'shield_hashtag_setting',
            'shield_post_setting',
            'shield_comment_setting',
            'it_publish_posts',
            'it_publish_comments',
            'it_likers',
            'it_followers',
            'it_shielders',
            'it_like_members',
            'it_like_groups',
            'it_like_hashtags',
            'it_like_posts',
            'it_like_comments',
            'it_follow_members',
            'it_follow_groups',
            'it_follow_hashtags',
            'it_follow_posts',
            'it_follow_comments',
            'it_shield_members',
            'it_shield_groups',
            'it_shield_hashtags',
            'it_shield_posts',
            'it_shield_comments',
            'it_home_list',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach($configs as $config) {
            $params[$config->item_key] = $config->item_value;
        }

        return view('panel::operation.interactive', compact('params'));
    }

    public function update(Request $request)
    {
        $configKeys = [
            'hashtag_show',
            'post_hot',
            'comment_preview',
            'nearby_length',
            'dialog_status',
            'dialog_files',
            'like_member_setting',
            'like_group_setting',
            'like_hashtag_setting',
            'like_post_setting',
            'like_comment_setting',
            'follow_member_setting',
            'follow_group_setting',
            'follow_hashtag_setting',
            'follow_post_setting',
            'follow_comment_setting',
            'shield_member_setting',
            'shield_group_setting',
            'shield_hashtag_setting',
            'shield_post_setting',
            'shield_comment_setting',
            'it_publish_posts',
            'it_publish_comments',
            'it_likers',
            'it_followers',
            'it_shielders',
            'it_like_members',
            'it_like_groups',
            'it_like_hashtags',
            'it_like_posts',
            'it_like_comments',
            'it_follow_members',
            'it_follow_groups',
            'it_follow_hashtags',
            'it_follow_posts',
            'it_follow_comments',
            'it_shield_members',
            'it_shield_groups',
            'it_shield_hashtags',
            'it_shield_posts',
            'it_shield_comments',
            'it_home_list',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach($configKeys as $configKey) {
            $config = $configs->where('item_key', $configKey)->first();
            if (!$config) {
                $continue;
            }

            if (!$request->has($configKey)) {
                $config->setDefaultValue();
                $config->save();
                continue;
            }

            $value = $request->$configKey;
            if ($configKey == 'dialog_files'){
                $value = join(',', $value);
            }

            $config->item_value = $value;
            $config->save();
        }

        return $this->updateSuccess();
    }
}
