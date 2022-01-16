<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Config;
use Illuminate\Http\Request;

class ColumnController extends Controller
{
    public function index()
    {
        // config keys
        $configKeys = [
            'menu_like_members',
            'menu_follow_members',
            'menu_shield_members',
            'menu_post_from_follow_members',
            'menu_like_groups',
            'menu_follow_groups',
            'menu_shield_groups',
            'menu_post_from_follow_groups',
            'menu_like_hashtags',
            'menu_follow_hashtags',
            'menu_shield_hashtags',
            'menu_post_from_follow_hashtags',
            'menu_like_posts',
            'menu_follow_posts',
            'menu_shield_posts',
            'menu_post_from_follow_all',
            'menu_post_from_nearby',
            'menu_post_from_location',
            'menu_like_comments',
            'menu_follow_comments',
            'menu_shield_comments',
            'menu_user',
            'menu_user_signup',
            'menu_user_signin',
            'menu_user_reset',
            'menu_user_members',
            'menu_user_wallet',
            'menu_user_settings',
            'menu_dialogs',
            'menu_messages',
            'menu_notifies',
            'menu_notify_systems',
            'menu_notify_recommends',
            'menu_notify_follows',
            'menu_notify_likes',
            'menu_notify_comments',
            'menu_notify_mentions',
            'menu_search',
            'menu_editor_functions',
            'menu_editor_drafts',
            'menu_profile_likes',
            'menu_profile_followers',
            'menu_profile_shielders',
            'menu_profile_like_members',
            'menu_profile_like_groups',
            'menu_profile_like_hashtags',
            'menu_profile_like_posts',
            'menu_profile_like_comments',
            'menu_profile_follow_members',
            'menu_profile_follow_groups',
            'menu_profile_follow_hashtags',
            'menu_profile_follow_posts',
            'menu_profile_follow_comments',
            'menu_profile_shield_members',
            'menu_profile_shield_groups',
            'menu_profile_shield_hashtags',
            'menu_profile_shield_posts',
            'menu_profile_shield_comments',
        ];

        foreach ($configKeys as $configKey) {
            $config = Config::where('item_tag', 'columns')->where('item_key', $configKey)->first();
            if (!$config) {
                $config = new Config();
                $config->item_key = $configKey;
                $config->item_type = 'string';
                $config->item_tag = 'columns';
                $config->is_enable = 1;
                $config->is_restful = 1;
                $config->save();
            }
        }

        $configs = Config::whereIn('item_key', $configKeys)
            ->with('languages')
            ->get();
        $configs = $configs->mapWithKeys(function ($config) {
            return [$config->item_key => $config];
        });


        return view('panel::client.columns', compact('configs'));
    }
}
