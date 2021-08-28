<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsCmd;

use App\Http\Center\Base\BasePlugin;
use App\Http\Center\Common\ErrorCodeService;
use App\Http\Center\Helper\PluginRpcHelper;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsDb\FresnsComments\FresnsCommentsConfig;
use App\Http\FresnsDb\FresnsConfigs\FresnsConfigs;
use App\Http\FresnsDb\FresnsDialogs\FresnsDialogsConfig;
use App\Http\FresnsDb\FresnsFileAppends\FresnsFileAppendsConfig;
use App\Http\FresnsDb\FresnsFiles\FresnsFilesConfig;
use App\Http\FresnsDb\FresnsMemberFollows\FresnsMemberFollowsConfig;
use App\Http\FresnsDb\FresnsMemberIcons\FresnsMemberIconsConfig;
use App\Http\FresnsDb\FresnsMemberLikes\FresnsMemberLikesConfig;
use App\Http\FresnsDb\FresnsMemberRoleRels\FresnsMemberRoleRels;
use App\Http\FresnsDb\FresnsMemberRoleRels\FresnsMemberRoleRelsConfig;
use App\Http\FresnsDb\FresnsMembers\FresnsMembersConfig;
use App\Http\FresnsDb\FresnsMemberShields\FresnsMemberShieldsConfig;
use App\Http\FresnsDb\FresnsMemberStats\FresnsMemberStatsConfig;
use App\Http\FresnsDb\FresnsNotifies\FresnsNotifiesConfig;
use App\Http\FresnsDb\FresnsPluginBadges\FresnsPluginBadgesConfig;
use App\Http\FresnsDb\FresnsPosts\FresnsPostsConfig;
use App\Http\FresnsDb\FresnsSessionLogs\FresnsSessionLogsConfig;
use App\Http\FresnsDb\FresnsSessionLogs\FresnsSessionLogsService;
use App\Http\FresnsDb\FresnsSessionTokens\FresnsSessionTokensConfig;
use App\Http\FresnsDb\FresnsUserConnects\FresnsUserConnectsConfig;
use App\Http\FresnsDb\FresnsUsers\FresnsUsersConfig;
use App\Http\FresnsDb\FresnsUserWalletLogs\FresnsUserWalletLogsConfig;
use App\Http\FresnsDb\FresnsUserWallets\FresnsUserWalletsConfig;
use Illuminate\Support\Facades\DB;

/**
 * Class FresnsCrontabPlugin
 * 主程序定时任务
 */
class FresnsCrontablPlugin extends BasePlugin
{
    // 构造函数
    public function __construct()
    {
        $this->pluginConfig = new FresnsCrontabPluginConfig();
        $this->pluginCmdHandlerMap = FresnsCrontabPluginConfig::PLG_CMD_HANDLE_MAP;
    }

    //新增订阅信息
    protected function addSubPluginItemHandler($input)
    {
        $item = $input['sub_table_plugin_item'];
        $config = FresnsConfigs::where('item_key', FresnsSubPluginConfig::SUB_ADD_TABLE_PLUGINS)->first();
        if (! empty($config)) {
            $configArr = json_decode($config['item_value'], true);
            foreach ($item as $v) {
                foreach ($configArr as $value) {
                    if ($v['subscribe_plugin_unikey'] == $value['subscribe_plugin_unikey'] && $v['subscribe_plugin_cmd'] == $value['subscribe_plugin_cmd'] && $v['subscribe_table_name'] == $value['subscribe_table_name']) {
                        return $this->pluginError(ErrorCodeService::CODE_EXCEPTION, '有重复数据');
                    }
                }
            }

            $data = array_merge($item, $configArr);
            FresnsConfigs::where('item_key',
                FresnsSubPluginConfig::SUB_ADD_TABLE_PLUGINS)->update(['item_value' => $data]);
        } else {
            $input = [
                'item_key' => FresnsSubPluginConfig::SUB_ADD_TABLE_PLUGINS,
                'item_tag' => 'sites',
                'item_type' => 'plugin',
                'item_value' => json_encode($item),
            ];
            FresnsConfigs::insert($input);
        }

        return $this->pluginSuccess();
    }

