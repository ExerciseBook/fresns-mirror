<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsInstall;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Response;

class FsControllerWeb
{
    // check install
    public function __construct(){
        $lock_file = storage_path('install/install.lock');
        if(is_file($lock_file)){
            return redirect('/');
        }
    }

    // choose language
    public function index()
    {
        return view('install.index');
    }

    // install desc
    public function step1()
    {
        return view('install.step1');
    }

    // check env
    public function step2()
    {
        return view('install.step2');
    }

    // check mysql
    public function step3()
    {
        return view('install.step3');
    }

    // init manager
    public function step4()
    {
        return view('install.step4');
    }

    // finish tips
    public function step5()
    {
        $file    = storage_path('install/install.txt');
        $content = date('Y-m-d H:i:s');
        file_put_contents($file,$content);
        return view('install.step5');
    }

    // env detect
    public function env(Request $request)
    {
        $name = $request->input('name');
        $result = InstallService::envDetect($name);
        return Response::json($result);
    }

    //
    public function initManage(Request $request){
        $back_host = $request->input('backend_host');
        $email = $request->input('email');
        $pure_phone = $request->input('pure_phone');
        $country_code = $request->input('country_code');
        $password = $request->input('password');
        $nickname = $request->input('nickname');
        // register config
        $result = InstallService::insertConfigs('backend_domain',$back_host);
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
        // Soft link
        Artisan::call('storage:link');

        return Response::json(['code'=>'000000','message'=>'success']);
    }





}
