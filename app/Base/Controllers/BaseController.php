<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Base\Controllers;

use App\Base\Config\BaseConfig;
use App\Helpers\CommonHelper;
use App\Http\Share\Common\ErrorCodeService;
use App\Http\Share\Common\ValidateService;
use App\Traits\ApiTrait;
use App\Traits\HookControllerTrait;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    use ApiTrait;
    use HookControllerTrait;

    protected $service;

    // 列表
    public function index(Request $request)
    {
        ValidateService::validateRule($request, $this->rules(BaseConfig::RULE_INDEX));

        $currentPage = $request->input('current', 1);

        $request->offsetSet('currentPage', $currentPage);

        $data = $this->service->searchData();

        $this->success($data);
    }

    // 新增
    public function store(Request $request)
    {
        $rules = $this->rules(BaseConfig::RULE_STORE);

        ValidateService::validateRule($request, $rules, $this->messages(BaseConfig::RULE_STORE));

        $this->hookStoreValidateAfter();

        $this->service->store();

        // 清空request数据
        CommonHelper::removeRequestFields($this->service->getSearchableFields());

        $this->index($request);
    }

    // 更新
    public function update(Request $request)
    {
        ValidateService::validateRule($request, $this->rules(BaseConfig::RULE_UPDATE));

        $this->hookUpdateValidateAfter();

        $id = $request->input('id');

        $this->service->update($id);

        // 清空request数据
        CommonHelper::removeRequestFields($this->service->getSearchableFields());

        $this->index($request);
    }

    // 详情
    public function detail(Request $request)
    {
        ValidateService::validateRule($request, $this->rules(BaseConfig::RULE_DETAIL));

        $id = $request->input('id');
        $data = $this->service->detail($id);

        $this->success($data);
    }

    // 删除
    public function destroy(Request $request)
    {
        //验证
        $ids = $request->input('ids');
        $idArr = explode(',', $ids);

        // 执行
        $this->service->destroy($idArr);

        // 清空request数据
        CommonHelper::removeRequestFields($this->service->getSearchableFields());

        $this->index($request);
    }

    // 验证规则
    public function rules($ruleType)
    {
        return [];
    }

    // 验证规则语言
    public function messages($ruleType)
    {
        return [];
    }

    // 导出
    public function export(Request $request)
    {
        $data = $this->service->exportData();
        $this->success($data);
    }

    // 导入
    public function import(Request $request)
    {
        //ValidateService::validateRule($request, BaseConfig::IMPORT_RULE);

        $uploadFile = $request->file('excel');

        $path = $uploadFile->store('public/avatars');

        $storagePath = storage_path();
        $filePath = implode(DIRECTORY_SEPARATOR, [$storagePath, 'app', $path]);
        $parseInfo = $this->service->importData($filePath);

        $code = $parseInfo['code'] ?? ErrorCodeService::CODE_OK;
        $data = $parseInfo['data'] ?? ['default' => ''];

        // 成功则返回数据
        if ($code == ErrorCodeService::CODE_OK) {
            $this->success($data);
        }

        $msg = $parseInfo['msg'] ?? [];

        // 失败返回信息详情
        $this->errorInfo($code, $msg, [], $data);
    }

    // 拉取数据
    public function fetch(Request $request)
    {
        $this->service->fetchData();
        $this->success();
    }
}
