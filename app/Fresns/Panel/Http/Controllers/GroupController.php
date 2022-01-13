<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Group;
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
        return view('panel::operation.group.index', compact(
            'categories', 'groups', 'typeModeLabels', 'parentId',
            'permissioLabels'
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
            'groups', 'typeModeLabels', 'permissioLabels'
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
            'groups', 'typeModeLabels', 'permissioLabels'
        ));
    }

    public function store(Group $gorup, $request)
    {
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
