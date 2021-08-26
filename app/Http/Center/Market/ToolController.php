<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Center\Market;

use App\Helpers\SignHelper;
use App\Http\Center\Base\BasePluginApiController;
use App\Http\Fresns\FresnsApi\Base\AmConfig;
use App\Http\Fresns\FresnsSessionKeys\FresnsSessionKeys;
use App\Http\Share\Common\ErrorCodeService;
use App\Http\Share\Common\LogService;
use Illuminate\Http\Request;

class ToolController extends BasePluginApiController
{
    /**
     * 获取远程插件列表.
     * @param Request $request
     */
    public function sign(Request $request)
    {
        $headerFieldArr = AmConfig::HEADER_FIELD_ARR;
        foreach ($headerFieldArr as $headerField) {
            $headerContent = request()->header($headerField);
            if (empty($headerContent)) {
                $info = [
                    'missing header' => $headerField,
                ];

                $this->error(ErrorCodeService::HEADER_ERROR, $info);
            }
        }

        $dataMap = [];
        foreach (AmConfig::SIGN_FIELD_ARR as $signField) {
            $signFieldValue = request()->header($signField);
            if (! empty($signFieldValue)) {
                $dataMap[$signField] = $signFieldValue;
            }
        }

        $appId = request()->header('appId');
        $sessionKeys = FresnsSessionKeys::where('app_id', $appId)->first();

        if (empty($sessionKeys)) {
            $info = [
                'appId' => '无此记录',
            ];
            $this->error(ErrorCodeService::NO_RECORD, $info);
        }
        $signKey = $sessionKeys['app_secret'];

        $genSign = SignHelper::genSign($dataMap, $signKey);
        $data = [
            '签名参数' => $dataMap,
            '签名key' => $signKey,
            '生成的签名' => $genSign,
        ];

        $this->success($data);
    }
}
