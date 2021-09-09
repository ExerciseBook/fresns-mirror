<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsPluginUsages;

use App\Base\Controllers\BaseAdminController;
use App\Helpers\CommonHelper;
use App\Http\Center\Common\ValidateService;
use Illuminate\Http\Request;

class AmControllerAdmin extends BaseAdminController
{
    public function __construct()
    {
        $this->service = new AmService();
    }

    // edit plugin usages
    public function update(Request $request)
    {
        ValidateService::validateRule($request, $this->rules(Amconfig::RULE_UPDATE));

        $this->hookUpdateValidateAfter();
        if ($request->is_group_admin) {
            // dd(1);
            $is_group_admin = $request->is_group_admin === 'true' ? 1 : 0;
            // dd($is_group_admin);
            $request->offsetSet('is_group_admin', $is_group_admin);
        }
        $id = $request->input('id');
        $this->service->update($id);
        // dd($request);
        if (empty($request->name)) {
            $this->index($request);
        }

        $this->service->hookUpdateAfter($id);

        // Clear request data
        CommonHelper::removeRequestFields($this->service->getSearchableFields());

        $this->index($request);
    }

    // Edit Sort
    public function updateRankNum(Request $request)
    {
        $more_json = $request->input('more_json');
        $more_json_decode = json_decode($more_json, true);
        if ($more_json_decode) {
            foreach ($more_json_decode as $v) {
                FresnsPluginUsages::where('id', $v['id'])->update(['rank_num' => $v['rank_num']]);
            }
        }
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
