<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\User;

use App\Base\Resources\BaseAdminResource;
use App\Helpers\DateHelper;
use App\Http\FresnsDb\FresnsConfigs\FresnsConfigsConfig;
use App\Http\FresnsDb\FresnsLanguages\FresnsLanguagesService;
use App\Http\FresnsDb\FresnsPluginUsages\FresnsPluginUsages;
use App\Http\FresnsDb\FresnsPluginUsages\FresnsPluginUsagesConfig;
use App\Http\FresnsDb\FresnsUserWalletLogs\FresnsUserWalletLogs;

class FresnsUserWalletLogsResource extends BaseAdminResource
{
    public function toArray($request)
    {
        //user_wallet_logs > object_name >> plugin_usages(type=1 或 2) > name 多语言，如果插件关联已经删除，则原样输出 object_name 字段值
        $uid = request()->input('uid');
        $langTag = request()->input('langTag');
        $pluginUsages = FresnsPluginUsages::whereIn('type', [1, 2])->where('plugin_unikey',
            $this->objuct_name)->first();

        if (empty($pluginUsages)) {
            $name = $this->object_name;
        } else {
            $name = FresnsLanguagesService::getLanguageByTableId(FresnsPluginUsagesConfig::CFG_TABLE, 'name',
                $pluginUsages['id'], $langTag);
        }
        // 默认字段
        $default = [
            'type' => $this->object_type,
            'amount' => $this->amount,
            'transactionAmount' => $this->transaction_amount,
            'systemFee' => $this->system_fee,
            'openingBalance' => $this->opening_balance,
            'closingBalance' => $this->closing_balance,
            'name' => $name,
            'remark' => $this->remark,
            'status' => $this->is_enable,
            'date' => DateHelper::asiaShanghaiToTimezone($this->created_at),

        ];

        return $default;
    }
}
