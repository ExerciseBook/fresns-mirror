<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Plugin;
use App\Models\PluginUsage;
use App\Models\Language;
use Illuminate\Http\Request;

class ExpandTypeController extends Controller
{
    public function index()
    {
        $plugins = Plugin::all();

        $plugins = $plugins->filter(function ($plugin) {
            return in_array('restful', $plugin->scene);
        });

        $pluginUsages = PluginUsage::where('type', 4)
            ->with('plugin', 'names')
            ->paginate();

        return view('panel::expand.type', compact('plugins', 'pluginUsages'));
    }

    public function store(Request $request)
    {
        $pluginUsage = new PluginUsage;
        $pluginUsage->type = 4;
        $pluginUsage->name = $request->names[$this->defaultLanguage] ?? (current(array_filter($request->names)) ?: '');
        $pluginUsage->plugin_unikey = $request->plugin_unikey;
        $pluginUsage->is_enable = $request->is_enable;
        $pluginUsage->rank_num = $request->rank_num;
        $pluginUsage->data_sources = [
            'postLists' => [
                'pluginUnikey' => $request->post_list,
                'sortNumber' => [],
            ],
            'postFollows' => [
                'pluginUnikey' => $request->post_follow,
                'sortNumber' => [],
            ],
            'postNearbys' => [
                'pluginUnikey' => $request->post_nearby,
                'sortNumber' => [],
            ],
        ];
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
        $pluginUsage = PluginUsage::findOrFail($id);
        $pluginUsage->name = $request->names[$this->defaultLanguage] ?? (current(array_filter($request->names)) ?: '');
        $pluginUsage->plugin_unikey = $request->plugin_unikey;
        $pluginUsage->is_enable = $request->is_enable;
        $pluginUsage->rank_num = $request->rank_num;
        $dataSources = $pluginUsage->data_sources;

        if ($request->post_list != ($dataSources['postLists']['pluginUnikey'] ?? null)) {
            $dataSources['postLists'] = [
                'pluginUnikey' => $request->post_list,
                'sortNumber' => [],
            ];
        }

        if ($request->post_follow != ($dataSources['postFollows']['pluginUnikey'] ?? null)) {
            $dataSources['postFollows'] = [
                'pluginUnikey' => $request->post_follow,
                'sortNumber' => [],
            ];
        }


        if ($request->post_nearby != ($dataSources['postNearbys']['pluginUnikey'] ?? null)) {
            $dataSources['postFollows'] = [
                'pluginUnikey' => $request->post_nearby,
                'sortNumber' => [],
            ];
        }

        $pluginUsage->data_sources = $dataSources;
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

    public function destroy()
    {
        return $this->deleteSuccess();
    }

    public function updateRank()
    {
        return $this->updateSuccess();
    }

    public function source()
    {
        return $this->updateSuccess();
    }
}
