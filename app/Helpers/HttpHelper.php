<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */


namespace App\Helpers;

use App\Http\Share\Common\LogService;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class HttpHelper
{
    /**
     * 发起请求
     * @param $url
     * @param array $postData
     * @param string $method
     * @param bool $useJson
     * @return mixed|array
     */
    public static function postFetch($url, $postFields = [],$header = [])
    {
        $postFields = json_encode($postFields);


        $ch = curl_init ();
        $content = ['Content-Type: application/json; charset=utf-8'];
        if($header){
            $content = array_merge($content,$header);
        }

		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $content);
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); //若果报错 name lookup timed out 报错时添加这一行代码
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt( $ch, CURLOPT_TIMEOUT,60);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
        $ret = curl_exec ( $ch );

        if (false == $ret) {
            $result = curl_error(  $ch);
        } else {
            $rsp = curl_getinfo( $ch, CURLINFO_HTTP_CODE);
            if (200 != $rsp) {
                $result = "请求状态 ". $rsp . " " . curl_error($ch);
            } else {
                $result = $ret;
            }
        }
        curl_close ( $ch );

		return $result;
    }

    public static function getFetch($url, $postData = [], $method = 'GET', $useJson = true)
    {
        $client = new \GuzzleHttp\Client();

        try {
            return json_decode($client->request($method, $url, [$useJson ? 'json' : 'form_params' => $postData])->getBody()->getContents(), true);
        } catch (RequestException $e) {
            Log::error("RequestException:" . $e->getCode() . ',' . $e->getMessage());
        }
    }

    /**
     * 发起请求
     * @param $url
     * @param array $postData
     * @param string $method
     * @param bool $useJson
     * @return mixed|array
     */
    public static function post($url, $dataArr = [], $header = [])
    {

        $postFields = json_encode($dataArr);
        $ch = curl_init ();

        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); //若果报错 name lookup timed out 报错时添加这一行代码
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt( $ch, CURLOPT_TIMEOUT,60);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
        $ret = curl_exec ( $ch );
        if (false == $ret) {
            $result = curl_error(  $ch);
        } else {
            $rsp = curl_getinfo( $ch, CURLINFO_HTTP_CODE);
            if (200 != $rsp) {
                $result = "请求状态 ". $rsp . " " . curl_error($ch);
            } else {
                $result = $ret;
            }
        }
        curl_close ( $ch );

        return $result;
    }

    //获取路径
    public static function getParseUrl()
    {
        $menu_path_arr = parse_url(url()->previous());
        $menu_path = isset($menu_path_arr['path']) ? $menu_path_arr['path'] : null;

        return $menu_path;
    }


    //
    public static function guzzleHttpPost($url, $params, $headers = []){

        // 发送请求
        $client = new \GuzzleHttp\Client();
        $respData = $client->request('post',
            $url, [
                'json'  => $params,
                'headers' => $headers,
            ])
            ->getBody()->getContents();

        $resArr = json_decode($respData, true);

        return $resArr;
    }
    public static function curl($url, $postData = [], $file = '')
    {
        #1. 初始化curl连接
        $ch = curl_init();

        #2. 设置各项参数
        // 启动curl
        $ch = curl_init();
        // 设置请求URL地址
        curl_setopt($ch, CURLOPT_URL, $url);
        // 不获取header头信息
        curl_setopt($ch, CURLOPT_HEADER, 0);
        // 结果不直接返回到终端
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // 设置curl不进行证书的检测
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        // 超时时间 秒
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        // 设置请求的浏览器
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36');

        // 发起POST请求
        curl_setopt($ch, CURLOPT_POST, 1);
        // post发送的数据，注意http_build_query可以将$postData数组数据格式化成http传输数据的格式
        //                              http_build_query这个函数在单纯传递post数据，注意不包含文件数据的时候，建议加上，否则可能出现传输数据不稳定的情况
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        #3. 执行curl连接
        $data = curl_exec($ch);

        //获得执行curl连接的相关信息
        $info = curl_getinfo($ch);

        #4. 关闭curl连接
        curl_close($ch);

        if ($info['http_code'] == '200') { //只有当响应状态码为200时，才有必要将执行的结果返回出去
            return $data;
        }

        return false; //如果响应状态码的值不为200，则返回false
    }

    /*请求外部地址*/
    public static function curlRequest($url,$mothed = "GET" , $data = array())
    {
        $ch = curl_init();
        $header = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $mothed);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $temp = curl_exec($ch);
        curl_close($ch);
        return $temp;
    }
}
