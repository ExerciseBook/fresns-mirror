<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsEmojis;

use App\Base\Controllers\BaseAdminController;
use App\Helpers\CommonHelper;
use App\Http\Share\Common\ErrorCodeService;
use Illuminate\Http\Request;

class AmControllerAdmin extends BaseAdminController
{

    public function __construct()
    {
        $this->service = new AmService();
    }

    public function index(Request $request)
    {
        $parentId = $request->input('parent_id');
        if (empty($parentId)) {
            $request->offsetSet('type', AmConfig::TYPE_GROUP);
        }

        parent::index($request);
    }

    // 删除
    public function destroy(Request $request)
    {
        //验证
        $ids = $request->input('ids');
        $idArr = explode(',', $ids);

        //判断是否有子类
        $count = AmModel::whereIn('parent_id', $idArr)->count();
        if ($count > 0) {
            $this->errorInfo(ErrorCodeService::CODE_FAIL, '存在子类，不允许删除');
        }
        // 执行
        $this->service->destroy($idArr);

        // 清空request数据
        CommonHelper::removeRequestFields($this->service->getSearchableFields());

        $this->index($request);
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
