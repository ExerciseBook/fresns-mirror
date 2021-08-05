<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsMemberRoles;

use App\Base\Controllers\BaseAdminController;


use App\Http\Fresns\FresnsMemberRoleRels\FresnsMemberRoleRels;
use App\Http\Share\Common\ValidateService;
use Illuminate\Http\Request;

class AmControllerAdmin extends BaseAdminController
{

    public function __construct()
    {
        $this->service = new AmService();
    }

    public function merge(Request $request)
    {
        // 校验参数
        $rule = [
            'role_id' => 'required',
            'to_role_id' => 'required',
        ];
        ValidateService::validateRule($request, $rule);

        $roleId = $request->input('role_id');
        $to_role_id = $request->input('to_role_id');

        $memberIdArr1 = FresnsMemberRoleRels::where('role_id', $roleId)->pluck('member_id')->toArray();
        $memberIdArr2 = FresnsMemberRoleRels::where('role_id', $to_role_id)->pluck('member_id')->toArray();

        $memberIdArr = [];
        foreach ($memberIdArr2 as $v) {
            if (!in_array($v, $memberIdArr1)) {
                $memberIdArr[] = $v;
            }
        }

        if ($memberIdArr) {
            FresnsMemberRoleRels::where('role_id', $to_role_id)->whereIn('member_id',
                $memberIdArr)->update(['role_id' => $roleId]);
        }


        FresnsMemberRoleRels::where('role_id', $to_role_id)->delete();

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
