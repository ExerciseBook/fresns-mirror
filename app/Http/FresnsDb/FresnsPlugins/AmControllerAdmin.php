<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsPlugins;

use App\Base\Controllers\BaseAdminController;
use App\Http\Center\Base\FresnsCode;
use App\Http\Center\Common\ValidateService;
use App\Http\Center\Helper\PluginHelper;
use Illuminate\Http\Request;

class AmControllerAdmin extends BaseAdminController
{
    public function __construct()
    {
        $this->service = new AmService();
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
