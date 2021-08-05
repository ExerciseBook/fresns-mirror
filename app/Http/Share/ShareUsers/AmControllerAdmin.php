<?php
/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Share\ShareUsers;


use App\Base\Controllers\BaseAdminController;
use App\Helpers\StrHelper;
use App\Http\Share\Common\ErrorCodeService;
use App\Http\Share\Common\ValidateService;
use App\Plugins\Share\ShareUsersAppend\ShareUsersAppend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AmControllerAdmin extends BaseAdminController
{

    public function __construct()
    {
        $this->service = new AmService();
    }

    public function index(Request $request)
    {
        $request->input('user_type', AmConfig::USER_TYPE_USER);
        parent::index($request);
    }
   
   
    public function hookStoreValidateAfter()
    {
        $request = \request();

        $request->offsetSet('user_type', AmConfig::USER_TYPE_USER);
        $request->offsetSet('api_token', StrHelper::createToken());
        $request->offsetSet('password', StrHelper::createPassword($request->input('password')));

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
