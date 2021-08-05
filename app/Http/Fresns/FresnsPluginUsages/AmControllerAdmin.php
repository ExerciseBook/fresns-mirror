<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsPluginUsages;

use App\Base\Controllers\BaseAdminController;
use Illuminate\Http\Request;
use App\Http\Share\Common\ValidateService;
use App\Helpers\CommonHelper;

class AmControllerAdmin extends BaseAdminController
{

    public function __construct()
    {
        $this->service = new AmService();
    }

    //编辑
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

        // 清空request数据
        CommonHelper::removeRequestFields($this->service->getSearchableFields());

        $this->index($request);
    }

    // 编辑排序
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

    // 添加多语言标识
    public function addLangCode(Request $request)
    {
        $content = json_encode(AmConfig::MULTILINGUAL_OPTION);
        $input = [
            'item_key' => '',
            'alias_key' => 'lang_code',
            'content' => $content,
        ];
        // dd($input);
        (new TweetConfig())->store($input);
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
