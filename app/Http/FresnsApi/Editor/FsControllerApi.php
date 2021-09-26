<?php

/*
 * Fresns (https://fresns.cn)
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

class FsControllerApi extends FresnsBaseApiController
{
    public function __construct()
    {
        $this->service = new FsService();
        $this->checkRequest();
        $this->initData();
        parent::__construct();
    }

    // Create a new log
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
        if ($deviceInfo) {
            if ($type == 1) {
                $logsId = FresnsSessionLogsService::addSessionLogs($request->getRequestUri(), 'Create draft post', $user_id, $mid, null, 'Create a new draft',11);
            } else {
                $logsId = FresnsSessionLogsService::addSessionLogs($request->getRequestUri(), 'Create draft comment', $user_id, $mid, null, 'Create a new draft',12);
            }
        }

        // In case of private mode, this feature is not available when it expires (members > expired_at).
        $checkInfo = FsChecker::checkCreate($mid);
        if (is_array($checkInfo)) {
            FresnsSessionLogs::where('id', $logsId)->update(['object_result' => FsConfig::OBJECT_DEFAIL]);

            return $this->errorCheckInfo($checkInfo);
        }

        // type = 1.post / 2.comment
        $type = $request->input('type');
        // Empty means create a blank log, with a value means edit existing content
        $uuid = $request->input('uuid', '');
        // type=2 / Dedicated，Indicates a comment under that post
        $pid = $request->input('pid', '');
        switch ($type) {
            // type=1
            case '1':
                // uuid=Empty
                // Create blank logs without quantity checking, post logs can have more than one.
                if (empty($uuid)) {
                    // Verify added permissions
                    $createdCheck = FsChecker::checkPermission($type, 1, $user_id, $mid);
                    if (is_array($createdCheck)) {
                        FresnsSessionLogs::where('id', $logsId)->update(['object_result' => FsConfig::OBJECT_DEFAIL]);

                        return $this->errorCheckInfo($createdCheck);
                    }

                    $postInput = [
                        'member_id' => $mid,
                        'platform_id' => $platform,
                    ];
                    $postLogId = DB::table('post_logs')->insertGetId($postInput);
                } else {
                    // uuid=valuable
                    // Check status=1, 2, 4 for the presence of the post ID log.
                    $postInfo = FresnsPosts::where('uuid', $uuid)->first();
                    // Verify editing privileges
                    $createdCheck = FsChecker::checkPermission($type, 2, $user_id, $mid, $postInfo['id']);
                    if (is_array($createdCheck)) {
                        FresnsSessionLogs::where('id', $logsId)->update(['object_result' => FsConfig::OBJECT_DEFAIL]);

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
                    'object_result' => FsConfig::OBJECT_SUCCESS,
                    'object_order_id' => $postLogId,
                ]);
                $FresnsPostLogsService = new FresnsPostLogsService();
                $request->offsetSet('id', $postLogId);
                $request->offsetUnset('type');
                $FresnsPostLogsService->setResource(FresnsPostLogsResourceDetail::class);
                $list = $FresnsPostLogsService->searchData();
                break;
            // type=2
            default:
                // uuid=Empty
                // means create a blank log, the pid must be filled, check if the pid exists for the log comment.
                if (empty($uuid)) {
                    if (empty($pid)) {
                        $this->errorInfo(ErrorCodeService::MEMBER_FAIL, ['info' => 'pid required']);
                    }
                    // Verify added permissions
                    $createdCheck = FsChecker::checkPermission($type, 1, $user_id, $mid);
                    if (is_array($createdCheck)) {
                        return $this->errorCheckInfo($createdCheck);
                    }
                    $postInfo = FresnsPosts::where('uuid', $pid)->first();
                    $commentLog = FresnsCommentLogs::where('member_id', $mid)->where('post_id', $postInfo['id'])->where('status', '!=', 3)->first();
                    // Exists and cannot be recreated. (Only one log comment on the same post returns directly to the current log details).
                    if ($commentLog) {
                        $commentLogId = $commentLog['id'];
                    } else {
                        // Does not exist, create a new log.
                        $commentLogInput = [
                            'member_id' => $mid,
                            'post_id' => $postInfo['id'],
                            'platform_id' => $platform,
                        ];
                        $commentLogId = DB::table('comment_logs')->insertGetId($commentLogInput);
                    }
                } else {
                    // uuid=valuable
                    // Check status=1, 2, 4 for the presence of this comment ID log.
                    $commentInfo = FresnsComments::where('uuid', $uuid)->first();
                    // Verify editing privileges
                    $createdCheck = FsChecker::checkPermission($type, 2, $user_id, $mid, $commentInfo['id']);
                    if (is_array($createdCheck)) {
                        return $this->errorCheckInfo($createdCheck);
                    }
                    $commentLog = FresnsCommentLogs::where('comment_id', $commentInfo['id'])->where('member_id',
                        $mid)->where('status', '!=', 3)->first();
                    if (! $commentLog) {
                        $commentLogId = ContentLogsService::commentLogInsert($uuid, $mid);
                    } else {
                        $commentLogId = $commentLog['id'];
                    }
                }
                FresnsSessionLogs::where('id', $logsId)->update([
                    'object_result' => FsConfig::OBJECT_SUCCESS,
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

    // Get log details
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

    // Get log list
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
            $page = $request->input('page', 1);
            $pageSize = $request->input('pageSize', 30);
            $FresnsPostLogsService = new FresnsPostLogsService();
            $request->offsetUnset('type');
            $request->offsetSet('member_id', $mid);
            $request->offsetSet('currentPage', $page);
            $request->offsetSet('pageSize', $pageSize);
            $FresnsPostLogsService->setResource(FresnsPostLogsResource::class);
            $list = $FresnsPostLogsService->searchData();
        } else {
            if (! empty($class)) {
                if ($class == 1) {
                    $idArr = FresnsCommentLogs::where('comment_id', null)->pluck('id')->toArray();
                } else {
                    $idArr = FresnsCommentLogs::where('comment_id', '!=', null)->pluck('id')->toArray();
                }
                $request->offsetSet('ids', implode(',', $idArr));
            }
            $page = $request->input('page', 1);
            $pageSize = $request->input('pageSize', 30);
            $FresnsCommentLogsService = new FresnsCommentLogsService();
            $request->offsetUnset('type');
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

    // update log
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
        $logType = $request->input('logType');
        $logId = $request->input('logId');
        $checkInfo = FsChecker::checkDrast($mid);
        if (is_array($checkInfo)) {
            return $this->errorCheckInfo($checkInfo);
        }
        if ($logType == 1) {
            ContentLogsService::updatePostLog($mid);
        } else {
            ContentLogsService::updateCommentLog($mid);
        }
        $this->success();
    }

    // submit log
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
        $logsId = 0;
        $mid = GlobalService::getGlobalKey('member_id');
        $user_id = GlobalService::getGlobalKey('user_id');
        if ($deviceInfo) {
            if ($type == 1) {
                $logsId = FresnsSessionLogsService::addSessionLogs("App\Http\FresnsDb\FresnsPosts", 'Publish Post Content', $user_id, $mid, null, 'Officially Published Post Content',13);
            } else {
                $logsId = FresnsSessionLogsService::addSessionLogs("App\Http\FresnsDb\FresnsComments", 'Publish Comment Content', $user_id, $mid, null, 'Officially Published Comment Content',14);
            }
        }
        $type = $request->input('type');
        $draftId = $request->input('logId');
        $FresnsPostsService = new FresnsPostsService();
        $fresnsCommentService = new FresnsCommentsService();
        $checkInfo = FsChecker::checkSubmit($mid);
        if (is_array($checkInfo)) {
            FresnsSessionLogs::where('id', $logsId)->update(['object_result' => FsConfig::OBJECT_DEFAIL]);

            return $this->errorCheckInfo($checkInfo);
        }
        switch ($type) {
            case 1:
                // Determine if it is an update or a new addition
                $draftPost = FresnsPostLogs::find($draftId);
                if (! $draftPost['post_id']) {
                    // Verify added permissions
                    $createdCheck = FsChecker::checkPermission(1, 1, $user_id, $mid);
                    if (is_array($createdCheck)) {
                        FresnsSessionLogs::where('id', $logsId)->update(['object_result' => FsConfig::OBJECT_DEFAIL]);

                        return $this->errorCheckInfo($createdCheck);
                    }
                } else {
                    // Verify added permissions
                    $createdCheck = FsChecker::checkPermission(1, 2, $user_id, $mid, $draftPost['post_id']);
                    if (is_array($createdCheck)) {
                        FresnsSessionLogs::where('id', $logsId)->update(['object_result' => FsConfig::OBJECT_DEFAIL]);

                        return $this->errorCheckInfo($createdCheck);
                    }
                }
                // Determine if review is required
                if ($type == 1) {
                    $draft = FresnsPostLogs::find($draftId);
                } else {
                    $draft = FresnsCommentLogs::find($draftId);
                }
                $checkAudit = FsChecker::checkAudit($type, $mid, $draft['content']);
                if ($checkAudit) {
                    // Need to review: modify the log status to be reviewed (status), enter the time to submit the review (submit_at), do not move the other, and then operate after the review is passed.
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
                // Call Release
                $result = $FresnsPostsService->releaseByDraft($draftId, $logsId);
                break;
            case 2:
                // Determine if it is an update or a new addition
                $draftComment = FresnsCommentLogs::find($draftId);
                if (! $draftComment['comment_id']) {
                    // Verify added permissions
                    $createdCheck = FsChecker::checkPermission(2, 1, $user_id, $mid);
                    if (is_array($createdCheck)) {
                        FresnsSessionLogs::where('id', $logsId)->update(['object_result' => FsConfig::OBJECT_DEFAIL]);

                        return $this->errorCheckInfo($createdCheck);
                    }
                } else {
                    // Verify editing privileges
                    $createdCheck = FsChecker::checkPermission(2, 2, $user_id, $mid, $draftComment['comment_id']);
                    if (is_array($createdCheck)) {
                        FresnsSessionLogs::where('id', $logsId)->update(['object_result' => FsConfig::OBJECT_DEFAIL]);

                        return $this->errorCheckInfo($createdCheck);
                    }
                }
                // Determine if review is required
                if ($type == 1) {
                    $draft = FresnsPostLogs::find($draftId);
                } else {
                    $draft = FresnsCommentLogs::find($draftId);
                }
                $checkAudit = FsChecker::checkAudit($type, $mid, $draft['content']);
                if ($checkAudit) {
                    // Need to review: modify the log status to be reviewed (status), enter the time to submit the review (submit_at), do not move the other, and then operate after the review is passed.
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

    // Fast Publishing
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
        $uid = GlobalService::getGlobalKey('user_id');
        $member_id = GlobalService::getGlobalKey('member_id');
        $logsId = 0;
        if ($deviceInfo) {
            if ($type == 1) {
                $logsId = FresnsSessionLogsService::addSessionLogs($request->getRequestUri(), 'Publish Post Content', $uid, $member_id, null, 'Officially Published Post Content',13);
            } else {
                $logsId = FresnsSessionLogsService::addSessionLogs($request->getRequestUri(), 'Publish Comment Content', $uid, $member_id, null, 'Officially Published Comment Content',14);
            }
        }
        LogService::Info('logsId', $logsId);
        $commentCid = $request->input('commentCid');
        $file = request()->file('file');

        $fileInfo = $request->input('fileInfo');
        $checkInfo = FsChecker::checkPublish($member_id);
        dd($checkInfo);
        if (is_array($checkInfo)) {
            FresnsSessionLogs::where('id', $logsId)->update(['object_result' => FsConfig::OBJECT_DEFAIL]);

            return $this->errorCheckInfo($checkInfo);
        }
        if (! empty($file)) {
            $pluginUniKey = ApiConfigHelper::getConfigByItemKey('images_service');
            // Perform Upload
            $pluginClass = PluginHelper::findPluginClass($pluginUniKey);

            if (empty($pluginClass)) {
                LogService::error('Plugin not found');
                FresnsSessionLogs::where('id', $logsId)->update(['object_result' => FsConfig::OBJECT_DEFAIL]);
                $this->error(ErrorCodeService::PLUGINS_CONFIG_ERROR);
            }

            $isPlugin = PluginHelper::pluginCanUse($pluginUniKey);
            if ($isPlugin == false) {
                LogService::error('Plugin not found');
                $this->error(ErrorCodeService::PLUGINS_CLASS_ERROR);
            }

            $paramsExist = false;

            $configMapInDB = FresnsConfigs::whereIn('item_key', ['images_secret_id', 'images_secret_key', 'images_bucket_domain'])->pluck('item_value',
                'item_key')->toArray();
            $paramsExist = ValidateService::validParamExist($configMapInDB,
                ['images_secret_id', 'images_secret_key', 'images_bucket_domain']);

            if ($paramsExist == false) {
                LogService::error('插件信息未配置');
                FresnsSessionLogs::where('id', $logsId)->update(['object_result' => FsConfig::OBJECT_DEFAIL]);
                $this->error(ErrorCodeService::PLUGINS_CONFIG_ERROR);
            }
        }

        // In case of private mode, this feature is not available when it expires (members > expired_at).
        $checker = FsChecker::checkPermission($type, 1, $uid, $member_id);
        if (is_array($checker)) {
            FresnsSessionLogs::where('id', $logsId)->update(['object_result' => FsConfig::OBJECT_DEFAIL]);

            return $this->errorCheckInfo($checker);
        }
        $FresnsPostsService = new FresnsPostsService();
        $fresnsCommentService = new FresnsCommentsService();
        // Determine if review is required
        $checkAudit = FsChecker::checkAudit($type, $member_id, $request->input('content'));

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
                // Call Release
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

    // Upload File
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
                    'Parameter Error: ' => 'Fill in at least one of tableId or tableKey',
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

            // Perform Upload
            $pluginClass = PluginHelper::findPluginClass($pluginUniKey);

            if (empty($pluginClass)) {
                LogService::error('Plugin not found');
                $this->error(ErrorCodeService::PLUGINS_CONFIG_ERROR);
            }

            $isPlugin = PluginHelper::pluginCanUse($pluginUniKey);
            if ($isPlugin == false) {
                LogService::error('Plugin not found');
                $this->error(ErrorCodeService::PLUGINS_CLASS_ERROR);
            }

            $file['file_type'] = $request->input('type', 1);
            $paramsExist = false;
            // Image
            if ($file['file_type'] == FileSceneConfig::FILE_TYPE_1) {
                $configMapInDB = FresnsConfigs::whereIn('item_key', ['images_secret_id', 'images_secret_key', 'images_bucket_domain'])->pluck('item_value', 'item_key')->toArray();
                $paramsExist = ValidateService::validParamExist($configMapInDB, ['images_secret_id', 'images_secret_key', 'images_bucket_domain']);
            }
            // Video
            if ($file['file_type'] == FileSceneConfig::FILE_TYPE_2) {
                $configMapInDB = FresnsConfigs::whereIn('item_key', ['videos_secret_id', 'videos_secret_key', 'videos_bucket_domain'])->pluck('item_value', 'item_key')->toArray();
                $paramsExist = ValidateService::validParamExist($configMapInDB, ['videos_secret_id', 'videos_secret_key', 'videos_bucket_domain']);
            }
            // Audio
            if ($file['file_type'] == FileSceneConfig::FILE_TYPE_3) {
                $configMapInDB = FresnsConfigs::whereIn('item_key', ['audios_secret_id', 'audios_secret_key', 'audios_bucket_domain'])->pluck('item_value', 'item_key')->toArray();
                $paramsExist = ValidateService::validParamExist($configMapInDB, ['audios_secret_id', 'audios_secret_key', 'audios_bucket_domain']);
            }
            // Doc
            if ($file['file_type'] == FileSceneConfig::FILE_TYPE_4) {
                $configMapInDB = FresnsConfigs::whereIn('item_key', ['docs_secret_id', 'docs_secret_key', 'docs_bucket_domain'])->pluck('item_value', 'item_key')->toArray();
                $paramsExist = ValidateService::validParamExist($configMapInDB, ['docs_secret_id', 'docs_secret_key', 'docs_bucket_domain']);
            }

            if ($paramsExist == false) {
                LogService::error('Please configure the storage information first');
                $this->error(ErrorCodeService::PLUGINS_CONFIG_ERROR);
            }

            // Confirm Catalog
            $options['file_type'] = $request->input('type');
            $options['table_type'] = $request->input('tableType');
            $storePath = FileSceneService::getEditorPath($options);

            if (! $storePath) {
                $this->error(ErrorCodeService::MEMBER_FAIL);
            }

            // Get an instance of UploadFile
            $uploadFile = $request->file('file');

            if (empty($uploadFile)) {
                $this->error(ErrorCodeService::FILE_EXIST_ERROR);
            }

            // Storage
            $fileSize = $uploadFile->getSize();
            $suffix = $uploadFile->getClientOriginalExtension();
            $checker = FsChecker::checkUploadPermission($memberId, $type, $fileSize, $suffix);
            if ($checker !== true) {
                $this->error($checker);
            }

            LogService::info('File Storage Local Success ', $file);
        } else {
            $fileInfo = $request->input('fileInfo');
            $isJson = StrHelper::isJson($fileInfo);
            if ($isJson == false) {
                $this->error(ErrorCodeService::FILE_INFO_JSON_ERROR);
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

    // Get Upload Token
    public function uploadToken(Request $request)
    {
        $rule = [
            'type' => 'required|in:1,2,3,4',
            'scene' => 'required|numeric|in:1,2,3,4,5,6,7,8,9,10,11',
        ];
        ValidateService::validateRule($request, $rule);

        $cmd = FresnsPluginConfig::PLG_CMD_GET_UPLOAD_TOKEN;
        $input['type'] = $request->input('type');
        $input['scene'] = $request->input('scene');

        $resp = PluginRpcHelper::call(FresnsCmdFresnsPlugin::class, $cmd, $input);
        if (PluginRpcHelper::isErrorPluginResp($resp)) {
            $this->errorCheckInfo($resp);
        }
        $output = $resp['output'];

        $data['storageId'] = $output['storageId'] ?? 1;
        $data['token'] = $output['token'] ?? '';
        $data['expireTime'] = DateHelper::fresnsOutputTimeToTimezone($output['expireTime']) ?? '';

        $this->success($data);
    }

    // Editor Delete
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

        // Check
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
                $this->error(ErrorCodeService::DELETE_POST_ERROR);
            } else {
                $this->error(ErrorCodeService::DELETE_COMMENT_ERROR);
            }
        }

        if ($logs['member_id'] != $mid) {
            $this->error(ErrorCodeService::CONTENT_AUTHOR_ERROR);
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
                    $this->error(ErrorCodeService::FILE_EXIST_ERROR);
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
                    $this->error(ErrorCodeService::EXTEND_EXIST_ERROR);
                }
            }
        }

        if ($logs['status'] == 3) {
            $this->error(ErrorCodeService::DELETE_CONTENT_ERROR);
        }

        $checkDelete = $this->service->deletePostComment($uid, $mid, $logs, $type);

        if ($checkDelete !== true) {
            $this->error($checkDelete);
        }

        $this->success();
    }

    // Withdraw content under review
    public function revoke(Request $request)
    {
        $rule = [
            'type' => 'required|in:1,2',
            'logId' => 'required',
        ];
        ValidateService::validateRule($request, $rule);

        $type = $request->input('type');
        $logId = $request->input('logId');
        // Post
        if ($type == 1) {
            $postLogs = FresnsPostLogs::find($logId);
            if (! $postLogs) {
                $this->error(ErrorCodeService::POST_LOG_EXIST_ERROR);
            }
            if ($postLogs['status'] != 2) {
                $this->error(ErrorCodeService::POST_REMOKE_ERROR);
            }
            FresnsPostLogs::where('id', $logId)->update(['status' => 1, 'submit_at' => null]);
        } else {
            // comment
            $commentLogs = FresnsCommentLogs::find($logId);
            if (! $commentLogs) {
                $this->error(ErrorCodeService::COMMENT_LOG_EXIST_ERROR);
            }
            if ($commentLogs['status'] != 2) {
                $this->error(ErrorCodeService::COMMENT_REMOKE_ERROR);
            }
            FresnsCommentLogs::where('id', $logId)->update(['status' => 1, 'submit_at' => null]);
        }
        $this->success();
    }
}
