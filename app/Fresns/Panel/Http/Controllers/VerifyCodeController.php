<?php

namespace App\Fresns\Panel\Http\Controllers;

use App\Models\Config;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Fresns\Panel\Http\Requests\UpdateVerifyCodeRequest;

class VerifyCodeController extends Controller
{
    public function index()
    {
        // config keys
        $configKeys = [
            __('panel::panel.verifycodeTemplate1') => 'verifycode_template1',
            '注册新账号' => 'verifycode_template2',
            '修改账号资料' => 'verifycode_template3',
            '更换新绑定' => 'verifycode_template4',
            '重置登录密码' => 'verifycode_template5',
            '重置支付密码' => 'verifycode_template6',
            '使用验证码登录' => 'verifycode_template7',
        ];

        $configs = Config::whereIn('item_key', $configKeys)->get();


        foreach($configs as $config) {
            $originValue = collect($config->item_value);
            $value['email'] = $originValue->where('type', 'email')->first();
            $value['sms'] = $originValue->where('type', 'sms')->first();
            $params[$config->item_key] = $value;
        }

        return view('panel::system.verifyCode.index', compact('params', 'configKeys'));
    }


    public function edit($itemKey)
    {
        $config = Config::where('item_key', $itemKey)->firstOrFail();
        $originValue = collect($config->item_value);
        $template['email'] = $originValue->where('type', 'email')->first();
        $template['sms'] = $originValue->where('type', 'sms')->first();

        return view('panel::system.verifyCode.edit', compact('template', 'itemKey'));
    }

    public function update($itemKey, UpdateVerifyCodeRequest $request)
    {
        $config = Config::where('item_key', $itemKey)->firstOrFail();

        $emailTemplates = [];
        foreach($request->email_templates as $langTag=> $template) {
            if (!array_filter($template)) {
                continue;
            }

            $emailTemplates[] = [
                'langTag' => $langTag,
                'title' => $template['title'] ?? '',
                'content' => $template['content'] ?? '',
            ];
        }

        $smsTemplates = [];
        foreach($request->sms_templates as $langTag=> $template) {
            if (!array_filter($template)) {
                continue;
            }

            $smsTemplates[] = [
                'langTag' => $langTag,
                'signName' => $template['signName'] ?? '',
                'templateCode' => $template['templateCode'] ?? '',
                'codeParam' => $template['codeParam'] ?? '',
            ];
        }

        $value = [
            [
                'type' => 'email',
                'isEnable' => $request->has_email ? true : false,
                'template' => $emailTemplates,
            ],
            [
                'type' => 'sms',
                'isEnable' => $request->has_sms ? true : false,
                'template' => $smsTemplates,
            ],
        ];

        $config->item_value = $value;
        $config->save();

        return $this->updateSuccess();
    }
}
