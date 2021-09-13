<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsPosts;

use App\Helpers\StrHelper;
use App\Http\Center\Common\LogService;
use App\Http\Center\Helper\PluginRpcHelper;
use App\Http\FresnsApi\Base\FresnsBaseApiController;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsCmd\FresnsSubPlugin;
use App\Http\FresnsCmd\FresnsSubPluginConfig;
use App\Http\FresnsDb\FresnsCommentLogs\FresnsCommentLogs;
use App\Http\FresnsDb\FresnsDomainLinks\FresnsDomainLinks;
use App\Http\FresnsDb\FresnsDomainLinks\FresnsDomainLinksConfig;
use App\Http\FresnsDb\FresnsDomains\FresnsDomains;
use App\Http\FresnsDb\FresnsExtends\FresnsExtends;
use App\Http\FresnsDb\FresnsFiles\FresnsFiles;
use App\Http\FresnsDb\FresnsGroups\FresnsGroups;
use App\Http\FresnsDb\FresnsHashtagLinkeds\FresnsHashtagLinkeds;
use App\Http\FresnsDb\FresnsHashtagLinkeds\FresnsHashtagLinkedsConfig;
use App\Http\FresnsDb\FresnsHashtags\FresnsHashtags;
use App\Http\FresnsDb\FresnsLanguages\AmModel as FresnsLanguagesModel;
use App\Http\FresnsDb\FresnsLanguages\FresnsLanguages;
use App\Http\FresnsDb\FresnsLanguages\FresnsLanguagesService;
use App\Http\FresnsDb\FresnsMemberRoles\FresnsMemberRoles;
use App\Http\FresnsDb\FresnsMembers\FresnsMembers;
use App\Http\FresnsDb\FresnsMemberStats\FresnsMemberStats;
use App\Http\FresnsDb\FresnsPostAllows\FresnsPostAllowsConfig;
use App\Http\FresnsDb\FresnsPostAppends\FresnsPostAppends;
use App\Http\FresnsDb\FresnsPostAppends\FresnsPostAppendsConfig;
use App\Http\FresnsDb\FresnsPostLogs\FresnsPostLogs;
use App\Http\FresnsDb\FresnsPosts\FresnsPosts;
use App\Http\FresnsDb\FresnsSessionLogs\FresnsSessionLogs;
use App\Http\FresnsDb\FresnsStopWords\FresnsStopWords;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

header('Content-Type:text/html;charset=utf-8');

class FresnsPostsService extends AmService
{
    // Publish post
    public function releaseByDraft($draftId, $sessionLogsId = 0)
    {
        // Direct Publishing
        $draftPost = FresnsPostLogs::find($draftId);
        $releaseResult = $this->doRelease($draftId, $sessionLogsId);
        if (! $releaseResult) {
            LogService::formatInfo('Post Publish Exception');

            return false;
        }
        return $releaseResult;
    }

    // Publish Type
    public function doRelease($draftId, $sessionLogsId)
    {
        // Determine if it is an update or a new addition
        $draftPost = FresnsPostLogs::find($draftId);
        if (! $draftPost) {
            LogService::formatInfo('Post log does not exist');
            return false;
        }
        // Type
        if (! $draftPost['post_id']) {
            // add
            $res = $this->storeToDb($draftId, $sessionLogsId);
        } else {
            // edit
            $res = $this->updateDb($draftId, $sessionLogsId);
        }
        return true;
    }

    // Insert main table (add)
    public function storeToDb($draftId, $sessionLogsId)
    {
        // Parsing basic information
        $draftPost = FresnsPostLogs::find($draftId);

        // Parse content information (determine whether the content needs to be truncated)
        $contentBrief = $this->parseDraftContent($draftId);
        $uuid = strtolower(StrHelper::randString(8));

        // Get the number of words in the brief of the post
        $postEditorBriefCount = ApiConfigHelper::getConfigByItemKey(AmConfig::POST_EDITOR_WORD_COUNT) ?? 280;
        if (mb_strlen($draftPost['content']) > $postEditorBriefCount) {
            $is_brief = 1;
        } else {
            $is_brief = 0;
        }
        $allosJsonDecode = json_decode($draftPost['allow_json'], true);
        $is_allow = $allosJsonDecode['isAllow'] ?? 0;

        // Location Config
        $locationJson = json_decode($draftPost['location_json'], true);
        $isLbs = $locationJson['isLbs'] ?? 0;
        $mapId = $locationJson['mapId'] ?? null;
        $latitude = $locationJson['latitude'] ?? null;
        $longitude = $locationJson['longitude'] ?? null;
        $more_json = [];
        $more_json['files'] = json_decode($draftPost['files_json'], true);
        $postInput = [
            'uuid' => $uuid,
            'member_id' => $draftPost['member_id'],
            'group_id' => $draftPost['group_id'],
            'type' => $draftPost['type'],
            'title' => $draftPost['title'],
            'content' => $contentBrief,
            'is_anonymous' => $draftPost['is_anonymous'],
            'is_brief' => $is_brief,
            // 'status' => 3,
            'is_allow' => $is_allow,
            'is_lbs' => $isLbs,
            'map_id' => $mapId,
            'map_latitude' => $latitude,
            'map_longitude' => $longitude,
            // 'release_at'  => date('Y-m-d H:i:s'),
            'more_json' => json_encode($more_json),
        ];
        $postId = (new FresnsPosts())->store($postInput);
        $AppendStore = $this->postAppendStore($postId, $draftId);
        if ($AppendStore) {
            FresnsSessionLogs::where('id', $sessionLogsId)->update([
                'object_result' => 2,
                'object_order_id' => $postId,
            ]);
            // Execute the corresponding operation after entering the master table
            $this->afterStoreToDb($postId, $draftId);
        }
    }

