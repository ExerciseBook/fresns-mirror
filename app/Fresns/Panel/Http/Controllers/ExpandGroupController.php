<?php

namespace App\Fresns\Panel\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Config;
use App\Models\Plugin;
use App\Models\Language;
use App\Models\PluginUsage;
use App\Models\MemberRole;
use App\Models\Group;

class ExpandGroupController extends Controller
{
    public function index(Request $request)
    {
        $plugins = Plugin::all();

        $plugins = $plugins->filter(function ($plugin) {
            return in_array('pay', $plugin->scene);
        });
        $groupId = $request->get('group_id')??0;
        $pluginUsages = PluginUsage::where('type', 6);
        if ($groupId) {
            $pluginUsages = $pluginUsages->where('group_id', $groupId);
        }
        $pluginUsages = $pluginUsages->with('plugin', 'names', 'group')->paginate();

        $memberRoles = MemberRole::all();

        $groups = Group::where('parent_id', 0)
            ->with('groups')
            ->get();
        $groupSelect = Group::find($groupId);

        return view('panel::expand.group', compact('pluginUsages', 'plugins', 'memberRoles', 'groups', 'groupId', 'groupSelect'));
    }

    public function store(Request $request)
    {
        $pluginUsage = new PluginUsage;
        $pluginUsage->type = 6;
        $pluginUsage->name = $request->names[$this->defaultLanguage] ?? (current(array_filter($request->names)) ?: '');
        $pluginUsage->plugin_unikey = $request->plugin_unikey;
        $pluginUsage->parameter = $request->parameter;
        $pluginUsage->is_enable = $request->is_enable;
        $pluginUsage->rank_num = $request->rank_num;
        $pluginUsage->member_roles = $request->member_roles?implode(',', $request->member_roles):$pluginUsage->member_roles;
        $groupId = implode(',', array_filter($request->group_id));
        $pluginUsage->group_id = $groupId?:0;
        $pluginUsage->save();

        if ($request->update_name) {
            foreach ($request->names as $langTag => $content) {
                $language = Language::tableName('plugin_usages')
                    ->where('table_id', $pluginUsage->id)
                    ->where('lang_tag', $langTag)
                    ->first();

                if (!$language) {
                    // create but no content
                    if (!$content) {
                        continue;
                    }
                    $language = new Language();
                    $language->fill([
                        'table_name' => 'plugin_usages',
                        'table_field' => 'name',
                        'table_id' => $pluginUsage->id,
                        'lang_tag' => $langTag,
                    ]);
                }

                $language->lang_content = $content;
                $language->save();
            }
        }
        return $this->createSuccess();
    }

    public function update($id, Request $request)
    {
        $pluginUsage = PluginUsage::find($id);
        if (!$pluginUsage) {
            return $this->updateSuccess();
        }
        $pluginUsage->name = $request->names[$this->defaultLanguage] ?? (current(array_filter($request->names)) ?: '');
        $pluginUsage->plugin_unikey = $request->plugin_unikey;
        $pluginUsage->parameter = $request->parameter;
        $pluginUsage->is_enable = $request->is_enable;
        $pluginUsage->rank_num = $request->rank_num;
        $pluginUsage->member_roles = $request->member_roles?implode(',', $request->member_roles):$pluginUsage->member_roles;
        $groupId = implode(',', array_filter($request->group_id));
        $pluginUsage->group_id = $groupId?:0;
        $pluginUsage->save();

        if ($request->update_name) {
            foreach ($request->names as $langTag => $content) {
                $language = Language::tableName('plugin_usages')
                    ->where('table_id', $pluginUsage->id)
                    ->where('lang_tag', $langTag)
                    ->first();

                if (!$language) {
                    // create but no content
                    if (!$content) {
                        continue;
                    }
                    $language = new Language();
                    $language->fill([
                        'table_name' => 'plugin_usages',
                        'table_field' => 'name',
                        'table_id' => $pluginUsage->id,
                        'lang_tag' => $langTag,
                    ]);
                }

                $language->lang_content = $content;
                $language->save();
            }
        }
        return $this->updateSuccess();
    }

    public function destroy()
    {
        return $this->deleteSuccess();
    }

    public function updateRank($id, Request $request)
    {
        $pluginUsage = PluginUsage::findOrFail($id);
        $pluginUsage->rank_num = $request->rank_num;
        $pluginUsage->save();

        return $this->updateSuccess();
    }
}
