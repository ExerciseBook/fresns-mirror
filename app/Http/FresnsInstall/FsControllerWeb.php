<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsInstall;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class FsControllerWeb
{

    private $lock_file = '';

    // check install
    public function __construct(){
        $this->lock_file = storage_path('app/install.lock');
        if(is_file($this->lock_file)){
            header('Location: /');exit;
        }else{
            $result = InstallService::checkPermission();
            if($result['code'] != '000000'){
                header('Location: '.$result['url']);exit;
            }
        }
    }

    // choose language
    public function index()
    {
        Cache::put('install_index',1);
        return view('install.index');
    }

    // install desc
    public function step1()
    {
        Cache::put('install_step1',1);
        return view('install.step1');
    }

    // check env
    public function step2()
    {
        Cache::put('install_step2',1);
        return view('install.step2');
    }

    // check mysql
    public function step3()
    {
        Cache::put('install_step3',1);
        return view('install.step3');
    }

    // init manager
    public function step4()
    {
        Cache::put('install_step4',1);
        return view('install.step4');
    }

    // finish tips
    public function step5()
    {
        file_put_contents($this->lock_file,date('Y-m-d H:i:s'));
        Cache::forget('install_index');
        Cache::forget('install_step1');
        Cache::forget('install_step2');
        Cache::forget('install_step3');
        Cache::forget('install_step4');

        // Soft link
        Artisan::call('key:generate');
        Artisan::call('storage:link');

        return view('install.step5');
    }

    // env detect
    public function env(Request $request)
    {
        $name = $request->input('name');
        $result = InstallService::envDetect($name);
        return Response::json($result);
    }

    // register manager
    public function initManage(Request $request){
        $back_host = $request->input('backend_host');
        $email = $request->input('email');
        $pure_phone = $request->input('pure_phone');
        $country_code = $request->input('country_code');
        $password = $request->input('password');
        $nickname = $request->input('nickname');
        // register config
        $result = InstallService::updateOrInsertConfig('backend_domain',$back_host,'string','backends');
        if($result['code'] != '000000'){
            return Response::json($result);
        }
        $result = InstallService::updateOrInsertConfig('install_time',date('Y-m-d H:i:s'),'string','systems');
        if($result['code'] != '000000'){
            return Response::json($result);
        }

        // register user
        $input = [
            'email' => $email,
            'purePhone' => $pure_phone,
            'countryCode' => $country_code,
            'password' => $password,
            'nickname' => $nickname,
        ];
        $result = InstallService::registerUser($input);
        if($result['code'] != '000000'){
            return Response::json($result);
        }

        return Response::json(['code'=>'000000','message'=>'success']);
    }



}
