<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsGroups;

use App\Base\Controllers\BaseAdminController;
use App\Helpers\CommonHelper;
use App\Helpers\StrHelper;
use App\Http\Center\Common\ValidateService;
use Illuminate\Http\Request;

class AmControllerAdmin extends BaseAdminController
{
    public function __construct()
    {
        $this->service = new AmService();
    }

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
        // permission (Parameter assembly)

        parent::store($request);
    }

    // edit
    public function update(Request $request)
    {
        ValidateService::validateRule($request, $this->rules(Amconfig::RULE_UPDATE));

        $this->hookUpdateValidateAfter();
        if ($request->is_recommend) {
            $is_recommend = $request->is_recommend === 'true' ? 1 : 0;
            $request->offsetSet('is_recommend', $is_recommend);
        }
        if ($request->type_find) {
            $type_find = $request->type_find === 'true' ? 1 : 0;
            $request->offsetSet('type_find', $type_find);
        }
        $id = $request->input('id');
        // permission (Parameter assembly)
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
        if (empty($request->nameArr)) {
            $this->index($request);
        }

        $this->service->hookUpdateAfter($id);

        // Clear request data
        CommonHelper::removeRequestFields($this->service->getSearchableFields());

        $this->index($request);
    }

    public function index2(Request $request)
    {
        parent::index($request);
    }

    // Delete group categories (can be deleted when there are no groups under the category)
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
            $this->errorInfo(3001, 'There are groups under the group category, can not be deleted by operation.');
        }
        FresnsGroups::where('id', $id)->delete();
        $this->success();
    }

    // Moving Group
    public function moveByGroups(Request $request)
    {
        $id = $request->input('group_id');
        $moveGroupId = $request->input('moveGroupId');
        $groupInfo = FresnsGroups::find($id);
        if ($groupInfo['type'] == 1) {
            $this->errorInfo(3001, 'Group category is not removable');
        }
        $pGroupInfo = FresnsGroups::find($moveGroupId);
        if ($pGroupInfo['type'] == 2) {
            $this->errorInfo(3001, 'Can only be moved under the group category');
        }
        FresnsGroups::where('id', $id)->update(['parent_id' => $moveGroupId]);
        $this->success();
    }

    // Validation Rules
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
