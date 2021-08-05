<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Center\Scene;

use App\Helpers\CommonHelper;
use App\Http\Share\Common\LogService;
use App\Http\Share\Common\ValidateService;
use App\Http\Center\Base\BasePluginConfig;
use App\Http\Center\Base\BaseSceneController;
use App\Http\Center\Base\FresnsCode;
use App\Http\Center\Helper\PluginHelper;
use App\Http\Center\Helper\PluginRpcHelper;
use App\Http\Fresns\FresnsConfigs\FresnsConfigs;
use App\Http\Fresns\FresnsConfigs\FresnsConfigsConfig;
use App\Plugins\Tweet\TweetFileAppends\TweetFileAppends;
use App\Plugins\Tweet\TweetFiles\TweetFiles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * 文件上传
 * 处理文件上传
 */
class FileSceneController extends BaseSceneController
{

    // 上传文件
    public function uploadFile(Request $request){
        $t1 = time();

        // 验证
        ValidateService::validateRule($request, $this->rules(FileSceneConfig::UPLOAD));


        // 确认目录
        $options['file_type'] = $request->input('file_type');
        $options['table_type'] = $request->input('table_type');
        $storePath = FileSceneService::getPath($options);
        if(!$storePath){
            $this->error(FresnsCode::CODE_FAIL);
        }

        // 获取UploadFile的实例
        $uploadFile = $request->file('file');


        // 存储
        $path = $uploadFile->store($storePath);

        $userId = Auth::id();
        $domain = CommonHelper::domain();
        $file['file_type'] = $request->input('file_type',1);
        $file['file_name'] = $uploadFile->getClientOriginalName();
        $file['file_extension'] = $uploadFile->getClientOriginalExtension();
        $file['file_path'] = str_replace('public','',$path);
        $file['rank_num'] = $request->input('rank_num',99);
        $file['table_type'] = $request->input('table_type',NULL);
        $file['table_name'] = $request->input('table_name',NULL);
        $file['table_field'] = $request->input('table_field',NULL);
        $file['table_id'] = $request->input('table_id',null);
        $file['table_key'] = $request->input('table_key',null);


        LogService::info("文件存储本地成功 ", $file);
        $t2 = time();

        // 插入
        $retId = TweetFiles::insertGetId($file);
        $data['file_id'] = $retId;
        $data['file_url'] = $domain . $file['file_path'];
        $file['real_path'] = $path;
        $input = [
            'file_id' => $retId,
            'file_mime' => $uploadFile->getMimeType(),
            'file_size' => $uploadFile->getSize(),
            // 'file_original_path' => Storage::url($path),
        ];
        TweetFileAppends::insert($input);
        LogService::info("上传本地时间", ($t2 - $t1));

        // 将file信息入库
     //   $file = FileSceneService::createFile($file);

        // 获取文件上传方式，本地或者插件，如果是有插件则使用插件上传
        $uploadProvider = FileSceneService::getUploadProvider();

        // 如果是插件, 1 获取插件key, 2 调用插件默认处理方法
        if($uploadProvider == FileSceneConfig::UPLOAD_PROVIDER_PLUGIN){
            $pluginUniKey = 'AliOss';

            // 执行上传
            $pluginClass = PluginHelper::findPluginClass($pluginUniKey);

            if(empty($pluginClass)){
                LogService::error("未找到插件类");
                $this->error(FresnsCode::CODE_FAIL);
            }

            $paramsExist = false;
            if ($file['file_type'] == FileSceneConfig::FILE_TYPE_1) {
                $configMapInDB = FresnsConfigs::where('item_tag',FresnsConfigsConfig::STORAGE)->pluck('item_value','item_key')->toArray();
                $paramsExist = ValidateService::validParamExist($configMapInDB,
                ['images_secret_id', 'images_secret_key', 'images_bucket_domain']);

            }
            if ($file['file_type'] == FileSceneConfig::FILE_TYPE_2) {
                $configMapInDB = FresnsConfigs::where('item_tag',FresnsConfigsConfig::VIDEO_STORAGE)->pluck('item_value','item_key')->toArray();
                $paramsExist = ValidateService::validParamExist($configMapInDB,
                ['videos_secret_id', 'videos_secret_key', 'videos_bucket_domain']);
            }
            if ($file['file_type'] == FileSceneConfig::FILE_TYPE_3) {
                $configMapInDB = FresnsConfigs::where('item_tag',FresnsConfigsConfig::AUDIO_STORAGE)->pluck('item_value','item_key')->toArray();
                $paramsExist = ValidateService::validParamExist($configMapInDB,
                ['audios_secret_id', 'audios_secret_key', 'audios_bucket_domain']);
            }
            if ($file['file_type'] == FileSceneConfig::FILE_TYPE_4) {
                $configMapInDB = FresnsConfigs::where('item_tag',FresnsConfigsConfig::DOC_STORAGE)->pluck('item_value','item_key')->toArray();
                $paramsExist = ValidateService::validParamExist($configMapInDB,
                ['docs_secret_id', 'docs_secret_key', 'docs_bucket_domain	']);
            }

            if($paramsExist == false){
                LogService::error("插件信息未配置");
                $this->error(FresnsCode::CODE_FAIL);
            }

            $cmd = BasePluginConfig::PLG_CMD_DEFAULT;
            $input = $file ;
            $resp = PluginRpcHelper::call($pluginClass, $cmd, $input);

            if(PluginRpcHelper::isErrorPluginResp($resp)){
                $this->errorCheckInfo($resp);
            }
            $file['oss_url'] = $resp['output']['oss_url'] ?? '';
        }

        // 数据返回格式根据联调确认
        $data = [];
        $data['file'] = $file;
        $this->success($data);
    }


}
