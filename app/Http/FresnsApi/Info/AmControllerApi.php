<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\Info;

use App\Helpers\DateHelper;
use App\Http\Center\Common\GlobalService;
use App\Http\Center\Common\ErrorCodeService;
use App\Http\Center\Common\ValidateService;
use App\Http\Center\Helper\PluginRpcHelper;
use App\Http\FresnsApi\Base\FresnsBaseApiController;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsApi\Helpers\ApiFileHelper;
use App\Http\FresnsApi\Helpers\ApiLanguageHelper;
use App\Http\FresnsCmd\FresnsCrontablPlugin;
use App\Http\FresnsCmd\FresnsCrontabPluginConfig;
use App\Http\FresnsCmd\FresnsPlugin;
use App\Http\FresnsCmd\FresnsPluginConfig;
use App\Http\FresnsDb\FresnsComments\FresnsComments;
use App\Http\FresnsDb\FresnsComments\FresnsCommentsConfig;
use App\Http\FresnsDb\FresnsDialogMessages\FresnsDialogMessages;
use App\Http\FresnsDb\FresnsDialogs\FresnsDialogs;
use App\Http\FresnsDb\FresnsEmojis\FresnsEmojisConfig;
use App\Http\FresnsDb\FresnsEmojis\FresnsEmojisService;
use App\Http\FresnsDb\FresnsExtends\FresnsExtends;
use App\Http\FresnsDb\FresnsExtends\FresnsExtendsConfig;
use App\Http\FresnsDb\FresnsFileLogs\FresnsFileLogs;
use App\Http\FresnsDb\FresnsFiles\FresnsFiles;
use App\Http\FresnsDb\FresnsGroups\FresnsGroups;
use App\Http\FresnsDb\FresnsGroups\FresnsGroupsConfig;
use App\Http\FresnsDb\FresnsHashtags\FresnsHashtags;
use App\Http\FresnsDb\FresnsLanguages\FresnsLanguages;
use App\Http\FresnsDb\FresnsMemberFollows\FresnsMemberFollows;
use App\Http\FresnsDb\FresnsMemberRoleRels\FresnsMemberRoleRels;
use App\Http\FresnsDb\FresnsMembers\FresnsMembers;
use App\Http\FresnsDb\FresnsNotifies\FresnsNotifies;
use App\Http\FresnsDb\FresnsPluginUsages\FresnsPluginUsages;
use App\Http\FresnsDb\FresnsPluginUsages\FresnsPluginUsagesService;
use App\Http\FresnsDb\FresnsPosts\FresnsPosts;
use App\Http\FresnsDb\FresnsPosts\FresnsPostsConfig;
use App\Http\FresnsDb\FresnsStopWords\FresnsStopWordsService;
use App\Http\FresnsDb\FresnsUsers\FresnsUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AmControllerApi extends FresnsBaseApiController
{
    public function __construct()
    {
        $this->service = new AmService();
        parent::__construct();
    }

    //系统配置信息
    public function infoConfigs(Request $request)
    {
        $itemTag = $request->input('itemTag');
        $itemKey = $request->input('itemKey');
        $data = [];
        if (empty($itemTag) && empty($itemKey)) {
            $data = ApiConfigHelper::getConfigsListsApi();
        } else {
            if (! empty($itemTag)) {
                $itemTagArr = explode(',', $itemTag);
                foreach ($itemTagArr as $v) {
                    $data = array_merge($data, ApiConfigHelper::getConfigByKeyApi($v));
                }
            }
            if (! empty($itemKey)) {
                $itemKeyArr = explode(',', $itemKey);
                foreach ($itemKeyArr as $v) {
                    $data = array_merge($data, ApiConfigHelper::getConfigByItemKeyApi($v));
                }
            }
        }

        $item = [];
        $item['list'] = $data;
        $pagination['total'] = count($data);
        $pagination['current'] = 1;
        $pagination['pageSize'] = count($data);
        $pagination['lastPage'] = 1;
        $item['pagination'] = $pagination;

        $this->success($item);
    }

    //表情
    public function infoEmojis(Request $request)
    {
        $pageSize = $request->input('pageSize', 10);
        $currentPage = $request->input('page', 1);
        $request->offsetSet('currentPage', $currentPage);
        $request->offsetSet('pageSize', $pageSize);
        $request->offsetSet('type', FresnsEmojisConfig::TYPE_GROUP);
        $request->offsetSet('is_enable', 1);
        $FresnsEmojisService = new FresnsEmojisService();

        $FresnsEmojisService->setResource(FresnsInfoEmojisResource::class);
        $data = $FresnsEmojisService->searchData();

        $this->success($data);
    }

    //敏感词
    public function infoStopWords(Request $request)
    {
        $currentPage = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 100);
        $request->offsetSet('currentPage', $currentPage);
        $request->offsetSet('pageSize', $pageSize);

        $FresnsStopWordsService = new FresnsStopWordsService();

        $FresnsStopWordsService->setResource(FresnsInfoStopWordsResource::class);
        $data = $FresnsStopWordsService->searchData();

        $this->success($data);
    }

    //上传交互日志
    public function infoUploadLog(Request $request)
    {
        // 校验参数
        $rule = [
            'objectName' => 'required',
            'objectAction' => 'required',
            'objectResult' => 'required|numeric',
            // 'objectOrderId'    => 'numeric',
            // 'deviceInfo'    => 'required|json',
            'moreJson' => 'json',
        ];
        ValidateService::validateRule($request, $rule);

        $langTag = ApiLanguageHelper::getLangTagByHeader();

        $cmd = FresnsPluginConfig::PLG_CMD_UPLOAD_SESSION_LOG;
        $input['platform'] = $request->header('platform');
        $input['version'] = $request->header('version');
        $input['versionInt'] = $request->header('versionInt');
        $input['objectName'] = $request->input('objectName');
        $input['objectAction'] = $request->input('objectAction');
        $input['objectResult'] = $request->input('objectResult');
        $input['objectType'] = $request->input('objectType');
        $input['langTag'] = $langTag;
        $input['objectOrderId'] = $request->input('objectOrderId');
        $input['deviceInfo'] = $request->header('deviceInfo');
        $input['uid'] = $this->uid;
        $input['mid'] = $this->mid;

        $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);

        if (PluginRpcHelper::isErrorPluginResp($resp)) {
            $this->errorCheckInfo($resp);
        }

        $this->success();
    }

    /**
     * 输入提示查询
     * 1.成员名 members > name
     * 2.小组名 groups > name
     * 3.话题名 hashtags > name
     * 4.帖子标题 posts > title
     * 5.扩展内容标题 extends > title
     * 成员名,话题,帖子,不涉及多语言表.
     */
    public function infoInputtips(Request $request)
    {
        // 校验参数
        $rule = [
            'queryType' => 'required|numeric|in:1,2,3,4,5',
            'queryKey' => 'required',
        ];
        ValidateService::validateRule($request, $rule);

        $queryType = $request->input('queryType');
        $queryKey = $request->input('queryKey');
        $langTag = ApiLanguageHelper::getLangTagByHeader();
        $mid = GlobalService::getGlobalKey('member_id');
        $data = [];

        $followIdArr = [];
        if ($mid && $queryType != 5) {
            $followIdArr = FresnsMemberFollows::where('member_id', $mid)->where('follow_type',
                $queryType)->pluck('follow_id')->toArray();
        }

        switch ($queryType) {

            case 1:
                $idArr = FresnsMembers::where('name', 'LIKE', "%$queryKey%")->orWhere('nickname', 'LIKE',
                    "%$queryKey%")->pluck('id')->toArray();
                $idArr = AmService::getMemberFollows($queryType, $idArr, $mid);
                $memberArr = FresnsMembers::whereIn('id', $idArr)->where('is_enable', 1)->get()->toArray();

                foreach ($memberArr as $v) {
                    $item = [];
                    $item['id'] = $v['uuid'];
                    $item['name'] = $v['name'];
                    $item['nickname'] = $v['nickname'];
                    $followStatus = 0;
                    if (in_array($v['id'], $followIdArr)) {
                        $followStatus = 1;
                    }
                    $item['followStatus'] = $followStatus;
                    if (empty($v['avatar_file_url']) && empty($v['avatar_file_id'])) {
                        $defaultAvatar = ApiConfigHelper::getConfigByItemKey('default_avatar');
                        $memberAvatar = ApiFileHelper::getImageSignUrl($defaultAvatar);
                    } else {
                        $memberAvatar = ApiFileHelper::getImageSignUrlByFileIdUrl($v['avatar_file_id'], $v['avatar_file_url']);
                    }
                    $item['image'] = $memberAvatar;
                    $item['title'] = '';
                    $item['titleColor'] = '';
                    $item['descPrimary'] = '';
                    $item['descPrimaryColor'] = '';
                    $item['descSecondary'] = '';
                    $item['descSecondaryColor'] = '';
                    $data[] = $item;
                }
                break;
            case 2:
                $langIdArr = FresnsLanguages::where('table_name', FresnsGroupsConfig::CFG_TABLE)->where('table_field',
                    'name')->where('lang_content', 'LIKE', "%$queryKey%")->where('lang_tag',
                    $langTag)->pluck('table_id')->toArray();
                $idArr = AmService::getMemberFollows($queryType, $langIdArr, $mid);
                $groupsArr = FresnsGroups::whereIn('id', $idArr)->where('is_enable', 1)->get()->toArray();
                $lenguagesMap = FresnsLanguages::where('table_name',
                    FresnsGroupsConfig::CFG_TABLE)->where('table_field', 'name')->where('lang_tag',
                    $langTag)->whereIn('table_id', $idArr)->pluck('lang_content', 'table_id')->toArray();

                foreach ($groupsArr as $v) {
                    $item = [];
                    $item['id'] = $v['uuid'];
                    $item['name'] = $lenguagesMap[$v['id']] ?? '';
                    $item['nickname'] = '';
                    $followStatus = 0;
                    if (in_array($v['id'], $followIdArr)) {
                        $followStatus = 1;
                    }
                    $item['followStatus'] = $followStatus;
                    $item['image'] = ApiFileHelper::getImageSignUrlByFileIdUrl($v['cover_file_id'],
                        $v['cover_file_url']);
                    $item['title'] = '';
                    $item['titleColor'] = '';
                    $item['descPrimary'] = '';
                    $item['descPrimaryColor'] = '';
                    $item['descSecondary'] = '';
                    $item['descSecondaryColor'] = '';
                    $data[] = $item;
                }
                break;
            case 3:
                $idArr = FresnsHashtags::where('name', 'LIKE', "%$queryKey%")->pluck('id')->toArray();
                $idArr = AmService::getMemberFollows($queryType, $idArr, $mid);
                $hashtagsArr = FresnsHashtags::whereIn('id', $idArr)->where('is_enable', 1)->get()->toArray();
                foreach ($hashtagsArr as $v) {
                    $item = [];
                    $item['id'] = $v['slug'];
                    $item['name'] = $v['name'];
                    $item['nickname'] = '';
                    $followStatus = 0;
                    if (in_array($v['id'], $followIdArr)) {
                        $followStatus = 1;
                    }
                    $item['followStatus'] = $followStatus;
                    $item['image'] = ApiFileHelper::getImageSignUrlByFileIdUrl($v['cover_file_id'],
                        $v['cover_file_url']);
                    $item['title'] = '';
                    $item['titleColor'] = '';
                    $item['descPrimary'] = '';
                    $item['descPrimaryColor'] = '';
                    $item['descSecondary'] = '';
                    $item['descSecondaryColor'] = '';
                    $data[] = $item;
                }
                break;
            case 4:
                $idArr = FresnsPosts::where('title', 'LIKE', "%$queryKey%")->pluck('id')->toArray();
                $idArr = AmService::getMemberFollows($queryType, $idArr, $mid);
                $hashtagsArr = FresnsPosts::whereIn('id', $idArr)->where('is_enable', 1)->get()->toArray();
                foreach ($hashtagsArr as $v) {
                    $item = [];
                    $item['id'] = $v['uuid'];
                    $item['name'] = $v['title'];
                    $item['nickname'] = '';
                    $followStatus = 0;
                    if (in_array($v['id'], $followIdArr)) {
                        $followStatus = 1;
                    }
                    $item['followStatus'] = $followStatus;
                    $item['image'] = '';
                    $item['title'] = '';
                    $item['titleColor'] = '';
                    $item['descPrimary'] = '';
                    $item['descPrimaryColor'] = '';
                    $item['descSecondary'] = '';
                    $item['descSecondaryColor'] = '';
                    $data[] = $item;
                }
                break;
            case 5:
                $langIdArr = FresnsLanguages::where('table_name', FresnsExtendsConfig::CFG_TABLE)->where('table_field',
                    'title')->where('lang_content', 'LIKE', "%$queryKey%")->where('lang_tag',
                    $langTag)->pluck('table_id')->toArray();

                $idArr = AmService::getMemberFollows($queryType, $langIdArr, $mid);
                $extendArr = FresnsExtends::whereIn('id', $idArr)->get()->toArray();
                $lenguagesMap = FresnsLanguages::where('table_name',
                    FresnsExtendsConfig::CFG_TABLE)->where('table_field', 'title')->whereIn('table_id',
                    $idArr)->where('lang_tag', $langTag)->pluck('lang_content', 'table_id')->toArray();
                $descSecondaryMap = FresnsLanguages::where('table_name',
                    FresnsExtendsConfig::CFG_TABLE)->where('table_field', 'desc_secondary')->whereIn('table_id',
                    $idArr)->where('lang_tag', $langTag)->pluck('lang_content', 'table_id')->toArray();
                $descPrimaryMap = FresnsLanguages::where('table_name',
                    FresnsExtendsConfig::CFG_TABLE)->where('table_field', 'desc_primary')->whereIn('table_id',
                    $idArr)->where('lang_tag', $langTag)->pluck('lang_content', 'table_id')->toArray();

                foreach ($extendArr as $v) {
                    $item = [];
                    $item['id'] = $v['uuid'];
                    $item['name'] = '';
                    $item['nickname'] = '';
                    $item['image'] = ApiFileHelper::getImageSignUrlByFileIdUrl($v['cover_file_id'],
                        $v['cover_file_url']);
                    $item['title'] = $lenguagesMap[$v['id']] ?? '';
                    $item['titleColor'] = $v['title_color'];
                    $item['descPrimary'] = $descPrimaryMap[$v['id']] ?? '';
                    $item['descPrimaryColor'] = $v['desc_primary_color'];
                    $item['descSecondary'] = $descSecondaryMap[$v['id']] ?? '';
                    $item['descSecondaryColor'] = $v['desc_secondary_color'];
                    $data[] = $item;
                }
                break;
            default:
                // code...
                break;
        }

        $this->success($data);
    }

    // 扩展配置信息
    public function extensions(Request $request)
    {
        $mid = GlobalService::getGlobalKey('member_id');

        $rule = [
            'type' => [
                'required',
                'numeric',
                'in:3,4,9',
            ],
            'scene' => 'in:1,2,3',
        ];
        $type = $request->input('type');
        $scene = $request->input('scene');
        // if($type != 3){
        //     if(!empty($scene)){
        //         $info = [
        //             'info' => 'scene error',
        //         ];
        //         $this->errorInfo(ErrorCodeService::CODE_FAIL,$info);
        //     }
        // }
        // 当 plugin_usages > member_roles 字段有值时，需要判断当前接口请求的成员是否在符合条件的角色当中，如果不在，则不输出。如果字段有值，接口又无成员参数，则默认当无权用户。
        if (! $mid) {
            $idArr = FresnsPluginUsages::where('member_roles', null)->pluck('id')->toArray();
            $request->offsetSet('ids', implode(',', $idArr));
        } else {
            $noRoleIdArr = FresnsPluginUsages::where('member_roles', null)->pluck('id')->toArray();
            // 查询角色
            $memberRole = FresnsMemberRoleRels::where('member_id', $mid)->where('expired_at', '<',
                date('Y-m-d H:i:s', time()))->first();
            // dd($memberRole);
            $RoleIdArr = [];
            if ($memberRole) {
                $memberRole = FresnsMemberRoleRels::where('member_id', $mid)->where('expired_at', '<',
                    date('Y-m-d H:i:s', time()))->first();
                $RoleIdArr = FresnsPluginUsages::where('member_roles', 'like',
                    '%'.$memberRole['role_id'].'%')->pluck('id')->toArray();
                // dump($memberRole['role_id']);
                $RoleIdArr = $RoleIdArr;
            }
            $idArr = array_merge($noRoleIdArr, $RoleIdArr);
            // dd($idArr);
            $request->offsetSet('ids', implode(',', $idArr));
        }
        ValidateService::validateRule($request, $rule);
        $currentPage = $request->input('page', 1) ?? 1;
        $pageSize = $request->input('pageSize', 30) ?? 30;
        $request->offsetSet('currentPage', $currentPage);
        $request->offsetSet('is_enable', 1);
        $request->offsetSet('pageSize', $pageSize);
        // dd($pageSize);
        $TweetPluginUsagesService = new FresnsPluginUsagesService();
        $TweetPluginUsagesService->setResource(FresnsPluginUsagesResource::class);
        $list = $TweetPluginUsagesService->searchData();
        $data = [
            'pagination' => $list['pagination'],
            'list' => $list['list'],
        ];
        $this->success($data);
    }

    // 发送验证码
    public function sendVerifyCode(Request $request)
    {
        $rule = [
            'type' => [
                'required',
                'numeric',
                'in:1,2',
            ],
            'useType' => [
                'required',
                'numeric',
                'in:1,2,3,4,5',
            ],
            'template' => [
                'required',
                'numeric',
                'in:1,2,3,4,5,6,7',
            ],
            // 'account' => 'required'
        ];
        ValidateService::validateRule($request, $rule);
        $useType = $request->input('useType');
        $type = $request->input('type');
        $template = $request->input('template');
        $account = $request->input('account');
        $langTag = $request->header('langTag');
        $user_id = GlobalService::getGlobalKey('user_id');
        // 验证提交参数
        $checkInfo = AmChecker::checkVerifyCode($type, $useType, $account);
        // dd($checkInfo);
        if (is_array($checkInfo)) {
            return $this->errorCheckInfo($checkInfo);
        }

        // dd($request);
        // 发送短信验证码
        // if($type == 2){
        $type = $request->input('type');
        $useType = $request->input('useType');
        $template = $request->input('template');
        $account = $request->input('account');

        $countryCode = $request->input('countryCode');
        // 用途类型为“修改资料验证”，用户参数必填，检查用户对应的邮箱或手机号是否存在，存在才可发送验证码。拿数据表里存储的邮箱或手机号发送验证码，无视 account 和 countryCode 参数。
        if ($useType == 4) {
            $userInfo = FresnsUsers::find($user_id);
            if (empty($userInfo)) {
                $this->error(ErrorCodeService::UID_EXIST_ERROR);
            }
            // dd($userInfo);
            if ($type == 1) {
                $account = $userInfo['email'];
            } else {
                $account = $userInfo['pure_phone'];
            }
        }
        // todo 执行验证码发送（手机）
        // if ($type == 2) {
        //     $cmd = PluginConfig::PLG_CMD_SEND_SMS_PHONE;
        //     // 准备参数
        //     $account = $account;
        //     $template = $template;
        //     $countryCode = $countryCode;
        //     $input = [
        //         'phone' => $account,
        //         'template' => $template,
        //         'countryCode' => $countryCode,
        //         'langTag' => $langTag,
        //     ];
        // }
        $cmd = FresnsPluginConfig::PLG_CMD_SEND_CODE;
        $input = [
            'type' => $type,
            // 'useType' => $useType,
            'template' => $template,
            'account' => $account,
            'langTag' => $langTag,
            'countryCode' => $countryCode,
        ];
        $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);
        // dd($resp);
        if (PluginRpcHelper::isErrorPluginResp($resp)) {
            $this->errorCheckInfo($resp);
        }
        // dd($resp['output']);
        $this->success($resp['output']);
        // 发送邮箱
        // if ($type == 1) {
        //     $cmd = PluginConfig::PLG_CMD_SEND_SMS_EMAIL;
        //     // 准备参数
        //     $account = $account;
        //     $template = $template;
        //     $input = [
        //         'email' => $account,
        //         'template' => $template,
        //         'langTag' => $langTag,
        //     ];
        // }
        // // dd($input);
        // // 发信设置插件
        // if ($type == 1) {
        //     $pluginUniKey = ApiConfigHelper::getConfigByItemKey('send_email_service');
        // } else {
        //     $pluginUniKey = ApiConfigHelper::getConfigByItemKey('send_sms_service');
        // }
        // $pluginClass = PluginHelper::findPluginClass($pluginUniKey);
        // $resp = PluginRpcHelper::call($pluginClass, $cmd, $input);
        // // dd($resp);
        // if (PluginRpcHelper::isErrorPluginResp($resp)) {
        //     $resp['msg'] = $resp['output'];
        //     $this->errorCheckInfo($resp);
        // }
        // $this->success($resp['output']);
    }

    //下载内容文件
    public function downloadFile(Request $request)
    {
        // 校验参数
        $rule = [
            'type' => 'required|in:1,2,3',
            'uuid' => 'required',
            'fid' => 'required',
        ];
        ValidateService::validateRule($request, $rule);

        $uid = GlobalService::getGlobalKey('user_id');
        $mid = GlobalService::getGlobalKey('member_id');

        //校验内容是否存在
        $type = $request->input('type');
        $uuid = $request->input('uuid');
        $fid = $request->input('fid');
        switch ($type) {
            case 1:
                //需要验证文件是否属于对应的来源目标，比如文件是否属于该帖子。
                $typeData = FresnsPosts::where('uuid', $uuid)->first();
                // dd($typeData);
                if (empty($typeData)) {
                    $this->error(ErrorCodeService::FILES_ERROR);
                }

                $files = FresnsFiles::where('uuid', $fid)->where('table_name',
                    FresnsPostsConfig::CFG_TABLE)->where('table_id', $typeData['id'])->first();
                if (empty($files)) {
                    $this->error(ErrorCodeService::FILES_ERROR);
                }
                //帖子附件需要判断帖子是否开启了权限功能 posts > is_allow
                if (! empty($typeData)) {
                    if ($typeData['is_allow'] != FresnsPostsConfig::IS_ALLOW_1) {
                        $this->error(ErrorCodeService::USERS_NOT_AUTHORITY_ERROR);
                    }
                }
                //如果帖子有阅读权限，则判断当前请求下载的成员本身和成员的主角色是否在授权列表 post_allows 表
                $count = DB::table('post_allows')->where('post_id', $typeData['id'])->where('type',
                    2)->where('object_id', $mid)->count();
                if (empty($count)) {
                    $this->error(ErrorCodeService::USERS_NOT_AUTHORITY_ERROR);
                }
                break;
            case 2:
                //需要验证文件是否属于对应的来源目标，比如文件是否属于该帖子。
                $typeData = FresnsComments::where('uuid', $uuid)->first();
                if (empty($typeData)) {
                    $this->error(ErrorCodeService::FILES_ERROR);
                }

                $files = FresnsFiles::where('uuid', $fid)->where('table_name',
                    FresnsCommentsConfig::CFG_TABLE)->where('table_id', $typeData['id'])->first();
                if (empty($files)) {
                    $this->error(ErrorCodeService::FILES_ERROR);
                }
                break;
            default:
                $typeData = FresnsExtends::where('uuid', $uuid)->first();
                //需要验证文件是否属于对应的来源目标，比如文件是否属于该帖子。
                if (empty($typeData)) {
                    $this->error(ErrorCodeService::FILES_ERROR);
                }

                $files = FresnsFiles::where('uuid', $fid)->where('table_name',
                    FresnsExtendsConfig::CFG_TABLE)->where('table_id', $typeData['id'])->first();
                if (empty($files)) {
                    $this->error(ErrorCodeService::FILES_ERROR);
                }
                break;
        }

        if (empty($typeData)) {
            $this->error(ErrorCodeService::FILES_ERROR);
        }

        $files = FresnsFiles::where('uuid', $fid)->first();
        //如果校验通过，将记录填充进file_logs表
        $input = [
            'file_id' => $files['file_type'],
            'user_id' => $uid,
            'member_id' => $mid,
            'object_type' => $type,
            'object_id' => $files['table_id'],
        ];
        FresnsFileLogs::insert($input);
        $data = [];
        $data['previewUrl'] = '';
        $filePath = $files['file_path'];
        switch ($files['file_type']) {
            case 1:
                $host = ApiConfigHelper::getConfigByItemKey('images_bucket_domain');
                $fileUrl = $host.$filePath;
                $data['previewUrl'] = $fileUrl;
                $downloadUrl = ApiFileHelper::getImageSignUrl($fileUrl);
                break;
            case 2:
                $host = ApiConfigHelper::getConfigByItemKey('videos_bucket_domain');
                $downloadUrl = $host.$filePath;
                break;
            case 3:
                $host = ApiConfigHelper::getConfigByItemKey('audios_bucket_domain');
                $downloadUrl = $host.$filePath;

                break;
            default:
                $host = ApiConfigHelper::getConfigByItemKey('docs_bucket_domain');
                $downloadUrl = $host.$filePath;
                break;
        }

        $data['downloadUrl'] = $downloadUrl;

        $this->success($data);
    }

    // 全局摘要信息
    public function overview(Request $request)
    {
        $member_id = GlobalService::getGlobalKey('member_id');
        //消息未读数
        // 系统
        $system_count = FresnsNotifies::where('member_id', $member_id)->where('source_type',
            AmConfig::SOURCE_TYPE_1)->where('status', AmConfig::NO_READ)->count();
        // 关注
        $follow_count = FresnsNotifies::where('member_id', $member_id)->where('source_type',
            AmConfig::SOURCE_TYPE_2)->where('status', AmConfig::NO_READ)->count();
        // 点赞
        $like_count = FresnsNotifies::where('member_id', $member_id)->where('source_type',
            AmConfig::SOURCE_TYPE_3)->where('status', AmConfig::NO_READ)->count();
        // 评论
        $comment_count = FresnsNotifies::where('member_id', $member_id)->where('source_type',
            AmConfig::SOURCE_TYPE_4)->where('status', AmConfig::NO_READ)->count();
        // 提及（艾特）
        $mention_count = FresnsNotifies::where('member_id', $member_id)->where('source_type',
            AmConfig::SOURCE_TYPE_5)->where('status', AmConfig::NO_READ)->count();
        // 推荐
        $recommend_count = FresnsNotifies::where('member_id', $member_id)->where('source_type',
            AmConfig::SOURCE_TYPE_6)->where('status', AmConfig::NO_READ)->count();
        // 会话及会话消息未读数
        // 查询未a_status未读
        $aStatusNoRead = FresnsDialogs::where('a_member_id', $member_id)->where('a_status', AmConfig::NO_READ)->count();
        // 查询未b_status未读
        $bStatusNoRead = FresnsDialogs::where('b_member_id', $member_id)->where('b_status', AmConfig::NO_READ)->count();
        $dialogNoRead = $aStatusNoRead + $bStatusNoRead;
        // 消息未读
        $dialogMessage = FresnsDialogMessages::where('recv_member_id', $member_id)->where('recv_read_at',
            null)->count();
        $dialogUnread = [
            'dialog' => $dialogNoRead,
            'message' => $dialogMessage,
        ];
        $notifyUnread = [
            'system' => $system_count,
            'follow' => $follow_count,
            'like' => $like_count,
            'comment' => $comment_count,
            'mention' => $mention_count,
            'recommend' => $recommend_count,
        ];
        $data = [
            'dialogUnread' => $dialogUnread,
            'notifyUnread' => $notifyUnread,
        ];
        $this->success($data);
    }
}
