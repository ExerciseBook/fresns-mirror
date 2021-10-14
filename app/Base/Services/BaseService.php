<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Base\Services;

use App\Base\Config\BaseConfig;
use App\Base\Models\BaseExport;
use App\Base\Models\BaseQuery;
use App\Helpers\CommonHelper;
use App\Traits\HookServiceTrait;
use Maatwebsite\Excel\Facades\Excel;


class BaseService
{
    use HookServiceTrait;
    public $msg = '-';
    protected $config;
    protected $resource;
    protected $resourceDetail;
    protected $model;
    protected $modelExport;
    protected $modelImport;
    public $treeData = [];
    protected $needCommon = true;

    public function __construct()
    {
        $this->hookInit();
    }

    public function searchData()
    {
        $baseQuery = new BaseQuery($this->model);
        // $baseQueryRes = $baseQuery->executeQuery();

        $queryType = request()->input('queryType');

        // Query Type
        if ($queryType == BaseConfig::QUERY_TYPE_DB_QUERY) {
            $baseQueryRes = $baseQuery->executeDbQuery();
        } elseif ($queryType == BaseConfig::QUERY_TYPE_SQL_QUERY) {
            $baseQueryRes = $baseQuery->executeSqlQuery();
        } else {
            $baseQueryRes = $baseQuery->executeQuery();
        }

        // Result Package
        $data = [];
        $result = $baseQueryRes['result'];
        $r = new $this->resource($result);
        $data['list'] = $r::collection($result);

        // common data
        if ($this->needCommon) {
            $data['common'] = $this->common();
        }

        // pagination
        $data['pagination'] = $baseQueryRes['pagination'];

        return $data;
    }

    public function setExportModel(BaseExport $class){
        $this->modelExport = $class;
    }

    // New
    public function store()
    {
        $input = $this->model->convertFormRequestToInput();

        $id = $this->model->store($input);

        return $id;
    }

    // New (by input)
    public function storeByInput($input)
    {
        $id = $this->model->store($input);

        return $id;
    }

    // Update
    public function update($id)
    {
        $input = $this->model->convertFormRequestToInput();

        unset($input['id']);
        $this->model->updateItem($id, $input);
    }

    // Update (by input)
    public function updateByInput($id, $input)
    {
        $this->model->updateItem($id, $input);
    }

    // Updated operations. (e.g. updating schedules, calculating properties, etc.)
    public function updateItemAfter($id)
    {
        $this->model->updateItemAfter($id);
    }

    // Detail
    public function detail($id)
    {
        $data['detail'] = new $this->resourceDetail($this->model->findById($id));

        // common data
        $data['common'] = $this->common();

        return $data;
    }

    // 导出数据
    public function exportData(){

        $baseQuery = new BaseQuery($this->model);
        $result = $baseQuery->executeQueryAll();

        $fileName = "export_" . date('Ymd_His') . ".xlsx";

        // 文件目录不存在则创建
        $basePath = base_path();
        $saveDir = implode(DIRECTORY_SEPARATOR, [
            $basePath, 'storage', 'app', 'public', 'export'
        ]);

        if(!is_dir($saveDir)){
            mkdir($saveDir, 0777);
        }

        // 导出
        Excel::store(new $this->modelExport($result), '/public/export/'. $fileName);

        $ret = [];
        $ret['export_file_url'] = implode(DIRECTORY_SEPARATOR, [
            CommonHelper::domain(), 'storage', 'export', $fileName
        ]);

        return $ret;
    }

    // Delete
    public function destroy($idArr)
    {
        $this->model->destroyByIdArr($idArr);
    }

    // set Tree Data
    public function setTreeData($treeData)
    {
        $this->treeData = $treeData;
    }

    public function treeData()
    {
        return $this->treeData;
    }

    // Get Table
    public function getTable()
    {
        return $this->model->getTable();
    }

    // Form personalization tips
    public function tips()
    {
        $arr = [];

        return $arr;
    }

    public function common()
    {
        $common['selectOption'] = [];
        $common['treeData'] = $this->treeData();
        $common['tips'] = $this->tips();

        return $common;
    }

    // Get the search field
    public function getSearchableFields()
    {
        return $this->model->initSearchableFields();
    }

    // Calculate/refresh an item
    public function computeItem($id)
    {
        return $this->model->computeItem($id);
    }

    public function getSelectOptions($key = 'id', $value = 'name', $where = [])
    {
        return $this->model->buildSelectOptions($key, $value, $where);
    }

    public function setMsg($msg)
    {
        $this->msg = $msg;

        return true;
    }

    public function getMsg()
    {
        return $this->msg;
    }

    public function setResource($resource)
    {
        $this->resource = $resource;
    }

    public function setResourceDetail($resourceDetail)
    {
        $this->resourceDetail = $resourceDetail;
    }
}