    //删除订阅信息
    protected function deleteSubPluginItemHandler($input)
    {
        $item = $input['sub_table_plugin_item'];
        $config = FresnsConfigs::where('item_key', FresnsSubPluginConfig::SUB_ADD_TABLE_PLUGINS)->first();
        if (empty($config['item_value'])) {
            return $this->pluginError(ErrorCodeService::CODE_EXCEPTION, '数据为空');
        }
        $configArr = json_decode($config['item_value'], true);
        $dataArr = [];
        foreach ($configArr as $v) {
            $isDel = 0;
            foreach ($item as $value) {
                if ($v['subscribe_plugin_unikey'] == $value['subscribe_plugin_unikey'] && $v['subscribe_plugin_cmd'] == $value['subscribe_plugin_cmd'] && $v['subscribe_table_name'] == $value['subscribe_table_name']) {
                    $isDel = 1;
                    break;
                }
            }
            if ($isDel == 1) {
                continue;
            }
            $dataArr[] = $v;
        }

        FresnsConfigs::where('item_key',
            FresnsSubPluginConfig::SUB_ADD_TABLE_PLUGINS)->update(['item_value' => $dataArr]);

        return $this->pluginSuccess();
    }

    //新增任务
    protected function addCrontabPluginItemHandler($input)
    {
        $item = $input['crontab_plugin_item'];
        $config = FresnsConfigs::where('item_key', 'crontab_plugins')->first();
        if (! empty($config)) {
            $configArr = json_decode($config['item_value'], true);
            foreach ($item as $v) {
                foreach ($configArr as $value) {
                    if ($v['crontab_plugin_unikey'] == $value['crontab_plugin_unikey'] && $v['crontab_plugin_cmd'] == $value['crontab_plugin_cmd']) {
                        return $this->pluginError(ErrorCodeService::CODE_EXCEPTION, '有重复数据');
                    }
                }
            }
            $data = array_merge($item, $configArr);
            FresnsConfigs::where('item_key', 'crontab_plugins')->update(['item_value' => $data]);
        } else {
            $input = [
                'item_key' => 'crontab_plugins',
                'item_value' => json_encode($item),
            ];
            FresnsConfigs::insert($input);
        }

        return $this->pluginSuccess();
    }

    //删除任务
    protected function deleteCrontabPluginItemHandler($input)
    {
        $item = $input['crontab_plugin_item'];
        $config = FresnsConfigs::where('item_key', 'crontab_plugins')->first();

        if (empty($config['item_value'])) {
            return $this->pluginError(ErrorCodeService::CODE_EXCEPTION, '数据为空');
        }
        $configArr = json_decode($config['item_value'], true);
        $dataArr = [];
        foreach ($configArr as $v) {
            $isDel = 0;
            foreach ($item as $value) {
                if ($v['crontab_plugin_unikey'] == $value['crontab_plugin_unikey'] && $v['crontab_plugin_cmd'] == $value['crontab_plugin_cmd']) {
                    $isDel = 1;
                    break;
                }
            }
            if ($isDel == 1) {
                continue;
            }
            $dataArr[] = $v;
        }

        FresnsConfigs::where('item_key', 'crontab_plugins')->update(['item_value' => $dataArr]);

        return $this->pluginSuccess();
    }

    // 每隔 10 分钟执行一次用户角色过期时间检测：
    protected function crontabCheckRoleExpiredHandler($input)
    {
        $sessionId = FresnsSessionLogsService::addSessionLogs('plg_cmd_crontab_check_role_expired', '定时任务');
        $memberInfo = FresnsMemberRoleRels::where('type', 2)->where('expired_at', '!=', null)->get()->toArray();
        if ($memberInfo) {
            foreach ($memberInfo as $m) {
                $expire_times = strtotime($m['expired_at']);
                // 判断是否已过日期，过了日期则将该记录删除
                if ($expire_times < time()) {
                    // 判断该记录restore_role_id是否有值
                    if (! empty($m['restore_role_id'])) {
                        // 判断该值是否与该成员关联
                        $memberCount = FresnsMemberRoleRels::where('member_id', $m['member_id'])->where('role_id',
                            $m['restore_role_id'])->count();
                        if ($memberCount == 0) {
                            // 无关联，删除该条过期记录，以estore_role_id字段值创建一条记录
                            $inputs = [
                                'type' => 2,
                                'member_id' => $m['member_id'],
                                'role_id' => $m['restore_role_id'],
                            ];
                            (new FresnsMemberRoleRels())->store($inputs);
                        } else {
                            // 已关联，删除该条过期记录，将已关联的type改为2
                            FresnsMemberRoleRels::where('member_id', $m['member_id'])->where('role_id',
                                $m['restore_role_id'])->update(['type' => 2]);
                        }
                        FresnsMemberRoleRels::where('id', $m['id'])->delete();
                    }
                }
            }
        }

        FresnsSessionLogsService::updateSessionLogs($sessionId, 2);

        return $this->pluginSuccess();
    }