    // Insert main table (edit)
    public function updateDb($draftId, $sessionLogsId)
    {
        $draftPost = FresnsPostLogs::find($draftId);
        FresnsSessionLogs::where('id', $sessionLogsId)->update([
            'object_result' => 2,
            'object_order_id' => $draftPost['post_id'],
        ]);
        $post = FresnsPosts::find($draftPost['post_id']);
        // Group post count
        FresnsGroups::where('id', $post['group_id'])->decrement('post_count');

        // Parse content information (determine whether the content needs to be truncated)
        $contentBrief = $this->parseDraftContent($draftId);

        // Get the number of words in the brief of the post
        $postEditorBriefCount = ApiConfigHelper::getConfigByItemKey(AmConfig::POST_EDITOR_WORD_COUNT) ?? 280;
        if (mb_strlen($draftPost['content']) > $postEditorBriefCount) {
            $is_brief = 1;
        } else {
            $is_brief = 0;
        }

        // Allow Config
        $allosJsonDecode = json_decode($draftPost['allow_json'], true);
        $is_allow = $allosJsonDecode['isAllow'] ?? 0;

        // Location Config
        $locationJson = json_decode($draftPost['location_json'], true);
        $isLbs = $locationJson['isLbs'] ?? 0;
        $mapId = $locationJson['mapId'] ?? null;
        $latitude = $locationJson['latitude'] ?? null;
        $longitude = $locationJson['longitude'] ?? null;

        $more_json = json_decode($post['more_json'], true) ?? null;
        $more_json['files'] = json_decode($draftPost['files_json'], true);
        $input = [
            'group_id' => $draftPost['group_id'],
            'type' => $draftPost['type'],
            'title' => $draftPost['title'],
            'content' => $contentBrief,
            'is_anonymous' => $draftPost['is_anonymous'],
            'is_brief' => $is_brief,
            'is_allow' => $is_allow,
            'is_lbs' => $isLbs,
            'map_id' => $mapId,
            'map_latitude' => $latitude,
            'map_longitude' => $longitude,
            'latest_edit_at' => date('Y-m-d H:i:s'),
            'more_json' => $more_json,
        ];
        FresnsPosts::where('id', $draftPost['post_id'])->update($input);
        $AppendStore = $this->postAppendUpdate($draftPost['post_id'], $draftId);
        if ($AppendStore) {
            // Perform the corresponding operation after inserting into the main table
            $this->afterUpdateToDb($draftPost['post_id'], $draftId);
        }
    }

    // post_appends (add)
    public function postAppendStore($postId, $draftId)
    {
        $draftPost = FresnsPostLogs::find($draftId);
        // Editor Config
        $pluginEdit = $draftPost['is_plugin_edit'];
        $pluginUnikey = $draftPost['plugin_unikey'];

        // Specific members config
        $member_list_json = $draftPost['member_list_json'];
        $member_list_name = [];
        if ($member_list_json) {
            $member_list_decode = json_decode($member_list_json, true);
            $member_list_status = $member_list_decode['memberListStatus'] ?? 0;
            $member_list_plugin_unikey = $member_list_decode['pluginUnikey'] ?? [];
            $member_list_name = $member_list_decode['memberListName'] ?? [];
            // Specific member names multilingual
            if ($member_list_name) {
                $inputArr = [];
                foreach ($member_list_name as $v) {
                    $item = [];
                    $tagArr = FresnsLanguagesService::conversionLangTag($v['langTag']);
                    $item['lang_code'] = $tagArr['lang_code'];
                    $item['area_code'] = $tagArr['area_code'];
                    $item['lang_tag'] = $v['langTag'];
                    $item['lang_content'] = $v['name'];
                    $item['table_field'] = 'member_list_name';
                    $item['table_id'] = $postId;
                    $item['table_name'] = AmConfig::CFG_TABLE;
                    $count = FresnsLanguages::where($item)->count();
                    if ($count == 0) {
                        FresnsLanguagesModel::insert($item);
                    }
                    $inputArr[] = $item;
                }
            }
        }

        // Comment Config
        $commentConfig = $draftPost['comment_set_json'];
        $commentBtnStatus = 0;
        $commentPluginUnikey = null;
        $commentBtnName = null;
        if ($commentConfig) {
            $commentConfig_decode = json_decode($commentConfig, true);
            $commentBtnStatus = $commentConfig_decode['btnStatus'] ?? 0;
            $commentPluginUnikey = $commentConfig_decode['pluginUnikey'] ?? null;
            $commentBtnName = $commentConfig_decode['btnName'] ?? null;
            // Btn names multilingual
            if ($commentConfig_decode['btnName']) {
                $btnNameArr = $commentConfig_decode['btnName'];
                $inputArr = [];
                foreach ($btnNameArr as $v) {
                    $item = [];
                    $tagArr = FresnsLanguagesService::conversionLangTag($v['langTag']);
                    $item['lang_code'] = $tagArr['lang_code'];
                    $item['area_code'] = $tagArr['area_code'];
                    $item['lang_tag'] = $v['langTag'];
                    $item['lang_content'] = $v['name'];
                    $item['table_field'] = 'comment_btn_name';
                    $item['table_id'] = $postId;
                    $item['table_name'] = AmConfig::CFG_TABLE;
                    $count = FresnsLanguages::where($item)->count();
                    if ($count == 0) {
                        FresnsLanguagesModel::insert($item);
                    }
                }
            }
        }

        // Read Allow Config
        $allowJson = $draftPost['allow_json'];
        $allowPluginUnikey = null;
        $allowBtnName = null;
        $proportion = null;
        $web_proportion = ApiConfigHelper::getConfigByItemKey(AmConfig::WEB_PROPORTION) ?? 30;
        if ($allowJson) {
            $allosJsonDecode = json_decode($allowJson, true);
            $allowPluginUnikey = $allosJsonDecode['pluginUnikey'] ?? null;
            $allowBtnName = $allosJsonDecode['btnName'] ?? null;
            $proportion = $allosJsonDecode['proportion'] ?? $web_proportion;
            $proportion = empty($proportion) ? $web_proportion : $proportion;
            // Btn names multilingual
            if ($allosJsonDecode['btnName']) {
                $btnNameArr = $allosJsonDecode['btnName'];
                $inputArr = [];
                foreach ($btnNameArr as $v) {
                    $item = [];
                    $tagArr = FresnsLanguagesService::conversionLangTag($v['langTag']);
                    $item['lang_code'] = $tagArr['lang_code'];
                    $item['area_code'] = $tagArr['area_code'];
                    $item['lang_tag'] = $v['langTag'];
                    $item['lang_content'] = $v['name'];
                    $item['table_field'] = 'allow_btn_name';
                    $item['table_id'] = $postId;
                    $item['table_name'] = AmConfig::CFG_TABLE;
                    $count = FresnsLanguages::where($item)->count();
                    if ($count == 0) {
                        FresnsLanguagesModel::insert($item);
                    }
                }
            }
            // postAllow data
            if ($allosJsonDecode['permission']) {
                $permission = $allosJsonDecode['permission'];
                // Allow members
                if ($permission['members']) {
                    $allowMemberArr = $permission['members'];
                    foreach ($allowMemberArr as $m) {
                        $memberInfo = FresnsMembers::where('uuid', $m['mid'])->first();
                        if ($memberInfo) {
                            DB::table(FresnsPostAllowsConfig::CFG_TABLE)->insert([
                                'post_id' => $postId,
                                'type' => 1,
                                'object_id' => $memberInfo['id'],
                            ]);
                        }
                    }
                }
                // Allow roles
                if ($permission['roles']) {
                    $allowRolesArr = $permission['roles'];
                    foreach ($allowRolesArr as $r) {
                        $memberRolesInfo = FresnsMemberRoles::find($r['rid']);
                        if ($memberRolesInfo) {
                            DB::table(FresnsPostAllowsConfig::CFG_TABLE)->insert([
                                'post_id' => $postId,
                                'type' => 2,
                                'object_id' => $memberRolesInfo['id'],
                            ]);
                        }
                    }
                }
            }
        }

        // Location Config
        $locationJson = json_decode($draftPost['location_json'], true);
        $scale = $locationJson['scale'] ?? '';
        $poi = $locationJson['poi'] ?? '';
        $poiId = $locationJson['poiId'] ?? '';
        $nation = $locationJson['nation'] ?? '';
        $province = $locationJson['province'] ?? '';
        $city = $locationJson['city'] ?? '';
        $district = $locationJson['district'] ?? '';
        $adcode = $locationJson['adcode'] ?? '';
        $address = $locationJson['address'] ?? '';

        // Extends
        $extendsJson = json_decode($draftPost['extends_json'], true);
        if ($extendsJson) {
            foreach ($extendsJson as $e) {
                $extend = FresnsExtends::where('uuid', $e['eid'])->first();
                if ($extend) {
                    $input = [
                        'linked_type' => 1,
                        'linked_id' => $postId,
                        'extend_id' => $extend['id'],
                        'plugin_unikey' => $extend['plugin_unikey'] ?? '',
                        'rank_num' => $e['rankNum'] ?? 9,
                    ];
                    Db::table('extend_linkeds')->insert($input);
                }
            }
        }
        // Existence of replacement words
        $content = $draftPost['content'];
        $content = $this->stopWords($content);
        // Removing html tags
        $content = strip_tags($content);
        $postAppendInput = [
            'post_id' => $postId,
            'platform_id' => $draftPost['platform_id'],
            'content' => $content,
            'is_markdown' => $draftPost['is_markdown'],
            'is_plugin_edit' => $pluginEdit,
            'plugin_unikey' => $pluginUnikey,
            'comment_btn_status' => $commentBtnStatus,
            'comment_btn_plugin_unikey' => $commentPluginUnikey,
            'comment_btn_name' => json_encode($commentBtnName),
            'allow_plugin_unikey' => $allowPluginUnikey,
            'member_list_status' => $member_list_status ?? 0,
            'member_list_plugin_unikey' => $member_list_plugin_unikey ?? null,
            'member_list_name' => json_encode($member_list_name) ?? null,
            'allow_btn_name' => json_encode($allowBtnName) ?? null,
            'allow_proportion' => $proportion ?? null,
            'map_scale' => $scale,
            'map_poi' => $poi,
            'map_poi_id' => $poiId,
            'map_nation' => $nation,
            'map_province' => $province,
            'map_city' => $city,
            'map_district' => $district,
            'map_adcode' => $adcode,
            'map_address' => $address,
        ];
        DB::table(FresnsPostAppendsConfig::CFG_TABLE)->insert($postAppendInput);

        return true;
    }

