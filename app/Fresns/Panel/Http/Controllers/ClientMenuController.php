<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Config;
use App\Models\Language;
use Illuminate\Http\Request;

class ClientMenuController extends Controller
{
    public function index()
    {
        $configKeys = [
            'default_homepage',
            'menu_portal_name',
            'menu_portal_title',
            'menu_portal_keywords',
            'menu_portal_description',
            'menu_portal_status',
            'menu_member_name',
            'menu_member_title',
            'menu_member_keywords',
            'menu_member_description',
            'menu_member_config',
            'menu_member_status',
            'menu_group_name',
            'menu_group_title',
            'menu_group_keywords',
            'menu_group_description',
            'menu_group_config',
            'menu_group_status',
            'menu_hashtag_name',
            'menu_hashtag_title',
            'menu_hashtag_keywords',
            'menu_hashtag_description',
            'menu_hashtag_config',
            'menu_hashtag_status',
            'menu_post_name',
            'menu_post_title',
            'menu_post_keywords',
            'menu_post_description',
            'menu_post_config',
            'menu_post_status',
            'menu_comment_name',
            'menu_comment_title',
            'menu_comment_keywords',
            'menu_comment_description',
            'menu_comment_config',
            'menu_comment_status',
            'menu_member_list_name',
            'menu_member_list_title',
            'menu_member_list_keywords',
            'menu_member_list_description',
            'menu_member_list_config',
            'menu_member_list_status',
            'menu_group_list_name',
            'menu_group_list_title',
            'menu_group_list_keywords',
            'menu_group_list_description',
            'menu_group_list_config',
            'menu_group_list_status',
            'menu_hashtag_list_name',
            'menu_hashtag_list_title',
            'menu_hashtag_list_keywords',
            'menu_hashtag_list_description',
            'menu_hashtag_list_config',
            'menu_hashtag_list_status',
            'menu_post_list_name',
            'menu_post_list_title',
            'menu_post_list_keywords',
            'menu_post_list_description',
            'menu_post_list_config',
            'menu_post_list_status',
            'menu_comment_list_name',
            'menu_comment_list_title',
            'menu_comment_list_keywords',
            'menu_comment_list_description',
            'menu_comment_list_config',
            'menu_comment_list_status',
        ];

        $langKeys = $configKeys;

        $configs = Config::whereIn('item_key', $configKeys)->get();
        $params = [];

        foreach($configs as $config) {
            $params[$config->item_key] = $config->item_value;
        }

        $languages = Language::ofConfig()->whereIn('table_key', $langKeys)->get();

        $langParams = [];
        foreach($langKeys as $langKey) {
            $langParams[$langKey] = $languages->where('table_key', $langKey)->pluck('lang_content', 'lang_tag')->toArray();
        }


        $menus = [
            // url => key name
            'portal' => [
                'name' => '门户',
                'url' => 'portal',
                'select' => true,
            ],
            'member' => [
                'name' => '成员',
                'url' => 'members',
                'select' => true
            ],
            'group' => [
                'name' => '小组',
                'url' => 'groups',
                'select' => true
            ],
            'hashtag' => [
                'name' => '话题',
                'url' => 'hashtags',
                'select' => true
            ],
            'post' => [
                'name' => '帖子',
                'url' => 'posts',
                'select' => true
            ],
            'comment' => [
                'name' => '评论',
                'url' => 'comments',
                'select' => true
            ],
            'member_list' => [
                'name' => '成员列表页',
                'url' => 'members/list',
                'select' => false
            ],
            'group_list' => [
                'name' => '小组列表页',
                'url' => 'groups/list',
                'select' => false
            ],
            'hashtag_list' => [
                'name' => '话题列表页',
                'url' => 'hashtags/list',
                'select' => false
            ],
            'post_list' => [
                'name' => '帖子列表页',
                'url' => 'posts/list',
                'select' => false
            ],
            'comment_list' => [
                'name' => '评论列表页',
                'url' => 'comments/list',
                'select' => false
            ],
        ];


        return view('panel::client.menus', compact('menus', 'params', 'langParams'));
    }

    public function update($key, Request $request)
    {
        $configKey = 'menu_'.$key.'_config';
        $enableKey = 'menu_'.$key.'_status';

        if ($key != 'portal' && $request->has('config')) {
            $config = Config::where('item_key', $configKey)->first();
            if (!$config) {
                $config = new Config;
                $config->item_key = $enableKey;
                $config->item_type = 'object';
                $config->item_tag = 'menus';
                $config->is_enable = 1;
            }

            $config->item_value = json_decode($request->config, true);
            $config->save();
        }

        if ($request->has('is_enable')) {
            $config = Config::where('item_key', $enableKey)->first();
            if (!$config) {
                $config = new Config;
                $config->item_key = $enableKey;
                $config->item_type = 'boolean';
                $config->item_tag = 'menus';
                $config->is_enable = 1;
            }
            $config->item_value = $request->is_enable;
            $config->save();
        }

        return $this->updateSuccess();
    }
}