    /**
     * 每隔 8 小时执行一次用户注销任务：
     * delete_account
     * 1.不启用注销功能
     * 2.软注销
     * 3.硬注销
     */
    protected function crontabCheckDeleteUserHandler($input)
    {
        $sessionId = FresnsSessionLogsService::addSessionLogs('plg_cmd_crontab_check_delete_user', '定时任务');
        $deleteAccount = ApiConfigHelper::getConfigByItemKey('delete_account');
        $deleteAccountTodo = ApiConfigHelper::getConfigByItemKey('delete_account_todo') ?? 0;
        if ($deleteAccount == 1) {
            return $this->pluginSuccess();
        }
        //查询所有deleted_at有值的数据
        $users = DB::table(FresnsUsersConfig::CFG_TABLE)->where('deleted_at', '!=', null)->get([
            'id',
            'email',
            'phone',
            'deleted_at',
        ])->toArray();
        // $users = DB::table(FresnsUsersConfig::CFG_TABLE)->where('deleted_at',NULL)->get(['id','email','phone','deleted_at'])->toArray();
        $time = date('Y-m-d H:i:s', time());
        if ($users) {
            foreach ($users as $v) {
                $userDeleteTime = date('Y-m-d H:i:s', strtotime("+$deleteAccountTodo day", strtotime($v->deleted_at)));
                if ($userDeleteTime > $time) {
                    continue;
                }

                //判断当前是何种注销方式 2-软注销 3-硬注销
                if ($deleteAccount == 2) {
                    $isEmail = strstr($v->email, 'deleted#');
                    $isPhone = strstr($v->phone, 'deleted#');
                    if ($isEmail != false || $isPhone != false) {
                        continue;
                    }
                    $this->softDelete($v);
                }

                if ($deleteAccount == 3) {
                    $this->hardDelete($v);
                }
            }
        }

        FresnsSessionLogsService::updateSessionLogs($sessionId, 2);

        return $this->pluginSuccess();
    }

    /**
     * 软删除
     * 在 users 表 phone 和 email 字段现有内容前加上 deleted#YmdHis# 前缀，YmdHis 为当前执行任务时的时间；
     * 物理删除 user_connects 表关联信息；
     * 会话表 dialogs 如果存在记录，则标注已停用，字段为 a_is_deactivate 或 b_is_deactivate
     * 为避免遗漏，定时任务每次都检测库里所有 users > deleted_at 有值的用户，如果 phone 和 email 前缀有 deleted# 则代表执行过任务，则跳过，如果没有则执行任务。
     * 配置表 delete_account_todo 键值规定时间内的不处理；比如当前执行任务时，配置的是 7 天，则查询所有记录时，7 天内的不查（不处理）。
     */
    public function softDelete($input)
    {
        $deleteTime = date('YmdHis', time());
        $id = $input->id;
        $email = $input->email;
        $phone = $input->phone;

        $deletePrefix = 'deleted#'.$deleteTime.'#';
        $input = [];
        if ($email) {
            $input['email'] = $deletePrefix.$email;
        }
        if ($phone) {
            $input['phone'] = $deletePrefix.$phone;
        }

        DB::table(FresnsUsersConfig::CFG_TABLE)->where('id', $id)->update($input);
        DB::table(FresnsUserConnectsConfig::CFG_TABLE)->where('user_id', $id)->delete();
        //会话表 dialogs 如果存在记录，则标注已停用，字段为 a_is_deactivate 或 b_is_deactivate
        //查询用户下所有的成员id
        $memberIdArr = DB::table(FresnsMembersConfig::CFG_TABLE)->where('user_id', $id)->pluck('id')->toArray();
        if ($memberIdArr) {
            $aInput = [
                'a_is_deactivate' => 0,
            ];
            $bInput = [
                'b_is_deactivate' => 0,
            ];
            DB::table(FresnsDialogsConfig::CFG_TABLE)->whereIn('a_member_id', $memberIdArr)->update($aInput);
            DB::table(FresnsDialogsConfig::CFG_TABLE)->whereIn('b_member_id', $memberIdArr)->update($bInput);
        }
    }

