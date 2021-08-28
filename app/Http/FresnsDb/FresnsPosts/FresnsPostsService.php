<?php

/*
 * Fresns
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
    // 发布文章
    public function releaseByDraft($draftId, $sessionLogsId = 0)
    {
        // 读取草稿表记录
        $draftPost = FresnsPostLogs::find($draftId);
        $releaseResult = $this->doRelease($draftId, $sessionLogsId);
        if (! $releaseResult) {
            LogService::formatInfo('帖子发布异常');

            return false;
        }

        return $releaseResult;
    }

    // 发表
    public function doRelease($draftId, $sessionLogsId)
    {
        // s判断是更新还是新增
        $draftPost = FresnsPostLogs::find($draftId);
        // $this->sendAtMessages(10,$draftId);
        if (! $draftPost['post_id']) {
            // dd(1);
            $res = $this->storeToDb($draftId, $sessionLogsId);
        } else {
            // 编辑帖子
            $res = $this->updateDb($draftId, $sessionLogsId);
        }

        return true;
    }

    // 入库(新增)
    public function storeToDb($draftId, $sessionLogsId)
    {
        // 解析基础信息
        $draftPost = FresnsPostLogs::find($draftId);
        // $baseInfoArr = $this->parseDraftBaseInfo($draftId);
        // dump($baseInfoArr);
        // 解析内容信息(判断内容是否需要截断)
        $contentBrief = $this->parseDraftContent($draftId);
        $uuid = strtolower(StrHelper::randString(8));
        // 获取帖子的摘要字数
        $commentEditorbRIEFCount = ApiConfigHelper::getConfigByItemKey(AmConfig::COMMENT_EDITOR_WORD_COUNT) ?? 280;
        if (mb_strlen($draftPost['content']) > $commentEditorbRIEFCount) {
            $is_brief = 1;
        } else {
            $is_brief = 0;
        }
        $allosJsonDecode = json_decode($draftPost['allow_json'], true);
        // dd($)
        $is_allow = $allosJsonDecode['isAllow'] ?? 0;
        // dd($is_allow);
        // 位置信息配置
        $locationJson = json_decode($draftPost['location_json'], true);
        $isLbs = $locationJson['isLbs'] ?? 0;
        $mapId = $locationJson['mapId'] ?? null;
        $latitude = $locationJson['latitude'] ?? null;
        $longitude = $locationJson['longitude'] ?? null;
        $more_json = [];
        $more_json['files'] = json_decode($draftPost['files_json'], true);
        // dd($more_json);
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
        // $postId = DB::table('posts')->insertGetId($postInput);
        // dd($postInput);
        $postId = (new FresnsPosts())->store($postInput);
        $AppendStore = $this->postAppendStore($postId, $draftId);
        if ($AppendStore) {
            FresnsSessionLogs::where('id', $sessionLogsId)->update([
                'object_result' => 2,
                'object_order_id' => $postId,
            ]);
            // 入库后执行相应操作
            $this->afterStoreToDb($postId, $draftId);
        }
    }

    // 入库（编辑）
    public function updateDb($draftId, $sessionLogsId)
    {
        $draftPost = FresnsPostLogs::find($draftId);
        FresnsSessionLogs::where('id', $sessionLogsId)->update([
            'object_result' => 2,
            'object_order_id' => $draftPost['post_id'],
        ]);
        $post = FresnsPosts::find($draftPost['post_id']);
        // 小组帖子是数-1(编辑前的小组数 - 1)
        FresnsGroups::where('id', $post['group_id'])->decrement('post_count');

        // 解析内容信息(判断内容是否需要截断)
        $contentBrief = $this->parseDraftContent($draftId);
        // 获取帖子的摘要字数
        $commentEditorbRIEFCount = ApiConfigHelper::getConfigByItemKey(AmConfig::COMMENT_EDITOR_WORD_COUNT) ?? 280;
        if (mb_strlen($draftPost['content']) > $commentEditorbRIEFCount) {
            $is_brief = 1;
        } else {
            $is_brief = 0;
        }
        $allosJsonDecode = json_decode($draftPost['allow_json'], true);
        // dd($)
        $is_allow = $allosJsonDecode['isAllow'] ?? 0;
        // 位置信息配置
        $locationJson = json_decode($draftPost['location_json'], true);
        $isLbs = $locationJson['isLbs'] ?? 0;
        $mapId = $locationJson['mapId'] ?? null;
        $latitude = $locationJson['latitude'] ?? null;
        $longitude = $locationJson['longitude'] ?? null;
        $more_json = json_decode($post['more_json'], true) ?? null;
        $more_json['files'] = json_decode($draftPost['files_json'], true);

        // $more_json['files'] = json_decode($draftPost['file_json'],true);
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
            // 入库后执行相应操作
            $this->afterUpdateToDb($draftPost['post_id'], $draftId);
        }
    }

    // 副表(新增)
    public function postAppendStore($postId, $draftId)
    {
        $draftPost = FresnsPostLogs::find($draftId);
        // 副表
        // 编辑器配置
        $pluginEdit = $draftPost['is_plugin_edit'];
        $pluginUnikey = $draftPost['plugin_unikey'];

        // 特定成员配置
        $member_list_json = $draftPost['member_list_json'];
        $member_list_name = [];
        if ($member_list_json) {
            $member_list_decode = json_decode($member_list_json, true);
            $member_list_status = $member_list_decode['memberListStatus'] ?? 0;
            $member_list_plugin_unikey = $member_list_decode['pluginUnikey'] ?? [];
            $member_list_name = $member_list_decode['memberListName'] ?? [];
            // 特定成员多语言
            if ($member_list_name) {
                // $memberListNameArr = $member_list_decode['memberListName'];
                $inputArr = [];
                foreach ($member_list_name as $v) {
                    $item = [];
                    $tagArr = FresnsLanguagesService::conversionLangTag($v['langTag']);
                    // $tagArr = explode('-',$v['lang_code']);
                    // $areaCode = array_pop($tagArr);
                    // $langCode = str_replace("-$areaCode",'',$v['lang_code']);
                    $item['lang_code'] = $tagArr['lang_code'];
                    $item['area_code'] = $tagArr['area_code'];
                    $item['lang_tag'] = $v['langTag'];
                    $item['lang_content'] = $v['name'];
                    $item['table_field'] = 'member_list_name';
                    $item['table_id'] = $postId;
                    $item['table_name'] = AmConfig::CFG_TABLE;
                    // $item['alias_key'] = $v['nickname'];
                    $count = FresnsLanguages::where($item)->count();
                    //  dump($count);
                    if ($count == 0) {
                        FresnsLanguagesModel::insert($item);
                    }
                    $inputArr[] = $item;
                }
                // FresnsLanguagesModel::insert($inputArr);
            }
        }
        // 评论设置
        $commentConfig = $draftPost['comment_set_json'];
        $commentBtnStatus = 0;
        $commentPluginUnikey = null;
        $commentBtnName = null;
        if ($commentConfig) {
            $commentConfig_decode = json_decode($commentConfig, true);
            $commentBtnStatus = $commentConfig_decode['btnStatus'] ?? 0;
            $commentPluginUnikey = $commentConfig_decode['pluginUnikey'] ?? null;
            $commentBtnName = $commentConfig_decode['btnName'] ?? null;
            // 评论多语言入库
            if ($commentConfig_decode['btnName']) {
                $btnNameArr = $commentConfig_decode['btnName'];
                $inputArr = [];
                foreach ($btnNameArr as $v) {
                    $item = [];
                    $tagArr = FresnsLanguagesService::conversionLangTag($v['langTag']);
                    // $tagArr = explode('-',$v['lang_code']);
                    // $areaCode = array_pop($tagArr);
                    // $langCode = str_replace("-$areaCode",'',$v['lang_code']);
                    $item['lang_code'] = $tagArr['lang_code'];
                    $item['area_code'] = $tagArr['area_code'];
                    $item['lang_tag'] = $v['langTag'];
                    $item['lang_content'] = $v['name'];
                    $item['table_field'] = 'comment_btn_name';
                    $item['table_id'] = $postId;
                    $item['table_name'] = AmConfig::CFG_TABLE;
                    // $item['alias_key'] = $v['nickname'];
                    $count = FresnsLanguages::where($item)->count();
                    //  dump($count);
                    if ($count == 0) {
                        FresnsLanguagesModel::insert($item);
                    }
                }
                // FresnsLanguagesModel::insert($inputArr);
            }
        }

        // 阅读权限配置
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
            // 权限多语言入库
            if ($allosJsonDecode['btnName']) {
                $btnNameArr = $allosJsonDecode['btnName'];
                $inputArr = [];
                foreach ($btnNameArr as $v) {
                    $item = [];
                    $tagArr = FresnsLanguagesService::conversionLangTag($v['langTag']);
                    // $tagArr = explode('-',$v['lang_code']);
                    // $areaCode = array_pop($tagArr);
                    // $langCode = str_replace("-$areaCode",'',$v['lang_code']);
                    $item['lang_code'] = $tagArr['lang_code'];
                    $item['area_code'] = $tagArr['area_code'];
                    $item['lang_tag'] = $v['langTag'];
                    $item['lang_content'] = $v['name'];
                    $item['table_field'] = 'allow_btn_name';
                    $item['table_id'] = $postId;
                    $item['table_name'] = AmConfig::CFG_TABLE;
                    // $item['alias_key'] = $v['nickname'];
                    $count = FresnsLanguages::where($item)->count();
                    //  dump($count);
                    if ($count == 0) {
                        FresnsLanguagesModel::insert($item);
                    }
                }
                // FresnsLanguagesModel::insert($inputArr);
            }
            // postAllow数据
            if ($allosJsonDecode['permission']) {
                $permission = $allosJsonDecode['permission'];
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

        // 位置信息配置
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

        // 扩展信息
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
        // 是否存在替换关键字
        $content = $draftPost['content'];
        $content = $this->stopWords($content);
        // 去除html标签
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

    // 副表（编辑）
    public function postAppendUpdate($postId, $draftId)
    {
        $draftPost = FresnsPostLogs::find($draftId);
        // 编辑器配置
        $pluginEdit = $draftPost['is_plugin_edit'];
        $pluginUnikey = $draftPost['plugin_unikey'];
        // 特定成员配置
        $member_list_json = $draftPost['member_list_json'];
        $member_list_name = [];
        $member_list_status = 0;
        if ($member_list_json) {
            // 先删除旧数据（清空多语言表）
            FresnsLanguages::where('table_name', AmConfig::CFG_TABLE)->where('table_id', $postId)->where('table_field',
                'member_list_name')->delete();
            $member_list_decode = json_decode($member_list_json, true);
            $member_list_status = $member_list_decode['memberListStatus'] ?? 0;
            $member_list_plugin_unikey = $member_list_decode['pluginUnikey'] ?? [];
            $member_list_name = $member_list_decode['memberListName'] ?? [];
            // 特定成员多语言
            if ($member_list_name) {
                // $memberListNameArr = $member_list_decode['memberListName'];
                $inputArr = [];
                foreach ($member_list_name as $v) {
                    $item = [];
                    $tagArr = FresnsLanguagesService::conversionLangTag($v['langTag']);
                    // $tagArr = explode('-',$v['lang_code']);
                    // $areaCode = array_pop($tagArr);
                    // $langCode = str_replace("-$areaCode",'',$v['lang_code']);
                    $item['lang_code'] = $tagArr['lang_code'];
                    $item['area_code'] = $tagArr['area_code'];
                    $item['lang_tag'] = $v['langTag'];
                    $item['lang_content'] = $v['name'];
                    $item['table_field'] = 'member_list_name';
                    $item['table_id'] = $postId;
                    $item['table_name'] = AmConfig::CFG_TABLE;
                    $count = FresnsLanguages::where($item)->count();
                    //  dump($count);
                    if ($count == 0) {
                        FresnsLanguagesModel::insert($item);
                    }
                    // $item['alias_key'] = $v['nickname'];
                    $inputArr[] = $item;
                }
                // FresnsLanguagesModel::insert($inputArr);
            }
        }

        // 评论设置
        $commentConfig = $draftPost['comment_set_json'];
        $commentBtnStatus = 0;
        $commentPluginUnikey = null;
        $commentBtnName = null;
        if ($commentConfig) {
            $commentConfig_decode = json_decode($commentConfig, true);
            $commentBtnStatus = $commentConfig_decode['btnStatus'] ?? 0;
            $commentPluginUnikey = $commentConfig_decode['pluginUnikey'] ?? null;
            $commentBtnName = $commentConfig_decode['btnName'] ?? null;
            // 评论多语言入库
            if ($commentConfig_decode['btnName']) {
                // 先删除旧数据（清空多语言表）
                FresnsLanguages::where('table_name', AmConfig::CFG_TABLE)->where('table_id',
                    $postId)->where('table_field', 'comment_btn_name')->delete();
                $btnNameArr = $commentConfig_decode['btnName'];
                $inputArr = [];
                foreach ($btnNameArr as $v) {
                    $item = [];
                    $tagArr = FresnsLanguagesService::conversionLangTag($v['langTag']);
                    // $tagArr = explode('-',$v['lang_code']);
                    // $areaCode = array_pop($tagArr);
                    // $langCode = str_replace("-$areaCode",'',$v['lang_code']);
                    $item['lang_code'] = $tagArr['lang_code'];
                    $item['area_code'] = $tagArr['area_code'];
                    $item['lang_tag'] = $v['langTag'];
                    $item['lang_content'] = $v['name'];
                    $item['table_field'] = 'comment_btn_name';
                    $item['table_id'] = $postId;
                    $item['table_name'] = AmConfig::CFG_TABLE;
                    $count = FresnsLanguages::where($item)->count();
                    //  dump($count);
                    if ($count == 0) {
                        FresnsLanguagesModel::insert($item);
                    }
                    // $item['alias_key'] = $v['nickname'];
                    //  $inputArr[] = $item;
                }
            }
        }

        // 阅读权限配置
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
            // 权限多语言入库
            if ($allosJsonDecode['btnName']) {
                $btnNameArr = $allosJsonDecode['btnName'];
                $inputArr = [];
                foreach ($btnNameArr as $v) {
                    $item = [];
                    $tagArr = FresnsLanguagesService::conversionLangTag($v['langTag']);
                    // $tagArr = explode('-',$v['lang_code']);
                    // $areaCode = array_pop($tagArr);
                    // $langCode = str_replace("-$areaCode",'',$v['lang_code']);
                    $item['lang_code'] = $tagArr['lang_code'];
                    $item['area_code'] = $tagArr['area_code'];
                    $item['lang_tag'] = $v['langTag'];
                    $item['lang_content'] = $v['name'];
                    $item['table_field'] = 'allow_btn_name';
                    $item['table_id'] = $postId;
                    $item['table_name'] = AmConfig::CFG_TABLE;
                    $count = FresnsLanguages::where($item)->count();
                    //  dump($count);
                    if ($count == 0) {
                        FresnsLanguagesModel::insert($item);
                    }
                    // $item['alias_key'] = $v['nickname'];
                    $inputArr[] = $item;
                }
                // FresnsLanguagesModel::insert($inputArr);
            }
            // postAllow数据
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
                    // 先清空（再新增）
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

        // 位置信息配置
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

        // 扩展信息
        $extendsJson = json_decode($draftPost['extends_json'], true);
        if ($extendsJson) {
            // 先清空
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
        // $member_list_status = empty($member_list_status) ? 1 : 0;
        // 是否存在替换关键字
        $content = $draftPost['content'];
        $content = $this->stopWords($content);
        // 去除html标签
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

    // 入库后执行相应操作
    public function afterStoreToDb($postId, $draftId)
    {
        // 调用插件订阅命令字
        $cmd = FresnsSubPluginConfig::PLG_CMD_SUB_ADD_TABLE;
        $input = [
            'tableName' => FresnsPostsConfig::CFG_TABLE,
            'insertId' => $postId,
        ];
        LogService::info('table_input', $input);
        // dd($input);
        PluginRpcHelper::call(FresnsSubPlugin::class, $cmd, $input);
        $draftPost = FresnsPostLogs::find($draftId);
        $content = $this->stopWords($draftPost['content']);
        // 草稿更新为已发布
        FresnsPostLogs::where('id', $draftId)->update(['status' => 3, 'post_id' => $postId, 'content' => $content]);
        // 小组帖子是数+1
        FresnsGroups::where('id', $draftPost['group_id'])->increment('post_count');
        $this->sendAtMessages($postId, $draftId);
        // $this->fillDbInfo($draftId);
        // 	我的 member_stats > post_publish_count
        $this->memberStats($draftId);
        // 解析话题
        $this->analisisHashtag($draftId, 1);
        //   域名链接表
        $this->domainStore($postId, $draftId);
        //  配置表键值 post_counts
        return true;
    }

    // 入库后执行相应操作(编辑)
    public function afterUpdateToDb($postId, $draftId)
    {
        // 调用插件订阅命令字
        $cmd = FresnsSubPluginConfig::PLG_CMD_SUB_ADD_TABLE;
        $input = [
            'tableName' => FresnsPostsConfig::CFG_TABLE,
            'insertId' => $postId,
        ];
        LogService::info('table_input', $input);
        // dd($input);
        PluginRpcHelper::call(FresnsSubPlugin::class, $cmd, $input);
        $draftPost = FresnsPostLogs::find($draftId);
        $content = $this->stopWords($draftPost['content']);
        // 草稿更新为已发布
        FresnsPostLogs::where('id', $draftId)->update(['status' => 3, 'post_id' => $postId, 'content' => $content]);
        // 小组帖子是数+1
        FresnsGroups::where('id', $draftPost['group_id'])->increment('post_count');
        // post_appends > edit_count	字段数值 +1
        FresnsPostAppends::where('post_id', $postId)->increment('edit_count');
        $this->sendAtMessages($postId, $draftId, 2);
        // $this->fillDbInfo($draftId);
        // 	我的 member_stats > post_publish_count
        // $this->memberStats($draftId);
        // 解析话题
        $this->analisisHashtag($draftId, 2);
        $this->domainStore($postId, $draftId, 2);
        //  配置表键值 post_counts
        return true;
    }

    // 不能艾特自己，艾特别人则给对方产生一条通知消息。
    // 调用 MessageService 处理
    public function sendAtMessages($postId, $draftId, $updateType = 2)
    {
        $draftPost = FresnsPostLogs::find($draftId);
        $postInfo = FresnsPosts::find($postId);
        // if ($updateType == 2) {
        //     DB::table('mentions')->where('linked_type', 1)->where('linked_id', $postId)->delete();
        // }
        preg_match_all("/@.*?\s/", $draftPost['content'], $atMatches);
        // 存在发送消息
        // dd($atMatches);
        if ($atMatches[0]) {
            foreach ($atMatches[0] as $s) {
                // dd($s);
                // 查询接受用户id
                $name = trim(ltrim($s, '@'));
                // dd($name);
                $memberInfo = FresnsMembers::where('name', $name)->first();
                // dd($memberInfo);
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
                    // 艾特记录表
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

    // 评论则判断父级是否为自己，不是自己则为对方产生一条通知。一级评论给帖子作者（帖子作者不是自己）产生通知。
    // 调用 MessageService 处理
    public function sendCommentMessages($postInfo)
    {
    }

    // 发表成功后，帖子或者评论的主键 ID 产生，然后把 ID 填到 files > table_id 字段里，补齐信息。
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

    // 我的 member_stats > post_publish_count
    // 配置表键值 post_counts
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

    // 解析话题(入库话题表)

    /**
     * $params
     * updateType 1 新增 2 编辑.
     */
    public function analisisHashtag($draftId, $updateType = 1)
    {
        $draftPost = FresnsPostLogs::find($draftId);
        // $draftPost['content'] = "这里是1帖子@成员3 的文本。<a onclick='return false;' href='http://www.baidu.com'>点击跳转百度</a>#话题 5##话题2#@成员2";
        if ($updateType == 2) {
            // 话题 post_count
            $hashtagIdArr = FresnsHashtagLinkeds::where('linked_type', 1)->where('linked_id', $draftPost['post_id'])->pluck('hashtag_id')->toArray();
            FresnsHashtags::whereIn('id', $hashtagIdArr)->decrement('post_count');
            // 删除话题关联
            FresnsHashtagLinkeds::where('linked_type', 1)->where('linked_id', $draftPost['post_id'])->delete();
            // DB::table(FresnsHashtagLinkedsConfig::CFG_TABLE)->where('linked_type', 1)->where('linked_id',$draftPost['post_id'])->delete();
        }
        // 当前后台话题的显示模式
        $hashtagShow = ApiConfigHelper::getConfigByItemKey(AmConfig::HASHTAG_SHOW) ?? 2;
        if ($hashtagShow == 1) {
            preg_match_all("/#.*?\s/", $draftPost['content'], $singlePoundMatches);
        } else {
            preg_match_all('/#.*?#/', $draftPost['content'], $singlePoundMatches);
        }
        // dd($singlePoundMatches);
        if ($singlePoundMatches[0]) {
            foreach ($singlePoundMatches[0] as $s) {
                // 将话题的#号去掉
                $s = trim(str_replace('#', '', $s));
                // 是否存在话题
                $hashInfo = FresnsHashtags::where('name', $s)->first();
                if ($hashInfo) {
                    // 话题表post_count +1
                    FresnsHashtags::where('id', $hashInfo['id'])->increment('post_count');
                    // 建立关联关系
                    $res = DB::table(FresnsHashtagLinkedsConfig::CFG_TABLE)->insert([
                        'linked_type' => 1,
                        'linked_id' => $draftPost['post_id'],
                        'hashtag_id' => $hashInfo['id'],
                    ]);
                } else {
                    // 新建话题和话题关联
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
                    // 建立关联关系
                    $res = DB::table(FresnsHashtagLinkedsConfig::CFG_TABLE)->insert([
                        'linked_type' => 1,
                        'linked_id' => $draftPost['post_id'],
                        'hashtag_id' => $hashtagId,
                    ]);
                    DB::table('configs')->where('item_key', AmConfig::HASHTAG_COUNTS)->increment('item_value');
                }
            }
        }
        // dd($res);
        return true;
        // dd($singlePoundMatches);
    }

    // 解析截断内容信息
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
                // 获取帖子的上线字数
                // $commentEditorWordCount = ApiConfigHelper::getConfigByItemKey(AmConfig::COMMENT_EDITOR_WORD_COUNT) ?? 1000;
                // 获取帖子的摘要字数
                $commentEditorbRIEFCount = ApiConfigHelper::getConfigByItemKey(AmConfig::COMMENT_EDITOR_BRIEF_COUNT) ?? 280;
                if ($proportionCount > $commentEditorbRIEFCount) {
                    $contentInfo = $this->truncatedContentInfo($content, $commentEditorbRIEFCount);
                    $content = $contentInfo['truncated_content'];
                } else {
                    $contentInfo = $this->truncatedContentInfo($content, $proportionCount);
                    $content = $contentInfo['truncated_content'];
                }
            }
        }

        // 是否存在替换关键字
        $content = $this->stopWords($content);
        // 去除html标签
        $content = strip_tags($content);

        return $content;
    }

    // 域名链接表

    /**
     * $params
     * updateType 1 新增 2 编辑.
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
                // 一级域名
                $firstDomain = $this->top_domain(trim($h));
                // 二级域名
                $domain = $this->regular_domain(trim($h));
                // dump($firstDomain);
                // dump($domain);
                // preg_match('/(.*\.)?\w+\.\w+$/', $domain, $secDomain);
                // dd($secDomain);
                // 域名表是否存在
                // if($secDomain){
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
                // }
            }
        }

        return true;
    }

    // 编辑内容的草稿，根据 status 参数去执行“清空”、“删除”、“替换”旧内容。
    public function oldContentByStatus($draftPost)
    {
    }

    // “艾特”、“话题”、“链接” content全文中三者的位置信息
    // 内容超过设置的字数时，需要摘要存储，如果摘要最后内容是“艾特”、“话题”、“链接”三种信息，要留全，不能截断，保全时可以限定字数。
    public function truncatedContentInfo($content, $wordCount = 280)
    {
        // dump($content);
        // dump($wordCount);
        // 当前后台话题的显示模式
        $hashtagShow = ApiConfigHelper::getConfigByItemKey(AmConfig::HASHTAG_SHOW) ?? 2;
        // dd($hashtagShow);
        // $content = "这里是1帖子@刘liuliu 的文本。https://tangjie.me #话题1 12345#话题 2#";
        // 在 $content 中匹配 位置信息,  这里的正则放到配置文件中
        if ($hashtagShow == 1) {
            preg_match("/#.*?\s/", $content, $singlePoundMatches, PREG_OFFSET_CAPTURE);
        } else {
            preg_match('/#.*?#/', $content, $singlePoundMatches, PREG_OFFSET_CAPTURE);
        }
        /**
         * preg_match("/<a .*?>.*?<\/a>/",$content,$hrefMatches,PREG_OFFSET_CAPTURE);.
         *  */
        preg_match("/http[s]{0,1}:\/\/.*?\s/", $content, $hrefMatches, PREG_OFFSET_CAPTURE);
        // dd($singlePoundMatches);
        // preg_match("/<a href=.*?}></a>/", $content, $hrefMatches,PREG_OFFSET_CAPTURE);
        preg_match("/@.*?\s/", $content, $atMatches, PREG_OFFSET_CAPTURE);
        $truncatedPos = ceil($wordCount);
        $findTruncatedPos = false;
        // 获取匹配到的数据对应的字符数（匹配到的是字节）
        $contentArr = self::getString($content);
        $charCounts = self::getChar($contentArr, $truncatedPos);
        // 判断这个wordCount落在的区间位置， 如果有命中，则找到对应的截断位置，并执行截断
        // https://www.php.net/manual/en/function.preg-match.php
        foreach ($singlePoundMatches as $currMatch) {
            $matchStr = $currMatch[0];
            $matchStrStartPosition = $currMatch[1];
            $matchStrEndPosition = $currMatch[1] + strlen($matchStr);
            // 命中
            if ($matchStrStartPosition <= $charCounts && $matchStrEndPosition >= $charCounts) {
                $findTruncatedPos = true;
                $truncatedPos = $matchStrEndPosition;
            }
        }
        // [1,4] [6,9] [15,33] [41,45], [50,77]
        // [1,4] [6,9] [15,33] [41,45], [65,69]
        //  adjfaljdfsdfidksieijsdfasdf@cccc

        // // 如果未发现则继续匹配
        // if(!$findTruncatedPos){
        //     foreach ($singlePoundMatches as $currMatch) {
        //         $matchStr = $currMatch[0];
        //         $matchStrStartPosition = $currMatch[1];
        //         $matchStrEndPosition = $currMatch[1] + strlen($matchStr);
        //         // 命中
        //         if ($matchStrStartPosition <= $wordCount && $matchStrEndPosition >= $wordCount) {
        //             $findTruncatedPos = true;
        //             $truncatedPos = $matchStrEndPosition;
        //         }
        //     }
        // }

        if (! $findTruncatedPos) {
            foreach ($hrefMatches as $currMatch) {
                $matchStr = $currMatch[0];
                $matchStrStartPosition = $currMatch[1];
                $matchStrEndPosition = $currMatch[1] + strlen($matchStr);
                // 命中
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
                // 命中
                if ($matchStrStartPosition <= $charCounts && $matchStrEndPosition >= $charCounts) {
                    $findTruncatedPos = true;
                    $truncatedPos = $matchStrEndPosition;
                }
            }
        }
        // 执行操作
        $info = [];
        $info['find_truncated_pos'] = $findTruncatedPos;
        $info['truncated_pos'] = $truncatedPos;  // 截断位置
        if ($findTruncatedPos) {
            // 字节数转字数
            $chars = self::getChars($content);
            $strLen = self::getStrLen($chars, $truncatedPos);
        } else {
            $strLen = $truncatedPos;
        }

        $info['truncated_content'] = Str::substr($content, 0, $strLen); // 最终内容
        // $info['double_pound_arr'] = $doublePoundMatches;
        $info['single_pound_arr'] = $singlePoundMatches;
        $info['link_pound_arr'] = $hrefMatches;
        $info['at_arr'] = $atMatches;
        // dd($info);
        return $info;
    }

    // 执行审核操作
    public function parseToReview($draftId)
    {
        // 帖子
        FresnsPostLogs::where('id', $draftId)->update(['status' => 2, 'submit_at' => date('Y-m-d H:i:s')]);

        return true;
    }

    public static function getString($content)
    {
        $utf8posCharPosMap = [];
        $len = mb_strlen($content);
        // dump($content);
        // dump($len);
        $charPos = 0;
        $str = 0;
        for ($i = 0; $i < $len; $i++) {
            $utf8Pos = $i;

            $utf8PosDesc = 'utf_'.$utf8Pos;
            $charPosDesc = 'char_'.$charPos;
            // $utf8posCharPosMap[$utf8PosDesc] = $charPosDesc;
            $utf8posCharPosMap[$utf8PosDesc] = $charPosDesc;
            // 匹配字符是否为中文
            // $char = $content[$i];
            $char = mb_substr($content, $i, 1);
            // dump($i);
            // dump($char);
            if (preg_match("/^[\x7f-\xff]+$/", $char)) {
                // dump($i);
                $charPos = $charPos + 3;
            // dump($i);
                // dump($char);
                // dd(mb_detect_encoding($char));
                // dump($charPos);
            } else {
                // dd($i);
                // dump($char);
                // dd((iconv('utf-8','UTF-8',$char));
                // dump($charPos);
                $charPos = $charPos + 1;
                // dump($charPos);
            }
        }
        // dump($content);
        // dd($utf8posCharPosMap);
        return $utf8posCharPosMap;
    }

    // 获取字数对应的字节数
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

    // 字节对应的字符数
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
            // $utf8posCharPosMap[$utf8PosDesc] = $charPosDesc;
            $utf8posCharPosMap[$charPosDesc] = $utf8PosDesc;
            // 匹配字符是否为中文
            $char = mb_substr($content, $i, 1);
            if (preg_match("/^[\x7f-\xff]+$/", $char)) {
                $charPos = $charPos + 3;
            } else {
                // dd($char);
                $charPos = $charPos + 1;
            }
        }

        return $utf8posCharPosMap;
    }

    // 获取字节数对应的字数
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
        //dd($domain);
        $iana_root = [
            'ac',
            'ad',
            'ae',
            'aero',
            'af',
            'ag',
            'ai',
            'al',
            'am',
            'an',
            'ao',
            'aq',
            'ar',
            'arpa',
            'as',
            'asia',
            'at',
            'au',
            'aw',
            'ax',
            'az',
            'ba',
            'bb',
            'bd',
            'be',
            'bf',
            'bg',
            'bh',
            'bi',
            'biz',
            'bj',
            'bl',
            'bm',
            'bn',
            'bo',
            'bq',
            'br',
            'bs',
            'bt',
            'bv',
            'bw',
            'by',
            'bz',
            'ca',
            'cat',
            'cc',
            'cd',
            'cf',
            'cg',
            'ch',
            'ci',
            'ck',
            'cl',
            'cm',
            'cn',
            'co',
            'com',
            'coop',
            'cr',
            'cu',
            'cv',
            'cw',
            'cx',
            'cy',
            'cz',
            'de',
            'dj',
            'dk',
            'dm',
            'do',
            'dz',
            'ec',
            'edu',
            'ee',
            'eg',
            'eh',
            'er',
            'es',
            'et',
            'eu',
            'fi',
            'fj',
            'fk',
            'fm',
            'fo',
            'fr',
            'ga',
            'gb',
            'gd',
            'ge',
            'gf',
            'gg',
            'gh',
            'gi',
            'gl',
            'gm',
            'gn',
            'gov',
            'gp',
            'gq',
            'gr',
            'gs',
            'gt',
            'gu',
            'gw',
            'gy',
            'hk',
            'hm',
            'hn',
            'hr',
            'ht',
            'hu',
            'id',
            'ie',
            'il',
            'im',
            'in',
            'info',
            'int',
            'io',
            'iq',
            'ir',
            'is',
            'it',
            'je',
            'jm',
            'jo',
            'jobs',
            'jp',
            'ke',
            'kg',
            'kh',
            'ki',
            'km',
            'kn',
            'kp',
            'kr',
            'kw',
            'ky',
            'kz',
            'la',
            'lb',
            'lc',
            'li',
            'lk',
            'lr',
            'ls',
            'lt',
            'lu',
            'lv',
            'ly',
            'ma',
            'mc',
            'md',
            'me',
            'mf',
            'mg',
            'mh',
            'mil',
            'mk',
            'ml',
            'mm',
            'mn',
            'mo',
            'mobi',
            'mp',
            'mq',
            'mr',
            'ms',
            'mt',
            'mu',
            'museum',
            'mv',
            'mw',
            'mx',
            'my',
            'mz',
            'na',
            'name',
            'nc',
            'ne',
            'net',
            'nf',
            'ng',
            'ni',
            'nl',
            'no',
            'np',
            'nr',
            'nu',
            'nz',
            'om',
            'org',
            'pa',
            'pe',
            'pf',
            'pg',
            'ph',
            'pk',
            'pl',
            'pm',
            'pn',
            'pr',
            'pro',
            'ps',
            'pt',
            'pw',
            'py',
            'qa',
            're',
            'ro',
            'rs',
            'ru',
            'rw',
            'sa',
            'sb',
            'sc',
            'sd',
            'se',
            'sg',
            'sh',
            'si',
            'sj',
            'sk',
            'sl',
            'sm',
            'sn',
            'so',
            'sr',
            'ss',
            'st',
            'su',
            'sv',
            'sx',
            'sy',
            'sz',
            'tc',
            'td',
            'tel',
            'tf',
            'tg',
            'th',
            'tj',
            'tk',
            'tl',
            'tm',
            'tn',
            'to',
            'tp',
            'tr',
            'travel',
            'tt',
            'tv',
            'tw',
            'tz',
            'ua',
            'ug',
            'uk',
            'um',
            'us',
            'uy',
            'uz',
            'va',
            'vc',
            've',
            'vg',
            'vi',
            'vn',
            'vu',
            'wf',
            'ws',
            'xxx',
            'ye',
            'yt',
            'za',
            'zm',
            'zw',
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

    // 过滤词规则
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
