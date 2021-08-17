<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Base\Services;

use App\Base\Config\BaseConfig;
use App\Helpers\CommonHelper;
use App\Helpers\TreeHelper;
use App\Http\Models\Perm\Menu;
use App\Http\Share\Common\LogService;
use App\Http\Share\Common\ValidateService;
use Illuminate\Cache\NullStore;

class BaseCategoryService extends BaseService
{
    // 搜索条件
    public $cond = [];

    // 钩子函数: index之前
    public function hookListTreeBefore()
    {
        $this->initTreeSearchCond();
    }

    // 初始化搜索条件
    public function initTreeSearchCond(){
        $req = request();
        $cond = [];
        $searchCondConfig = $this->config->getTreeSearchRule();
        foreach ($searchCondConfig as $searchField => $arr){
            // 无key跳过
            if(!$req->has($searchField)){
                continue;
            }

            $searchValue = $req->input($searchField);
            // 无值跳过
            if($searchValue === NULL){
                continue;
            }

            // 验证数组字段
            ValidateService::validParamExist($arr, ['field', 'op']);

            $field = $arr['field'];
            $op = $arr['op'];

            $upperOp = strtoupper($op);

            $cond = [];
            switch ($upperOp){
                case '=' :
                    $cond[] = [$field, $op, $searchValue];
                    break;
                case 'LIKE' :
                    $cond[] = [$field, $op, "%$searchValue%"];
                    break;
            }


        }

        $this->cond = $cond;
        // dd($cond);
    }

    public function detail($id){
        //  dd($this->resourceDetail);
        $data['detail'] = new $this->resourceDetail($this->model->findById($id));

        // common 数据
        // 为了获取treeData
        $this->listTree();
        $data['common'] = $this->common();
        return $data;
    }

    // 列表展示
    public function listTree()
    {
        $this->hookListTreeBefore();

        $topCategoryArr = $this->model->where('parent_id', NULL)->where($this->cond)->get();
        // dd($topCategoryArr);
        foreach ($topCategoryArr as &$category){
            $category['key'] = $category->id;
            $allChildren = $category->getAllChildren();

            if(!empty($allChildren)){
                $category->children = $allChildren;
            //    array_multisort(array_column($category['children'], 'rank_num'), SORT_ASC, $category['children']);
            }
        }

        if(count($topCategoryArr) > 0){
            // 先处理格式化
            $this->formatTree($topCategoryArr);

            $tree = [];
            $this->buildTreeData($topCategoryArr, $tree);
            $this->setTreeData($tree);
        }

        return $topCategoryArr;
    }
    // 列表展示
    public function listTreeNoRankNum()
    {
        $this->hookListTreeBefore();

        $topCategoryArr = $this->model->where('parent_id', 0)->where($this->cond)->get();
        // dd($topCategoryArr);
        foreach ($topCategoryArr as &$category){
            $category['key'] = $category->id;
            $allChildren = $category->getAllChildren();

            if(!empty($allChildren)){
                $category->children = $allChildren;
            //    array_multisort(array_column($category['children'], 'rank_num'), SORT_ASC, $category['children']);
            }
        }
        // dd($topCategoryArr);
        if(count($topCategoryArr) > 0){
            // 先处理格式化
            $this->formatTree($topCategoryArr);
            // dd($topCategoryArr);
            $tree = [];
            $this->buildTreeData($topCategoryArr, $tree);
            // dd($tree);
            $this->setTreeData($tree);

        }
        // dd($topCategoryArr);
        $childrenIdArr = [];
        // $this->getChildrenIds2($topCategoryArr,$childrenIdArr);
        // dd($childrenIdArr);
        return $topCategoryArr;
    }
    // 列表展示
    public function listTreeNoCond()
    {
        $topCategoryArr = $this->model->where('parent_id', NULL)->get();
        // dd($topCategoryArr);
        foreach ($topCategoryArr as &$category){
            $category['key'] = $category->id;
            $allChildren = $category->getAllChildren();

            if(!empty($allChildren)){
                $category->children = $allChildren;
                //    array_multisort(array_column($category['children'], 'rank_num'), SORT_ASC, $category['children']);
            }
        }

        if(count($topCategoryArr) > 0){
            // 先处理格式化
            $this->formatTree($topCategoryArr);

            $tree = [];
            $this->buildTreeData($topCategoryArr, $tree);
            $this->setTreeData($tree);
        }

        return $topCategoryArr;
    }