    // post_appends (edit)
    public function postAppendUpdate($postId, $draftId)
    {
        $draftPost = FresnsPostLogs::find($draftId);
        // Editor Config
        $pluginEdit = $draftPost['is_plugin_edit'];
        $pluginUnikey = $draftPost['plugin_unikey'];
        // Specific members config
        $member_list_json = $draftPost['member_list_json'];
        $member_list_name = [];
        $member_list_status = 0;
        if ($member_list_json) {
            // Delete the old data first (empty the multilingual table)
            FresnsLanguages::where('table_name', AmConfig::CFG_TABLE)->where('table_id', $postId)->where('table_field',
                'member_list_name')->delete();
            $member_list_decode = json_decode($member_list_json, true);
            $member_list_status = $member_list_decode['memberListStatus'] ?? 0;
            $member_list_plugin_unikey = $member_list_decode['pluginUnikey'] ?? [];
            $member_list_name = $member_list_decode['memberListName'] ?? [];
            // Specific member names multilingual
            if ($member_list_name) {
                $inputArr = [];
                foreach ($member_list_name as $v) {
                    $item = [];
                    $tagArr = FresnsLanguagesService::conversionLangTag($v['langTag']);
                    $item['lang_code'] = $tagArr['lang_code'];
                    $item['area_code'] = $tagArr['area_code'];
                    $item['lang_tag'] = $v['langTag'];
                    $item['lang_content'] = $v['name'];
                    $item['table_field'] = 'member_list_name';
                    $item['table_id'] = $postId;
                    $item['table_name'] = AmConfig::CFG_TABLE;
                    $count = FresnsLanguages::where($item)->count();
                    if ($count == 0) {
                        FresnsLanguagesModel::insert($item);
                    }
                    $inputArr[] = $item;
                }
            }
        }

        // Comment Config
        $commentConfig = $draftPost['comment_set_json'];
        $commentBtnStatus = 0;
        $commentPluginUnikey = null;
        $commentBtnName = null;
        if ($commentConfig) {
            $commentConfig_decode = json_decode($commentConfig, true);
            $commentBtnStatus = $commentConfig_decode['btnStatus'] ?? 0;
            $commentPluginUnikey = $commentConfig_decode['pluginUnikey'] ?? null;
            $commentBtnName = $commentConfig_decode['btnName'] ?? null;
            // Btn names multilingual
            if ($commentConfig_decode['btnName']) {
                // Delete the old data first (empty the multilingual table)
                FresnsLanguages::where('table_name', AmConfig::CFG_TABLE)->where('table_id',
                    $postId)->where('table_field', 'comment_btn_name')->delete();
                $btnNameArr = $commentConfig_decode['btnName'];
                $inputArr = [];
                foreach ($btnNameArr as $v) {
                    $item = [];
                    $tagArr = FresnsLanguagesService::conversionLangTag($v['langTag']);
                    $item['lang_code'] = $tagArr['lang_code'];
                    $item['area_code'] = $tagArr['area_code'];
                    $item['lang_tag'] = $v['langTag'];
                    $item['lang_content'] = $v['name'];
                    $item['table_field'] = 'comment_btn_name';
                    $item['table_id'] = $postId;
                    $item['table_name'] = AmConfig::CFG_TABLE;
                    $count = FresnsLanguages::where($item)->count();
                    if ($count == 0) {
                        FresnsLanguagesModel::insert($item);
                    }
                }
            }
        }

        // Read Allow Config
        $allowJson = $draftPost['allow_json'];
        $allowPluginUnikey = '';
        $allowBtnName = null;
        $proportion = null;
        $web_proportion = ApiConfigHelper::getConfigByItemKey(AmConfig::WEB_PROPORTION) ?? 30;
        if ($allowJson) {
            $allosJsonDecode = json_decode($allowJson, true);
            $allowPluginUnikey = $allosJsonDecode['pluginUnikey'] ?? null;
            $allowBtnName = $allosJsonDecode['btnName'] ?? null;
            $proportion = $allosJsonDecode['proportion'] ?? $web_proportion;
            $proportion = empty($proportion) ? $web_proportion : $proportion;
            // Btn names multilingual
            if ($allosJsonDecode['btnName']) {
                $btnNameArr = $allosJsonDecode['btnName'];
                $inputArr = [];
                foreach ($btnNameArr as $v) {
                    $item = [];
                    $tagArr = FresnsLanguagesService::conversionLangTag($v['langTag']);
                    $item['lang_code'] = $tagArr['lang_code'];
                    $item['area_code'] = $tagArr['area_code'];
                    $item['lang_tag'] = $v['langTag'];
                    $item['lang_content'] = $v['name'];
                    $item['table_field'] = 'allow_btn_name';
                    $item['table_id'] = $postId;
                    $item['table_name'] = AmConfig::CFG_TABLE;
                    $count = FresnsLanguages::where($item)->count();
                    if ($count == 0) {
                        FresnsLanguagesModel::insert($item);
                    }
                    $inputArr[] = $item;
                }
            }
            // postAllow data
            if ($allosJsonDecode['permission']) {
                $permission = $allosJsonDecode['permission'];
                if ($permission['members']) {
                    $allowMemberArr = $permission['members'];
                    if ($allowMemberArr) {
                        foreach ($allowMemberArr as $m) {
                            $memberInfo = FresnsMembers::where('uuid', $m['mid'])->first();
                            if ($memberInfo) {
                                $count = DB::table(FresnsPostAllowsConfig::CFG_TABLE)->where('post_id', $postId)->where('type',
                                    1)->where('object_id', $memberInfo['id'])->count();
                                if ($count == 0) {
                                    DB::table(FresnsPostAllowsConfig::CFG_TABLE)->insert([
                                        'post_id' => $postId,
                                        'type' => 1,
                                        'object_id' => $memberInfo['id'],
                                    ]);
                                }
                            }
                        }
                    }
                }

                if ($permission['roles']) {
                    // Empty first, then add
                    DB::table(FresnsPostAllowsConfig::CFG_TABLE)->where('post_id', $postId)->where('type', 2)->delete();
                    $allowRolesArr = $permission['roles'];
                    if ($allowRolesArr) {
                        foreach ($allowRolesArr as $r) {
                            $memberRolesInfo = FresnsMemberRoles::find($r['rid']);
                            if ($memberRolesInfo) {
                                $count = DB::table(FresnsPostAllowsConfig::CFG_TABLE)->where('post_id', $postId)->where('type',
                                    2)->where('object_id', $memberRolesInfo['id'])->count();
                                if ($count == 0) {
                                    DB::table(FresnsPostAllowsConfig::CFG_TABLE)->insert([
                                        'post_id' => $postId,
                                        'type' => 2,
                                        'object_id' => $memberRolesInfo['id'],
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        }

        // Location Config
        $locationJson = json_decode($draftPost['location_json'], true);
        $scale = $locationJson['scale'] ?? '';
        $poi = $locationJson['poi'] ?? '';
        $poiId = $locationJson['poiId'] ?? '';
        $nation = $locationJson['nation'] ?? '';
        $province = $locationJson['province'] ?? '';
        $city = $locationJson['city'] ?? '';
        $district = $locationJson['district'] ?? '';
        $adcode = $locationJson['adcode'] ?? '';
        $address = $locationJson['address'] ?? '';

        // Extends
        $extendsJson = json_decode($draftPost['extends_json'], true);
        if ($extendsJson) {
            // Empty first, then add
            Db::table('extend_linkeds')->where('linked_type', 1)->where('linked_id', $postId)->delete();
            foreach ($extendsJson as $e) {
                $extend = FresnsExtends::where('uuid', $e['eid'])->first();
                if ($extend) {
                    $input = [
                        'linked_type' => 1,
                        'linked_id' => $postId,
                        'extend_id' => $extend['id'],
                        'plugin_unikey' => $extend['plugin_unikey'] ?? '',
                        'rank_num' => $e['rankNum'] ?? '',
                    ];
                    Db::table('extend_linkeds')->insert($input);
                }
            }
        }
        // Existence of replacement words
        $content = $draftPost['content'];
        $content = $this->stopWords($content);
        // Removing html tags
        $content = strip_tags($content);
        $postAppendInput = [
            'platform_id' => $draftPost['platform_id'],
            'content' => $content,
            'is_markdown' => $draftPost['is_markdown'],
            'is_plugin_edit' => $pluginEdit,
            'plugin_unikey' => $pluginUnikey,
            'comment_btn_status' => $commentBtnStatus,
            'comment_btn_plugin_unikey' => $commentPluginUnikey,
            'comment_btn_name' => json_encode($commentBtnName),
            'allow_plugin_unikey' => $allowPluginUnikey,
            'member_list_status' => $member_list_status ?? 0,
            'member_list_plugin_unikey' => $member_list_plugin_unikey ?? null,
            'member_list_name' => json_encode($member_list_name) ?? null,
            'allow_btn_name' => json_encode($allowBtnName) ?? null,
            'allow_proportion' => $proportion,
            'map_scale' => $scale,
            'map_poi' => $poi,
            'map_poi_id' => $poiId,
            'map_nation' => $nation,
            'map_province' => $province,
            'map_city' => $city,
            'map_district' => $district,
            'map_adcode' => $adcode,
            'map_address' => $address,
        ];
        FresnsPostAppends::where('post_id', $postId)->update($postAppendInput);

        return true;
    }

    // Perform the corresponding operation after inserting into the main table (add)
    public function afterStoreToDb($postId, $draftId)
    {
        // Call the plugin to subscribe to the command word
        $cmd = FresnsSubPluginConfig::PLG_CMD_SUB_ADD_TABLE;
        $input = [
            'tableName' => FresnsPostsConfig::CFG_TABLE,
            'insertId' => $postId,
        ];
        LogService::info('table_input', $input);
        PluginRpcHelper::call(FresnsSubPlugin::class, $cmd, $input);
        $draftPost = FresnsPostLogs::find($draftId);
        $content = $this->stopWords($draftPost['content']);

        // Log updated to published
        FresnsPostLogs::where('id', $draftId)->update(['status' => 3, 'post_id' => $postId, 'content' => $content]);
        // Add stats: groups > post_count
        FresnsGroups::where('id', $draftPost['group_id'])->increment('post_count');
        // Notification
        $this->sendAtMessages($postId, $draftId);
        // Add stats: member_stats > post_publish_count
        $this->memberStats($draftId);
        // Analyze the hashtag and domain
        $this->analisisHashtag($draftId, 1);
        $this->domainStore($postId, $draftId);

        return true;
    }

    // Perform the corresponding operation after inserting into the main table (edit)
    public function afterUpdateToDb($postId, $draftId)
    {
        // Call the plugin to subscribe to the command word
        $cmd = FresnsSubPluginConfig::PLG_CMD_SUB_ADD_TABLE;
        $input = [
            'tableName' => FresnsPostsConfig::CFG_TABLE,
            'insertId' => $postId,
        ];
        LogService::info('table_input', $input);
        PluginRpcHelper::call(FresnsSubPlugin::class, $cmd, $input);
        $draftPost = FresnsPostLogs::find($draftId);
        $content = $this->stopWords($draftPost['content']);

        // Log updated to published
        FresnsPostLogs::where('id', $draftId)->update(['status' => 3, 'post_id' => $postId, 'content' => $content]);
        // Add stats: groups > post_count
        FresnsGroups::where('id', $draftPost['group_id'])->increment('post_count');
        // Add stats: post_appends > edit_count
        FresnsPostAppends::where('post_id', $postId)->increment('edit_count');
        // Notification
        $this->sendAtMessages($postId, $draftId, 2);
        // Analyze the hashtag and domain
        $this->analisisHashtag($draftId, 2);
        $this->domainStore($postId, $draftId, 2);

        return true;
    }

    // Notifications (Call MessageService to handle)
    // Can't @ self, @ others generate a notification message to each other.
    public function sendAtMessages($postId, $draftId, $updateType = 2)
    {
        $draftPost = FresnsPostLogs::find($draftId);
        $postInfo = FresnsPosts::find($postId);
        preg_match_all("/@.*?\s/", $draftPost['content'], $atMatches);
        // Presence send message
        if ($atMatches[0]) {
            foreach ($atMatches[0] as $s) {
                // Query accept member id
                $name = trim(ltrim($s, '@'));
                $memberInfo = FresnsMembers::where('name', $name)->first();
                if ($memberInfo && ($memberInfo['id'] != $draftPost['member_id'])) {
                    $input = [
                        'source_id' => $postId,
                        'source_brief' => $postInfo['content'],
                        'member_id' => $memberInfo['id'],
                        'source_member_id' => $postInfo['member_id'],
                        'source_type' => 5,
                        'source_class' => 1,
                    ];
                    DB::table('notifies')->insert($input);
                    // @ Record table
                    $mentions = [
                        'member_id' => $postInfo['member_id'],
                        'linked_type' => 1,
                        'linked_id' => $postId,
                        'mention_member_id' => $memberInfo['id'],
                    ];
                    $count = DB::table('mentions')->where($mentions)->count();
                    if ($count == 0) {
                        DB::table('mentions')->insert($mentions);
                    }
                }
            }
        }

        return true;
    }

    // After successful posting, the primary key ID of the post is generated, and then the ID is filled into the files > table_id field to perfection the information.
    public function fillDbInfo($draftId)
    {
        $draftPost = FresnsPostLogs::find($draftId);
        $fileArr = json_decode($draftPost['files_json'], true);
        if ($fileArr) {
            foreach ($fileArr as $f) {
                $fileCount = FresnsFiles::where('uuid', $f['fid'])->count();
                if ($fileCount > 0) {
                    // FresnsFiles::where('uuid', $f['fid'])->update(['table_id' => $draftPost['post_id']]);
                    FresnsFiles::where('uuid', $f['fid'])->update(['table_id' => $draftId]);
                }
            }
        }

        return true;
    }

    // Add stats: member_stats > post_publish_count
    // Add stats: Configs item_key = post_counts
    public function memberStats($draftId)
    {
        $draftPost = FresnsPostLogs::find($draftId);
        $memberStats = FresnsMemberStats::where('member_id', $draftPost['member_id'])->first();
        if ($memberStats) {
            FresnsMemberStats::where('id', $memberStats['id'])->increment('post_publish_count');
        } else {
            (new FresnsMemberStats())->store(['member_id' => $draftPost['member_id'], 'post_publish_count' => 1]);
        }
        DB::table('configs')->where('item_key', AmConfig::POST_COUNTS)->increment('item_value');

        return true;
    }

    /**
     * Parsing Hashtag (insert hashtags table)
     * $params
     * updateType 1.add 2.edit
     */
    public function analisisHashtag($draftId, $updateType = 1)
    {
        $draftPost = FresnsPostLogs::find($draftId);
        if ($updateType == 2) {
            // Hashtag post_count
            $hashtagIdArr = FresnsHashtagLinkeds::where('linked_type', 1)->where('linked_id', $draftPost['post_id'])->pluck('hashtag_id')->toArray();
            FresnsHashtags::whereIn('id', $hashtagIdArr)->decrement('post_count');
            // Remove Hashtag association
            FresnsHashtagLinkeds::where('linked_type', 1)->where('linked_id', $draftPost['post_id'])->delete();
            // DB::table(FresnsHashtagLinkedsConfig::CFG_TABLE)->where('linked_type', 1)->where('linked_id',$draftPost['post_id'])->delete();
        }
        // The currently configured Hashtag display mode
        $hashtagShow = ApiConfigHelper::getConfigByItemKey(AmConfig::HASHTAG_SHOW) ?? 2;
        if ($hashtagShow == 1) {
            preg_match_all("/#.*?\s/", $draftPost['content'], $singlePoundMatches);
        } else {
            preg_match_all('/#.*?#/', $draftPost['content'], $singlePoundMatches);
        }

        if ($singlePoundMatches[0]) {
            foreach ($singlePoundMatches[0] as $s) {
                // Remove the # sign from Hashtag
                $s = trim(str_replace('#', '', $s));
                // Existence of Hashtag
                $hashInfo = FresnsHashtags::where('name', $s)->first();
                if ($hashInfo) {
                    // hashtags table: post_count +1
                    FresnsHashtags::where('id', $hashInfo['id'])->increment('post_count');
                    // Establishing Affiliations
                    $res = DB::table(FresnsHashtagLinkedsConfig::CFG_TABLE)->insert([
                        'linked_type' => 1,
                        'linked_id' => $draftPost['post_id'],
                        'hashtag_id' => $hashInfo['id'],
                    ]);
                } else {
                    // New Hashtag and Hashtag Association
                    $slug = urlencode(str_replace(' ', '-', $s));

                    if (preg_match("/^[a-zA-Z\s]+$/", $s)) {
                        $slug = $slug;
                    } else {
                        $slug = str_replace('-', '%20', $slug);
                    }
                    $input = [
                        'slug' => $slug,
                        'name' => $s,
                        'member_id' => $draftPost['member_id'],
                        'post_count' => 1,
                    ];
                    $hashtagId = (new FresnsHashtags())->store($input);
                    // Establishing Affiliations
                    $res = DB::table(FresnsHashtagLinkedsConfig::CFG_TABLE)->insert([
                        'linked_type' => 1,
                        'linked_id' => $draftPost['post_id'],
                        'hashtag_id' => $hashtagId,
                    ]);
                    DB::table('configs')->where('item_key', AmConfig::HASHTAG_COUNTS)->increment('item_value');
                }
            }
        }

        return true;
    }

    // Parsing truncated content information
    public function parseDraftContent($draftId)
    {
        $draftPost = FresnsPostLogs::find($draftId);
        $content = $draftPost['content'];
        if ($draftPost['allow_json']) {
            $allow_json = json_decode($draftPost['allow_json'], true);
            if ($allow_json['isAllow'] == 1) {
                $web_proportion = ApiConfigHelper::getConfigByItemKey(AmConfig::WEB_PROPORTION) ?? 30;
                if (! isset($allow_json['proportion'])) {
                    $proportion = $web_proportion;
                } else {
                    if (empty($allow_json['proportion'])) {
                        $proportion = $web_proportion;
                    } else {
                        $proportion = $allow_json['proportion'];
                    }
                }
                $proportionCount = (mb_strlen(trim($draftPost['content'])) * $proportion) / 100;

                // Get the maximum number of words for the post brief
                $postEditorBriefCount = ApiConfigHelper::getConfigByItemKey(AmConfig::POST_EDITOR_BRIEF_COUNT) ?? 280;
                if ($proportionCount > $postEditorBriefCount) {
                    $contentInfo = $this->truncatedContentInfo($content, $postEditorBriefCount);
                    $content = $contentInfo['truncated_content'];
                } else {
                    $contentInfo = $this->truncatedContentInfo($content, $proportionCount);
                    $content = $contentInfo['truncated_content'];
                }
            }
        }

        // Existence of replacement words
        $content = $this->stopWords($content);
        // Removing html tags
        $content = strip_tags($content);

        return $content;
    }

    /**
     * Domain Link Table
     * $params
     * updateType 1.add 2.edit
     */
    public function domainStore($postId, $draftId, $updateType = 1)
    {
        $draftPost = FresnsPostLogs::find($draftId);
        if ($updateType == 2) {
            $domainLinksIdArr = FresnsDomainLinks::where('linked_type', 1)->where('linked_id', $postId)->pluck('domain_id')->toArray();
            FresnsDomains::where('id', $domainLinksIdArr)->decrement('post_count');
            DB::table(FresnsDomainLinksConfig::CFG_TABLE)->where('linked_type', 1)->where('linked_id', $postId)->delete();
        }
        $postInfo = FresnsPosts::find($postId);
        preg_match_all("/http[s]{0,1}:\/\/.*?\s/", $draftPost['content'], $hrefMatches);
        if ($hrefMatches[0]) {
            foreach ($hrefMatches[0] as $h) {
                // Top level domains
                $firstDomain = $this->top_domain(trim($h));
                // Second level domain name
                $domain = $this->regular_domain(trim($h));
                // Does the domain table exist
                if ($domain) {
                    $domain_input = [
                        'domain' => $firstDomain,
                        'sld' => $domain,
                    ];
                    $domainInfo = FresnsDomains::where($domain_input)->first();
                    if ($domainInfo) {
                        $domainId = $domainInfo['id'];
                        FresnsDomains::where('id', $domainId)->increment('post_count');
                    } else {
                        $domainId = (new FresnsDomains())->store($domain_input);
                        FresnsDomains::where('id', $domainId)->increment('post_count');
                    }
                    $input = [
                        'linked_type' => 1,
                        'linked_id' => $postId,
                        'link_url' => trim($h),
                        'domain_id' => $domainId,
                    ];
                    $domainLinkCount = DB::table('domain_links')->where($input)->count();
                    if ($domainLinkCount == 0) {
                        DB::table('domain_links')->insert($input);
                    }
                }
            }
        }

        return true;
    }

    // "@", "#", "Link" Location information of the three in the full text
    
    // If the content exceeds the set number of words, the brief is stored.
    // If the last content of the brief is "@", "#", and "Link", it should be kept in full and not truncated.
    // The maximum number of words in the brief can be exceeded when preserving.
    public function truncatedContentInfo($content, $wordCount = 280)
    {
        // The currently configured Hashtag display mode
        $hashtagShow = ApiConfigHelper::getConfigByItemKey(AmConfig::HASHTAG_SHOW) ?? 2;
        // Match the location information in $content, where the rule is placed in the configuration file
        if ($hashtagShow == 1) {
            preg_match("/#.*?\s/", $content, $singlePoundMatches, PREG_OFFSET_CAPTURE);
        } else {
            preg_match('/#.*?#/', $content, $singlePoundMatches, PREG_OFFSET_CAPTURE);
        }
        /**
         * preg_match("/<a .*?>.*?<\/a>/",$content,$hrefMatches,PREG_OFFSET_CAPTURE);.
         */
        preg_match("/http[s]{0,1}:\/\/.*?\s/", $content, $hrefMatches, PREG_OFFSET_CAPTURE);
        
        // preg_match("/<a href=.*?}></a>/", $content, $hrefMatches,PREG_OFFSET_CAPTURE);
        preg_match("/@.*?\s/", $content, $atMatches, PREG_OFFSET_CAPTURE);
        $truncatedPos = ceil($wordCount);
        $findTruncatedPos = false;
        // Get the number of characters corresponding to the matched data (the match is bytes)
        $contentArr = self::getString($content);
        $charCounts = self::getChar($contentArr, $truncatedPos);
        // Determine the position of the interval where this wordCount falls.
        // If there is a hit, find the corresponding truncation position and execute the truncation
        // https://www.php.net/manual/en/function.preg-match.php
        foreach ($singlePoundMatches as $currMatch) {
            $matchStr = $currMatch[0];
            $matchStrStartPosition = $currMatch[1];
            $matchStrEndPosition = $currMatch[1] + strlen($matchStr);
            // Hit
            if ($matchStrStartPosition <= $charCounts && $matchStrEndPosition >= $charCounts) {
                $findTruncatedPos = true;
                $truncatedPos = $matchStrEndPosition;
            }
        }

        if (! $findTruncatedPos) {
            foreach ($hrefMatches as $currMatch) {
                $matchStr = $currMatch[0];
                $matchStrStartPosition = $currMatch[1];
                $matchStrEndPosition = $currMatch[1] + strlen($matchStr);
                // Hit
                if ($matchStrStartPosition <= $charCounts && $matchStrEndPosition >= $charCounts) {
                    $findTruncatedPos = true;
                    $truncatedPos = $matchStrEndPosition;
                }
            }
        }
        if (! $findTruncatedPos) {
            foreach ($atMatches as $currMatch) {
                $matchStr = $currMatch[0];
                $matchStrStartPosition = $currMatch[1];
                $matchStrEndPosition = $currMatch[1] + strlen($matchStr);
                // Hit
                if ($matchStrStartPosition <= $charCounts && $matchStrEndPosition >= $charCounts) {
                    $findTruncatedPos = true;
                    $truncatedPos = $matchStrEndPosition;
                }
            }
        }
        // Execute the operation
        $info = [];
        $info['find_truncated_pos'] = $findTruncatedPos;
        $info['truncated_pos'] = $truncatedPos;  // Truncation position
        if ($findTruncatedPos) {
            // Byte count to word count
            $chars = self::getChars($content);
            $strLen = self::getStrLen($chars, $truncatedPos);
        } else {
            $strLen = $truncatedPos;
        }

        $info['truncated_content'] = Str::substr($content, 0, $strLen); // Final content
        $info['single_pound_arr'] = $singlePoundMatches;
        $info['link_pound_arr'] = $hrefMatches;
        $info['at_arr'] = $atMatches;
        return $info;
    }

    // Perform Review Operations
    public function parseToReview($draftId)
    {
        // post
        FresnsPostLogs::where('id', $draftId)->update(['status' => 2, 'submit_at' => date('Y-m-d H:i:s')]);

        return true;
    }

    public static function getString($content)
    {
        $utf8posCharPosMap = [];
        $len = mb_strlen($content);
        $charPos = 0;
        $str = 0;
        for ($i = 0; $i < $len; $i++) {
            $utf8Pos = $i;

            $utf8PosDesc = 'utf_'.$utf8Pos;
            $charPosDesc = 'char_'.$charPos;
            $utf8posCharPosMap[$utf8PosDesc] = $charPosDesc;
            $char = mb_substr($content, $i, 1);
            if (preg_match("/^[\x7f-\xff]+$/", $char)) {
                $charPos = $charPos + 3;
            } else {
                $charPos = $charPos + 1;
            }
        }
        return $utf8posCharPosMap;
    }

    public static function getChar($utf8posCharPosMap, $sublen)
    {
        $chars = '';
        foreach ($utf8posCharPosMap as $key => $u) {
            if ($key == 'utf_'.$sublen) {
                $chars = str_replace('char_', '', $u);
            }
        }

        return $chars;
    }

    public static function getChars($content)
    {
        $utf8posCharPosMap = [];
        $len = mb_strlen($content, 'utf8');
        $charPos = 0;
        $char = '';
        for ($i = 0; $i < $len; $i++) {
            $utf8Pos = $i;

            $utf8PosDesc = 'utf_'.$utf8Pos;
            $charPosDesc = 'char_'.$charPos;
            $utf8posCharPosMap[$charPosDesc] = $utf8PosDesc;
            $char = mb_substr($content, $i, 1);
            if (preg_match("/^[\x7f-\xff]+$/", $char)) {
                $charPos = $charPos + 3;
            } else {
                $charPos = $charPos + 1;
            }
        }

        return $utf8posCharPosMap;
    }

    // Get the number of words corresponding to the number of bytes
    public static function getStrLen($utf8posCharPosMap, $sublen)
    {
        $strLen = '';
        foreach ($utf8posCharPosMap as $key => $u) {
            if ($key == 'char_'.$sublen) {
                $strLen = str_replace('utf_', '', $u);
            }
        }

        return $strLen;
    }

    public function regular_domain($domain)
    {
        if (substr($domain, 0, 7) == 'http://') {
            $domain = substr($domain, 7);
        }
        if (substr($domain, 0, 8) == 'https://') {
            $domain = substr($domain, 8);
        }
        if (strpos($domain, '/') !== false) {
            $domain = substr($domain, 0, strpos($domain, '/'));
        }

        return strtolower($domain);
    }

    public function top_domain($domain)
    {
        $domain = $this->regular_domain($domain);
        // Domain name suffix
        $iana_root = [
            // gTLDs
            'com','net','org','edu','gov','int','mil','arpa','biz','info','pro','name','coop','travel','xxx','idv','aero','museum','mobi','asia','tel','post','jobs','cat',
            // ccTLDs
            'ad','ae','af','ag','ai','al','am','an','ao','aq','ar','as','at','au','aw','az','ba','bb','bd','be','bf','bg','bh','bi','bj','bm','bn','bo','br','bs','bt','bv','bw','by','bz','ca','cc','cd','cf','cg','ch','ci','ck','cl','cm','cn','co','cr','cu','cv','cx','cy','cz','de','dj','dk','dm','do','dz','ec','ee','eg','eh','er','es','et','eu','fi','fj','fk','fm','fo','fr','ga','gd','ge','gf','gg','gh','gi','gl','gm','gn','gp','gq','gr','gs','gt','gu','gw','gy','hk','hm','hn','hr','ht','hu','id','ie','il','im','in','io','iq','ir','is','it','je','jm','jo','jp','ke','kg','kh','ki','km','kn','kp','kr','kw','ky','kz','la','lb','lc','li','lk','lr','ls','ma','mc','md','me','mg','mh','mk','ml','mm','mn','mo','mp','mq','mr','ms','mt','mu','mv','mw','mx','my','mz','na','nc','ne','nf','ng','ni','nl','no','np','nr','nu','nz','om','pa','pe','pf','pg','ph','pk','pl','pm','pn','pr','ps','pt','pw','py','qa','re','ro','ru','rw','sa','sb','sc','sd','se','sg','sh','si','sj','sk','sm','sn','so','sr','st','sv','sy','sz','tc','td','tf','tg','th','tj','tk','tl','tm','tn','to','tp','tr','tt','tv','tw','tz','ua','ug','uk','um','us','uy','uz','va','vc','ve','vg','vi','vn','vu','wf','ws','ye','yt','yu','yr','za','zm','zw',
            // new gTLDs (Business)
            'accountant','club','coach','college','company','construction','consulting','contractors','cooking','corp','credit','creditcard','dance','dealer','democrat','dental','dentist','design','diamonds','direct','doctor','drive','eco','education','energy','engineer','engineering','equipment','events','exchange','expert','express','faith','farm','farmers','fashion','finance','financial','fish','fit','fitness','flights','florist','flowers','food','football','forsale','furniture','game','games','garden','gmbh','golf','health','healthcare','hockey','holdings','holiday','home','hospital','hotel','hotels','house','inc','industries','insurance','insure','investments','islam','jewelry','justforu','kid','kids','law','lawyer','legal','lighting','limited','live','llc','llp','loft','ltd','ltda','managment','marketing','media','medical','men','money','mortgage','moto','motorcycles','music','mutualfunds','ngo','partners','party','pharmacy','photo','photography','photos','physio','pizza','plumbing','press','prod','productions','radio','rehab','rent','repair','report','republican','restaurant','room','rugby','safe','sale','sarl','save','school','secure','security','services','shoes','show','soccer','spa','sport','sports','spot','srl','storage','studio','tattoo','taxi','team','tech','technology','thai','tips','tour','tours','toys','trade','trading','travelers','university','vacations','ventures','versicherung','versicherung','vet','wedding','wine','winners','work','works','yachts','zone',
            // new gTLDs (Construction & Real Estate)
            'archi','architect','casa','contruction','estate','haus','house','immo','immobilien','lighting','loft','mls','realty',
            // new gTLDs (Community & Religion)
            'academy','arab','bible','care','catholic','charity','christmas','church','college','community','contact','degree','education','faith','foundation','gay','halal','hiv','indiands','institute','irish','islam','kiwi','latino','mba','meet','memorial','ngo','phd','prof','school','schule','science','singles','social','swiss','thai','trust','university','uno',
            // new gTLDs (E-commerce & Shopping)
            'auction','best','bid','boutique','center','cheap','compare','coupon','coupons','deal','deals','diamonds','discount','fashion','forsale','free','gift','gold','gratis','hot','jewelry','kaufen','luxe','luxury','market','moda','pay','promo','qpon','review','reviews','rocks','sale','shoes','shop','shopping','store','tienda','top','toys','watch','zero',
            // new gTLDs (Dining)
            'bar','bio','cafe','catering','coffee','cooking','diet','eat','food','kitchen','menu','organic','pizza','pub','rest','restaurant','vodka','wine',
            // new gTLDs (Travel)
            'abudhabi','africa','alsace','amsterdam','barcelona','bayern','berlin','boats','booking','boston','brussels','budapest','caravan','casa','catalonia','city','club','cologne','corsica','country','cruise','cruises','deal','deals','doha','dubai','durban','earth','flights','fly','fun','gent','guide','hamburg','helsinki','holiday','hotel','hoteles','hotels','ist','istanbul','joburg','koeln','land','london','madrid','map','melbourne','miami','moscow','nagoya','nrw','nyc','osaka','paris','party','persiangulf','place','quebec','reise','reisen','rio','roma','room','ruhr','saarland','stockholm','swiss','sydney','taipei','tickets','tirol','tokyo','tour','tours','town','travelers','vacations','vegas','wales','wien','world','yokohama','zuerich',
            // new gTLDs (Sports & Hobbies)
            'art','auto','autos','baby','band','baseball','beats','beauty','beknown','bike','book','boutique','broadway','car','cars','club','coach','contact','cool','cricket','dad','dance','date','dating','design','dog','events','family','fan','fans','fashion','film','final','fishing','football','fun','furniture','futbol','gallery','game','games','garden','gay','golf','guru','hair','hiphop','hockey','home','horse','icu','joy','kid','kids','life','lifestyle','like','living','lol','makeup','meet','men','moda','moi','mom','movie','movistar','music','party','pet','pets','photo','photography','photos','pics','pictures','play','poker','rodeo','rugby','run','salon','singles','ski','skin','smile','soccer','social','song','soy','sport','sports','star','style','surf','tatoo','tennis','theater','theatre','tunes','vip','wed','wedding','winwinners','yoga','you',
            // new gTLDs (Network Technology)
            'analytics','antivirus','app','blog','call','camera','channel','chat','click','cloud','computer','contact','data','dev','digital','direct','docs','domains','dot','download','email','foo','forum','graphics','guide','help','home','host','hosting','idn','link','lol','mail','mobile','network','online','open','page','phone','pin','search','site','software','webcam',
            // new gTLDs (Other)
            'airforce','army','black','blue','box','buzz','casa','cool','day','discover','donuts','exposed','fast','finish','fire','fyi','global','green','help','here','how','international','ira','jetzt','jot','like','live','kim','navy','new','news','next','ninja','now','one','ooo','pink','plus','red','solar','tips','today','weather','wow','wtf','xyz','abogado','adult','anquan','aquitaine','attorney','audible','autoinsurance','banque','bargains','bcn','beer','bet','bingo','blackfriday','bom','boo','bot','broker','builders','business','bzh','cab','cal','cam','camp','cancerresearch','capetown','carinsurance','casino','ceo','cfp','circle','claims','cleaning','clothing','codes','condos','connectors','courses','cpa','cymru','dds','delivery','desi','directory','diy','dvr','ecom','enterprises','esq','eus','fail','feedback','financialaid','frontdoor','fund','gal','gifts','gives','giving','glass','gop','got','gripe','grocery','group','guitars','hangout','homegoods','homes','homesense','hotels','ing','ink','juegos','kinder','kosher','kyoto','lat','lease','lgbt','liason','loan','loans','locker','lotto','love','maison','markets','matrix','meme','mov','okinawa','ong','onl','origins','parts','patch','pid','ping','porn','progressive','properties','property','protection','racing','read','realestate','realtor','recipes','rentals','sex','sexy','shopyourway','shouji','silk','solutions','stroke','study','sucks','supplies','supply','tax','tires','total','training','translations','travelersinsurcance','ventures','viajes','villas','vin','vivo','voyage','vuelos','wang','watches',
        ];
        $sub_domain = explode('.', $domain);
        $top_domain = '';
        $top_domain_count = 0;
        for ($i = count($sub_domain) - 1; $i >= 0; $i--) {
            if ($i == 0) {
                // just in case of something like NAME.COM
                break;
            }
            if (in_array($sub_domain [$i], $iana_root)) {
                $top_domain_count++;
                $top_domain = '.'.$sub_domain [$i].$top_domain;
                if ($top_domain_count >= 2) {
                    break;
                }
            }
        }
        $top_domain = $sub_domain [count($sub_domain) - $top_domain_count - 1].$top_domain;

        return $top_domain;
    }

    // Stop Word Rules
    public function stopWords($text)
    {
        $stopWordsArr = FresnsStopWords::get()->toArray();

        foreach ($stopWordsArr as $v) {
            $str = strstr($text, $v['word']);
            // dd($str);
            if ($str != false) {
                if ($v['content_mode'] == 2) {
                    $text = str_replace($v['word'], $v['replace_word'], $text);

                    return $text;
                }
            }
        }

        return $text;
    }
}
