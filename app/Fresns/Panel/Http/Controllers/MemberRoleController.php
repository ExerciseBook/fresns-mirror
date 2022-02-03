<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Language;
use App\Models\MemberRole;
use Illuminate\Http\Request;
use App\Models\MemberRoleRel;
use App\Fresns\Panel\Http\Requests\UpdateMemberRoleRequest;

class MemberRoleController extends Controller
{
    public function index()
    {
        $roles = MemberRole::orderBy('rank_num')->with('names')->get();

        $typeLabels = [
            1 => '管理人员类',
            2 => '系统设置类',
            3 => '用户运营类',
        ];

        return view('panel::operation.role', compact(
            'roles',
            'typeLabels'
        ));
    }

    public function store(MemberRole $memberRole, UpdateMemberRoleRequest $request)
    {
        $memberRole->fill($request->all());
        $memberRole->permission = json_decode(config('panel.member_role_default_permission'), true);
        if ($request->no_color) {
            $memberRole->nickname_color = null;
        }
        $memberRole->name = $request->names[$this->defaultLanguage] ?? (current(array_filter($request->names)) ?: '');
        $memberRole->save();

        foreach ($request->names as $langTag => $content) {
            $language = Language::tableName('member_roles')
                ->where('table_id', $memberRole->id)
                ->where('lang_tag', $langTag)
                ->first();

            if (!$language) {
                // create but no content
                if (!$content) {
                    continue;
                }
                $language = new Language();
                $language->fill([
                    'table_name' => 'member_roles',
                    'table_field' => 'name',
                    'table_id' => $memberRole->id,
                    'lang_tag' => $langTag,
                ]);
            }

            $language->lang_content = $content;
            $language->save();
        }

        return $this->createSuccess();
    }

    public function update(MemberRole $memberRole, UpdateMemberRoleRequest $request)
    {
        $memberRole->update($request->all());
        if ($request->no_color) {
            $memberRole->nickname_color = null;
        }
        $memberRole->name = $request->names[$this->defaultLanguage] ?? (current(array_filter($request->names)) ?: '');
        $memberRole->save();

        foreach ($request->names as $langTag => $content) {
            $language = Language::tableName('member_roles')
                ->where('table_id', $memberRole->id)
                ->where('lang_tag', $langTag)
                ->first();

            if (!$language) {
                // create but no content
                if (!$content) {
                    continue;
                }
                $language = new Language();
                $language->fill([
                    'table_name' => 'member_roles',
                    'table_field' => 'name',
                    'table_id' => $memberRole->id,
                    'lang_tag' => $langTag,
                ]);
            }

            $language->lang_content = $content;
            $language->save();
        }
        return $this->updateSuccess();
    }

    public function destroy(MemberRole $memberRole, Request $request)
    {
        if ($request->role_id) {
            MemberRoleRel::where('role_id', $memberRole->id)->update(['role_id' => $request->role_id]);
        }

        $memberRole->delete();
        return $this->deleteSuccess();
    }


    public function updateRank($id, Request $request)
    {
        $memberRole = MemberRole::findOrFail($id);
        $memberRole->rank_num = $request->rank_num;
        $memberRole->save();

        return $this->updateSuccess();
    }

    public function showPermissions(MemberRole $memberRole)
    {
        $permission = collect($memberRole->permission)->mapWithKeys(function($perm) {
            return [$perm['permKey'] => $perm];
        })->toArray();

        $customPermission = collect($memberRole->permission)->filter(function($perm) {
            return $perm['isCustom'] ?? false;
        })->mapWithKeys(function($perm) {
            return [$perm['permKey'] => $perm];
        })->toArray();

        return view('panel::operation.permission', compact('permission', 'memberRole', 'customPermission'));
    }

    public function updatePermissions(MemberRole $memberRole, Request $request)
    {
        $permission = collect($request->permission)->map(function($value, $key) {
            $boolPerms = [
                'content_view', 'dialog', 'post_publish', 'post_review',
                'post_email_verify', 'post_phone_verify', 'post_prove_verify', 'post_limit_status',
                'comment_publish', 'comment_review', 'comment_email_verify', 'comment_phone_verify',
                'comment_prove_verify', 'post_editor_image', 'post_editor_video', 'post_editor_audio',
                'post_editor_doc', 'comment_editor_image', 'comment_editor_video', 'comment_editor_audio',
                'comment_editor_doc'
            ];
            if (in_array($key, $boolPerms)) {
                $value = (bool) $value;
            }
            return [
                'permKey' => $key,
                'permValue' => $value,
                'permStatus' => '',
                'isCustom' => false,
            ];
        });
        $customPermission = collect($request->custom_permissions['permKey'] ?? [])->filter()->map(function($value, $key) use ($request) {
            return [
                'permKey' => $value,
                'permValue' => $request->custom_permissions['permValue'][$key] ?? '',
                'permStatus' => '',
                'isCustom' => true,
            ];
        });
        $memberRole->permission = $permission->merge($customPermission)->values()->toArray();
        $memberRole->save();

        return $this->updateSuccess();
    }
}
