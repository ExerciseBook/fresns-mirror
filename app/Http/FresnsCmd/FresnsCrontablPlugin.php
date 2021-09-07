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
 * Fresns Timed Tasks
 */
class FresnsCrontablPlugin extends BasePlugin
{
    // Constructors
    public function __construct()
    {
        $this->pluginConfig = new FresnsCrontabPluginConfig();
        $this->pluginCmdHandlerMap = FresnsCrontabPluginConfig::PLG_CMD_HANDLE_MAP;
    }

    // Add subscription information
    protected function addSubPluginItemHandler($input)
    {
        $item = $input['sub_table_plugin_item'];
        $config = FresnsConfigs::where('item_key', FresnsSubPluginConfig::SUB_ADD_TABLE_PLUGINS)->first();
        if (! empty($config)) {
            $configArr = json_decode($config['item_value'], true);
            foreach ($item as $v) {
                foreach ($configArr as $value) {
                    if ($v['subscribe_plugin_unikey'] == $value['subscribe_plugin_unikey'] && $v['subscribe_plugin_cmd'] == $value['subscribe_plugin_cmd'] && $v['subscribe_table_name'] == $value['subscribe_table_name']) {
                        return $this->pluginError(ErrorCodeService::CODE_EXCEPTION, 'There are duplicate data');
                    }
                }
            }

            $data = array_merge($item, $configArr);
            FresnsConfigs::where('item_key', FresnsSubPluginConfig::SUB_ADD_TABLE_PLUGINS)->update(['item_value' => $data]);
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

    // Delete subscription information
    protected function deleteSubPluginItemHandler($input)
    {
        $item = $input['sub_table_plugin_item'];
        $config = FresnsConfigs::where('item_key', FresnsSubPluginConfig::SUB_ADD_TABLE_PLUGINS)->first();
        if (empty($config['item_value'])) {
            return $this->pluginError(ErrorCodeService::CODE_EXCEPTION, 'Data is empty');
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

        FresnsConfigs::where('item_key', FresnsSubPluginConfig::SUB_ADD_TABLE_PLUGINS)->update(['item_value' => $dataArr]);

        return $this->pluginSuccess();
    }

    // New Timed Tasks
    protected function addCrontabPluginItemHandler($input)
    {
        $item = $input['crontab_plugin_item'];
        $config = FresnsConfigs::where('item_key', 'crontab_plugins')->first();
        if (! empty($config)) {
            $configArr = json_decode($config['item_value'], true);
            foreach ($item as $v) {
                foreach ($configArr as $value) {
                    if ($v['crontab_plugin_unikey'] == $value['crontab_plugin_unikey'] && $v['crontab_plugin_cmd'] == $value['crontab_plugin_cmd']) {
                        return $this->pluginError(ErrorCodeService::CODE_EXCEPTION, 'There are duplicate data');
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

    // Delete Timed Tasks
    protected function deleteCrontabPluginItemHandler($input)
    {
        $item = $input['crontab_plugin_item'];
        $config = FresnsConfigs::where('item_key', 'crontab_plugins')->first();

        if (empty($config['item_value'])) {
            return $this->pluginError(ErrorCodeService::CODE_EXCEPTION, 'Data is empty');
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

    /**
     * Perform user role expiration time detection every 10 minutes
     * https://fresns.org/contributing/information/task.html
     */
    // Database Table: member_role_rels
    protected function crontabCheckRoleExpiredHandler($input)
    {
        $sessionId = FresnsSessionLogsService::addSessionLogs('plg_cmd_crontab_check_role_expired', 'Timed Tasks');
        $memberInfo = FresnsMemberRoleRels::where('type', 2)->where('expired_at', '!=', null)->get()->toArray();
        if ($memberInfo) {
            foreach ($memberInfo as $m) {
                $expire_times = strtotime($m['expired_at']);
                // Determine whether the date has passed, and delete the record if the date has passed
                if ($expire_times < time()) {
                    // Determine if the record restore_role_id has a value
                    if (! empty($m['restore_role_id'])) {
                        // Determines if the value is associated with the member
                        $memberCount = FresnsMemberRoleRels::where('member_id', $m['member_id'])->where('role_id', $m['restore_role_id'])->count();
                        if ($memberCount == 0) {
                            // No association, delete the expired record and create a record with the estore_role_id field value
                            $inputs = [
                                'type' => 2,
                                'member_id' => $m['member_id'],
                                'role_id' => $m['restore_role_id'],
                            ];
                            (new FresnsMemberRoleRels())->store($inputs);
                        } else {
                            // There is an association, delete the expired record, change the associated type to 2
                            FresnsMemberRoleRels::where('member_id', $m['member_id'])->where('role_id', $m['restore_role_id'])->update(['type' => 2]);
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
     * Perform user deletion tasks every 8 hours：
     * configs > item_key: delete_account
     * 1.Delete function is not enabled
     * 2.Logical Deletion
     * 3.Physical Deletion
     * https://fresns.org/contributing/information/task.html
     */
    protected function crontabCheckDeleteUserHandler($input)
    {
        $sessionId = FresnsSessionLogsService::addSessionLogs('plg_cmd_crontab_check_delete_user', 'Timed Tasks');
        $deleteAccount = ApiConfigHelper::getConfigByItemKey('delete_account');
        $deleteAccountTodo = ApiConfigHelper::getConfigByItemKey('delete_account_todo') ?? 0;
        if ($deleteAccount == 1) {
            return $this->pluginSuccess();
        }
        // Query all the data with the value of deleted_at
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

                // Determine which deletion method is currently in use
                // 2.Logical Deletion
                // 3.Physical Deletion
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
     * 2.Logical Deletion
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
        /**
         * Handle Dialogs: If a record exists, it is marked as deactivated
         * The column is a_is_deactivate or b_is_deactivate
         */
        // Query all member ids under the user
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
     * 3.Physical Deletion
     * Delete all records from the following tables of the user
     * 
     * users
     * user_connects
     * user_wallets
     * user_wallet_logs
     * session_tokens
     * session_logs
     * plugin_badges
     * members
     * member_stats
     * member_role_rels
     * member_icons
     * member_likes
     * member_follows
     * member_shields
     * files
     * file_appends
     * notifies
     * seo
     * posts
     * post_logs
     * comments
     * comment_logs
     * https://fresns.org/extensions/delete.html
     */
    public function hardDelete($data)
    {
        $deleteTime = date('YmdHis', time());
        $id = $data->id;
        $email = $data->email;
        $phone = $data->phone;
        // Query all member ids under the user
        $memberIdArr = DB::table(FresnsMembersConfig::CFG_TABLE)->where('user_id', $id)->pluck('id')->toArray();
        // Physical Deletion: data
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
        DB::table(FresnsMemberLikesConfig::CFG_TABLE)->where('like_type', 1)->whereIn('like_id', $memberIdArr)->delete();
        DB::table(FresnsMemberFollowsConfig::CFG_TABLE)->whereIn('member_id', $memberIdArr)->delete();
        DB::table(FresnsMemberFollowsConfig::CFG_TABLE)->where('follow_type', 1)->whereIn('follow_id', $memberIdArr)->delete();
        DB::table(FresnsMemberShieldsConfig::CFG_TABLE)->whereIn('member_id', $memberIdArr)->delete();
        DB::table(FresnsMemberShieldsConfig::CFG_TABLE)->where('shield_type', 1)->whereIn('shield_id', $memberIdArr)->delete();
        DB::table(FresnsSessionLogsConfig::CFG_TABLE)->where('user_id', $id)->delete();
        DB::table(FresnsSessionTokensConfig::CFG_TABLE)->where('user_id', $id)->delete();
        $fileIdArr = DB::table(FresnsFileAppendsConfig::CFG_TABLE)->where('user_id', $id)->pluck('file_id')->toArray();
        $fileUuIdArr = DB::table(FresnsFileAppendsConfig::CFG_TABLE)->where('user_id', $id)->pluck('uuid')->toArray();
        $cmd = FresnsPluginConfig::PLG_CMD_HARD_DELETE_FID;
        // Physical Deletion: files
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
        // Physical Deletion: post data
        $postIdArr = DB::table(FresnsPostsConfig::CFG_TABLE)->whereIn('member_id', $memberIdArr)->pluck('id')->toArray();
        if ($postIdArr) {
            $cmd = FresnsPluginConfig::PLG_CMD_DELETE_CONTENT;
            foreach ($postIdArr as $v) {
                $input = [];
                $input['type'] = 1;
                $input['content'] = $v;
                $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);
            }
        }
        // Physical Deletion: comment data
        $commentIdArr = DB::table(FresnsCommentsConfig::CFG_TABLE)->whereIn('member_id', $memberIdArr)->pluck('id')->toArray();
        if ($commentIdArr) {
            $cmd = FresnsPluginConfig::PLG_CMD_DELETE_CONTENT;
            foreach ($commentIdArr as $v) {
                $input = [];
                $input['type'] = 2;
                $input['content'] = $v;
                $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);
            }
        }
        // Handle Dialogs
        if ($memberIdArr) {
            $DialogsInput = [
                'a_is_deactivate' => 0,
                'b_is_deactivate' => 0,
            ];
            DB::table(FresnsDialogsConfig::CFG_TABLE)->whereIn('a_member_id', $memberIdArr)->orWhere('b_member_id', $memberIdArr)->update($DialogsInput);
        }
    }
}
