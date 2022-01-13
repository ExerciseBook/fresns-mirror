<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Config;
use App\Models\Language;
use Illuminate\Http\Request;

class PublishConfigController extends Controller
{
    public function postShow()
    {
        // config keys
        $configKeys = [
            'post_email_verify',
            'post_phone_verify',
            'post_prove_verify',
            'post_limit_status',
            'post_limit_type',
            'post_limit_period_start',
            'post_limit_period_end',
            'post_limit_cycle_start',
            'post_limit_cycle_end',
            'post_limit_rule',
            'post_limit_prompt',
            'post_limit_whitelist',
            'post_edit',
            'post_edit_timelimit',
            'post_edit_sticky',
            'post_edit_essence',
            'post_editor_service',
            'post_editor_group',
            'post_editor_title',
            'post_editor_emoji',
            'post_editor_image',
            'post_editor_video',
            'post_editor_audio',
            'post_editor_doc',
            'post_editor_mention',
            'post_editor_hashtag',
            'post_editor_expand',
            'post_editor_lbs',
            'post_editor_anonymous',
            'post_editor_group_required',
            'post_editor_title_view',
            'post_editor_title_required',
            'post_editor_title_word_count',
            'post_editor_word_count',
            'post_editor_brief_count',
        ];
        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach ($configs as $config) {
            $params[$config->item_key] = $config->item_value;
        }
        // dd($params);
        $languages = Language::ofConfig()->where('table_key', 'post_limit_prompt')->get();

        return view('panel::operation.post', compact('params', 'languages'));
    }

    public function postUpdate(Request $request)
    {
        $configKeys = [
            'post_email_verify',
            'post_phone_verify',
            'post_prove_verify',
            'post_limit_status',
            'post_limit_type',
            'post_limit_period_start',
            'post_limit_period_end',
            'post_limit_cycle_start',
            'post_limit_cycle_end',
            'post_limit_rule',
            'post_limit_prompt',
            'post_limit_whitelist',
            'post_edit',
            'post_edit_timelimit',
            'post_edit_sticky',
            'post_edit_essence',
            'post_editor_service',
            'post_editor_group',
            'post_editor_title',
            'post_editor_emoji',
            'post_editor_image',
            'post_editor_video',
            'post_editor_audio',
            'post_editor_doc',
            'post_editor_mention',
            'post_editor_hashtag',
            'post_editor_expand',
            'post_editor_lbs',
            'post_editor_anonymous',
            'post_editor_group_required',
            'post_editor_title_view',
            'post_editor_title_required',
            'post_editor_title_word_count',
            'post_editor_word_count',
            'post_editor_brief_count',
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

    public function commentShow()
    {
        // config keys
        $configKeys = [
            'comment_email_verify',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();

        foreach ($configs as $config) {
            $params[$config->item_key] = $config->item_value;
        }

        $languages = Language::ofConfig()->where('table_key', 'comment_limit_prompt')->get();

        return view('panel::operation.comment', compact('params', 'languages'));
    }

    public function commentUpdate(UpdateSiteRequest $request)
    {
        $configKeys = [
            'post_email_verify',
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
