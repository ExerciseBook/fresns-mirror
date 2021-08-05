<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

use App\Base\Config\BaseConfig;
use Illuminate\Support\Facades\DB;

class CommonHelper
{

    // 是否https请求
    public static function isHttpsRequest(){

        if((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
        ){
            return true;
        }
        return false;
    }

    // 获取域名
    public static function domain(){
        $request = request();
        $httpHost = $request->server('HTTP_HOST');

        if(self::isHttpsRequest()){
            return "https://" . $httpHost;
        }
        return "http://" . $httpHost;
    }

    // 获取host
    public static function host(){
        $request = request();
        $httpHost = $request->server('HTTP_HOST');

        return $httpHost;
    }

    // 判断是否为微信浏览器
    public static function isWeixinBrowser()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }

    // 移除请求的数据
    public static function removeRequestFields($fieldMap){
        foreach ($fieldMap as $field => $arr){
            request()->offsetUnset($field);
        }
    }

    // 只保留请求的字段
    public static function onlyRequestFields($onlyFieldArr){
        $allFiledMap = request()->all();

        foreach ($allFiledMap as $field => $value) {
            //不在则移除
            if(!in_array($field, $onlyFieldArr)){
                request()->offsetUnset($field);
            }
        }
    }

    // object to array
    public static function objectToArray($obj){
        $a = json_encode($obj);
        $b = json_decode($a, true);
        return $b;
    }

    // 获取地址全称
    public static function getFullAddress($input){

        $fullAddressArr = [];

        $addressFieldsArr = ['province_id', 'city_id', 'region_id'];

        foreach ($addressFieldsArr as $field){
            if(!isset($input[$field]) || empty($input[$field])){
                continue;
            }
            $fullAddressArr[] = DB::table(BaseConfig::TABLE_AREA)
                ->where('id', $input[$field])->value('name');
        }


        if(isset($input['address_detail'])){
            $fullAddressArr[] = $input['address_detail'];
        }

        return implode('', $fullAddressArr);
    }


    public static function isMobile()
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return TRUE;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA'])) {
            return stristr($_SERVER['HTTP_VIA'], "wap") ? TRUE : FALSE;// 找不到为flase,否则为TRUE
        }
        // 判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array(
                'mobile',
                'nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap'
            );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return TRUE;
            }
        }
        if (isset ($_SERVER['HTTP_ACCEPT'])) { // 协议法，因为有可能不准确，放到最后判断
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== FALSE) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === FALSE || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return TRUE;
            }
        }
        return FALSE;
    }

    // 价格: 元 转 分
    public static function formatPrice2Fen($price){
        return intval($price * 100);
    }

    // 价格: 分 转 元
    public static function formatFen2Yuan($price, $precision = 2){
        return sprintf("%.{$precision}f",$price / 100);
    }

    // 获取单个show button
    public static function generateShowButtonInfo($showBtnKey, $show = true, $buttonKeyNameMap = []){
        $showButton = [
            'key'    => $showBtnKey,
            'name'   => $buttonKeyNameMap[$showBtnKey] ?? '未知',
            'show'   => $show,
            'status' => 'normal',
        ];

        return $showButton;
    }

    // 获取网页cdn资源路径
    public static function getWebCdnStatic(){
        $domain = CommonHelper::domain();
        $cdnUrl = $domain;

        if(DBHelper::hasTableInCurrentDB('config')){
            $cdnUrl =  DB::table('config')->where('nickname', 'web_cdn_path')->value('content');
        }

        return $cdnUrl;
    }

    // 获取网页cdn_h5资源路径
    public static function getWebCdnH5Static(){
        $domain = CommonHelper::domain();
        $cdnUrl = $domain;

        if(DBHelper::hasTableInCurrentDB('config')){
            $cdnUrl =  DB::table('config')->where('nickname', 'web_cdn_path_h5')->value('content');
        }

        return $cdnUrl;
    }

    

}
