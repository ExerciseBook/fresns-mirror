<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsFiles;


use App\Base\Controllers\BaseAdminController;
use App\Helpers\CommonHelper;
use App\Http\Center\Base\BasePluginConfig;
use App\Http\Center\Helper\PluginHelper;
use App\Http\Center\Helper\PluginRpcHelper;
use App\Http\Fresns\FresnsApi\Helpers\ApiCommonHelper;
use App\Http\Share\Common\LogService;
use App\Http\Share\Common\ValidateService;
use App\Plugins\Tweet\TweetConfigs\TweetConfigs;
use App\Plugins\Tweet\TweetConfigs\TweetConfigsConfig;
use App\Plugins\Tweet\TweetFileAppends\TweetFileAppends;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AmControllerAdmin extends BaseAdminController
{

    public function __construct()
    {
        $this->service = new AmService();
    }

    //上传图片
    public function upload(Request $request)
    {
        $t1 = time();

        // 获取UploadFile的实例
        $uploadFile = $request->file('file');

        $path = $uploadFile->store('public/avatars');

        $pluginUniKey = 'AliOss';
        // 执行上传
        $pluginClass = PluginHelper::findPluginClass($pluginUniKey);
        if (empty($pluginClass)) {
            LogService::error("未找到插件类");
            $this->errorInfo(30001, '未配置存储设置，请配置后再上传');
        }
        $file['file_type'] = $request->input('type', 1);
        $configMapInDB = TweetConfigs::where('item_tag', TweetConfigsConfig::STORAGE)->pluck('item_value',
            'item_key')->toArray();
        $paramsExist = ValidateService::validParamExist($configMapInDB,
            ['images_secret_id', 'images_secret_key', 'images_bucket_domain']);
        if ($paramsExist == false) {
            LogService::error("插件信息未配置");
            $this->errorInfo(30001, '未配置存储设置，请配置后再上传');
        }
        $domain = CommonHelper::domain();

        $file['file_name'] = $uploadFile->getClientOriginalName();
        $file['file_type'] = $request->input('file_type', 1);
        $file['file_extension'] = $uploadFile->getMimeType();
        $file['file_path'] = Storage::url($path);
        $file['table_name'] = $request->input('table_name', 'configs');
        $file['table_field'] = $request->input('table_field', null);
        $file['table_key'] = $request->input('table_key', null);
        $file['table_id'] = $request->input('table_id', null);
        $file['uuid'] = ApiCommonHelper::createUuid();
        $file['real_path'] = $path;

        $cmd = BasePluginConfig::PLG_CMD_DEFAULT;
        $input = $file;
        $resp = PluginRpcHelper::call($pluginClass, $cmd, $input);

        if (PluginRpcHelper::isErrorPluginResp($resp)) {
            $this->errorCheckInfo($resp);
        }
        // dd($resp);
        LogService::info("文件存储本地成功 ", $file);
        $t2 = time();
        unset($file['real_path']);
        // 插入
        $retId = AmModel::insertGetId($file);
        $data['file_id'] = $retId;
        $data['file_url'] = $resp['output']['oss_url'] ?? '';

        $input = [
            'file_id' => $retId,
            'platform_id' => 0,
            'transcoding_status' => 0,
        ];
        TweetFileAppends::insert($input);
        LogService::info("上传本地时间", ($t2 - $t1));

        $this->success($data);
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
