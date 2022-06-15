<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\Controllers;

use App\Exceptions\ApiException;
use App\Fresns\Api\Http\DTO\InteractiveDTO;
use App\Fresns\Api\Http\DTO\UserAuthDTO;
use App\Fresns\Api\Http\DTO\UserEditDTO;
use App\Fresns\Api\Http\DTO\UserListDTO;
use App\Fresns\Api\Http\DTO\UserMarkDTO;
use App\Fresns\Api\Http\DTO\UserMarkListDTO;
use App\Fresns\Api\Http\DTO\UserMarkNoteDTO;
use App\Fresns\Api\Services\InteractiveService;
use App\Fresns\Api\Services\UserService;
use App\Helpers\CacheHelper;
use App\Helpers\ConfigHelper;
use App\Helpers\PrimaryHelper;
use App\Models\BlockWord;
use App\Models\CommentLog;
use App\Models\Dialog;
use App\Models\DialogMessage;
use App\Models\DomainLinkLinked;
use App\Models\HashtagLinked;
use App\Models\Mention;
use App\Models\Notify;
use App\Models\PluginUsage;
use App\Models\PostLog;
use App\Models\Seo;
use App\Models\User;
use App\Models\UserBlock;
use App\Models\UserFollow;
use App\Models\UserStat;
use App\Utilities\ContentUtility;
use App\Utilities\ExtendUtility;
use App\Utilities\InteractiveUtility;
use App\Utilities\ValidationUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    // list
    public function list(Request $request)
    {
        $dtoRequest = new UserListDTO($request->all());

        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUserId = $this->user()?->id;

        $userQuery = UserStat::with('user');

        $userQuery->when($dtoRequest->verified, function ($query, $value) {
            $query->whereRelation('user', 'verified', $value);
        });

        $userQuery->when($dtoRequest->gender, function ($query, $value) {
            $query->whereRelation('user', 'gender', $value);
        });

        if ($dtoRequest->createDateGt) {
            $userQuery->whereDate('created_at', '>=', $dtoRequest->createDateGt);
        }

        if ($dtoRequest->createDateLt) {
            $userQuery->whereDate('created_at', '<=', $dtoRequest->createDateLt);
        }

        if ($dtoRequest->likeCountGt) {
            $userQuery->where('like_me_count', '>=', $dtoRequest->likeCountGt);
        }

        if ($dtoRequest->likeCountLt) {
            $userQuery->where('like_me_count', '<=', $dtoRequest->likeCountLt);
        }

        if ($dtoRequest->dislikeCountGt) {
            $userQuery->where('dislike_me_count', '>=', $dtoRequest->dislikeCountGt);
        }

        if ($dtoRequest->dislikeCountLt) {
            $userQuery->where('dislike_me_count', '<=', $dtoRequest->dislikeCountLt);
        }

        if ($dtoRequest->followCountGt) {
            $userQuery->where('follow_me_count', '>=', $dtoRequest->followCountGt);
        }

        if ($dtoRequest->followCountLt) {
            $userQuery->where('follow_me_count', '<=', $dtoRequest->followCountLt);
        }

        if ($dtoRequest->blockCountGt) {
            $userQuery->where('block_me_count', '>=', $dtoRequest->blockCountGt);
        }

        if ($dtoRequest->blockCountLt) {
            $userQuery->where('block_me_count', '<=', $dtoRequest->blockCountLt);
        }

        if ($dtoRequest->postCountGt) {
            $userQuery->where('post_publish_count', '>=', $dtoRequest->postCountGt);
        }

        if ($dtoRequest->postCountLt) {
            $userQuery->where('post_publish_count', '<=', $dtoRequest->postCountLt);
        }

        if ($dtoRequest->commentCountGt) {
            $userQuery->where('comment_publish_count', '>=', $dtoRequest->commentCountGt);
        }

        if ($dtoRequest->commentCountLt) {
            $userQuery->where('comment_publish_count', '<=', $dtoRequest->commentCountLt);
        }

        if ($dtoRequest->postDigestCountGt) {
            $userQuery->where('post_digest_count', '>=', $dtoRequest->postDigestCountGt);
        }

        if ($dtoRequest->postDigestCountLt) {
            $userQuery->where('post_digest_count', '<=', $dtoRequest->postDigestCountLt);
        }

        if ($dtoRequest->commentDigestCountGt) {
            $userQuery->where('comment_digest_count', '>=', $dtoRequest->commentDigestCountGt);
        }

        if ($dtoRequest->commentDigestCountLt) {
            $userQuery->where('comment_digest_count', '<=', $dtoRequest->commentDigestCountLt);
        }

        if ($dtoRequest->extcredits1CountGt) {
            $userQuery->where('extcredits1', '>=', $dtoRequest->extcredits1CountGt);
        }

        if ($dtoRequest->extcredits1CountLt) {
            $userQuery->where('extcredits1', '<=', $dtoRequest->extcredits1CountLt);
        }

        if ($dtoRequest->extcredits2CountGt) {
            $userQuery->where('extcredits2', '>=', $dtoRequest->extcredits2CountGt);
        }

        if ($dtoRequest->extcredits2CountLt) {
            $userQuery->where('extcredits2', '<=', $dtoRequest->extcredits2CountLt);
        }

        if ($dtoRequest->extcredits3CountGt) {
            $userQuery->where('extcredits3', '>=', $dtoRequest->extcredits3CountGt);
        }

        if ($dtoRequest->extcredits3CountLt) {
            $userQuery->where('extcredits3', '<=', $dtoRequest->extcredits3CountLt);
        }

        if ($dtoRequest->extcredits4CountGt) {
            $userQuery->where('extcredits4', '>=', $dtoRequest->extcredits4CountGt);
        }

        if ($dtoRequest->extcredits4CountLt) {
            $userQuery->where('extcredits4', '<=', $dtoRequest->extcredits4CountLt);
        }

        if ($dtoRequest->extcredits5CountGt) {
            $userQuery->where('extcredits5', '>=', $dtoRequest->extcredits5CountGt);
        }

        if ($dtoRequest->extcredits5CountLt) {
            $userQuery->where('extcredits5', '<=', $dtoRequest->extcredits5CountLt);
        }

        $orderType = match ($dtoRequest->orderType) {
            default => 'created_at',
            'like' => 'like_me_count',
            'dislike' => 'dislike_me_count',
            'follow' => 'follow_me_count',
            'block' => 'block_me_count',
            'post' => 'post_publish_count',
            'comment' => 'comment_publish_count',
            'postDigest' => 'post_digest_count',
            'commentDigest' => 'comment_digest_count',
            'extcredits1' => 'extcredits1',
            'extcredits2' => 'extcredits2',
            'extcredits3' => 'extcredits3',
            'extcredits4' => 'extcredits4',
            'extcredits5' => 'extcredits5',
            'createDate' => 'created_at',
        };

        $orderDirection = match ($dtoRequest->orderDirection) {
            default => 'desc',
            'asc' => 'asc',
            'desc' => 'desc',
        };

        $userQuery->orderBy($orderType, $orderDirection);

        $userData = $userQuery->paginate($request->get('pageSize', 15));

        $userList = [];
        $service = new UserService();
        foreach ($userData as $user) {
            $userList[] = $service->userList($user->user, $langTag, $timezone, $authUserId);
        }

        return $this->fresnsPaginate($userList, $userData->total(), $userData->perPage());
    }

    // detail
    public function detail(string $uidOrUsername)
    {
        if (is_int($uidOrUsername)) {
            $viewUser = User::where('uid', $uidOrUsername)->first();
        } else {
            $viewUser = User::where('username', $uidOrUsername)->first();
        }

        if (empty($viewUser)) {
            throw new ApiException(31602);
        }

        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUserId = $this->user()?->id;

        $seoData = Seo::where('linked_type', Seo::TYPE_USER)->where('linked_id', $viewUser->id)->where('lang_tag', $langTag)->first();

        $item['title'] = $seoData->title ?? null;
        $item['keywords'] = $seoData->keywords ?? null;
        $item['description'] = $seoData->description ?? null;
        $item['manages'] = ExtendUtility::getPluginExtends(PluginUsage::TYPE_MANAGE, null, PluginUsage::SCENE_USER, $authUserId, $langTag);
        $data['items'] = $item;

        $service = new UserService();
        $data['detail'] = $service->userDetail($viewUser, $langTag, $timezone, $authUserId);

        return $this->success($data);
    }

    // interactive
    public function interactive(string $uidOrUsername, string $type, Request $request)
    {
        if (is_int($uidOrUsername)) {
            $viewUser = User::where('uid', $uidOrUsername)->first();
        } else {
            $viewUser = User::where('username', $uidOrUsername)->first();
        }

        if (empty($viewUser)) {
            throw new ApiException(31602);
        }

        $requestData = $request->all();
        $requestData['type'] = $type;
        $dtoRequest = new InteractiveDTO($requestData);

        $orderDirection = $dtoRequest->orderDirection ?: 'desc';

        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUserId = $this->user()?->id;

        if ($viewUser->id == $authUserId) {
            InteractiveService::checkMyInteractiveSetting($dtoRequest->type, 'user');
        } else {
            InteractiveService::checkInteractiveSetting($dtoRequest->type, 'user');
        }

        $service = new InteractiveService();
        $data = $service->getUsersWhoMarkIt($dtoRequest->type, InteractiveService::TYPE_USER, $viewUser->id, $orderDirection, $langTag, $timezone, $authUserId);

        return $this->fresnsPaginate($data['paginateData'], $data['interactiveData']->total(), $data['interactiveData']->perPage());
    }

    // markList
    public function markList(string $uidOrUsername, string $markType, string $listType, Request $request)
    {
        if (is_int($uidOrUsername)) {
            $viewUser = User::where('uid', $uidOrUsername)->first();
        } else {
            $viewUser = User::where('username', $uidOrUsername)->first();
        }

        if (empty($viewUser)) {
            throw new ApiException(31602);
        }

        $requestData = $request->all();
        $requestData['markType'] = $markType;
        $requestData['listType'] = $listType;
        $dtoRequest = new UserMarkListDTO($requestData);

        $markSet = ConfigHelper::fresnsConfigByItemKey("it_{$dtoRequest->markType}_{$dtoRequest->listType}");
        if (! $markSet) {
            throw new ApiException(36201);
        }

        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUserId = $this->user()?->id;

        $orderDirection = $dtoRequest->orderDirection ?: 'desc';

        $service = new InteractiveService();
        $data = $service->getItMarkList($dtoRequest->markType, $dtoRequest->listType, $viewUser->id, $orderDirection, $langTag, $timezone, $authUserId);

        return $this->fresnsPaginate($data['paginateData'], $data['markData']->total(), $data['markData']->perPage());
    }

    // auth
    public function auth(Request $request)
    {
        $dtoRequest = new UserAuthDTO($request->all());

        if (is_int($dtoRequest->uidOrUsername)) {
            $authUser = User::where('uid', $dtoRequest->uidOrUsername)->first();
        } else {
            $authUser = User::where('username', $dtoRequest->uidOrUsername)->first();
        }

        if (empty($authUser)) {
            throw new ApiException(31602);
        }

        $langTag = $this->langTag();
        $timezone = $this->timezone();

        $password = base64_decode($dtoRequest->password, true);

        // login
        $wordBody = [
            'aid' => $request->header('aid'),
            'uid' => $authUser->uid,
            'password' => $password,
        ];
        $fresnsResponse = \FresnsCmdWord::plugin('Fresns')->verifyUser($wordBody);

        if ($fresnsResponse->isErrorResponse()) {
            return $fresnsResponse->errorResponse();
        }

        // create token
        $createTokenWordBody = [
            'platformId' => $request->header('platformId'),
            'aid' => $fresnsResponse->getData('aid'),
            'uid' => $fresnsResponse->getData('uid'),
            'expiredTime' => null,
        ];
        $fresnsTokenResponse = \FresnsCmdWord::plugin('Fresns')->createSessionToken($createTokenWordBody);

        if ($fresnsTokenResponse->isErrorResponse()) {
            return $fresnsTokenResponse->errorResponse();
        }

        // get user token
        $token['token'] = $fresnsTokenResponse->getData('token');
        $token['expiredTime'] = $fresnsTokenResponse->getData('expiredTime');
        $data['sessionToken'] = $token;

        // get user data
        $service = new UserService();
        $data['detail'] = $service->userDetail($authUser, $langTag, $timezone);

        return $this->success($data);
    }

    // panel
    public function panel()
    {
        $langTag = $this->langTag();
        $authUserId = $this->user()->id;

        $data['features'] = ExtendUtility::getPluginExtends(PluginUsage::TYPE_FEATURE, null, null, $authUserId, $langTag);
        $data['profiles'] = ExtendUtility::getPluginExtends(PluginUsage::TYPE_PROFILE, null, null, $authUserId, $langTag);

        $dialogACount = Dialog::where('a_user_id', $authUserId)->where('a_is_read', 0)->where('a_is_display', 1)->count();
        $dialogBCount = Dialog::where('b_user_id', $authUserId)->where('b_is_read', 0)->where('b_is_display', 1)->count();
        $dialogMessageCount = DialogMessage::where('receive_user_id', $authUserId)->whereNull('receive_read_at')->whereNull('receive_deleted_at')->isEnable()->count();
        $dialogUnread['dialog'] = $dialogACount + $dialogBCount;
        $dialogUnread['message'] = $dialogMessageCount;
        $data['dialogUnread'] = $dialogUnread;

        $notify = Notify::where('user_id', $authUserId)->where('is_read', 0);
        $notifyUnread['system'] = $notify->where('action_type', 1)->count();
        $notifyUnread['follow'] = $notify->where('action_type', 2)->count();
        $notifyUnread['like'] = $notify->where('action_type', 3)->count();
        $notifyUnread['comment'] = $notify->where('action_type', 4)->count();
        $notifyUnread['mention'] = $notify->where('action_type', 5)->count();
        $notifyUnread['recommend'] = $notify->where('action_type', 6)->count();
        $data['notifyUnread'] = $notifyUnread;

        $draftCount['posts'] = PostLog::where('user_id', $authUserId)->whereIn('state', [1, 4])->count();
        $draftCount['comments'] = CommentLog::where('user_id', $authUserId)->whereIn('state', [1, 4])->count();
        $data['draftCount'] = $draftCount;

        return $this->success($data);
    }

    // edit
    public function edit(Request $request)
    {
        $dtoRequest = new UserEditDTO($request->all());

        $authUser = $this->user();

        if ($dtoRequest->avatarFid && $dtoRequest->avatarUrl) {
            throw new ApiException(30005);
        }

        if ($dtoRequest->bannerFid && $dtoRequest->bannerUrl) {
            throw new ApiException(30005);
        }

        $editNameConfig = ConfigHelper::fresnsConfigByItemKeys([
            'username_edit',
            'nickname_edit',
        ]);

        // edit username
        if ($dtoRequest->username) {
            $isEmpty = Str::of($dtoRequest->username)->trim()->isEmpty();
            if ($isEmpty) {
                throw new ApiException(35102);
            }

            $nextEditUsernameTime = $authUser->last_username_at?->addDays($editNameConfig['username_edit']);

            if (now() < $nextEditUsernameTime) {
                throw new ApiException(35101);
            }

            $username = Str::of($dtoRequest->username)->trim();
            $validateUsername = ValidationUtility::username($username);

            if (! $validateUsername['formatString'] || ! $validateUsername['formatHyphen'] || ! $validateUsername['formatNumeric']) {
                throw new ApiException(35102);
            }

            if (! $validateUsername['minLength']) {
                throw new ApiException(35103);
            }

            if (! $validateUsername['maxLength']) {
                throw new ApiException(35104);
            }

            if (! $validateUsername['use']) {
                throw new ApiException(35105);
            }

            if (! $validateUsername['banName']) {
                throw new ApiException(35106);
            }

            $authUser->update([
                'username' => $username,
                'last_username_at' => now(),
            ]);
        }

        // edit nickname
        if ($dtoRequest->nickname) {
            $isEmpty = Str::of($dtoRequest->nickname)->trim()->isEmpty();
            if ($isEmpty) {
                throw new ApiException(35107);
            }

            $nextEditNicknameTime = $authUser->last_nickname_at?->addDays($editNameConfig['nickname_edit']);

            if (now() < $nextEditNicknameTime) {
                throw new ApiException(35101);
            }

            $nickname = Str::of($dtoRequest->nickname)->trim();

            $validateNickname = ValidationUtility::nickname($nickname);

            if (! $validateNickname['formatString'] || ! $validateUsername['formatSpace']) {
                throw new ApiException(35107);
            }

            if (! $validateNickname['minLength']) {
                throw new ApiException(35108);
            }

            if (! $validateNickname['maxLength']) {
                throw new ApiException(35109);
            }

            if (! $validateNickname['banName']) {
                throw new ApiException(35110);
            }

            $blockWords = BlockWord::where('user_mode', 2)->get('word', 'replace_word');

            $newNickname = str_ireplace($blockWords->pluck('word')->toArray(), $blockWords->pluck('replace_word')->toArray(), $nickname);

            $authUser->update([
                'nickname' => $newNickname,
                'last_nickname_at' => now(),
            ]);
        }

        // edit avatarFid
        if ($dtoRequest->avatarFid) {
            $fileId = PrimaryHelper::fresnsFileIdByFid($dtoRequest->avatarFid);

            $authUser->update([
                'avatar_file_id' => $fileId,
                'avatar_file_url' => null,
            ]);
        }

        // edit avatarUrl
        if ($dtoRequest->avatarUrl) {
            $authUser->update([
                'avatar_file_id' => null,
                'avatar_file_url' => $dtoRequest->avatarUrl,
            ]);
        }

        // edit bannerFid
        if ($dtoRequest->bannerFid) {
            $fileId = PrimaryHelper::fresnsFileIdByFid($dtoRequest->bannerFid);

            $authUser->update([
                'banner_file_id' => $fileId,
                'banner_file_url' => null,
            ]);
        }

        // edit bannerUrl
        if ($dtoRequest->bannerUrl) {
            $authUser->update([
                'banner_file_id' => null,
                'banner_file_url' => $dtoRequest->bannerUrl,
            ]);
        }

        // edit gender
        if ($dtoRequest->gender) {
            $authUser->update([
                'gender' => $dtoRequest->gender,
            ]);
        }

        // edit birthday
        if ($dtoRequest->birthday) {
            $authUser->update([
                'birthday' => $dtoRequest->birthday,
            ]);
        }

        // edit bio
        if ($dtoRequest->bio) {
            $bio = Str::of($dtoRequest->bio)->trim();

            $validateBio = ValidationUtility::bio($bio);

            if (! $validateBio['banWord']) {
                throw new ApiException(33106);
            }

            if (! $validateBio['length']) {
                throw new ApiException(33107);
            }

            $blockWords = BlockWord::where('user_mode', 2)->get('word', 'replace_word');

            $newBio = str_ireplace($blockWords->pluck('word')->toArray(), $blockWords->pluck('replace_word')->toArray(), $bio);

            $bioConfig = ConfigHelper::fresnsConfigByItemKeys([
                'bio_support_mention',
                'bio_support_link',
                'bio_support_hashtag',
            ]);

            if ($bioConfig['bio_support_mention']) {
                ContentUtility::saveMention($newBio, Mention::TYPE_USER, $authUser->id, $authUser->id);
            }

            if ($bioConfig['bio_support_link']) {
                ContentUtility::saveLink($newBio, DomainLinkLinked::TYPE_USER, $authUser->id);
            }

            if ($bioConfig['bio_support_hashtag']) {
                ContentUtility::saveHashtag($newBio, HashtagLinked::TYPE_USER, $authUser->id);
            }

            $authUser->update([
                'gender' => $newBio,
            ]);
        }

        // edit location
        if ($dtoRequest->location) {
            $location = Str::of($dtoRequest->location)->trim();
            $authUser->update([
                'location' => $location,
            ]);
        }

        // edit dialogLimit
        if ($dtoRequest->dialogLimit) {
            $authUser->update([
                'dialog_limit' => $dtoRequest->dialogLimit,
            ]);
        }

        // edit commentLimit
        if ($dtoRequest->commentLimit) {
            $authUser->update([
                'comment_limit' => $dtoRequest->commentLimit,
            ]);
        }

        // edit timezone
        if ($dtoRequest->timezone) {
            $authUser->update([
                'timezone' => $dtoRequest->timezone,
            ]);
        }

        CacheHelper::forgetApiUser($authUser->uid);

        return $this->success();
    }

    // mark
    public function mark(Request $request)
    {
        $dtoRequest = new UserMarkDTO($request->all());

        $markSet = ConfigHelper::fresnsConfigByItemKey("{$dtoRequest->interactiveType}_{$dtoRequest->markType}_setting");
        if (! $markSet) {
            throw new ApiException(36200);
        }

        $primaryId = PrimaryHelper::fresnsPrimaryId($dtoRequest->markType, $dtoRequest->fsid);
        if (empty($primaryId)) {
            throw new ApiException(32201);
        }

        $authUserId = $this->user()->id;

        $markType = match ($dtoRequest->markType) {
            'user' => 1,
            'group' => 2,
            'hashtag' => 3,
            'post' => 4,
            'comment' => 5,
        };

        switch ($dtoRequest->interactiveType) {
            // like
            case 'like':
                InteractiveUtility::markUserLike($authUserId, $markType, $primaryId);
            break;

            // dislike
            case 'dislike':
                InteractiveUtility::markUserDislike($authUserId, $markType, $primaryId);
            break;

            // follow
            case 'follow':
                $validMark = ValidationUtility::userMarkOwn($authUserId, $markType, $primaryId);
                if (! $validMark) {
                    throw new ApiException(36202);
                }

                InteractiveUtility::markUserFollow($authUserId, $markType, $primaryId);
            break;

            // block
            case 'block':
                $validMark = ValidationUtility::userMarkOwn($authUserId, $markType, $primaryId);
                if (! $validMark) {
                    throw new ApiException(36202);
                }

                InteractiveUtility::markUserBlock($authUserId, $markType, $primaryId);
            break;
        }

        return $this->success();
    }

    // mark note
    public function markNote(Request $request)
    {
        $dtoRequest = new UserMarkNoteDTO($request->all());

        $primaryId = PrimaryHelper::fresnsPrimaryId($dtoRequest->markType, $dtoRequest->fsid);
        if (empty($primaryId)) {
            throw new ApiException(32201);
        }

        $authUserId = $this->user()->id;

        $markType = match ($dtoRequest->markType) {
            'user' => 1,
            'group' => 2,
            'hashtag' => 3,
            'post' => 4,
            'comment' => 5,
        };

        switch ($dtoRequest->interactiveType) {
            // follow
            case 'follow':
                $userNote = UserFollow::withTrashed()->where('user_id', $authUserId)->type($markType)->where('follow_id', $primaryId)->first();
            break;

            // block
            case 'block':
                $userNote = UserBlock::withTrashed()->where('user_id', $authUserId)->type($markType)->where('block_id', $primaryId)->first();
            break;
        }

        if (empty($dtoRequest->note)) {
            $userNote->update([
                'user_note' => null,
            ]);
        }

        $userNote->update([
            'user_note' => $dtoRequest->note,
        ]);

        return $this->success();
    }
}
