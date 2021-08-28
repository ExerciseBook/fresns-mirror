<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\Notify;

use App\Base\Resources\BaseAdminResource;
use App\Http\Center\AmGlobal\GlobalService;
use App\Http\FresnsApi\Content\AmConfig as ContentConfig;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsApi\Helpers\ApiFileHelper;
use App\Http\FresnsDb\FresnsDialogMessages\FresnsDialogMessagesConfig;
use App\Http\FresnsDb\FresnsDialogs\FresnsDialogs;
use App\Http\FresnsDb\FresnsDialogs\FresnsDialogsConfig;
use App\Http\FresnsDb\FresnsMembers\FresnsMembers;
use App\Http\FresnsDb\FresnsMembers\FresnsMembersConfig;
use Illuminate\Support\Facades\DB;

class DialogsMessageResource extends BaseAdminResource
{
    public function toArray($request)
    {
        // dd(1);
        // form 字段
        $formMap = FresnsDialogMessagesConfig::FORM_FIELDS_MAP;
        $formMapFieldsArr = [];
        foreach ($formMap as $k => $dbField) {
            $formMapFieldsArr[$dbField] = $this->$dbField;
        }
        // $mid = request()->header('mid');
        $mid = GlobalService::getGlobalKey('member_id');
        // $memberInfo = TweetMembers::find($mid);
        $memberInfo = DB::table(FresnsMembersConfig::CFG_TABLE)->where('id', $this->send_member_id)->first();
        $messageArr = [];
        $sendDeactivate = true;
        $sendMid = $this->send_member_id;
        if ($memberInfo) {
            if ($memberInfo->deleted_at != null) {
                $sendMid = '';
                $sendDeactivate = false;
            }
        } else {
            $sendMid = '';
            $sendDeactivate = false;
        }
        $sendMemberInfo = FresnsMembers::find($sendMid);

        if ($this->message_text) {
            $messageArr['messageId'] = $this->id;
            $messageArr['isMe'] = $this->send_member_id == $mid ? 1 : 2;
            $messageArr['type'] = 1;
            $messageArr['content'] = $this->message_text;
            $messageArr['sendDeactivate'] = $sendDeactivate;
            $messageArr['sendMid'] = $sendMemberInfo['uuid'] ?? '';
            $messageArr['sendAvatar'] = $memberInfo->avatar_file_url ?? '';

            // 为空用默认头像
            if (empty($messageArr['sendAvatar'])) {
                $defaultIcon = ApiConfigHelper::getConfigByItemKey(ContentConfig::DEFAULT_AVATAR);
                $messageArr['sendAvatar'] = $defaultIcon;
            }
            // 已注销头像 deactivate_avatar 键值"
            if ($memberInfo) {
                if ($memberInfo->deleted_at != null) {
                    $deactivateAvatar = ApiConfigHelper::getConfigByItemKey(ContentConfig::DEACTIVATE_AVATAR);
                    $messageArr['sendAvatar'] = $deactivateAvatar;
                }
            } else {
                $deactivateAvatar = ApiConfigHelper::getConfigByItemKey(ContentConfig::DEACTIVATE_AVATAR);
                $messageArr['sendAvatar'] = $deactivateAvatar;
            }

            $messageArr['sendAvatar'] = ApiFileHelper::getImageSignUrl($messageArr['sendAvatar']);
            $messageArr['sendTime'] = $this->created_at;
        }
        $fileInfo = [];
        if ($this->file_id) {
            $fileInfo = ApiFileHelper::getFileInfo($this->id, $this->file_id, $mid);
            // dd($fileInfo);
        }
        // if($this->file_id){
        //     $fileInfo = TweetFiles::find($this->file_id);
        //     $fileAppend = TweetFileAppends::findAppend('file_id',$this->file_id);
        //     $fileArr['messageId'] =  $this->id;
        //     $fileArr['isMe'] =  $this->send_member_id == $mid ? true : false;
        //     $fileArr['type'] =  "附件消息";
        //     $file['fileId'] = $this->file_id;
        //     $file['fileType'] = $fileInfo['file_type'];
        //     $file['fileName'] = $fileInfo['file_name'];
        //     $file['fileExtension'] = $fileInfo['file_extension'];
        //     $file['fileSize'] = $fileAppend['file_extension'];
        //     $file['imageWidth'] = $fileAppend['image_width'];
        //     $file['imageHeight'] = $fileAppend['image_height'];
        //     $file['imageLong'] = $fileInfo['image_is_long'];
        //     $file['imageThumbUrl'] = "";
        //     $file['imageSquareUrl'] = "";
        //     $file['imageBigUrl'] = "";
        //     $file['videoTime'] = $fileInfo['video_time'];
        //     $file['videoCover'] = $fileInfo['video_cover'];
        //     $file['videoGif'] = $fileInfo['video_gif'];
        //     $file['videoUrl'] = "";
        //     $file['audioTime'] = $fileInfo['audio_time'];
        //     $file['audioUrl'] = "";
        //     $file['docPreviewUrl'] = "";
        //     $file['docUrl'] = "";
        //     $file['moreJson'] = [];
        //     $fileArr['file'] = $file;
        // }
        // 默认字段
        if ($messageArr) {
            $default = $messageArr;
        } else {
            $default = $fileInfo;
        }
        // 合并
        // $arr = $default;

        return $default;
    }
}
