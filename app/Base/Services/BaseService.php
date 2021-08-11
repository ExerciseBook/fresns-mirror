<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Base\Services;

use App\Base\Config\BaseConfig;
use App\Base\Models\BaseQuery;

use App\Traits\HookServiceTrait;


class BaseService
{
    use HookServiceTrait;
    public  $msg = '-';
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

    public function searchData(){
        $baseQuery = new BaseQuery($this->model);
       // $baseQueryRes = $baseQuery->executeQuery();

        $queryType = request()->input('queryType');
        // dd($queryType);
        // 查询类型
        // join查询
        if($queryType == BaseConfig::QUERY_TYPE_DB_QUERY){
            $baseQueryRes = $baseQuery->executeDbQuery();
        }else if($queryType == BaseConfig::QUERY_TYPE_SQL_QUERY){
            $baseQueryRes = $baseQuery->executeSqlQuery();
        }else{

            $baseQueryRes = $baseQuery->executeQuery();
        }
    //    dd($baseQueryRes);
        // 结果封装
        $data = [];
        $result = $baseQueryRes['result'];
        // dd($result);
        $r = new $this->resource($result);
        // dd($r);
        $data['list'] = $r::collection($result);

        // common 数据
        if($this->needCommon){
            $data['common'] = $this->common();
        }

        //分页
        $data['pagination'] = $baseQueryRes['pagination'];

        // dd($data);
        return $data;
    }



    // 新增
    public function store(){

        $input = $this->model->convertFormRequestToInput();

        $id = $this->model->store($input);
        return $id;
    }

    // 新增
    public function storeByInput($input){

        $id = $this->model->store($input);
        return $id;
    }

    // 更新
    public function update($id){
        $input = $this->model->convertFormRequestToInput();

        unset($input['id']);
        unset($input['create_user_id']);// 更新不要更改原始数据
        $this->model->updateItem($id, $input);
    }

    // 更新
    public function updateByInput($id, $input){
        $this->model->updateItem($id, $input);
    }

    // 更新后的操作, 如更新附表, 计算属性等
    public function updateItemAfter($id){
        $this->model->updateItemAfter($id);
    }


    public function detail($id){
        //dd($this->resourceDetail);
        $data['detail'] = new $this->resourceDetail($this->model->findById($id));

        // common 数据
        $data['common'] = $this->common();
        return $data;
    }

    // 删除
    public function destroy($idArr){
        $this->model->destroyByIdArr($idArr);
    }

    


    public function setTreeData($treeData){
        $this->treeData = $treeData;
    }

    public function treeData(){
        return $this->treeData;
    }

    // 获取表
    public function getTable(){
        return $this->model->getTable();
    }

    // 表单个性化提示
    public function tips(){
        $arr = [
            'picture_banner'   => '图片大小为300x180',
        ];
        return $arr;
    }

    public function common(){
        $common['selectOption'] = [];
        $common['treeData'] = $this->treeData();
        $common['showButtons'] = $this->showButtons();
        $common['tips'] = $this->tips();

        return $common;
    }

    public function showButtons(){

        $data = [];

        return $data;
    }

    // 获取搜索字段
    public function getSearchableFields(){
        return $this->model->initSearchableFields();
    }

    // 计算/刷新 某个item
    public function computeItem($id){
        return $this->model->computeItem($id);
    }

    public function getSelectOptions($key = 'id', $value = 'name',$where = []){
        return $this->model->buildSelectOptions($key, $value,$where);
    }

    public function setMsg($msg){
        $this->msg = $msg;
        return true;
    }

    public function getMsg(){
        return $this->msg;
    }

    public function setResource($resource){
        $this->resource = $resource;
    }

    public function setResourceDetail($resourceDetail){
        $this->resourceDetail = $resourceDetail;
    }
}
