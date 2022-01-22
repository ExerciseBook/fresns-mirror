<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Language;
use App\Models\MemberRole;
use Illuminate\Http\Request;
use App\Fresns\Panel\Http\Requests\UpdateMemberRoleRequest;

class MemberRoleController extends Controller
{
    public function index()
    {
        $roles = MemberRole::with('names')->get();

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
        $memberRole->permission = [];
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

    public function destroy(StopWord $memberRole)
    {
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
}
