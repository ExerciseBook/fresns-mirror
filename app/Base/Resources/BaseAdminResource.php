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
    // show button info
    public $columnShowButtons = [];

    // get resource show buttons info
    public function getColumnShowButtons()
    {
        return $this->columnShowButtons;
    }

    /**
     * Get the column permission under the current menu.
     * @var Return Status: 1=true / 2=false / 3=Random Return
     */
    public function getColumnShowButtonsType($type = 1)
    {
        $typeMap = [true, false];
        //Get all permissions under the current menu
        $menuPerm = RolePermService::getAllShowButtonFiledUrl(2);

        $data = [];
        foreach ($menuPerm as $v) {
            $item = [];
            $item['key'] = $v['show_btn_nickname'];
            if ($type == 1) {
                $item['show'] = true;
            }
            if ($type == 2) {
                $item['show'] = false;
            }
            if ($type == 3) {
                $item['show'] = $typeMap[rand(0, 1)];
            }
            $item['name'] = $v['name'];
            $item['status'] = 'normal';
            $data[] = $item;
        }

        return $data;
    }
}
