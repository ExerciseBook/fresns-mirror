<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsInstall;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

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
    public function optMysql(Request $request){
        $rule = [
            'db_host' => 'required',
            'db_port' => 'required',
            'db_name' => 'required',
            'db_user' => 'required',
            'db_pwd' => 'required',
            'db_prefix' => 'required',
        ];
        $validator = Validator::make($request->all(), $rule);
        if ($validator->fails()) {

        }
        $db_host = $request->input('db_host');
        $db_port = $request->input('db_port');
        $db_name = $request->input('db_name');
        $db_user = $request->input('db_user');
        $db_pwd = $request->input('db_pwd');
        $db_prefix = $request->input('db_prefix');
        // config 设置配置参数，测试连接
            config(['']);
        //创建数据库

        //执行migrate

        // replace env
        InstallService::envUpdate('db_host',$db_host);
        InstallService::envUpdate('db_port',$db_port);
        InstallService::envUpdate('db_name',$db_name);
        InstallService::envUpdate('db_user',$db_user);
        InstallService::envUpdate('db_pwd',$db_pwd);
        InstallService::envUpdate('db_prefix',$db_prefix);
    }


    //
    public function optManager(Request $request){
        $rule = [
            'email' => 'required',
            'pure_phone' => 'required',
            'country_code' => 'required',
            'password' => 'required',
            'nickname' => 'required',
        ];

        $validator = Validator::make($request->all(), $rule);
        if ($validator->fails()) {

        }
        //fs_config

        //fs_user

        //fs_member

    }



}
