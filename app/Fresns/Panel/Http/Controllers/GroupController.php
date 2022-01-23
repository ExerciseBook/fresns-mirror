<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Group;
use App\Models\Plugin;
use App\Models\MemberRole;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Fresns\Panel\Http\Requests\UpdateMemberRoleRequest;

class GroupController extends Controller
{
    protected $typeModeLabels = [
        1 => '公开',
        2 => '非公开',
    ];

    protected $permissioLabels = [
        1  => '所有成员',
        2  => '仅关注了小组的成员',
        3  => '仅指定的角色成员',
    ];

    public function index(Request $request)
    {
        $categories = Group::typeCategory()
            ->with('names', 'descriptions')
            ->get();

        $parentId = $request->parent_id ?: (optional($categories->first())->id ?: 0);

        $groups = [];

        if ($parentId) {
            $groups = Group::typeGroup()
                ->where('parent_id', $parentId)
                ->where('is_enable', 1)
                ->with('member', 'plugin')
                ->paginate();
        }

        extract(get_object_vars($this));


        $plugins = Plugin::all();

        $plugins = $plugins->filter(function ($plugin) {
            return in_array('pay', $plugin->scene);
        });

        $roles = MemberRole::with('names')->get();

        return view('panel::operation.group.index', compact(
            'categories',
            'groups',
            'typeModeLabels',
            'parentId',
            'permissioLabels',
            'plugins',
            'roles'
        ));
    }

    public function recommendIndex()
    {
        $groups = Group::typeGroup()
            ->with('member', 'plugin', 'category')
            ->where('is_recommend', 1)
            ->paginate();

        extract(get_object_vars($this));
        return view('panel::operation.group.recommend', compact(
            'groups',
            'typeModeLabels',
            'permissioLabels'
        ));
    }

    public function disableIndex()
    {
        $groups = Group::typeGroup()
            ->where('is_enable', 0)
            ->with('member', 'plugin', 'category')
            ->paginate();

        extract(get_object_vars($this));
        return view('panel::operation.group.disable', compact(
            'groups',
            'typeModeLabels',
            'permissioLabels'
        ));
    }

    public function store(Group $gorup, Request $request)
    {
        $gorup->uuid     = time();
        $gorup->name = $request->names[$this->defaultLanguage] ?? (current(array_filter($request->names)) ?: '');
        $gorup->rank_num = $request->rank_num;
        $gorup->cover_file_url = $request->cover_file_url;
        $gorup->banner_file_url = $request->banner_file_url;
        $gorup->is_enable = $request->is_enable;
        if ($request->is_category) {
            $gorup->type = 1;
        } else {
            $gorup->type = 2;
        }
        $gorup->save();


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



        if ($request->update_name) {
            foreach ($request->langdesc as $langTag => $content) {
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

    public function update(Group $Group, $request)
    {
    }

    public function updateEnable(Group $group, Request $request)
    {
        $group->is_enable = $request->is_enable ?: 0;
        $group->save();
        return $this->updateSuccess();
    }
}
