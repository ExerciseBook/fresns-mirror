<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

use App\Http\Share\Common\LogService;

class ShowButtonHelper
{
    public $showButtonsNameMap = [];

    public $showButtonsArr = [];

    // 添加showButtons
    public function addShowButtonInfo($showBtnKey, $show = true){
        $showButton = [
            'key'    => $showBtnKey,
            'name'   => $this->showButtonsNameMap[$showBtnKey] ?? '未知名称',
            'show'   => $show,
            'status' => 'normal',
        ];

        $this->showButtonsArr[] = $showButton;
        return $showButton;
    }

    public function getShowButtonsArr(){
        return $this->showButtonsArr;
    }

    public function setShowButtonNameMap($nameMap){
        $this->showButtonsNameMap = $nameMap;
    }

    // 是否显示button
    public static function isShowButton($showButtonArr, $key){
        $res = true;
        $info = [
            'show_button_result' => $res,
            'show_button_key' => $key,
            'show_button_arr' => $showButtonArr,
        ];

        foreach ($showButtonArr as $showButton){
            $showButtonKey = $showButton['key'] ?? '';
            if($showButtonKey == $key){
                $res = $showButton['show'] ?? true;
                $info['show_button_result'] = $res;
                break;
            }
        }

        LogService::info("isShowButton 匹配结果", $info);

        return $res;
    }

    // 获取某个文件的showButton
    public static function isShowFileItemButton($fileArr, $fileId, $key){
        foreach ($fileArr as $fileItem){
            if($fileItem['id'] == $fileId){
                return self::isShowButton($fileItem['show_button_arr'], $key);
            }
        }
        LogService::info("isShowFileItemButton {$key} 结果: ", true);

        return true;
    }

}