    //获取已选中菜单ID所有上一级
    public function getParentMenuArr($menuIdArr)
    {
        $parentAllId = [];
        foreach ($menuIdArr as $v){
            $parentId = Menu::where('id',$v)->value('parent_id');
            $parentIdArr = [];

            $this->parentIdArr($parentId,$parentIdArr);
            $parentAllId[] = $parentIdArr;
        }

        $itemIdArr = [];
        foreach ($parentAllId as $parentId){
            $itemIdArr = array_merge($itemIdArr,$parentId);
        }

        $itemIdArr = empty($itemIdArr) ? [] : array_unique($itemIdArr);

        return array_unique(array_merge($menuIdArr,$itemIdArr));
    }

    public function parentIdArr($parentId,&$parentIdArr){
        $menu = Menu::find($parentId);

        if ($menu){
            $parentIdArr[] = $menu['id'];

            $this->parentIdArr($menu['parent_id'],$parentIdArr);
        }

    }

    // 格式化处理
    public function formatTree(&$categoryArr){
        foreach($categoryArr as &$item){
            $children = $item->children;
            if ($item->is_enable == 1){
                $item->is_enable = true;
            }else {
                $item->is_enable = false;
            }

            // 不为空则获取childrenIds
            if($children && count($children) > 0){
                $childrenIdArr = [];
                $this->getChildrenIds($item->toArray(), $childrenIdArr);
                $item->childrenIds = $childrenIdArr;
                // dd($childrenIdArr);
                $this->formatTree($children);
            }
        }
    }

    // 生成数据
    public function buildTreeData(&$itemArr, &$categoryArr){
        foreach($itemArr as $item){
            $children   = $item->children;

            // 这里获取直接的children
            $directChildren = [];
            foreach ($children as $child){
                if($child->parent_id == $item->id){
                    $directChildren[] = $child;
                }
            }

            $children = $directChildren;

//            dd(CommonHelper::objectToArray($directChildren));
//            dd(CommonHelper::objectToArray($children));
            $c = [];
            $c['key']   = $item->id;
            $c['value'] = $item->id;
            $c['name']  = $item->name;
            $c['title'] = $item->name;

            if($children && count($children) > 0){
                $this->buildTreeData($children, $c['children']);
            }

            $categoryArr[] = $c;
        }

    }

    // 获取childrenIds
    public function getChildrenIds($categoryItem, &$childrenIdArr){
        // dd($categoryItem);
        if(key_exists('children', $categoryItem)){
            $childrenArr = $categoryItem['children'];
            foreach ($childrenArr as $children){
                $childrenIdArr[] = $children['id'];
                $this->getChildrenIds($children, $childrenIdArr);
            }
        }
        // dd($childrenIdArr);
    }
    // 获取childrenIds
    public function getChildrenIds2($categoryItem, &$childrenIdArr){
        // dd($categoryItem);
        if(key_exists('children', $categoryItem)){
            $childrenArr = $categoryItem['children'];
            // dd($childrenArr);
            foreach ($childrenArr as $children){
                $childrenIdArr[] = $children['id'];
                $this->getChildrenIds($children, $childrenIdArr);
            }
        }
        // dd($childrenIdArr);
    }
    // 列表展示
    public function listTreeBycond($cond=[])
    {
        $this->hookListTreeBefore();

        $topCategoryArr = $this->model->where('parent_id', NULL)->where($this->cond)->where($cond)->get();
        // dd($topCategoryArr);
        foreach ($topCategoryArr as &$category){
            $category['key'] = $category->id;
            $allChildren = $category->getAllChildren();
            // dd($allChildren);
            if(!empty($allChildren)){
                $category->children = $allChildren;
            //    array_multisort(array_column($category['children'], 'rank_num'), SORT_ASC, $category['children']);
            }
        }

        if(count($topCategoryArr) > 0){
            // 先处理格式化
            $this->formatTree($topCategoryArr);

            $tree = [];
            $this->buildTreeData($topCategoryArr, $tree);
            $this->setTreeData($tree);
        }

        return $topCategoryArr;
    }
}
