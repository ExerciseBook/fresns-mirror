<?php
/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Traits;

use App\Http\FresnsApi\Helpers\ApiLanguageHelper;
use App\Http\FresnsDb\FresnsCodeMessages\FresnsCodeMessagesConfig;
use App\Http\FresnsDb\FresnsCodeMessages\FresnsCodeMessagesService;
use App\Http\FresnsDb\FresnsSessionLogs\FresnsSessionLogsService;
use App\Http\Center\AmGlobal\GlobalService;
use App\Http\Center\Common\ErrorCodeService;
use App\Http\Center\Common\LogService;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;

trait ApiTrait
{
    protected $statusCode = FoundationResponse::HTTP_OK;
    protected $errorCode = 0;
    protected $errorMsg = 0;
    protected $data = null;

    public function success($data = [], $header = [])
    {
        $sessionLogId = GlobalService::getGlobalSessionKey('session_log_id');
        if ($sessionLogId) {
            FresnsSessionLogsService::updateSessionLogs($sessionLogId, 2);
        }
        $plugin = FresnsCodeMessagesConfig::ERROR_CODE_DEFAULT_PLUGIN;
        $langTag = ApiLanguageHelper::getLangTag();
        $message = FresnsCodeMessagesService::getCodeMessage($plugin, $langTag, ErrorCodeService::CODE_OK);
        if (empty($message)) {
            $message = ErrorCodeService::getMsg($this->errorCode);
        }

        $this->errorCode = ErrorCodeService::CODE_OK;
        $this->errorMsg = $message;
        $this->data = $data;
        $this->respond($header);
    }

    public function error($code, $data = [], $header = [])
    {
        $sessionLogId = GlobalService::getGlobalSessionKey('session_log_id');
        if ($sessionLogId) {
            FresnsSessionLogsService::updateSessionLogs($sessionLogId, 1);
        }
        $plugin = FresnsCodeMessagesConfig::ERROR_CODE_DEFAULT_PLUGIN;
        $langTag = ApiLanguageHelper::getLangTag();

        $message = FresnsCodeMessagesService::getCodeMessage($plugin, $langTag, $code);
        if (empty($message)) {
            $message = ErrorCodeService::getMsg($code, $data);
        }

        $this->errorCode = $code;
        $this->errorMsg = $message;
        $this->data = $data;
        $this->respond($header);
    }

    public function errorInfo($code, $msg, $header = [], $data = [])
    {
        // $data = ['info' => 'error'];
        $sessionLogId = GlobalService::getGlobalSessionKey('session_log_id');
        if ($sessionLogId) {
            FresnsSessionLogsService::updateSessionLogs($sessionLogId, 1);
        }
        $plugin = FresnsCodeMessagesConfig::ERROR_CODE_DEFAULT_PLUGIN;
        $langTag = ApiLanguageHelper::getLangTag();

        $message = FresnsCodeMessagesService::getCodeMessage($plugin, $langTag, $code);
        if (empty($message)) {
            $message = empty($msg) ? ErrorCodeService::getMsg($code, $data) : $msg;
        }
        $this->errorCode = $code;
        $this->errorMsg = $message;
        $this->data = $data;
        $this->respond($header);
    }

    public function errorCheckInfo($checkInfo, $header = [], $data = [])
    {
        // $data = ['info' => 'error'];
        $sessionLogId = GlobalService::getGlobalSessionKey('session_log_id');
        if ($sessionLogId) {
            FresnsSessionLogsService::updateSessionLogs($sessionLogId, 1);
        }
        // dd($checkInfo);
        $plugin = FresnsCodeMessagesConfig::ERROR_CODE_DEFAULT_PLUGIN;
        $langTag = ApiLanguageHelper::getLangTag();

        $message = FresnsCodeMessagesService::getCodeMessage($plugin, $langTag, $checkInfo['code']);
        if (empty($message)) {
            $message = $checkInfo['msg'] ?? $checkInfo['message'];
        }
        $this->errorCode = $checkInfo['code'] ?? $checkInfo['code'];
        $this->errorMsg = $message;
        $this->data = $data;

        // 补充
        if (isset($checkInfo['data'])) {
            $this->data = $checkInfo['data'];
        }

        $this->respond($header);
    }

    public function exceptionError($code, $data = [], $header = [])
    {
        $plugin = FresnsCodeMessagesConfig::ERROR_CODE_DEFAULT_PLUGIN;
        $langTag = ApiLanguageHelper::getLangTag();

        $message = FresnsCodeMessagesService::getCodeMessage($plugin, $langTag, $code);
        if (empty($message)) {
            $message = ErrorCodeService::getMsg($code);
        }
        $this->errorCode = $code;
        $this->errorMsg = $message;
        $this->data = $data;
        $this->respond($header);
    }

    public function respond($header = [])
    {
        $data['code'] = $this->errorCode;
        $data['message'] = $this->errorMsg;
        $data['data'] = $this->data;

        $request = request();
        $requestAll = $request->all();

        LogService::info('requestData is', $requestAll);
        JsonResponse::create($data, $this->getStatusCode(), $header)->send();
        exit;
    }

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }
}
