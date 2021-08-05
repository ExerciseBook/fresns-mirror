<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsClientKeys;

use App\Base\Controllers\BaseAdminController;
use Illuminate\Http\Request;
use App\Helpers\StrHelper;
use App\Http\Share\Common\ValidateService;
use App\Helpers\CommonHelper;

class AmControllerAdmin extends BaseAdminController
{

    public function __construct()
    {
        $this->service = new AmService();
    }

    public function store(Request $request)
    {
        ValidateService::validateRule($request, $this->rules(Amconfig::RULE_STORE));
        $app_id = strtolower('tw'.StrHelper::randString(14));
        $app_secret = strtolower(StrHelper::randString(32));
        $request->offsetSet('app_secret', $app_secret);
        $request->offsetSet('app_id', $app_id);
        parent::store($request); // TODO: Change the autogenerated stub
    }

    //编辑
    public function update(Request $request)
    {
        ValidateService::validateRule($request, $this->rules(Amconfig::RULE_UPDATE));

        $this->hookUpdateValidateAfter();

        $id = $request->input('id');
        $this->service->update($id);

        if (empty($request->name)) {
            $this->index($request);
        }


        // 清空request数据
        CommonHelper::removeRequestFields($this->service->getSearchableFields());

        $this->index($request);
    }

    // 重置密钥
    public function referSecret(Request $request)
    {
        $id = $request->input('id');
        $app_secret = strtolower(StrHelper::randString(32));
        TweetClientKeys::where('id', $id)->update(['app_secret' => $app_secret]);
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
