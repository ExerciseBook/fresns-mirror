<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsGroups;

use App\Base\Controllers\BaseAdminController;
use App\Helpers\CommonHelper;
use App\Helpers\StrHelper;
use App\Http\Share\Common\ValidateService;
use Illuminate\Http\Request;

class AmControllerAdmin extends BaseAdminController
{
    public function __construct()
    {
        $this->service = new AmService();
    }

    // public function index(Request $request)
    // {

    //     $data = $this->service->searchData();
    //     // dd($data);
    //     $listTreeData = $this->service->listTree();
    //     // dd($listTreeData);
    //     $data['list'] = $listTreeData;
    //     $data['common'] = $this->service->common();

    //     $this->success($data);
    // }

    public function store(Request $request)
    {
        // $uuid = rand(10000000,99999999);
        $uuid = strtolower(StrHelper::randString(8));
        $parent_id = $request->input('parent_id');
        if (! $parent_id) {
            $request->offsetSet('type', 1);
        } else {
            $admin_members = $request->input('admin_members', '');
            $publish_post = $request->input('publish_post');
            $publish_post_roles = $request->input('publish_post_roles', '');
            $publish_post_review = $request->input('publish_post_review');
            $publish_comment = $request->input('publish_comment');
            $publish_comment_roles = $request->input('publish_comment_roles', '');
            $publish_comment_review = $request->input('publish_comment_review');
            if ($admin_members) {
                $admin_members = explode(',', $admin_members);
            }
            if ($publish_post_roles) {
                $publish_post_roles = explode(',', $publish_post_roles);
            }
            if ($publish_comment_roles) {
                $publish_comment_roles = explode(',', $publish_comment_roles);
            }
            $permission = [];
            $permission['admin_members'] = $admin_members;
            $permission['publish_post'] = $publish_post;
            $permission['publish_post_roles'] = $publish_post_roles;
            $permission['publish_post_review'] = $publish_post_review;
            $permission['publish_comment'] = $publish_comment;
            $permission['publish_comment_roles'] = $publish_comment_roles;
            $permission['publish_comment_review'] = $publish_comment_review;
            $request->offsetSet('permission', json_encode($permission));
        }
        $request->offsetUnset('admin_members');
        $request->offsetSet('uuid', $uuid);
        // permission参数组装

        parent::store($request);
    }

    //编辑
    public function update(Request $request)
    {
        ValidateService::validateRule($request, $this->rules(Amconfig::RULE_UPDATE));

        $this->hookUpdateValidateAfter();
        if ($request->is_recommend) {
            // dd(1);
            $is_recommend = $request->is_recommend === 'true' ? 1 : 0;
            // dd($is_recommend);
            $request->offsetSet('is_recommend', $is_recommend);
        }
        if ($request->type_find) {
            // dd(1);
            $type_find = $request->type_find === 'true' ? 1 : 0;
            // dd($is_recommend);
            $request->offsetSet('type_find', $type_find);
        }
        $id = $request->input('id');
        // permission参数组装
        $admin_members = $request->input('admin_members', '');
        $publish_post = $request->input('publish_post');
        $publish_post_roles = $request->input('publish_post_roles');
        $publish_post_review = $request->input('publish_post_review');
        $publish_comment = $request->input('publish_comment');
        $publish_comment_roles = $request->input('publish_comment_roles');
        $publish_comment_review = $request->input('publish_comment_review');
        if ($admin_members) {
            $admin_members = explode(',', $admin_members);
        }
        if ($publish_post_roles) {
            $publish_post_roles = explode(',', $publish_post_roles);
        }
        if ($publish_comment_roles) {
            $publish_comment_roles = explode(',', $publish_comment_roles);
        }
        $permission = [];
        $permission['admin_members'] = $admin_members;
        $permission['publish_post'] = $publish_post;
        $permission['publish_post_roles'] = $publish_post_roles;
        $permission['publish_post_review'] = $publish_post_review;
        $permission['publish_comment'] = $publish_comment;
        $permission['publish_comment_roles'] = $publish_comment_roles;
        $permission['publish_comment_review'] = $publish_comment_review;
        $request->offsetSet('permission', json_encode($permission));
        $request->offsetUnset('admin_members');
        $this->service->update($id);
        // dd($request);
        if (empty($request->nameArr)) {
            $this->index($request);
        }

        $this->service->hookUpdateAfter($id);

        // 清空request数据
        CommonHelper::removeRequestFields($this->service->getSearchableFields());

        $this->index($request);
    }

    public function index2(Request $request)
    {
        parent::index($request);
    }

    // 删除小组（没有二级分类的小组爱可以删除）
    public function destroy(Request $request)
    {
        $table = AmConfig::CFG_TABLE;
        $rule = [
            'id' => "required|numeric|exists:{$table},id|",
        ];
        ValidateService::validateRule($request, $rule);
        $id = $request->input('id');
        $count = FresnsGroups::where('parent_id', $id)->count();
        if ($count > 0) {
            $this->errorInfo(3001, '存在下级分组，不能删除');
        }
        FresnsGroups::where('id', $id)->delete();
        $this->success();
    }

    // 移动小组
    public function moveByGroups(Request $request)
    {
        $id = $request->input('group_id');
        $moveGroupId = $request->input('moveGroupId');
        $groupInfo = FresnsGroups::find($id);
        if ($groupInfo['type'] == 1) {
            $this->errorInfo(3001, '小组分类不可移动');
        }
        $pGroupInfo = FresnsGroups::find($moveGroupId);
        if ($pGroupInfo['type'] == 2) {
            $this->errorInfo(3001, '只可移动到小组分类下面');
        }
        FresnsGroups::where('id', $id)->update(['parent_id' => $moveGroupId]);
        $this->success();
    }

    // 验证规则
    public function rules($ruleType)
    {
        $rule = [];

        $config = new AmConfig($this->service->getTable());

        switch ($ruleType) {
            case AmConfig::RULE_STORE:
                $rule = $config->storeRule();
                break;

            case AmConfig::RULE_UPDATE:
                $rule = $config->updateRule();
                break;

            case AmConfig::RULE_DESTROY:
                $rule = $config->destroyRule();
                break;

            case AmConfig::RULE_DETAIL:
                $rule = $config->detailRule();
                break;
        }

        return $rule;
    }
}
