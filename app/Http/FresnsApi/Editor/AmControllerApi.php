<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\Editor;

use App\Helpers\DateHelper;
use App\Helpers\StrHelper;
use App\Http\Center\Common\GlobalService;
use App\Http\Center\Common\ErrorCodeService;
use App\Http\Center\Common\LogService;
use App\Http\Center\Common\ValidateService;
use App\Http\Center\Helper\PluginHelper;
use App\Http\Center\Helper\PluginRpcHelper;
use App\Http\Center\Scene\FileSceneConfig;
use App\Http\Center\Scene\FileSceneService;
use App\Http\FresnsApi\Base\FresnsBaseApiController;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsCmd\FresnsPlugin as FresnsCmdFresnsPlugin;
use App\Http\FresnsCmd\FresnsPluginConfig;
use App\Http\FresnsDb\FresnsCommentLogs\FresnsCommentLogs;
use App\Http\FresnsDb\FresnsCommentLogs\FresnsCommentLogsService;
use App\Http\FresnsDb\FresnsComments\FresnsComments;
use App\Http\FresnsDb\FresnsComments\FresnsCommentsService;
use App\Http\FresnsDb\FresnsConfigs\FresnsConfigs;
use App\Http\FresnsDb\FresnsConfigs\FresnsConfigsConfig;
use App\Http\FresnsDb\FresnsPostLogs\FresnsPostLogs;
use App\Http\FresnsDb\FresnsPostLogs\FresnsPostLogsService;
use App\Http\FresnsDb\FresnsPosts\FresnsPosts;
use App\Http\FresnsDb\FresnsPosts\FresnsPostsService;
use App\Http\FresnsDb\FresnsSessionLogs\FresnsSessionLogs;
use App\Http\FresnsDb\FresnsSessionLogs\FresnsSessionLogsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AmControllerApi extends FresnsBaseApiController
{
    public function __construct()
    {
        $this->service = new AmService();
        $this->checkRequest();
        $this->initData();
        parent::__construct();
    }

    // 创建新草稿
    public function create(Request $request)
    {
        $rule = [
            'type' => 'required|in:1,2',
        ];
        ValidateService::validateRule($request, $rule);
        $type = $request->input('type');
        $deviceInfo = $request->header('deviceInfo');
        $platform = $this->platform;
        $user_id = GlobalService::getGlobalKey('user_id');
        $mid = GlobalService::getGlobalKey('member_id');
        // dd($platform);
        if ($deviceInfo) {
            if ($type == 1) {
                $logsId = FresnsSessionLogsService::addSessionLogs($request->getRequestUri(), '创建帖子草稿', $user_id,
                    $mid, null, '创建新草稿');
            } else {
                $logsId = FresnsSessionLogsService::addSessionLogs($request->getRequestUri(), '创建评论草稿',
                    $user_id, $mid, null, '创建新草稿');
            }
        }

        // 如果是私有模式，当过期后 members > expired_at，该接口不可请求。
        $checkInfo = AmChecker::checkCreate($mid);
        // dd($checkInfo);
        if (is_array($checkInfo)) {
            FresnsSessionLogs::where('id', $logsId)->update(['object_result' => AmConfig::OBJECT_DEFAIL]);

            return $this->errorCheckInfo($checkInfo);
        }

        // 	1.帖子 / 2.评论
        $type = $request->input('type');
        // 为空代表创建空白草稿，有值代表编辑现有内容
        $uuid = $request->input('uuid', '');
        // 2.评论专用，表示该帖子下的评论
        $pid = $request->input('pid', '');
        switch ($type) {
            case '1':
                // uuid=空，创建空白草稿，不做数量检查，帖子草稿可以有多个。
                if (empty($uuid)) {
                    // 验证新增权限
                    $createdCheck = AmChecker::checkPermission($type, 1, $user_id, $mid);
                    if (is_array($createdCheck)) {
                        FresnsSessionLogs::where('id', $logsId)->update(['object_result' => AmConfig::OBJECT_DEFAIL]);

                        return $this->errorCheckInfo($createdCheck);
                    }

                    // dd($createdCheck);
                    $postInput = [
                        'member_id' => $mid,
                        'platform_id' => $platform,
                    ];
                    $postLogId = DB::table('post_logs')->insertGetId($postInput);
                } else {
                    // uuid=有值，检查 status=1、2、4 是否存在该帖子 ID 草稿。
                    /**
                     * 存在，该 ID 不可再创建新草稿，相当于同一篇帖子只能有一篇正在编辑的草稿，直接返回当前草稿详情。
                     * 不存在，获取该帖子现有内容创建草稿。
                     */
                    $postInfo = FresnsPosts::where('uuid', $uuid)->first();
                    // 验证编辑权限
                    $createdCheck = AmChecker::checkPermission($type, 2, $user_id, $mid, $postInfo['id']);
                    // dd($createdCheck);
                    if (is_array($createdCheck)) {
                        FresnsSessionLogs::where('id', $logsId)->update(['object_result' => AmConfig::OBJECT_DEFAIL]);

                        return $this->errorCheckInfo($createdCheck);
                    }
                    $postLog = FresnsPostLogs::where('post_id', $postInfo['id'])->where('member_id',
                        $mid)->where('status', '!=', 3)->first();
                    if (! $postLog) {
                        $postLogId = ContentLogsService::postLogInsert($uuid, $mid);
                    } else {
                        $postLogId = $postLog['id'];
                    }
                }
                FresnsSessionLogs::where('id', $logsId)->update([
                    'object_result' => AmConfig::OBJECT_SUCCESS,
                    'object_order_id' => $postLogId,
                ]);
                // dd($postLogId);
                $FresnsPostLogsService = new FresnsPostLogsService();
                $request->offsetSet('id', $postLogId);
                $request->offsetUnset('type');
                $FresnsPostLogsService->setResource(FresnsPostLogsResourceDetail::class);
                $list = $FresnsPostLogsService->searchData();
                break;
            /**type=2
             * uuid=空，代表创建空白草稿，此时 pid 必填，检查该 pid 是否存在评论草稿。
             * 存在，不可再创建，相当于同一篇帖子只有一篇评论草稿，直接返回当前草稿详情。
             * 不存在，创建新草稿。
             * uuid=有值，检查 status=1、2、4 是否存在该评论 ID 草稿。
             * 存在，该 ID 不可再创建新草稿，相当于同一篇评论只能有一篇正在编辑的草稿，直接返回当前草稿详情。
             * 不存在，获取该评论现有内容创建草稿。
             * 只有一级评论可以有草稿，子级评论不能生成草稿，所以评论创建草稿只需要 pid 即可。
             */
            default:
                if (empty($uuid)) {
                    if (empty($pid)) {
                        $this->errorInfo(ErrorCodeService::CODE_FAIL, ['info' => 'pid required']);
                    }
                    // 验证新增权限
                    $createdCheck = AmChecker::checkPermission($type, 1, $user_id, $mid);
                    if (is_array($createdCheck)) {
                        return $this->errorCheckInfo($createdCheck);
                    }
                    $postInfo = FresnsPosts::where('uuid', $pid)->first();
                    // if(!$postInfo){
                    //     $this->error(ErrorCodeService::DELETE_FILE_ERROR);
                    // }
                    $commentLog = FresnsCommentLogs::where('member_id', $mid)->where('post_id',
                        $postInfo['id'])->where('status', '!=', 3)->first();
                    // 存在，不可再创建，相当于同一篇帖子只有一篇评论草稿，直接返回当前草稿详情。
                    if ($commentLog) {
                        $commentLogId = $commentLog['id'];
                    } else {
                        // 不存在，创建新草稿。
                        $commentLogInput = [
                            'member_id' => $mid,
                            'post_id' => $postInfo['id'],
                            'platform_id' => $platform,
                        ];
                        $commentLogId = DB::table('comment_logs')->insertGetId($commentLogInput);
                    }
                } else {
                    $commentInfo = FresnsComments::where('uuid', $uuid)->first();
                    // 验证编辑权限
                    $createdCheck = AmChecker::checkPermission($type, 2, $user_id, $mid, $commentInfo['id']);
                    // dd($createdCheck);
                    if (is_array($createdCheck)) {
                        return $this->errorCheckInfo($createdCheck);
                    }
                    // if(!$commentInfo){
                    //     $this->error(ErrorCodeService::DELETE_COMMENT_ERROR);
                    // }
                    // if($commentInfo['parent_id'] != 0){
                    //     $this->error(ErrorCodeService::COMMENT_LOGS_ERROR);
                    // }
                    $commentLog = FresnsCommentLogs::where('comment_id', $commentInfo['id'])->where('member_id',
                        $mid)->where('status', '!=', 3)->first();
                    if (! $commentLog) {
                        $commentLogId = ContentLogsService::commentLogInsert($uuid, $mid);
                    } else {
                        $commentLogId = $commentLog['id'];
                    }
                }
                FresnsSessionLogs::where('id', $logsId)->update([
                    'object_result' => AmConfig::OBJECT_SUCCESS,
                    'object_order_id' => $commentLogId,
                ]);
                $FresnsCommentLogsService = new FresnsCommentLogsService();
                $request->offsetSet('id', $commentLogId);
                $request->offsetUnset('type');
                $FresnsCommentLogsService->setResource(FresnsCommentLogsResourceDetail::class);
                $list = $FresnsCommentLogsService->searchData();
                break;
        }
        $data = [
            'detail' => $list['list'],
        ];
        $this->success($data);
    }

    // 草稿详情
    public function detail(Request $request)
    {
        $rule = [
            'type' => 'required|in:1,2',
        ];
        ValidateService::validateRule($request, $rule);

        $mid = GlobalService::getGlobalKey('member_id');
        $type = $request->input('type');

        switch ($type) {
            case '1':
                $FresnsPostLogsService = new FresnsPostLogsService();
                $request->offsetUnset('type');
                // $request->offsetset('inStatus',"1,4");
                $request->offsetSet('member_id', $mid);
                $FresnsPostLogsService->setResource(FresnsPostLogsResourceDetail::class);
                $list = $FresnsPostLogsService->searchData();
                break;

            default:
                $FresnsCommentLogsService = new FresnsCommentLogsService();
                $request->offsetUnset('type');
                // $request->offsetset('inStatus',"1,4");
                $request->offsetSet('member_id', $mid);
                $FresnsCommentLogsService->setResource(FresnsCommentLogsResourceDetail::class);
                $list = $FresnsCommentLogsService->searchData();
                break;
        }
        $data = [
            'detail' => $list['list'],
        ];
        $this->success($data);
    }

    // 获取草稿列表
    public function lists(Request $request)
    {
        $rule = [
            'type' => 'required|in:1,2',
            'status' => 'required|in:1,2,4',
            'class' => 'in:1,2',
        ];
        ValidateService::validateRule($request, $rule);

        $mid = GlobalService::getGlobalKey('member_id');
        $type = $request->input('type');
        $status = $request->input('status');
        $class = $request->input('class');
        if ($type == 1) {
            if (! empty($class)) {
                if ($class == 1) {
                    $idArr = FresnsPostLogs::where('post_id', null)->pluck('id')->toArray();
                } else {
                    $idArr = FresnsPostLogs::where('post_id', '!=', null)->pluck('id')->toArray();
                }
                $request->offsetSet('ids', implode(',', $idArr));
            }
            // if($status == 1){
            //     $statusStr = "1,4";
            // }else{
            //     $statusStr = "2";
            // }
            // dd($statusStr);
            // $request->offsetSet('queryType', AmConfig::QUERY_TYPE_SQL_QUERY);
            $page = $request->input('page', 1);
            $pageSize = $request->input('pageSize', 30);
            $FresnsPostLogsService = new FresnsPostLogsService();
            // $request->offsetSet('inStatus', $statusStr);
            $request->offsetUnset('type');
            // $request->offsetUnset('status');
            $request->offsetSet('member_id', $mid);
            $request->offsetSet('currentPage', $page);
            $request->offsetSet('pageSize', $pageSize);
            // dd($request);
            $FresnsPostLogsService->setResource(FresnsPostLogsResource::class);
            $list = $FresnsPostLogsService->searchData();
        // dd(1);
        } else {
            // $request->offsetSet('queryType', AmConfig::QUERY_TYPE_SQL_QUERY);
            if (! empty($class)) {
                if ($class == 1) {
                    $idArr = FresnsCommentLogs::where('comment_id', null)->pluck('id')->toArray();
                } else {
                    $idArr = FresnsCommentLogs::where('comment_id', '!=', null)->pluck('id')->toArray();
                }
                $request->offsetSet('ids', implode(',', $idArr));
            }
            // if($status == 1){
            //     $statusStr = "1,4";
            // }else{
            //     $statusStr = "2";
            // }
            $page = $request->input('page', 1);
            $pageSize = $request->input('pageSize', 30);
            $FresnsCommentLogsService = new FresnsCommentLogsService();
            // $request->offsetSet('ids', implode(',',$idArr));
            // dd($idArr);
            // $request->offsetSet('inStatus', $statusStr);
            $request->offsetUnset('type');
            // $request->offsetUnset('status');
            $request->offsetSet('member_id', $mid);
            $request->offsetSet('currentPage', $page);
            $request->offsetSet('pageSize', $pageSize);
            $FresnsCommentLogsService->setResource(FresnsCommentLogsResource::class);
            $list = $FresnsCommentLogsService->searchData();
        }
        $data = [
            'list' => $list['list'],
            'pagination' => $list['pagination'],
        ];
        $this->success($data);
    }

    // 更新草稿内容
    public function update(Request $request)
    {
        $rule = [
            'logType' => 'required|in:1,2',
            'logId' => 'required',
            'isMarkdown' => 'in:0,1',
            'isAnonymous' => 'in:0,1',
            'isPluginEdit' => 'in:0,1',
            'fileJson' => 'json',
            'extendsJson' => 'json',
            'locationJson' => 'json',
            'allowJson' => 'json',
            'commentSetJson' => 'json',
            'memberListJson' => 'json',
        ];
        ValidateService::validateRule($request, $rule);

        $mid = GlobalService::getGlobalKey('member_id');
        // dd($mid);
        // $type = $request->input('type');
        $logType = $request->input('logType');
        $logId = $request->input('logId');
        $checkInfo = AmChecker::checkDrast($mid);
        if (is_array($checkInfo)) {
            return $this->errorCheckInfo($checkInfo);
        }
        // 帖子更新
        if ($logType == 1) {
            ContentLogsService::updatePostLog($mid);
        } else {
            ContentLogsService::updateCommentLog($mid);
        }
        $this->success();
    }

    // 提交内容
    public function submit(Request $request)
    {
        $rule = [
            'type' => 'required|in:1,2',
            'logId' => 'required',
        ];
        ValidateService::validateRule($request, $rule);
        $deviceInfo = $request->header('deviceInfo');
        $platform = $this->platform;
        $type = $request->input('type');
        // dd($platform);
        $logsId = 0;
        $mid = GlobalService::getGlobalKey('member_id');
        $user_id = GlobalService::getGlobalKey('user_id');
        if ($deviceInfo) {
            if ($type == 1) {
                $logsId = FresnsSessionLogsService::addSessionLogs("App\Http\FresnsDb\FresnsPosts", '发表帖子内容', $user_id,
                    $mid, null, '提交内容正式发表');
            } else {
                $logsId = FresnsSessionLogsService::addSessionLogs("App\Http\FresnsDb\FresnsComments", '发表评论内容', $user_id,
                    $mid, null, '提交内容正式发表');
            }
        }
        $type = $request->input('type');
        $draftId = $request->input('logId');
        $FresnsPostsService = new FresnsPostsService();
        $fresnsCommentService = new FresnsCommentsService();
        $checkInfo = AmChecker::checkSubmit($mid);
        if (is_array($checkInfo)) {
            FresnsSessionLogs::where('id', $logsId)->update(['object_result' => AmConfig::OBJECT_DEFAIL]);

            return $this->errorCheckInfo($checkInfo);
        }
        switch ($type) {
            case 1:
                // 判断是更新还是新增
                $draftPost = FresnsPostLogs::find($draftId);
                // $this->sendAtMessages(10,$draftId);
                if (! $draftPost['post_id']) {
                    // 验证新增权限
                    $createdCheck = AmChecker::checkPermission(1, 1, $user_id, $mid);
                    if (is_array($createdCheck)) {
                        FresnsSessionLogs::where('id', $logsId)->update(['object_result' => AmConfig::OBJECT_DEFAIL]);

                        return $this->errorCheckInfo($createdCheck);
                    }
                } else {
                    // 验证新增权限
                    $createdCheck = AmChecker::checkPermission(1, 2, $user_id, $mid, $draftPost['post_id']);
                    if (is_array($createdCheck)) {
                        FresnsSessionLogs::where('id', $logsId)->update(['object_result' => AmConfig::OBJECT_DEFAIL]);

                        return $this->errorCheckInfo($createdCheck);
                    }
                }
                // 是否需要审核
                if ($type == 1) {
                    $draft = FresnsPostLogs::find($draftId);
                } else {
                    $draft = FresnsCommentLogs::find($draftId);
                }
                $checkAudit = AmChecker::checkAudit($type, $mid, $draft['content']);
                if ($checkAudit) {
                    // 修改草稿状态为待审核 status，录入提交审核时间 submit_at，其他不动，待审核通过后再操作。
                    if ($type == 1) {
                        FresnsPostLogs::where('id', $draftId)->update([
                            'status' => 2,
                            'submit_at' => date('Y-m-d H:i:s', time()),
                        ]);
                    } else {
                        FresnsCommentLogs::where('id', $draftId)->update([
                            'status' => 2,
                            'submit_at' => date('Y-m-d H:i:s', time()),
                        ]);
                    }
                    $this->success();
                }
                // 调用发布
                $result = $FresnsPostsService->releaseByDraft($draftId, $logsId);
                break;
            case 2:
                // 判断是更新还是新增
                $draftComment = FresnsCommentLogs::find($draftId);
                // $this->sendAtMessages(10,$draftId);
                if (! $draftComment['comment_id']) {
                    // 验证新增权限
                    $createdCheck = AmChecker::checkPermission(2, 1, $user_id, $mid);
                    if (is_array($createdCheck)) {
                        FresnsSessionLogs::where('id', $logsId)->update(['object_result' => AmConfig::OBJECT_DEFAIL]);

                        return $this->errorCheckInfo($createdCheck);
                    }
                } else {
                    // 验证编辑权限
                    $createdCheck = AmChecker::checkPermission(2, 2, $user_id, $mid, $draftComment['comment_id']);
                    if (is_array($createdCheck)) {
                        FresnsSessionLogs::where('id', $logsId)->update(['object_result' => AmConfig::OBJECT_DEFAIL]);

                        return $this->errorCheckInfo($createdCheck);
                    }
                }
                // 是否需要审核
                if ($type == 1) {
                    $draft = FresnsPostLogs::find($draftId);
                } else {
                    $draft = FresnsCommentLogs::find($draftId);
                }
                $checkAudit = AmChecker::checkAudit($type, $mid, $draft['content']);
                if ($checkAudit) {
                    // 修改草稿状态为待审核 status，录入提交审核时间 submit_at，其他不动，待审核通过后再操作。
                    if ($type == 1) {
                        FresnsPostLogs::where('id', $draftId)->update([
                            'status' => 2,
                            'submit_at' => date('Y-m-d H:i:s', time()),
                        ]);
                    } else {
                        FresnsCommentLogs::where('id', $draftId)->update([
                            'status' => 2,
                            'submit_at' => date('Y-m-d H:i:s', time()),
                        ]);
                    }
                    $this->success();
                }
                $result = $fresnsCommentService->releaseByDraft($draftId, 0, $logsId);

                break;
        }
        $this->success();
    }

    //上传文件
    public function upload(Request $request)
    {
        $rule = [
            'type' => 'required|in:1,2,3,4',
            'tableType' => 'required',
            'tableName' => 'required',
            'tableField' => 'required',
            'mode' => 'required|in:1,2',
        ];
        ValidateService::validateRule($request, $rule);
        $type = $request->input('type');
        $mode = $request->input('mode');
        $tableId = $request->input('tableId');
        $tableKey = $request->input('tableKey');
        if ($mode == 2) {
            if (empty($tableId) && empty($tableKey)) {
                $input = [
                    '参数错误：' => 'tableId或tableKey至少填一项',
                ];
                $this->error(ErrorCodeService::CODE_PARAM_ERROR, $input);
            }
        }

        $memberId = GlobalService::getGlobalKey('member_id');

        $data = [];
        if ($mode == 1) {
            $type = $request->input('type');
            switch ($type) {
                case 1:
                    $unikey = ApiConfigHelper::getConfigByItemKey('images_service');
                    break;
                case 2:
                    $unikey = ApiConfigHelper::getConfigByItemKey('videos_service');
                    break;
                case 3:
                    $unikey = ApiConfigHelper::getConfigByItemKey('audios_service');
                    break;
                default:
                    $unikey = ApiConfigHelper::getConfigByItemKey('docs_service');
                    break;
            }
            $pluginUniKey = $unikey;

            // 执行上传
            $pluginClass = PluginHelper::findPluginClass($pluginUniKey);

            if (empty($pluginClass)) {
                LogService::error('未找到插件类');
                $this->error(ErrorCodeService::FILE_SALE_ERROR);
            }

            $isPlugin = PluginHelper::pluginCanUse($pluginUniKey);
            if ($isPlugin == false) {
                LogService::error('未找到插件类');
                $this->error(ErrorCodeService::DOWMLOAD_ERROR);
            }

            $file['file_type'] = $request->input('type', 1);
            $paramsExist = false;
            if ($file['file_type'] == FileSceneConfig::FILE_TYPE_1) {
                $configMapInDB = FresnsConfigs::whereIn('item_key', ['images_secret_id', 'images_secret_key', 'images_bucket_domain'])->pluck('item_value',
                    'item_key')->toArray();
                $paramsExist = ValidateService::validParamExist($configMapInDB,
                    ['images_secret_id', 'images_secret_key', 'images_bucket_domain']);
            }
            if ($file['file_type'] == FileSceneConfig::FILE_TYPE_2) {
                $configMapInDB = FresnsConfigs::whereIn('item_key', ['videos_secret_id', 'videos_secret_key', 'videos_bucket_domain'])->pluck('item_value',
                    'item_key')->toArray();

                $paramsExist = ValidateService::validParamExist($configMapInDB,
                    ['videos_secret_id', 'videos_secret_key', 'videos_bucket_domain']);
            }

            if ($file['file_type'] == FileSceneConfig::FILE_TYPE_3) {
                $configMapInDB = FresnsConfigs::whereIn('item_key', ['audios_secret_id', 'audios_secret_key', 'audios_bucket_domain'])->pluck('item_value',
                    'item_key')->toArray();
                $paramsExist = ValidateService::validParamExist($configMapInDB,
                    ['audios_secret_id', 'audios_secret_key', 'audios_bucket_domain']);
            }
            if ($file['file_type'] == FileSceneConfig::FILE_TYPE_4) {
                $configMapInDB = FresnsConfigs::whereIn('item_key', ['docs_secret_id', 'docs_secret_key', 'docs_bucket_domain'])->pluck('item_value',
                    'item_key')->toArray();
                $paramsExist = ValidateService::validParamExist($configMapInDB,
                    ['docs_secret_id', 'docs_secret_key', 'docs_bucket_domain']);
            }

            if ($paramsExist == false) {
                LogService::error('插件信息未配置');
                $this->error(ErrorCodeService::FILE_SALE_ERROR);
            }

            // 确认目录
            $options['file_type'] = $request->input('type');
            $options['table_type'] = $request->input('tableType');
            $storePath = FileSceneService::getEditorPath($options);

            if (! $storePath) {
                $this->error(ErrorCodeService::CODE_FAIL);
            }

            // 获取UploadFile的实例
            $uploadFile = $request->file('file');

            if (empty($uploadFile)) {
                $this->error(ErrorCodeService::FILES_ERROR);
            }

            // 存储
            $fileSize = $uploadFile->getSize();
            $suffix = $uploadFile->getClientOriginalExtension();
            $checker = AmChecker::checkUploadPermission($memberId, $type, $fileSize, $suffix);
            if ($checker !== true) {
                $this->error($checker);
            }

            LogService::info('文件存储本地成功 ', $file);
        } else {
            $fileInfo = $request->input('fileInfo');
            $isJson = StrHelper::isJson($fileInfo);
            if ($isJson == false) {
                $this->error(ErrorCodeService::FILES_INFO_ERROR);
            }
        }

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
        $input['platform'] = $request->header('platform');
        $input['uid'] = $request->header('uid');
        $input['mid'] = $request->header('mid');
        $resp = PluginRpcHelper::call(FresnsCmdFresnsPlugin::class, $cmd, $input);

        if (PluginRpcHelper::isErrorPluginResp($resp)) {
            $this->errorCheckInfo($resp);
        }

        $data = $resp['output'];

        $this->success($data);
    }

    //删除
    public function delete(Request $request)
    {
        $rule = [
            'type' => 'required|in:1,2',
            'logId' => 'required',
            'deleteType' => 'required|in:1,2,3',

        ];
        ValidateService::validateRule($request, $rule);

        $uid = GlobalService::getGlobalKey('user_id');
        $mid = GlobalService::getGlobalKey('member_id');
        $type = $request->input('type');
        $logId = $request->input('logId');
        $deleteType = $request->input('deleteType');

        //校验
        switch ($type) {
            case 1:
                $logs = FresnsPostLogs::where('id', $logId)->first();

                break;
            default:
                $logs = FresnsCommentLogs::where('id', $logId)->first();
                break;
        }

        if (empty($logs)) {
            if ($type == 1) {
                $this->error(ErrorCodeService::DELETE_FILE_ERROR);
            } else {
                $this->error(ErrorCodeService::DELETE_COMMENT_ERROR);
            }
        }

        if ($logs['member_id'] != $mid) {
            $this->error(ErrorCodeService::POSTS_USER_ERROR);
        }

        if ($deleteType == 2 || $deleteType == 3) {
            $rule = [
                'deleteUuid' => 'required',
            ];
            ValidateService::validateRule($request, $rule);
            $deleteUuid = $request->input('deleteUuid');
            if ($deleteType == 2) {
                $filesJson = json_decode($logs['files_json'], true);
                $filesIdArr = [];
                if (! empty($filesJson)) {
                    foreach ($filesJson as $v) {
                        $filesIdArr[] = $v['fid'];
                    }
                }
                if (! in_array($deleteUuid, $filesIdArr)) {
                    $this->error(ErrorCodeService::FILES_ERROR);
                }
            }

            if ($deleteType == 3) {
                $extendsJson = json_decode($logs['extends_json'], true);
                $eidArr = [];
                if (! empty($extendsJson)) {
                    foreach ($extendsJson as $v) {
                        $eidArr[] = $v['eid'];
                    }
                }

                if (! in_array($deleteUuid, $eidArr)) {
                    $this->error(ErrorCodeService::EXTEND_ERROR);
                }
            }
        }

        if ($logs['status'] == 3) {
            $this->error(ErrorCodeService::DELETED_ERROR);
        }

        $checkDelete = $this->service->deletePostComment($uid, $mid, $logs, $type);

        if ($checkDelete !== true) {
            $this->error($checkDelete);
        }

        $this->success();
    }

    //获取上传凭证
    public function uploadToken(Request $request)
    {
        $rule = [
            'type' => 'required|in:1,2,3,4',
            'mode' => 'required|in:1,2',
            'scene' => 'required|numeric|in:1,2,3,4,5,6,7,8,9,10,11',

        ];
        ValidateService::validateRule($request, $rule);

        $cmd = FresnsPluginConfig::PLG_CMD_GET_UPLOAD_TOKEN;
        $input['type'] = $request->input('type');
        $input['mode'] = $request->input('mode');
        $input['scene'] = $request->input('scene');

        $resp = PluginRpcHelper::call(FresnsCmdFresnsPlugin::class, $cmd, $input);
        if (PluginRpcHelper::isErrorPluginResp($resp)) {
            $this->errorCheckInfo($resp);
        }
        $output = $resp['output'];

        $data['storageId'] = $output['storageId'] ?? 1;
        $data['token'] = $output['token'] ?? '';
        $data['expireTime'] = DateHelper::asiaShanghaiToTimezone($output['expireTime']) ?? '';

        $this->success($data);
    }

    // 快速发表
    public function publish(Request $request)
    {
        $rule = [
            'type' => 'required|in:1,2',
            'content' => 'required',
            'isMarkdown' => 'required|in:0,1',
            'content' => 'required',
            'isAnonymous' => 'required | in:0,1',
        ];
        ValidateService::validateRule($request, $rule);
        $deviceInfo = $request->header('deviceInfo');
        $platform = $this->platform;
        $type = $request->input('type');
        // dd($platform);
        $uid = GlobalService::getGlobalKey('user_id');
        $member_id = GlobalService::getGlobalKey('member_id');
        $logsId = 0;
        if ($deviceInfo) {
            if ($type == 1) {
                $logsId = FresnsSessionLogsService::addSessionLogs($request->getRequestUri(), '发表帖子内容', $uid,
                    $member_id, null, '快速发表');
            } else {
                $logsId = FresnsSessionLogsService::addSessionLogs($request->getRequestUri(), '发表评论内容', $uid,
                    $member_id, null, '快速发表');
            }
        }
        LogService::Info('logsId', $logsId);
        $commentCid = $request->input('commentCid');
        $file = request()->file('file');

        $fileInfo = $request->input('fileInfo');
        $checkInfo = AmChecker::checkPublish($member_id);
        if (is_array($checkInfo)) {
            FresnsSessionLogs::where('id', $logsId)->update(['object_result' => AmConfig::OBJECT_DEFAIL]);

            return $this->errorCheckInfo($checkInfo);
        }
        if (! empty($file)) {
            $pluginUniKey = ApiConfigHelper::getConfigByItemKey('images_service');
            // 执行上传
            $pluginClass = PluginHelper::findPluginClass($pluginUniKey);

            if (empty($pluginClass)) {
                LogService::error('未找到插件类');
                FresnsSessionLogs::where('id', $logsId)->update(['object_result' => AmConfig::OBJECT_DEFAIL]);
                $this->error(ErrorCodeService::FILE_SALE_ERROR);
            }

            $isPlugin = PluginHelper::pluginCanUse($pluginUniKey);
            if ($isPlugin == false) {
                LogService::error('未找到插件类');
                $this->error(ErrorCodeService::DOWMLOAD_ERROR);
            }

            $paramsExist = false;

            $configMapInDB = FresnsConfigs::whereIn('item_key', ['images_secret_id', 'images_secret_key', 'images_bucket_domain'])->pluck('item_value',
                'item_key')->toArray();
            $paramsExist = ValidateService::validParamExist($configMapInDB,
                ['images_secret_id', 'images_secret_key', 'images_bucket_domain']);

            if ($paramsExist == false) {
                LogService::error('插件信息未配置');
                FresnsSessionLogs::where('id', $logsId)->update(['object_result' => AmConfig::OBJECT_DEFAIL]);
                $this->error(ErrorCodeService::FILE_SALE_ERROR);
            }
        }
        // dd($platform);

        // 如果是私有模式，当过期后 members > expired_at，该接口不可请求。
        $checker = AmChecker::checkPermission($type, 1, $uid, $member_id);
        if (is_array($checker)) {
            FresnsSessionLogs::where('id', $logsId)->update(['object_result' => AmConfig::OBJECT_DEFAIL]);

            return $this->errorCheckInfo($checker);
        }
        // dd($checker);
        $FresnsPostsService = new FresnsPostsService();
        $fresnsCommentService = new FresnsCommentsService();
        // 是否需要审核
        $checkAudit = AmChecker::checkAudit($type, $member_id, $request->input('content'));

        switch ($type) {
            case 1:
                $draftId = ContentLogsService::publishCreatedPost($request);
                if ($checkAudit) {
                    FresnsPostLogs::where('id', $draftId)->update([
                        'status' => 2,
                        'submit_at' => date('Y-m-d H:i:s', time()),
                    ]);
                    $this->success();
                }
                // 提交内容方法
                $FresnsPostsService->releaseByDraft($draftId, $logsId);
                break;
            default:
                if ($commentCid) {
                    $commentInfo = FresnsComments::where('uuid', $commentCid)->first();
                    $commentCid = $commentInfo['id'];
                }
                if (empty($commentCid)) {
                    $commentCid = 0;
                }
                $draftId = ContentLogsService::publishCreatedComment($request);
                if ($checkAudit) {
                    FresnsCommentLogs::where('id', $draftId)->update([
                        'status' => 2,
                        'submit_at' => date('Y-m-d H:i:s', time()),
                    ]);
                    $this->success();
                }
                $fresnsCommentService->releaseByDraft($draftId, $commentCid, $logsId);
                break;
        }
        $this->success();
    }

    // 撤回审核中帖子
    public function revoke(Request $request)
    {
        $rule = [
            'type' => 'required|in:1,2',
            'logId' => 'required',
        ];
        ValidateService::validateRule($request, $rule);

        $type = $request->input('type');
        $logId = $request->input('logId');
        // dd($logId);
        // 帖子
        if ($type == 1) {
            $postLogs = FresnsPostLogs::find($logId);
            if (! $postLogs) {
                $this->error(ErrorCodeService::POSTS_LOGS_EXISTS_ERROR);
            }
            if ($postLogs['status'] != 2) {
                $this->error(ErrorCodeService::POST_REMOKE_ERROR);
            }
            //  dd($postLogs);
            FresnsPostLogs::where('id', $logId)->update(['status' => 1, 'submit_at' => null]);
        } else {
            // 评论
            $commentLogs = FresnsCommentLogs::find($logId);
            if (! $commentLogs) {
                $this->error(ErrorCodeService::COMMENT_LOGS_EXISTS_ERROR);
            }
            if ($commentLogs['status'] != 2) {
                $this->error(ErrorCodeService::COMMENT_REMOKE_ERROR);
            }
            FresnsCommentLogs::where('id', $logId)->update(['status' => 1, 'submit_at' => null]);
        }
        $this->success();
    }
}
