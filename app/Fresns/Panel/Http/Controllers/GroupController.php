<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Group;
use App\Models\Plugin;
use App\Models\Member;
use App\Models\Language;
use App\Models\MemberRole;
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
                ->with('member', 'plugin', 'names', 'descriptions')
                ->paginate();

            $groups->map(function($group) {
                $memberIds = $group->permission['admin_members'] ?? [];
                $group->admin_members = Member::whereIn('id', $memberIds)->get();
            });
        }

        extract(get_object_vars($this));

        $plugins = Plugin::all();
        $plugins = $plugins->filter(function ($plugin) {
            return in_array('pay', $plugin->scene);
        });

        $roles = MemberRole::with('names')->get();
        $members = Member::all();

        return view('panel::operation.group.index', compact(
            'categories',
            'groups',
            'typeModeLabels',
            'parentId',
            'permissioLabels',
            'plugins',
            'roles',
            'members'
        ));
    }

    public function recommendIndex()
    {
        $categories = Group::typeCategory()
            ->with('names', 'descriptions')
            ->get();

        $groups = Group::typeGroup()
            ->with('member', 'plugin', 'category')
            ->where('is_recommend', 1)
            ->paginate();

        $groups->map(function($group) {
            $memberIds = $group->permission['admin_members'] ?? [];
            $group->admin_members = Member::whereIn('id', $memberIds)->get();
        });

        $plugins = Plugin::all();
        $plugins = $plugins->filter(function ($plugin) {
            return in_array('pay', $plugin->scene);
        });

        $roles = MemberRole::with('names')->get();
        $members = Member::all();

        extract(get_object_vars($this));
        return view('panel::operation.group.recommend', compact(
            'categories',
            'groups',
            'typeModeLabels',
            'permissioLabels',
            'plugins',
            'roles',
            'members'
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

    public function store(Group $group, Request $request)
    {
        $group->uuid = \Str::random(12);
        $group->name = $request->names[$this->defaultLanguage] ?? (current(array_filter($request->names)) ?: '');
        $group->description = $request->descriptions[$this->defaultLanguage] ?? (current(array_filter($request->descriptions)) ?: '');
        $group->rank_num = $request->rank_num;
        $group->cover_file_url = $request->cover_file_url;
        $group->banner_file_url = $request->banner_file_url;
        $group->is_enable = $request->is_enable;
        // 分类
        if ($request->is_category) {
            $group->permission = [];
            $group->type = 1;
        } else {
            $group->parent_id = $request->parent_id;
            $group->type_mode = $request->type_mode;
            $group->type_find = $request->type_find;
            $group->type_follow = $request->type_follow;
            $group->is_recommend = $request->is_recommend;
            $group->plugin_unikey = $request->plugin_unikey;
            $permission = $request->permission;
            $permission['publish_post_review'] = (bool)$permission['publish_post_review'] ?? 0;
            $permission['publish_comment_review'] = (bool)$permission['publish_comment_review'] ?? 0;
            $group->permission = $permission;
            $group->type = 2;
        }
        $group->save();

        if ($request->update_name) {
            foreach ($request->names as $langTag => $content) {
                $language = Language::tableName('groups')
                    ->where('table_id', $group->id)
                    ->where('table_field', 'name')
                    ->where('lang_tag', $langTag)
                    ->first();

                if (!$language) {
                    // create but no content
                    if (!$content) {
                        continue;
                    }
                    $language = new Language();
                    $language->fill([
                      'table_name' => 'groups',
                      'table_field' => 'name',
                      'table_id' => $group->id,
                      'lang_tag' => $langTag,
                  ]);
                }

                $language->lang_content = $content;
                $language->save();
            }
        }


        if ($request->update_description) {
            foreach ($request->descriptions as $langTag => $content) {
                $language = Language::tableName('groups')
                    ->where('table_id', $group->id)
                    ->where('table_field', 'description')
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
                        'table_field' => 'description',
                        'table_id' => $group->id,
                        'lang_tag' => $langTag,
                    ]);
                }

                $language->lang_content = $content;
                $language->save();
            }
        }

        return $this->createSuccess();
    }

    public function update(Group $group, Request $request)
    {
        $group->name = $request->names[$this->defaultLanguage] ?? (current(array_filter($request->names)) ?: '');
        $group->description = $request->descriptions[$this->defaultLanguage] ?? (current(array_filter($request->descriptions)) ?: '');
        $group->rank_num = $request->rank_num;
        $group->cover_file_url = $request->cover_file_url;
        $group->banner_file_url = $request->banner_file_url;
        $group->is_enable = $request->is_enable;
        // 分类
        if ($request->is_category) {
            $group->permission = [];
        } else {
            $group->parent_id = $request->parent_id;
            $group->type_mode = $request->type_mode;
            $group->type_find = $request->type_find;
            $group->type_follow = $request->type_follow;
            $group->is_recommend = $request->is_recommend;
            $group->plugin_unikey = $request->plugin_unikey;
            $permission = $request->permission;
            $permission['publish_post_review'] = (bool)$permission['publish_post_review'] ?? 0;
            $permission['publish_comment_review'] = (bool)$permission['publish_comment_review'] ?? 0;
            $group->permission = $permission;
        }
        $group->save();

        if ($request->update_name) {
            foreach ($request->names as $langTag => $content) {
                $language = Language::tableName('groups')
                    ->where('table_id', $group->id)
                    ->where('table_field', 'name')
                    ->where('lang_tag', $langTag)
                    ->first();

                if (!$language) {
                    // create but no content
                    if (!$content) {
                        continue;
                    }
                    $language = new Language();
                    $language->fill([
                      'table_name' => 'groups',
                      'table_field' => 'name',
                      'table_id' => $group->id,
                      'lang_tag' => $langTag,
                  ]);
                }

                $language->lang_content = $content;
                $language->save();
            }
        }

        if ($request->update_description) {
            foreach ($request->descriptions as $langTag => $content) {
                $language = Language::tableName('groups')
                    ->where('table_id', $group->id)
                    ->where('table_field', 'description')
                    ->where('lang_tag', $langTag)
                    ->first();

                if (!$language) {
                    // create but no content
                    if (!$content) {
                        continue;
                    }
                    $language = new Language();
                    $language->fill([
                        'table_name' => 'groups',
                        'table_field' => 'description',
                        'table_id' => $group->id,
                        'lang_tag' => $langTag,
                    ]);
                }

                $language->lang_content = $content;
                $language->save();
            }
        }


        return $this->updateSuccess();
    }

    public function updateEnable(Group $group, Request $request)
    {
        $group->is_enable = $request->is_enable ?: 0;
        $group->save();
        return $this->updateSuccess();
    }

    public function destroy(Group $group)
    {
        // 分类
        if ($group->type == 1 ) {
            $group->groups()->delete();
        }
        $group->delete();
        return $this->deleteSuccess();
    }

    public function changeCategory(Group $group, Request $request)
    {
        if ($request->category_id) {
            $group->parent_id = $request->category_id;
            $group->save();
        }

        return $this->updateSuccess();
    }
}