    /**
     * 硬删除
     * 删除用户以下表的所有记录
     * users
     * user_connects
     * user_wallets
     * user_wallet_logs
     * plugin_badges
     * members
     * member_stats
     * member_role_rels
     * member_icons
     * member_likes
     * member_follows
     * member_shields
     * files 删除头像文件 + 该用户或成员上传的所有图片，转述给插件删除物理文件
     * file_appends
     * notifies
     * seo
     * posts 见「物理删除说明」文档“删除正式内容”部分的介绍
     * post_logs
     * comments 见「物理删除说明」文档“删除正式内容”部分的介绍
     * comment_logs.
     */
    public function hardDelete($data)
    {
        $deleteTime = date('YmdHis', time());
        $id = $data->id;
        $email = $data->email;
        $phone = $data->phone;
        //查找用户下所有的成员
        $memberIdArr = DB::table(FresnsMembersConfig::CFG_TABLE)->where('user_id', $id)->pluck('id')->toArray();
        // $memberIdArr = [20];
        DB::table(FresnsUsersConfig::CFG_TABLE)->where('id', $id)->delete();
        DB::table(FresnsUserConnectsConfig::CFG_TABLE)->where('user_id', $id)->delete();
        DB::table(FresnsUserWalletsConfig::CFG_TABLE)->where('user_id', $id)->delete();
        DB::table(FresnsUserWalletLogsConfig::CFG_TABLE)->where('user_id', $id)->delete();
        DB::table(FresnsPluginBadgesConfig::CFG_TABLE)->whereIn('member_id', $memberIdArr)->delete();
        DB::table(FresnsMembersConfig::CFG_TABLE)->where('user_id', $id)->delete();
        DB::table(FresnsMemberStatsConfig::CFG_TABLE)->whereIn('member_id', $memberIdArr)->delete();
        DB::table(FresnsMemberRoleRelsConfig::CFG_TABLE)->whereIn('member_id', $memberIdArr)->delete();
        DB::table(FresnsMemberIconsConfig::CFG_TABLE)->whereIn('member_id', $memberIdArr)->delete();
        DB::table(FresnsMemberLikesConfig::CFG_TABLE)->whereIn('member_id', $memberIdArr)->delete();
        DB::table(FresnsMemberLikesConfig::CFG_TABLE)->where('like_type', 1)->whereIn('like_id',
            $memberIdArr)->delete();
        DB::table(FresnsMemberFollowsConfig::CFG_TABLE)->whereIn('member_id', $memberIdArr)->delete();
        DB::table(FresnsMemberFollowsConfig::CFG_TABLE)->where('follow_type', 1)->whereIn('follow_id',
            $memberIdArr)->delete();
        DB::table(FresnsMemberShieldsConfig::CFG_TABLE)->whereIn('member_id', $memberIdArr)->delete();
        DB::table(FresnsMemberShieldsConfig::CFG_TABLE)->where('shield_type', 1)->whereIn('shield_id',
            $memberIdArr)->delete();
        DB::table(FresnsSessionLogsConfig::CFG_TABLE)->where('user_id', $id)->delete();
        DB::table(FresnsSessionTokensConfig::CFG_TABLE)->where('user_id', $id)->delete();
        $fileIdArr = DB::table(FresnsFileAppendsConfig::CFG_TABLE)->where('user_id', $id)->pluck('file_id')->toArray();
        $fileUuIdArr = DB::table(FresnsFileAppendsConfig::CFG_TABLE)->where('user_id', $id)->pluck('uuid')->toArray();
        $cmd = FresnsPluginConfig::PLG_CMD_HARD_DELETE_FID;
        //删除物理文件
        foreach ($fileUuIdArr as $v) {
            $input = [];
            $input['fid'] = $v;
            $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);
        }
        DB::table(FresnsFilesConfig::CFG_TABLE)->whereIn('id', $fileIdArr)->delete();
        DB::table(FresnsFileAppendsConfig::CFG_TABLE)->whereIn('file_id', $fileIdArr)->delete();
        DB::table(FresnsNotifiesConfig::CFG_TABLE)->whereIn('member_id', $memberIdArr)->delete();
        DB::table(FresnsNotifiesConfig::CFG_TABLE)->whereIn('source_id', $memberIdArr)->delete();
        DB::table('seo')->where('linked_type', 1)->whereIn('linked_id', $memberIdArr)->delete();
        //删除posts相关内容
        $postIdArr = DB::table(FresnsPostsConfig::CFG_TABLE)->whereIn('member_id',
            $memberIdArr)->pluck('id')->toArray();
        // dd($postIdArr);
        if ($postIdArr) {
            $cmd = FresnsPluginConfig::PLG_CMD_DELETE_CONTENT;
            foreach ($postIdArr as $v) {
                $input = [];
                $input['type'] = 1;
                $input['content'] = $v;
                $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);
                // dd($resp);
            }
        }
        //删除comment相关内容
        $commentIdArr = DB::table(FresnsCommentsConfig::CFG_TABLE)->whereIn('member_id',
            $memberIdArr)->pluck('id')->toArray();
        if ($commentIdArr) {
            $cmd = FresnsPluginConfig::PLG_CMD_DELETE_CONTENT;
            foreach ($commentIdArr as $v) {
                $input = [];
                $input['type'] = 2;
                $input['content'] = $v;
                $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);
            }
        }
        if ($memberIdArr) {
            $DialogsInput = [
                'a_is_deactivate' => 0,
                'b_is_deactivate' => 0,
            ];
            DB::table(FresnsDialogsConfig::CFG_TABLE)->whereIn('a_member_id', $memberIdArr)->orWhere('b_member_id',
                $memberIdArr)->update($DialogsInput);
        }
    }
}
