<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsPlugins;

use App\Base\Controllers\BaseAdminController;
use App\Http\Center\Base\FresnsCode;
use App\Http\Center\Helper\PluginHelper;
use App\Http\Share\Common\ValidateService;
use Illuminate\Http\Request;

class AmControllerAdmin extends BaseAdminController
{
    public function __construct()
    {
        $this->service = new AmService();
    }

    public function index(Request $request)
    {
        $plugins = app()->call('App\Http\Center\Market\RemoteController@index');
        //插入plugin表
        if ($plugins) {
            foreach ($plugins as $plugin) {
                // dd($plugin);
                $pluginCount = FresnsPlugins::where('unikey', $plugin['uniKey'])->where('type',
                    AmConfig::PLUGINS_TYPE)->count();
                if ($pluginCount == 0) {
                    $input = [
                        'unikey' => $plugin['uniKey'],
                        'name' => $plugin['name'],
                        'type' => AmConfig::PLUGINS_TYPE,
                        'description' => $plugin['description'],
                        'version' => $plugin['version'],
                        'version_int' => $plugin['versionInt'],
                        'is_enable' => AmConfig::ENABLE_FALSE,
                    ];
                    (new FresnsPlugins())->store($input);
                }
            }
        }
        $request->offsetSet('type', AmConfig::PLUGINS_TYPE);
        parent::index($request);
    }

    public function update(Request $request)
    {
        ValidateService::validateRule($request, $this->rules(Amconfig::RULE_UPDATE));
        $id = $request->input('id');
        $localPlugin = PluginHelper::getPluginJsonFileArr();
        // dump($localPlugin);
        $plugin = FresnsPlugins::find($id);
        // 是否下载
        $isDownload = AmConfig::NO_DOWNLOAD;
        if ($localPlugin) {
            foreach ($localPlugin as $v) {
                if ($plugin['unikey'] == $v['uniKey']) {
                    $isDownload = AmConfig::DOWNLOAD;
                }
            }
        }
        if ($isDownload == AmConfig::NO_DOWNLOAD) {
            $this->error(FresnsCode::DOWMLOAD_ERROR);
        }
        $this->service->update($id);

        if (empty($request->name)) {
            $this->index($request);
        }
    }

    // 验证规则
    public function rules($ruleType)
    {
        $rule = [];

        $config = new AmConfig($this->service->getTable());

        switch ($ruleType) {
            case AmConfig::RULE_STORE:
                $rule = $config->storeRule();
                break;

            case AmConfig::RULE_UPDATE:
                $rule = $config->updateRule();
                break;

            case AmConfig::RULE_DESTROY:
                $rule = $config->destroyRule();
                break;

            case AmConfig::RULE_DETAIL:
                $rule = $config->detailRule();
                break;
        }

        return $rule;
    }
}
