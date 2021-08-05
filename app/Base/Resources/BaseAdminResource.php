<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Base\Resources;

use App\Http\Share\Common\Perm\RolePermService;

class BaseAdminResource extends BaseResource
{
    // show button 信息
    public $columnShowButtons = [];

    // 获取 resource show buttons 信息
    public function getColumnShowButtons(){

        return $this->columnShowButtons;
    }

    /**
     * 获取当前菜单下的column权限
     * @type 返回状态 1 = true 2 = false 3 = 随机返回
     */
    public function getColumnShowButtonsType($type = 1){
        $typeMap = [true,false];
        //获取当前菜单下所有的权限
        $menuPerm = RolePermService::getAllShowButtonFiledUrl(2);

        $data = [];
        foreach($menuPerm as $v){
            $item = [];
            $item['key'] = $v['show_btn_nickname'];
            if($type == 1){
                $item['show'] = true;
            }
            if($type == 2){
                $item['show'] = false;
            }
            if($type == 3){
                $item['show'] = $typeMap[rand(0,1)];
            }
            $item['name'] = $v['name'];
            $item['status'] = 'normal';
            $data[] = $item;
        }

        return $data;
    }

}
