<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Info;

use App\Helpers\DateHelper;
use App\Http\Fresns\FresnsApi\Base\FresnsBaseApiController;
use App\Http\Fresns\FresnsApi\Helpers\ApiConfigHelper;
use Illuminate\Http\Request;
use App\Http\Fresns\FresnsApi\Helpers\ApiLanguageHelper;
use App\Http\Fresns\FresnsExtends\FresnsExtends;
use App\Http\Fresns\FresnsExtends\FresnsExtendsConfig;
use App\Http\Fresns\FresnsGroups\FresnsGroups;
use App\Http\Fresns\FresnsGroups\FresnsGroupsConfig;
use App\Http\Fresns\FresnsHashtags\FresnsHashtags;
use App\Http\Fresns\FresnsPosts\FresnsPosts;
use App\Http\Fresns\FresnsSessionLogs\FresnsSessionLogs;
use App\Http\Fresns\FresnsEmojis\FresnsEmojisService;
use App\Http\Fresns\FresnsPluginUsages\FresnsPluginUsagesService;
use App\Http\Share\Common\ValidateService;
use App\Http\Fresns\FresnsCmds\FresnsPluginConfig;
use App\Http\Fresns\FresnsCmds\FresnsPlugin;
use App\Http\Center\Helper\PluginRpcHelper;
use App\Http\Fresns\FresnsCmds\FresnsCrontablPlugin;
use App\Http\Fresns\FresnsCmds\FresnsCrontabPluginConfig;
use App\Http\Fresns\FresnsComments\FresnsComments;
use App\Http\Fresns\FresnsComments\FresnsCommentsConfig;
use App\Http\Fresns\FresnsEmojis\FresnsEmojisConfig;
use App\Http\Fresns\FresnsFileLogs\FresnsFileLogs;
use App\Http\Fresns\FresnsFiles\FresnsFiles;
use App\Http\Fresns\FresnsLanguages\FresnsLanguages;
use App\Http\Fresns\FresnsMemberFollows\FresnsMemberFollows;
use App\Http\Fresns\FresnsMembers\FresnsMembers;
use App\Http\Fresns\FresnsPosts\FresnsPostsConfig;
use App\Http\Fresns\FresnsStopWords\FresnsStopWordsService;
use App\Http\Fresns\FresnsUsers\FresnsUsers;
use Illuminate\Support\Facades\DB;
use App\Http\Share\Common\ErrorCodeService;
use App\Http\Fresns\FresnsPluginUsages\FresnsPluginUsages;
use App\Http\Fresns\FresnsMemberRoleRels\FresnsMemberRoleRels;
use App\Http\Share\AmGlobal\GlobalService;
use App\Http\Fresns\FresnsNotifies\FresnsNotifies;
use App\Http\Fresns\FresnsDialogs\FresnsDialogs;
use App\Http\Fresns\FresnsDialogMessages\FresnsDialogMessages;
use App\Plugins\TestPlugin\PluginConfig;
use App\Http\Center\Helper\PluginHelper;
use App\Http\Fresns\FresnsApi\Helpers\ApiFileHelper;

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
            if (!empty($itemTag)) {
                $itemTagArr = explode(',', $itemTag);
                foreach ($itemTagArr as $v) {
                    $data = array_merge($data, ApiConfigHelper::getConfigByKeyApi($v));
                }
            }
            if (!empty($itemKey)) {
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
        $input['version'] =  $request->header('version');
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
        $input['moreJson'] = $request->input('moreJson');

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
     * 成员名,话题,帖子,不涉及多语言表
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
                    $item['image'] = ApiFileHelper::getImageSignUrlByFileIdUrl($v['avatar_file_id'],
                        $v['avatar_file_url']);
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
                # code...
                break;
        }

        $this->success($data);
    }

    // 扩展配置信息
    public function expands(Request $request)
    {
        $mid = GlobalService::getGlobalKey('member_id');

        $rule = [
            'type' => [
                'required',
                'numeric',
                "in:3,4,9",
            ],
            'scene' => 'in:1,2,3'
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
        if (!$mid) {
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
        $TweetPluginUsagesService->setResource(TweetPluginUsagesResource::class);
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
                "in:1,2",
            ],
            'useType' => [
                'required',
                'numeric',
                "in:1,2,3,4,5",
            ],
            'template' => [
                'required',
                'numeric',
                "in:1,2,3,4,5,6,7",
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
                if (!empty($typeData)) {
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
            'object_id' => $files['table_id']
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
    public function summary(Request $request)
    {
        $member_id = GlobalService::getGlobalKey('member_id');
        #消息未读数
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

    public function testPlugin(Request $request)
    {
        $type = $request->input('type');
        $field = $request->input('field');
        switch ($type) {
            case 1:
                $cmd = FresnsCrontabPluginConfig::ADD_SUB_TABLE_PLUGIN_ITEM;
                $input['sub_table_plugin_item'] = json_decode($field, true);
                $resp = PluginRpcHelper::call(FresnsCrontablPlugin::class, $cmd, $input);
                if (PluginRpcHelper::isErrorPluginResp($resp)) {
                    $this->errorCheckInfo($resp, [], $resp);
                }
                break;
            case 2:
                $cmd = FresnsCrontabPluginConfig::DELETE_SUB_TABLE_PLUGIN_ITEM;
                $input['sub_table_plugin_item'] = json_decode($field, true);
                $resp = PluginRpcHelper::call(FresnsCrontablPlugin::class, $cmd, $input);
                if (PluginRpcHelper::isErrorPluginResp($resp)) {
                    $this->errorCheckInfo($resp, [], $resp);
                }
                break;
            case 3:
                $cmd = FresnsPluginConfig::PLG_CMD_CREATE_SESSION_TOKEN;
                $input['user_id'] = $request->input('uid');
                $input['platform'] = $request->header('platform');
                $input['member_id'] = $request->input('mid');
                $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);
                if (PluginRpcHelper::isErrorPluginResp($resp)) {
                    $this->errorCheckInfo($resp, [], $resp);
                }
                break;
            case 4:
                $cmd = FresnsPluginConfig::PLG_CMD_VERIFY_SESSION_TOKEN;
                $input['user_id'] = $request->input('uid');
                $input['platform'] = $request->header('platform');
                $input['member_id'] = $request->input('mid');
                $input['token'] = $request->input('token');
                $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);
                if (PluginRpcHelper::isErrorPluginResp($resp)) {
                    $this->errorCheckInfo($resp, [], $resp);
                }
                break;
            case 5:
                $cmd = FresnsPluginConfig::PLG_CMD_UPLOAD_SESSION_LOG;
                $input['platform'] = $request->header('platform');
                $input['version'] = $request->header('version');
                $input['versionInt'] = $request->header('versionInt');
                $input['object_name'] = '123';
                $input['object_action'] = '123';
                $input['object_result'] = '1';
                $input['object_type'] = '1';

                $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);
                if (PluginRpcHelper::isErrorPluginResp($resp)) {
                    $this->errorCheckInfo($resp, [], $resp);
                }
                break;
            case 6:
                $cmd = FresnsPluginConfig::PLG_CMD_GET_UPLOAD_TOKEN;
                $input['type'] = 1;
                $input['mode'] = 1;
                $input['scene'] = 1;

                $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);
                if (PluginRpcHelper::isErrorPluginResp($resp)) {
                    $this->errorCheckInfo($resp, [], $resp);
                }
                break;
            case 7:
                $cmd = FresnsPluginConfig::PLG_CMD_UPLOAD_FILE;
                $input['type'] = $request->input('plug_type');
                $input['tableType'] = $request->input('tableType');
                $input['tableName'] = $request->input('tableName');
                $input['tableField'] = $request->input('tableField');
                $input['tableId'] = $request->input('tableId');
                $input['tableKey'] = $request->input('tableKey');
                $input['mode'] = $request->input('mode');
                $input['file'] = $request->file('file');
                $input['fileInfo'] = $request->input('fileInfo');
                $input['platform'] = $request->header('platform');
                $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);
                if (PluginRpcHelper::isErrorPluginResp($resp)) {
                    $this->errorCheckInfo($resp, [], $resp);
                }

                break;
            case 8:
                $cmd = FresnsPluginConfig::PLG_CMD_ANTI_LINK_IMAGE;
                $input['fid'] = $request->input('fid');

                $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);
                if (PluginRpcHelper::isErrorPluginResp($resp)) {
                    $this->errorCheckInfo($resp, [], $resp);
                }
                break;
            case 9:
                $cmd = FresnsPluginConfig::PLG_CMD_HARD_DELETE_FID;
                $input['fid'] = $request->input('fid');

                $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);
                if (PluginRpcHelper::isErrorPluginResp($resp)) {
                    $this->errorCheckInfo($resp, [], $resp);
                }
                break;
            case 10:
                $cmd = FresnsCrontabPluginConfig::ADD_CRONTAB_PLUGIN_ITEM;
                $input['crontab_plugin_item'] = json_decode($field, true);
                $resp = PluginRpcHelper::call(FresnsCrontablPlugin::class, $cmd, $input);
                if (PluginRpcHelper::isErrorPluginResp($resp)) {
                    $this->errorCheckInfo($resp, [], $resp);
                }
                break;
            case 11:
                $cmd = FresnsCrontabPluginConfig::DELETE_CRONTAB_PLUGIN_ITEM;
                $input['crontab_plugin_item'] = json_decode($field, true);

                $resp = PluginRpcHelper::call(FresnsCrontablPlugin::class, $cmd, $input);
                if (PluginRpcHelper::isErrorPluginResp($resp)) {
                    $this->errorCheckInfo($resp, [], $resp);
                }
                break;
            case 12:
                $cmd = FresnsCrontabPluginConfig::PLG_CMD_CRONTAB_CHECK_ROLE_EXPIRED;
                $input = [];
                $resp = PluginRpcHelper::call(FresnsCrontablPlugin::class, $cmd, $input);
                if (PluginRpcHelper::isErrorPluginResp($resp)) {
                    $this->errorCheckInfo($resp, [], $resp);
                }
                break;
            case 13:
                $cmd = FresnsCrontabPluginConfig::PLG_CMD_CRONTAB_CHECK_DELETE_USER;
                $input = [];
                $resp = PluginRpcHelper::call(FresnsCrontablPlugin::class, $cmd, $input);
                if (PluginRpcHelper::isErrorPluginResp($resp)) {
                    $this->errorCheckInfo($resp, [], $resp);
                }
                break;
            default:
                $cmd = FresnsPluginConfig::PLG_CMD_UPLOAD_FILE;
                $input['type'] = $request->input('plug_type');
                $input['tableType'] = $request->input('tableType');
                $input['tableName'] = $request->input('tableName');
                $input['tableField'] = $request->input('tableField');
                $input['tableId'] = $request->input('tableId');
                $input['tableKey'] = $request->input('tableKey');
                $input['mode'] = $request->input('mode');
                $input['file'] = $request->input('file');
                $input['fileInfo'] = $request->input('fileInfo');
                $input['platform'] = $request->header('platform');
                $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);
                if (PluginRpcHelper::isErrorPluginResp($resp)) {
                    $this->errorCheckInfo($resp, [], $resp);
                }
                return $resp;
                break;
        }

        $this->success($resp['output']);
    }

    //创建交互凭证
    public function createSessionToken(Request $request)
    {
        $cmd = FresnsPluginConfig::PLG_CMD_CREATE_SESSION_TOKEN;
        $input['uid'] = $request->input('uid');
        $input['platform'] = $request->header('platform');
        $input['mid'] = $request->input('mid');
        $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);
        if (PluginRpcHelper::isErrorPluginResp($resp)) {
            $this->errorCheckInfo($resp);
        }

        $this->success($resp);
    }

    //校验交互凭证
    public function checkSessionToken(Request $request)
    {
        $cmd = FresnsPluginConfig::PLG_CMD_VERIFY_SESSION_TOKEN;
        $input['uid'] = $request->input('uid');
        $input['platform'] = $request->header('platform');
        $input['mid'] = $request->input('mid');
        $input['token'] = $request->input('token');
        $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);
        if (PluginRpcHelper::isErrorPluginResp($resp)) {
            $this->errorCheckInfo($resp);
        }

        $this->success($resp);
    }

    //上传交互日志
    public function uploadSessionLog(Request $request)
    {
        $cmd = FresnsPluginConfig::PLG_CMD_UPLOAD_SESSION_LOG;
        $input['platform'] = $request->input('platform');
        $input['version'] = $request->input('version');
        $input['versionInt'] = $request->input('versionInt');
        $input['objectName'] = $request->input('objectName');
        $input['objectAction'] = $request->input('objectAction');
        $input['objectResult'] = $request->input('objectResult');
        $input['objectType'] = $request->input('objectType');
        $input['langTag'] = $request->input('langTag');
        $input['objectOrderId'] = $request->input('objectOrderId');
        $input['deviceInfo'] = $request->input('deviceInfo');
        $input['uid'] = $request->input('uid');
        $input['mid'] = $request->input('mid');
        $input['moreJson'] = $request->input('moreJson');

        $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);
        if (PluginRpcHelper::isErrorPluginResp($resp)) {
            $this->errorCheckInfo($resp);
        }

        $this->success($resp['output']);
    }

    //获取上传凭证
    public function getUploadToken(Request $request)
    {
        $cmd = FresnsPluginConfig::PLG_CMD_GET_UPLOAD_TOKEN;
        $input['type'] = $request->input('type');
        $input['mode'] = $request->input('mode');
        $input['scene'] = $request->input('scene');

        $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);
        // dd($resp);
        if (PluginRpcHelper::isErrorPluginResp($resp)) {
            // dd($resp);
            // $this->errorCheckInfo($resp, [], $resp);
            // $resp['msg'] = $resp['output'];
            $this->errorCheckInfo($resp);
        }

        $this->success($resp);
    }

    public function uploadFile(Request $request)
    {
        $cmd = FresnsPluginConfig::PLG_CMD_UPLOAD_FILE;
        $input['type'] = $request->input('type');
        $input['tableType'] = $request->input('tableType');
        $input['tableName'] = $request->input('tableName');
        $input['tableField'] = $request->input('tableField');
        $input['tableId'] = $request->input('tableId');
        $input['tableKey'] = $request->input('tableKey');
        $input['mode'] = $request->input('mode');
        $input['file'] = $request->file('file');
        $input['fileInfo'] = $request->input('fileInfo');
        $input['platform'] = $request->input('platform');
        $input['uid'] = $request->input('uid');
        $input['mid'] = $request->input('mid');
        $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);

        if (PluginRpcHelper::isErrorPluginResp($resp)) {
            $this->errorCheckInfo($resp);
        }

        $this->success($resp);
    }

    public function linkImage(Request $request)
    {
        $cmd = FresnsPluginConfig::PLG_CMD_ANTI_LINK_IMAGE;
        $input['fid'] = $request->input('fid');

        $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);
        if (PluginRpcHelper::isErrorPluginResp($resp)) {
            $this->errorCheckInfo($resp);
        }

        $this->success($resp);
    }


    public function linkVideo(Request $request)
    {
        $cmd = FresnsPluginConfig::PLG_CMD_ANTI_LINK_VIDEO;
        $input['fid'] = $request->input('fid');

        $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);
        if (PluginRpcHelper::isErrorPluginResp($resp)) {
            $this->errorCheckInfo($resp);
        }

        $this->success($resp);
    }

    public function linkAudio(Request $request)
    {
        $cmd = FresnsPluginConfig::PLG_CMD_ANTI_LINK_AUDIO;
        $input['fid'] = $request->input('fid');

        $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);
        if (PluginRpcHelper::isErrorPluginResp($resp)) {
            $this->errorCheckInfo($resp);
        }

        $this->success($resp);
    }

    public function linkDoc(Request $request)
    {
        $cmd = FresnsPluginConfig::PLG_CMD_ANTI_LINK_DOC;
        $input['fid'] = $request->input('fid');

        $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);
        if (PluginRpcHelper::isErrorPluginResp($resp)) {
            $this->errorCheckInfo($resp);
        }

        $this->success($resp);
    }

    public function deleteFid(Request $request)
    {
        $cmd = FresnsPluginConfig::PLG_CMD_HARD_DELETE_FID;
        $input['fid'] = $request->input('fid');

        $resp = PluginRpcHelper::call(FresnsPlugin::class, $cmd, $input);
        if (PluginRpcHelper::isErrorPluginResp($resp)) {
            $this->errorCheckInfo($resp);
        }

        $this->success($resp);

    }

    public function updateConfigs()
    {

        $configsDev = DB::table('configs_dev')->whereIn('item_key', AmConfig::CONFIGS_ITEM_KEY)->get()->toArray();
        dd($configsDev);
    }

}

