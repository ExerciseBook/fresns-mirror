<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\Content;

use App\Helpers\StrHelper;
use App\Http\Center\Base\BasePluginConfig;
use App\Http\Center\Helper\PluginHelper;
use App\Http\Center\Helper\PluginRpcHelper;
use App\Http\Center\Scene\FileSceneService;
use App\Http\FresnsApi\Base\FresnsBaseApiController;
use App\Http\FresnsApi\Content\Resource\CommentResource;
use App\Http\FresnsApi\Content\Resource\CommentResourceDetail;
use App\Http\FresnsApi\Content\Resource\FresnsExtendsResource;
use App\Http\FresnsApi\Content\Resource\FresnsPostResource;
use App\Http\FresnsApi\Content\Resource\FresnsPostResourceDetail;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsApi\Info\AmService;
use App\Http\FresnsCmds\FresnsPlugin;
use App\Http\FresnsCmds\FresnsPluginConfig;
use App\Http\FresnsCmds\FresnsSubPlugin;
use App\Http\FresnsCmds\FresnsSubPluginConfig;
use App\Http\FresnsDb\FresnsCommentAppends\FresnsCommentAppendsConfig;
use App\Http\FresnsDb\FresnsComments\FresnsComments;
use App\Http\FresnsDb\FresnsComments\FresnsCommentsConfig;
use App\Http\FresnsDb\FresnsComments\FresnsCommentsService;
use App\Http\FresnsDb\FresnsDownloads\FresnsDownloadsService;
use App\Http\FresnsDb\FresnsExtendLinkeds\FresnsExtendLinkedsConfig;
use App\Http\FresnsDb\FresnsExtends\FresnsExtendsService;
use App\Http\FresnsDb\FresnsFiles\FresnsFiles;
use App\Http\FresnsDb\FresnsGroups\FresnsGroups;
use App\Http\FresnsDb\FresnsGroups\FresnsGroupsConfig;
use App\Http\FresnsDb\FresnsGroups\FresnsGroupsService;
use App\Http\FresnsDb\FresnsHashtagLinkeds\FresnsHashtagLinkeds;
use App\Http\FresnsDb\FresnsHashtagLinkeds\FresnsHashtagLinkedsConfig;
use App\Http\FresnsDb\FresnsHashtags\FresnsHashtags;
use App\Http\FresnsDb\FresnsHashtags\FresnsHashtagsConfig;
use App\Http\FresnsDb\FresnsHashtags\FresnsHashtagsService;
use App\Http\FresnsDb\FresnsImplants\FresnsImplantsService;
use App\Http\FresnsDb\FresnsMemberFollows\FresnsMemberFollows;
use App\Http\FresnsDb\FresnsMemberFollows\FresnsMemberFollowsConfig;
use App\Http\FresnsDb\FresnsMemberLikes\FresnsMemberLikes;
use App\Http\FresnsDb\FresnsMemberLikes\FresnsMemberLikesService;
use App\Http\FresnsDb\FresnsMemberRoleRels\FresnsMemberRoleRels;
use App\Http\FresnsDb\FresnsMembers\FresnsMembers;
use App\Http\FresnsDb\FresnsMemberShields\FresnsMemberShields;
use App\Http\FresnsDb\FresnsMemberShields\FresnsMemberShieldsConfig;
use App\Http\FresnsDb\FresnsPluginBadges\FresnsPluginBadges;
use App\Http\FresnsDb\FresnsPlugins\FresnsPlugins as pluginUnikey;
use App\Http\FresnsDb\FresnsPluginUsages\FresnsPluginUsages;
use App\Http\FresnsDb\FresnsPluginUsages\FresnsPluginUsagesService;
use App\Http\FresnsDb\FresnsPostMembers\FresnsPostMembersService;
use App\Http\FresnsDb\FresnsPosts\FresnsPosts;
use App\Http\FresnsDb\FresnsPosts\FresnsPostsConfig;
use App\Http\FresnsDb\FresnsPosts\FresnsPostsService;
use App\Http\Center\AmGlobal\GlobalService;
use App\Http\Center\Common\ErrorCodeService;
use App\Http\Center\Common\LogService;
use App\Http\Center\Common\ValidateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AmControllerApi extends FresnsBaseApiController
{
    // public function __construct()
    // {
    //     $fresnsMemberFollowsModel = new FresnsMemberFollows();
    //     $fresnsMemberFollowsModel->hasDeletedAt = false;
    //     parent::__construct();
    // }
    // 获取小组[树结构列表]
    public function trees(Request $request)
    {
        $site_mode = ApiConfigHelper::getConfigByItemKey(AmConfig::SITE_MODEL);
        if ($site_mode == AmConfig::PRIVATE) {
            $uid = $this->uid;
            $member_id = $this->mid;
            $uid = $this->uid;
            if (empty($uid)) {
                $this->error(ErrorCodeService::USER_REQUIRED_ERROR);
            }
            if (empty($member_id)) {
                $this->error(ErrorCodeService::MEMBER_REQUIRED_ERROR);
            }
        }
        $mid = GlobalService::getGlobalKey('member_id');
        // $follow = $request->input('follow',"");
        // 如果是非公开小组的帖子，不是小组成员，不输出。
        $FresnsGroups = FresnsGroups::where('type_mode', 2)->where('type_find', 2)->pluck('id')->toArray();
        // $noGroupArr = FresnsMemberFollows::where('member_id',$mid)->where('follow_type',2)->pluck('follow_id')->toArray();
        $noGroupArr = DB::table(FresnsMemberFollowsConfig::CFG_TABLE)->where('member_id', $mid)->where('follow_type',
            2)->pluck('follow_id')->toArray();
        $groupArr = FresnsGroups::whereNotIn('id', $noGroupArr)->where('parent_id', null)->pluck('id')->toArray();
        $ids = implode(',', $groupArr);
        $request->offsetSet('ids', $ids);
        // if($mid){
        //     // 不需要关注获取的小组
        //     $noFollowGroupIdArr1 = FresnsGroups::where('type_find',2)->where('type_mode',2)->pluck('id')->toArray();
        //     $noFollowGroupIdArr1 = FresnsGroups::whereNotIn('id',$noFollowGroupIdArr1)->pluck('id')->toArray();
        //     // $noFollowGroupIdArr2 = FresnsGroups::where('type_mode',2)->where('parent_id',null)->where('type_find',1)->pluck('id')->toArray();
        //     // 查询需要关注才能获取的小组
        //     $groupIdArr = FresnsGroups::where('type_mode',2)->where('type_find',2)->where('parent_id',null)->pluck('id')->toArray();
        //     $memberGroupArr = FresnsMemberFollows::where('member_id',$mid)->where('follow_type',2)->whereIn('follow_id',$groupIdArr)->pluck('follow_id')->toArray();
        //     // dd($memberGroupArr);
        //     $noFollowGroupIdArr = array_merge($noFollowGroupIdArr1,$memberGroupArr);
        //     $ids = implode(',',$noFollowGroupIdArr);
        //     $request->offsetSet('ids',$ids);
        //     // if($follow == 'true'){
        //     //     // 获取已经关注的小组
        //     //    $groupArr = FresnsMemberFollows::where('member_id',$mid)->where('follow_type',2)->pluck('follow_id')->toArray();
        //     //    $ids = implode(',',$groupArr);
        //     //    $request->offsetSet('ids',$ids);
        //     // }
        // }else{
        //     // 查询type_find 模式为“不可发现”
        //     $groupArr = FresnsGroups::where('type_find','!=',2)->where('parent_id',null)->pluck('id')->toArray();
        //     $ids = implode(',',$groupArr);
        //     $request->offsetSet('ids',$ids);
        // }
        // dd($ids);
        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 30);
        $FresnsDialogsService = new FresnsGroupsService();
        $request->offsetSet('currentPage', $page);
        $request->offsetSet('pageSize', $pageSize);
        $FresnsDialogsService->setResource(FresnsGroupTreeResource::class);
        $data = $FresnsDialogsService->searchData();
        $data = [
            'pagination' => $data['pagination'],
            'list' => $data['list'],
        ];
        $this->success($data);
    }

    // 获取小组【列表】
    public function group_lists(Request $request)
    {
        // dd(1);
        $rule = [
            'type' => 'required|in:1,2',
            'createdTimeGt' => 'date_format:"Y-m-d H:i:s"',
            'createdTimeLt' => 'date_format:"Y-m-d H:i:s"',
        ];
        ValidateService::validateRule($request, $rule);
        $site_mode = ApiConfigHelper::getConfigByItemKey(AmConfig::SITE_MODEL);
        if ($site_mode == AmConfig::PRIVATE) {
            $uid = $this->uid;
            $member_id = $this->mid;
            $uid = $this->uid;
            if (empty($uid)) {
                $this->error(ErrorCodeService::USER_REQUIRED_ERROR);
            }
            if (empty($member_id)) {
                $this->error(ErrorCodeService::MEMBER_REQUIRED_ERROR);
            }
        }
        $mid = GlobalService::getGlobalKey('member_id');
        // 如果是非公开小组的帖子，不是小组成员，不输出。
        $FresnsGroups = FresnsGroups::where('type_mode', 2)->where('type_find', 2)->pluck('id')->toArray();
        // $groupMember = FresnsMemberFollows::where('member_id',$mid)->where('follow_type',2)->pluck('follow_id')->toArray();
        $groupMember = DB::table(FresnsMemberFollowsConfig::CFG_TABLE)->where('member_id', $mid)->where('follow_type',
            2)->pluck('follow_id')->toArray();
        $noGroupArr = array_diff($FresnsGroups, $groupMember);

        $groupArr = FresnsGroups::whereNotIn('id', $noGroupArr)->pluck('id')->toArray();
        $ids = implode(',', $groupArr);
        $request->offsetSet('ids', $ids);
        // dd($ids);
        // $follow = $request->input('follow',"");
        // if($mid){
        //     // 不需要关注获取的小组
        //     $noFollowGroupIdArr1 = FresnsGroups::where('type_find',2)->where('type_mode',2)->pluck('id')->toArray();
        //     $noFollowGroupIdArr1 = FresnsGroups::whereNotIn('id',$noFollowGroupIdArr1)->pluck('id')->toArray();
        //     // $noFollowGroupIdArr2 = FresnsGroups::where('type_mode',2)->where('type_find',1)->pluck('id')->toArray();
        //     // 查询需要关注才能获取的小组
        //     $groupIdArr = FresnsGroups::where('type_mode',2)->where('type_find',2)->pluck('id')->toArray();
        //     $memberGroupArr = FresnsMemberFollows::where('member_id',$mid)->where('follow_type',2)->whereIn('follow_id',$groupIdArr)->pluck('follow_id')->toArray();
        //     // dd($memberGroupArr);
        //     $noFollowGroupIdArr = array_merge($noFollowGroupIdArr1,$memberGroupArr);
        //     $ids = implode(',',$noFollowGroupIdArr);
        //     $request->offsetSet('ids',$ids);
        //     // if($follow == 'true'){
        //     //     // 获取已经关注的小组
        //     //    $groupArr = FresnsMemberFollows::where('member_id',$mid)->where('follow_type',2)->pluck('follow_id')->toArray();
        //     //    $ids = implode(',',$groupArr);
        //     //    $request->offsetSet('ids',$ids);
        //     // }
        // }else{
        //     // 查询type_find 模式为“不可发现”
        //     $groupArr = FresnsGroups::where('type_find',2)->where('type_mode',2)->pluck('id')->toArray();
        //     $groupArr = FresnsGroups::whereNotIn('id',$groupArr)->pluck('id')->toArray();
        //     $ids = implode(',',$groupArr);
        //     $request->offsetSet('ids',$ids);
        // }
        $parentId = $request->input('parentGId');
        if ($parentId) {
            $group = FresnsGroups::where('uuid', $parentId)->first();
            // dump($group['id']);
            if ($group) {
                $request->offsetSet('pid', $group['id']);
            } else {
                $request->offsetSet('pid', 0);
            }
        }
        // dd($request);
        // dd(1);
        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 30);
        $FresnsDialogsService = new FresnsGroupsService();
        // dd($FresnsDialogsService);
        $request->offsetSet('currentPage', $page);
        $request->offsetSet('pageSize', $pageSize);
        $FresnsDialogsService->setResource(FresnsGroupResource::class);
        $data = $FresnsDialogsService->searchData();
        $data = [
            'pagination' => $data['pagination'],
            'list' => $data['list'],
        ];
        $this->success($data);
    }

    // 获取小组【单条】
    public function group_detail(Request $request)
    {
        $table = FresnsGroupsConfig::CFG_TABLE;
        $rule = [
            'gid' => "required|exists:{$table},uuid",
        ];
        $site_mode = ApiConfigHelper::getConfigByItemKey(AmConfig::SITE_MODEL);
        if ($site_mode == AmConfig::PRIVATE) {
            $uid = $this->uid;
            $member_id = $this->mid;
            $uid = $this->uid;
            if (empty($uid)) {
                $this->error(ErrorCodeService::USER_REQUIRED_ERROR);
            }
            if (empty($member_id)) {
                $this->error(ErrorCodeService::MEMBER_REQUIRED_ERROR);
            }
        }
        $mid = GlobalService::getGlobalKey('member_id');
        $langTag = $this->langTag;
        $mid = $this->mid;
        ValidateService::validateRule($request, $rule);
        $id = $request->input('gid');
        // dd(1);
        $FresnsGroupsService = new FresnsGroupsService();
        // dd($FresnsGroupsService);
        $request->offsetSet('gid', $id);
        $FresnsGroupsService->setResourceDetail(FresnsGroupResourceDetail::class);
        // $data = $FresnsGroupsService->searchData();
        $group = FresnsGroups::where('uuid', $id)->first();
        $detail = $FresnsGroupsService->detail($group['id']);
        // $data = [
        //     // 'pagination' => $data['pagination'],
        //     'detail' => $data['list'],
        //     'common' => $data['common']
        // ];
        $this->success($detail);
    }

    // 获取帖子【列表】
    public function post_lists(Request $request)
    {
        $rule = [
            'searchEssence' => 'in:1,2,3',
            'searchSticky' => 'in:1,2,3',
            'createdTimeGt' => 'date_format:"Y-m-d H:i:s"',
            'createdTimeLt' => 'date_format:"Y-m-d H:i:s"',
            'viewCountGt' => 'numeric',
            'viewCountLt' => 'numeric',
            'likeCountGt' => 'numeric',
            'likeCountLt' => 'numeric',
            'followCountGt' => 'numeric',
            'followCountLt' => 'numeric',
            'shieldCountGt' => 'numeric',
            'shieldCountLt' => 'numeric',
            'commentCountGt' => 'numeric',
            'commentCountLt' => 'numeric',
        ];
        ValidateService::validateRule($request, $rule);
        // $fresnsMemberFollowsModel = new FresnsMemberFollows();
        // $fresnsMemberFollowsModel->hasDeletedAt = false;
        // 未登录，私有模式 不输出
        $site_mode = ApiConfigHelper::getConfigByItemKey(AmConfig::SITE_MODEL);
        if ($site_mode == AmConfig::PRIVATE) {
            $uid = $this->uid;
            $member_id = $this->mid;
            $uid = $this->uid;
            if (empty($uid)) {
                $this->error(ErrorCodeService::USER_REQUIRED_ERROR);
            }
            if (empty($member_id)) {
                $this->error(ErrorCodeService::MEMBER_REQUIRED_ERROR);
            }
        }
        $mid = GlobalService::getGlobalKey('member_id');
        // 是否为插件返回数据
        $sortNumber = $request->input('sortNumber');
        $this->isPluginData('postLists');
        $request->offsetSet('queryType', AmConfig::QUERY_TYPE_SQL_QUERY);
        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 30);
        $FresnsPostsService = new FresnsPostsService();
        // dd($FresnsPostsService);
        $request->offsetSet('currentPage', $page);
        $request->offsetSet('pageSize', $pageSize);
        $FresnsPostsService->setResource(FresnsPostResource::class);
        $list = $FresnsPostsService->searchData();
        $implants = FresnsImplantsService::getImplants($page, $pageSize, 1);
        $common['implants'] = $implants;
        $data = [
            'pagination' => $list['pagination'],
            'list' => $list['list'],
            'common' => $common,
        ];
        $this->success($data);
    }

    // 获取帖子【单条】
    public function post_detail(Request $request)
    {
        $table = FresnsPostsConfig::CFG_TABLE;
        $rule = [
            'pid' => "required|exists:{$table},uuid",
        ];
        ValidateService::validateRule($request, $rule);
        $site_mode = ApiConfigHelper::getConfigByItemKey(AmConfig::SITE_MODEL);
        if ($site_mode == AmConfig::PRIVATE) {
            $uid = $this->uid;
            $member_id = $this->mid;
            $uid = $this->uid;
            if (empty($uid)) {
                $this->error(ErrorCodeService::USER_REQUIRED_ERROR);
            }
            if (empty($member_id)) {
                $this->error(ErrorCodeService::MEMBER_REQUIRED_ERROR);
            }
        }
        //  请求接口时查询配置表键名‘post_detail_service’（有值，将请求转述给插件，由插件处理
        $post_detail_config = ApiConfigHelper::getConfigByItemKey(AmConfig::POST_DETAIL_SERVICE);
        if ($post_detail_config) {
            $cmd = BasePluginConfig::PLG_CMD_DEFAULT;
            $pluginClass = PluginHelper::findPluginClass($post_detail_config);
            if (empty($pluginClass)) {
                LogService::error('未找到插件类');
                $this->error(ErrorCodeService::PLUGINS_CLASS_ERROR);
            }
            $input = [
                'type' => 'postDetail',
                'header' => $this->getHeader($request->header()),
                'body' => $request->all(),
            ];
            $resp = PluginRpcHelper::call($pluginClass, $cmd, $input);
            $this->success($resp['output']);
        }
        $mid = GlobalService::getGlobalKey('member_id');
        $langTag = $this->langTag;
        $id = $request->input('pid');
        $postId = FresnsPosts::where('uuid', $id)->first();
        $FresnsPostsService = new FresnsPostsService();
        $FresnsPostsService->setResourceDetail(FresnsPostResourceDetail::class);
        $detail = $FresnsPostsService->detail($postId['id']);
        // $this->success($detail);
        // dd(gettype($detail));

        // 如果是非公开小组的帖子，不是小组成员，不输出。
        $FresnsGroups = FresnsGroups::where('type_mode', 2)->where('type_find', 2)->pluck('id')->toArray();
        // $groupMember = FresnsMemberFollows::where('member_id',$mid)->where('follow_type',2)->pluck('follow_id')->toArray();
        $groupMember = DB::table(FresnsMemberFollowsConfig::CFG_TABLE)->where('member_id', $mid)->where('deleted_at',
            null)->where('follow_type', 2)->pluck('follow_id')->toArray();
        // // dd($FresnsGroups);
        $noGroupArr = array_diff($FresnsGroups, $groupMember);
        if (! empty($detail['detail']['group_id'])) {
            if (in_array($detail['detail']['group_id'], $noGroupArr)) {
                $detail['detail'] = [];
            }
        }
        // dd($noGroupArr);
        // 过滤屏蔽对象的帖子（成员、小组、话题、帖子），屏蔽对象的帖子不输出。
        $memberShieldsTable = FresnsMemberShieldsConfig::CFG_TABLE;
        $memberShields = DB::table($memberShieldsTable)->where('member_id', $mid)->where('deleted_at',
            null)->where('shield_type', 1)->pluck('shield_id')->toArray();
        if (in_array($detail['detail']['member_id'], $memberShields)) {
            $detail['detail'] = [];
        }
        $GroupShields = DB::table($memberShieldsTable)->where('member_id', $mid)->where('shield_type',
            2)->where('deleted_at', null)->pluck('shield_id')->toArray();
        if (! empty($detail['detail']['group_id'])) {
            if (in_array($detail['detail']['group_id'], $GroupShields)) {
                $detail['detail'] = [];
            }
        }
        $shieldshashtags = DB::table($memberShieldsTable)->where('member_id', $mid)->where('shield_type',
            3)->where('deleted_at', null)->pluck('shield_id')->toArray();
        // $noPostHashtags = FresnsHashtagLinkeds::where('linked_type',1)->whereIn('hashtag_id',$shieldshashtags)->pluck('linked_id')->toArray();
        $noPostHashtags = DB::table(FresnsHashtagLinkedsConfig::CFG_TABLE)->where('linked_type', 1)->where('deleted_at',
            null)->whereIn('hashtag_id', $shieldshashtags)->pluck('linked_id')->toArray();
        if (in_array($detail['detail']['id'], $noPostHashtags)) {
            $detail['detail'] = [];
        }
        $commentShields = DB::table($memberShieldsTable)->where('member_id', $mid)->where('shield_type',
            4)->pluck('shield_id')->where('deleted_at', null)->toArray();
        if (in_array($detail['detail']['id'], $commentShields)) {
            $detail['detail'] = [];
        }
        // $query = DB::table('posts as p');
        // // dump($noPostHashtags);
        // // dump($commentShields);
        // $query = $query->select('p.*')
        //     ->leftJoin("post_appends as append", 'p.id', '=', 'append.post_id')
        //     // ->whereNotIn('p.group_id',$noGroupArr)
        //     // ->whereNotIn('p.group_id',$GroupShields)
        //     ->whereNotIn('p.member_id', $memberShields)
        //     ->whereNotIn('p.id', $noPostHashtags)
        //     ->whereNotIn('p.id', $commentShields)
        //     ->where('p.deleted_at', null)
        //     // ->where('p.status',3)
        //     ->where('p.uuid', $id);
        // if (!empty($noGroupArr)) {
        //     // dump($noGroupArr);
        //     // $query->whereNotIn('post.group_id',$noGroupArr);
        //     $postgroupIdArr = FresnsPosts::whereNotIn('group_id', $noGroupArr)->pluck('id')->toArray();
        //     $noPostgroupIdArr = FresnsPosts::where('group_id',NULL)->pluck('id')->toArray();
        //     // dd($postIdArr);
        //     $postIdArr = array_merge($postgroupIdArr,$noPostgroupIdArr);
        //     // dd($postIdArr);
        //     $query->whereIn('p.id', $postIdArr);
        // }
        // if (!empty($GroupShields)) {
        //     // dump($GroupShields);
        //     // $query->whereNotIn('post.group_id',$GroupShields);
        //     $postIdArr = FresnsPosts::whereNotIn('group_id', $GroupShields)->pluck('id')->toArray();
        //     // dd($postIdArr);
        //     $query->whereIn('p.id', $postIdArr);
        // }
        if ($site_mode == 'private') {
            $memberInfo = FresnsMembers::find($mid);
            if (! empty($memberInfo['expired_at']) && (strtotime($memberInfo['expired_at'])) < time()) {
                $site_private_end = ApiConfigHelper::getConfigByItemKey('site_private_end');
                if ($site_private_end == 1) {
                    // $query->where('p.member_id','=',0);
                    $this->error(ErrorCodeService::USER_EXPIRED_ERROR);
                }
                if ($site_private_end == 2) {
                    // $query->where('p.created_at', '<=', $memberInfo['expired_at']);
                    if ($detail['detail']['created_at'] > $memberInfo['expired_at']) {
                        $detail['detail'] = [];
                    }
                }
            }
        }
        // $item = $query->paginate(10, ['*'], 'page', 1);
        $data = [];
        // $detail = FresnsPostResourceDetail::collection($item->items())->toArray($item->items());

        $post = Fresnsposts::where('uuid', $id)->first();
        $seoPost['seoInfo'] = [];
        if (! $langTag) {
            $langTag = FresnsPluginUsagesService::getDefaultLanguage();
        }
        // dd($post);
        $seo = [];
        if ($post) {
            $seo = DB::table('seo')->where('linked_type', 4)->where('linked_id', $post['id'])->where('lang_tag',
                $langTag)->where('deleted_at', null)->first();
        }
        $seoInfo = [];
        if ($seo) {
            $seoInfo['title'] = $seo->title;
            $seoInfo['keywords'] = $seo->keywords;
            $seoInfo['description'] = $seo->description;
            $seoPost['seoInfo'] = $seoInfo;
        }
        $seoPost['seoInfo'] = (object) $seoPost['seoInfo'];
        // $data = [
        //     // 'pagination' => $data['pagination'],
        //     'detail' => $detail,
        //     'common' => $seoPost
        // ];
        $detail['common'] = $seoPost;
        $this->success($detail);
    }

    // 获取话题列表
    public function hashtag_lists(Request $request)
    {
        $site_mode = ApiConfigHelper::getConfigByItemKey(AmConfig::SITE_MODEL);
        // dump($site_mode);
        if ($site_mode == AmConfig::PRIVATE) {
            $uid = $this->uid;
            $member_id = $this->mid;
            $uid = $this->uid;
            if (empty($uid)) {
                $this->error(ErrorCodeService::USER_REQUIRED_ERROR);
            }
            if (empty($member_id)) {
                $this->error(ErrorCodeService::MEMBER_REQUIRED_ERROR);
            }
        }
        $mid = GlobalService::getGlobalKey('member_id');
        $langTag = $this->langTag;
        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 30);
        $FresnsHashtagsService = new FresnsHashtagsService();
        $request->offsetSet('currentPage', $page);
        $request->offsetSet('pageSize', $pageSize);
        $FresnsHashtagsService->setResource(FresnsHashtagsResource::class);
        $data = $FresnsHashtagsService->searchData();
        $data = [
            'pagination' => $data['pagination'],
            'list' => $data['list'],
        ];
        $this->success($data);
    }

    // 获取话题单个
    public function hashtag_detail(Request $request)
    {
        $table = FresnsHashtagsConfig::CFG_TABLE;
        $rule = [
            'huri' => "required|exists:{$table},slug",
        ];
        ValidateService::validateRule($request, $rule);
        $site_mode = ApiConfigHelper::getConfigByItemKey(AmConfig::SITE_MODEL);
        // dump($site_mode);
        if ($site_mode == AmConfig::PRIVATE) {
            $uid = $this->uid;
            $member_id = $this->mid;
            $uid = $this->uid;
            if (empty($uid)) {
                $this->error(ErrorCodeService::USER_REQUIRED_ERROR);
            }
            if (empty($member_id)) {
                $this->error(ErrorCodeService::MEMBER_REQUIRED_ERROR);
            }
        }
        $langTag = $this->langTag;
        $FresnsHashtagsService = new FresnsHashtagsService();
        $FresnsHashtagsService->setResourceDetail(FresnsHashtagsResourceDetail::class);
        $id = FresnsHashtags::where('slug', $request->input('huri'))->first();
        // $data = $FresnsHashtagsService->searchData();
        $detail = $FresnsHashtagsService->detail($id['id']);
        // dd($data);
        // $data = [
        //     // 'pagination' => $data['pagination'],
        //     'detail' => $data['list'],
        //     'common' => $data['common']
        // ];
        $this->success($detail);
    }

    // 获取评论【列表】
    public function comment_lists(Request $request)
    {
        // 未登录，私有模式 不输出
        $site_mode = ApiConfigHelper::getConfigByItemKey(AmConfig::SITE_MODEL);
        if ($site_mode == AmConfig::PRIVATE) {
            $uid = $this->uid;
            $member_id = $this->mid;
            $uid = $this->uid;
            if (empty($uid)) {
                $this->error(ErrorCodeService::USER_REQUIRED_ERROR);
            }
            if (empty($member_id)) {
                $this->error(ErrorCodeService::MEMBER_REQUIRED_ERROR);
            }
        }
        $mid = GlobalService::getGlobalKey('member_id');
        $request->offsetSet('queryType', AmConfig::QUERY_TYPE_SQL_QUERY);
        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 30);
        $fresnsCommentsService = new FresnsCommentsService();
        $request->offsetSet('currentPage', $page);
        $request->offsetSet('pageSize', $pageSize);
        $fresnsCommentsService->setResource(CommentResource::class);
        $list = $fresnsCommentsService->searchData();
        $implants = FresnsImplantsService::getImplants($page, $pageSize, 1);
        $common['implants'] = $implants;
        $data = [
            'pagination' => $list['pagination'],
            'list' => $list['list'],
            'common' => $common,
        ];
        $this->success($data);
    }

    // 获取评论【单条】
    public function commentDetail(Request $request)
    {
        $table = FresnsCommentsConfig::CFG_TABLE;
        $rule = [
            'cid' => "required|exists:{$table},uuid",
        ];
        ValidateService::validateRule($request, $rule);
        // 未登录，私有模式 不输出
        $site_mode = ApiConfigHelper::getConfigByItemKey(AmConfig::SITE_MODEL);
        if ($site_mode == AmConfig::PRIVATE) {
            $uid = $this->uid;
            $member_id = $this->mid;
            $uid = $this->uid;
            if (empty($uid)) {
                $this->error(ErrorCodeService::USER_REQUIRED_ERROR);
            }
            if (empty($member_id)) {
                $this->error(ErrorCodeService::MEMBER_REQUIRED_ERROR);
            }
        }
        $comment = FresnsComments::where('uuid', $request->input('cid'))->first();
        // dd($comment);s
        $fresnsCommentsService = new FresnsCommentsService();
        $fresnsCommentsService->setResourceDetail(CommentResourceDetail::class);
        $detail = $fresnsCommentsService->detail($comment['id']);
        // 屏蔽的目标字段
        $memberShieldsTable = FresnsMemberShieldsConfig::CFG_TABLE;
        $commentTable = FresnsCommentsConfig::CFG_TABLE;
        $commentAppendTable = FresnsCommentAppendsConfig::CFG_TABLE;
        $postTable = FresnsPostsConfig::CFG_TABLE;
        /**
         * 过滤屏蔽对象的评论（成员、评论）。
         */
        // 屏蔽的目标字段
        $request = request();
        $mid = GlobalService::getGlobalKey('member_id');
        $memberShields = DB::table($memberShieldsTable)->where('member_id', $mid)->where('deleted_at',
            null)->where('shield_type', 1)->pluck('shield_id')->toArray();
        if (in_array($detail['detail']['member_id'], $memberShields)) {
            $detail['detail'] = [];
        }
        $commentShields = DB::table($memberShieldsTable)->where('member_id', $mid)->where('deleted_at',
            null)->where('shield_type', 5)->pluck('shield_id')->toArray();
        if (in_array($detail['detail']['id'], $commentShields)) {
            $detail['detail'] = [];
        }
        // $query = DB::table("$commentTable as comment")->select('comment.*')
        //     ->leftJoin("$commentAppendTable as append", 'comment.id', '=', 'append.comment_id')
        //     ->whereNotIn('comment.member_id', $memberShields)
        //     ->whereNotIn('comment.id', $commentShields)
        //     ->where('comment.deleted_at', null)
        //     ->where('comment.uuid', $request->input('cid'));
        if ($site_mode == 'private') {
            $memberInfo = FresnsMembers::find($mid);
            if (! empty($memberInfo['expired_at']) && (strtotime($memberInfo['expired_at'])) < time()) {
                $site_private_end = ApiConfigHelper::getConfigByItemKey('site_private_end');
                if ($site_private_end == 1) {
                    // $query->where('comment.member_id','=',0);
                    $this->error(ErrorCodeService::USER_EXPIRED_ERROR);
                }
                if ($site_private_end == 2) {
                    // $query->where('comment.created_at', '<=', $memberInfo['expired_at']);
                    if ($detail['detail']['created_at'] > $memberInfo['expired_at']) {
                        $detail['detail'] = [];
                    }
                }
            }
        }
        // $item = $query->paginate(10, ['*'], 'page', 1);
        // $data = [];
        // $data['list'] = CommentResourceDetail::collection($item->items())->toArray($item->items());
        $langTag = $this->langTag;
        // $fresnsCommentsService = new FresnsCommentsService();
        // $request->offsetSet('is_enable',true);
        // $request->offsetSet('status',3);
        // $fresnsCommentsService->setResource(CommentResourceDetail::class);
        // $data = $fresnsCommentsService->searchData();
        $comment = FresnsComments::where('uuid', $request->input('cid'))->first();
        $seoComment['seoInfo'] = [];
        if (! $langTag) {
            $langTag = FresnsPluginUsagesService::getDefaultLanguage();
        }
        $seo = [];
        if ($comment) {
            $seo = DB::table('seo')->where('linked_type', 5)->where('linked_id', $comment['id'])->where('lang_tag',
                $langTag)->where('deleted_at', null)->first();
        }
        $seoInfo = [];
        if ($seo) {
            $seoInfo['title'] = $seo->title;
            $seoInfo['keywords'] = $seo->keywords;
            $seoInfo['description'] = $seo->description;
            $seoComment['seoInfo'] = $seoInfo;
        }
        $seoComment['seoInfo'] = (object) $seoComment['seoInfo'];

        $detail['common'] = $seoComment;
        // $data = [
        //     // 'pagination' => $data['pagination'],
        //     'detail' => $data['list'],
        //     'common' => $seoComment
        // ];
        $this->success($detail);
    }

    // 获取tiezi关注的【列表】

    /**
     *   获取「1.成员 / 2.小组 / 3.话题」这三个对象的帖子。
     *   成员的全部帖子，小组和话题下只输出被加精华的帖子，以发表时间倒序排列。
     *   帖子有重复时，以成员的帖子为主。比如我关注的成员，在小组里帖子被加精华了，这时候会重复，届时以成员身份输出为主。
     *   type 留空时，输出三个对象的帖子，届时无论是否关注了对象，被设置为二级精华的帖子，强制输出。
     *   type 有值时，输出对应对象的全部帖子，无论是否为精华。
     */
    public function postFollows(Request $request)
    {
        $site_mode = ApiConfigHelper::getConfigByItemKey(AmConfig::SITE_MODEL);
        // 是否为插件返回数据
        $sortNumber = $request->input('sortNumber');
        $this->isPluginData('postFollows');
        $mid = GlobalService::getGlobalKey('member_id');
        // dd($mid);
        $type = $request->input('followType');
        switch ($type) {
            case 'member':
                //$followMemberArr = FresnsMemberFollows::where('member_id',$mid)->where('follow_type',1)->pluck('follow_id')->toArray();
                $mePostsArr = FresnsPosts::where('member_id', $mid)->pluck('id')->toArray();
                $followMemberArr = DB::table(FresnsMemberFollowsConfig::CFG_TABLE)->where('member_id',
                    $mid)->where('follow_type', 1)->where('deleted_at', null)->pluck('follow_id')->toArray();
                $postIdArr = FresnsPosts::whereIn('member_id', $followMemberArr)->pluck('id')->toArray();
                $postIdArr = array_merge($mePostsArr, $postIdArr);
                $ids = implode(',', $postIdArr);
                //    dd($ids);
                break;
            case 'group':
                // $folloGroupArr = FresnsMemberFollows::where('member_id',$mid)->where('follow_type',2)->pluck('follow_id')->toArray();
                $folloGroupArr = DB::table(FresnsMemberFollowsConfig::CFG_TABLE)->where('member_id',
                    $mid)->where('follow_type', 2)->where('deleted_at', null)->pluck('follow_id')->toArray();
                $postIdArr = FresnsPosts::whereIn('group_id', $folloGroupArr)->pluck('id')->toArray();
                $ids = implode(',', $postIdArr);
                break;
            case 'hashtag':
                // $folloHashtagArr = FresnsMemberFollows::where('member_id',$mid)->where('follow_type',3)->pluck('follow_id')->toArray();
                $folloHashtagArr = DB::table(FresnsMemberFollowsConfig::CFG_TABLE)->where('member_id',
                    $mid)->where('follow_type', 3)->where('deleted_at', null)->pluck('follow_id')->toArray();
                $postIdArr = FresnsHashtagLinkeds::where('linked_type', 1)->whereIn('hashtag_id',
                    $folloHashtagArr)->pluck('linked_id')->toArray();
                $ids = implode(',', $postIdArr);
                break;
            default:
                // 我自己发布的帖子
                $mePostsArr = FresnsPosts::where('member_id', $mid)->pluck('id')->toArray();
                // 关注成员下全部
                // $followMemberArr = FresnsMemberFollows::where('member_id',$mid)->where('follow_type',1)->pluck('follow_id')->toArray();
                $followMemberArr = DB::table(FresnsMemberFollowsConfig::CFG_TABLE)->where('member_id',
                    $mid)->where('follow_type', 1)->pluck('follow_id')->toArray();
                $postMemberIdArr = FresnsPosts::whereIn('member_id', $followMemberArr)->pluck('id')->toArray();
                // dd($postMemberIdArr);
                // 小组和话题下只输出被加精华的帖子
                // $folloGroupArr = FresnsMemberFollows::where('member_id',$mid)->where('follow_type',2)->pluck('follow_id')->toArray();
                $folloGroupArr = DB::table(FresnsMemberFollowsConfig::CFG_TABLE)->where('member_id',
                    $mid)->where('follow_type', 2)->where('deleted_at', null)->pluck('follow_id')->toArray();
                $postGroupIdArr = FresnsPosts::whereIn('group_id', $folloGroupArr)->where('essence_status', '!=',
                    1)->pluck('id')->toArray();
                // $folloHashtagArr = FresnsMemberFollows::where('member_id',$mid)->where('follow_type',3)->pluck('follow_id')->toArray();
                $folloHashtagArr = DB::table(FresnsMemberFollowsConfig::CFG_TABLE)->where('member_id',
                    $mid)->where('follow_type', 3)->where('deleted_at', null)->pluck('follow_id')->toArray();
                $postIdArr = FresnsHashtagLinkeds::where('linked_type', 1)->whereIn('hashtag_id',
                    $folloHashtagArr)->pluck('linked_id')->toArray();
                $postHashtagIdArr = FresnsPosts::whereIn('id', $postIdArr)->where('essence_status', '!=',
                    1)->pluck('id')->toArray();
                // 设置为二级精华的帖子，强制输出
                $essenceIdArr = FresnsPosts::where('essence_status', 3)->pluck('id')->toArray();
                $idArr = array_merge($mePostsArr, $postMemberIdArr, $postGroupIdArr, $postHashtagIdArr, $essenceIdArr);
                $ids = implode(',', $idArr);
                break;
        }
        // dd($ids);
        if ($site_mode == 'private') {
            $memberInfo = FresnsMembers::find($mid);
            if (! empty($memberInfo['expired_at']) && (strtotime($memberInfo['expired_at'])) < time()) {
                $site_private_end = ApiConfigHelper::getConfigByItemKey('site_private_end');
                if ($site_private_end == 1) {
                    $request->offsetSet('id', 0);
                }
                if ($site_private_end == 2) {
                    $request->offsetSet('expired_at', $memberInfo['expired_at']);
                }
            }
        }
        // dd($ids);
        $searchType = $request->input('searchType', '');
        if ($searchType == 'all') {
            $request->offsetSet('searchType', '');
        }
        // dd($ids);
        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 30);
        $FresnsPostsService = new FresnsPostsService();
        $request->offsetSet('ids', $ids);
        $request->offsetSet('is_enable', 1);
        // $request->offsetSet('status', 3);
        $request->offsetSet('currentPage', $page);
        $request->offsetSet('pageSize', $pageSize);
        $FresnsPostsService->setResource(FresnsPostResource::class);
        $list = $FresnsPostsService->searchData();
        $implants = FresnsImplantsService::getImplants($page, $pageSize, 1);
        $common['implants'] = $implants;
        // $list['list']['followType'] = $type;
        $data = [
            'pagination' => $list['pagination'],
            'list' => $list['list'],
            'common' => $common,
        ];
        $this->success($data);
    }

    // 获取帖子附近的列表
    public function postNearbys(Request $request)
    {
        // 是否为插件返回数据
        $sortNumber = $request->input('sortNumber');
        $this->isPluginData('postNearbys');
        $table = FresnsGroupsConfig::CFG_TABLE;
        $rule = [
            'longitude' => 'required',
            'latitude' => 'required',
            'mapId' => 'required',
        ];
        ValidateService::validateRule($request, $rule);
        $site_mode = ApiConfigHelper::getConfigByItemKey(AmConfig::SITE_MODEL);
        $mid = GlobalService::getGlobalKey('member_id');
        $langTag = $this->langTag;
        // 默认的公里数
        $configLength = ApiConfigHelper::getConfigByItemKey('nearby_length');
        $length = $request->input('length', $configLength);

        $lengthUnits = $request->input('lengthUnits');
        if (! $lengthUnits) {
            // 距离
            $languages = ApiConfigHelper::distanceUnits($langTag);
            $lengthUnits = empty($languages) ? 'km' : $languages;
        }
        $longitude = $request->input('longitude');
        $latitude = $request->input('latitude');
        if ($lengthUnits == 'mi') {
            $distance = 1609.344 * $length;
        } else {
            $distance = 1000 * $length;
        }
        // dd($distance);
        $postArr1 = self::distance1($longitude, $latitude, $distance);
        // dd($postArr1);
        $memberShieldsTable = FresnsMemberShieldsConfig::CFG_TABLE;
        // 如果是非公开小组的帖子，不是小组成员，不输出。
        $FresnsGroups = FresnsGroups::where('type_mode', 2)->where('type_find', 2)->pluck('id')->toArray();
        // $groupMember = FresnsMemberFollows::where('member_id',$mid)->where('follow_type',2)->pluck('follow_id')->toArray();
        $groupMember = DB::table(FresnsMemberFollowsConfig::CFG_TABLE)->where('member_id', $mid)->where('follow_type',
            2)->pluck('follow_id')->toArray();

        // dump($FresnsGroups);
        $noGroupArr = array_diff($FresnsGroups, $groupMember);
        // dump($noGroupArr);
        // 过滤屏蔽对象的帖子（成员、小组、话题、帖子），屏蔽对象的帖子不输出。
        $memberShields = DB::table($memberShieldsTable)->where('member_id', $mid)->where('shield_type',
            1)->pluck('shield_id')->toArray();
        $GroupShields = DB::table($memberShieldsTable)->where('member_id', $mid)->where('shield_type',
            2)->pluck('shield_id')->toArray();
        $shieldshashtags = DB::table($memberShieldsTable)->where('member_id', $mid)->where('shield_type',
            3)->pluck('shield_id')->toArray();
        $noPostHashtags = FresnsHashtagLinkeds::where('linked_type', 1)->whereIn('hashtag_id',
            $shieldshashtags)->pluck('linked_id')->toArray();
        $commentShields = DB::table($memberShieldsTable)->where('member_id', $mid)->where('shield_type',
            4)->pluck('shield_id')->toArray();
        $postArr2 = FresnsPosts::whereNotIn('group_id', $noGroupArr)->whereNotIn('member_id',
            $memberShields)->whereNotIn('group_id', $GroupShields)->whereNotIn('id', $noPostHashtags)->whereNotIn('id',
            $commentShields)->pluck('id')->toArray();
        // dd($postArr2);
        $idArr = array_intersect($postArr1, $postArr2);
        // dd($idArr);
        $searchType = $request->input('searchType', '');
        if ($searchType == 'all') {
            $request->offsetSet('searchType', '');
        }
        /**
         * 2、成员 members > expired_at 是否在有效期内（为空代表永久有效）。
         *2.1、过期后内容不可见，不输出帖子列表。
         *2.2、过期后，到期前的内容可见，输出到期日期前的帖子列表。
         *2.3、在有效期内，继续往下判断。
         */
        if ($site_mode == 'private') {
            $memberInfo = FresnsMembers::find($mid);
            // dd($memberInfo);
            if (! empty($memberInfo['expired_at']) && (strtotime($memberInfo['expired_at'])) < time()) {
                $site_private_end = ApiConfigHelper::getConfigByItemKey('site_private_end');
                if ($site_private_end == 1) {
                    // $request->offsetSet('id', 0);
                    $this->error(ErrorCodeService::USER_EXPIRED_ERROR);
                }
                // dump($memberInfo['expired_at']);
                // dd($site_private_end);

                if ($site_private_end == 2) {
                    $request->offsetSet('expired_at', $memberInfo['expired_at']);
                }
            }
        }
        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 30);
        $FresnsPostsService = new FresnsPostsService();
        $request->offsetSet('ids', implode(',', $idArr));
        $request->offsetSet('is_enable', true);
        // $request->offsetSet('status', 3);
        $request->offsetSet('currentPage', $page);
        $request->offsetSet('pageSize', $pageSize);
        $FresnsPostsService->setResource(FresnsPostResource::class);
        $list = $FresnsPostsService->searchData();
        $implants = FresnsImplantsService::getImplants($page, $pageSize, 1);
        $common['implants'] = $implants;
        $data = [
            'pagination' => $list['pagination'],
            'list' => $list['list'],
            'common' => $common,
        ];
        $this->success($data);
    }

    // 获取扩展内容[列表]
    public function extendsLists(Request $request)
    {
        $request->offsetSet('queryType', AmConfig::QUERY_TYPE_SQL_QUERY);
        $page = $request->input('page', 1);
        $pageSize = $request->input('pageSize', 30);
        $fresnsExtendsService = new FresnsExtendsService();
        $request->offsetSet('currentPage', $page);
        $request->offsetSet('pageSize', $pageSize);
        $fresnsExtendsService->setResource(FresnsExtendsResource::class);
        $data = $fresnsExtendsService->searchData();
        $data = [
            'pagination' => $data['pagination'],
            'detail' => $data['list'],
        ];
        $this->success($data);
    }

    // 内容互动记录[列表]
    // public function content_members(Request $request)
    // {
    //     $rule = [
    //         'type' => "required|in:1,2,3,4",
    //         'uuid' => 'required'
    //     ];
    //     ValidateService::validateRule($request, $rule);
    //     // 1.帖子点赞用户列表 / 2.帖子特定成员列表 / 3.评论点赞用户列表 / 4.下载附件用户列表
    //     $type = $request->input('type');
    //     $uid = $request->input('uuid');
    //     $page = $request->input('page', 1);
    //     $pageSize = $request->input('pageSize', 30);
    //     switch ($type) {
    //         case 1:
    //             // 获取uuid对应的主键id
    //             $like_id = FresnsPosts::where('uuid', $uid)->first('id');
    //             // dd($like_id);
    //             $dataId = $like_id['id'] ?? "";
    //             $FresnsMemberLikesService = new FresnsMemberLikesService();
    //             $request->offsetSet('currentPage', $page);
    //             $request->offsetSet('type', 4);
    //             $request->offsetSet('like_id', $dataId);
    //             $request->offsetSet('pageSize', $pageSize);
    //             $FresnsMemberLikesService->setResource(FresnsMemberLikesResource::class);
    //             $data = $FresnsMemberLikesService->searchData();
    //             break;
    //         case 2:
    //             $post = FresnsPosts::where('uuid', $uid)->first('id');
    //             $dataId = $post['id'] ?? "";
    //             $FresnsPostMembersService = new FresnsPostMembersService();
    //             $request->offsetSet('currentPage', $page);
    //             $request->offsetSet('post_id', $dataId);
    //             $request->offsetSet('pageSize', $pageSize);
    //             $FresnsPostMembersService->setResource(FresnsPostMembersResource::class);
    //             $data = $FresnsPostMembersService->searchData();
    //         case 3:
    //             // 获取uuid对应的主键id
    //             $comment = FresnsComments::where('uuid', $uid)->first('id');
    //             $like_id = $comment == null ? 0 : $comment['id'];
    //             $FresnsMemberLikesService = new FresnsMemberLikesService();
    //             $request->offsetSet('currentPage', $page);
    //             $request->offsetSet('type', 5);
    //             $request->offsetSet('like_id', $like_id);
    //             $request->offsetSet('pageSize', $pageSize);
    //             $FresnsMemberLikesService->setResource(FresnsMemberLikesResource::class);
    //             $data = $FresnsMemberLikesService->searchData();
    //             break;
    //         default:
    //             // 获取uuid对应的主键id
    //             $like_id = FresnsFiles::where('uuid', $uid)->first('id');
    //             $dataId = $like_id['id'] ?? "";
    //             $FresnsDownloadsService = new FresnsDownloadsService();
    //             $request->offsetSet('currentPage', $page);
    //             $request->offsetSet('file_id', $dataId);
    //             $request->offsetSet('pageSize', $pageSize);
    //             $FresnsDownloadsService->setResource(FresnsDownloadsResource::class);
    //             $data = $FresnsDownloadsService->searchData();
    //             break;
    //     }
    //     $data = [
    //         'pagination' => $data['pagination'],
    //         'detail' => $data['list'],
    //     ];
    //     $this->success($data);
    // }

    // 距离
    public static function distance1($longitude, $latitude, $distance)
    {
        $sql = "SELECT id,
        ROUND(
            6378.138 * 2 * ASIN(
                SQRT(
                    POW(
                        SIN(
                            (
                                $latitude * PI() / 180 - map_latitude * PI() / 180
                            ) / 2
                        ),
                        2
                    ) + COS($latitude * PI() / 180) * COS(map_latitude * PI() / 180) * POW(
                        SIN(
                            (
                                $longitude * PI() / 180 - map_longitude * PI() / 180
                            ) / 2
                        ),
                        2
                    )
                )
            ) * 1000
        ) AS juli
        FROM
            am_posts
        HAVING
            juli < $distance
        ORDER BY
            juli ASC";
        $result = DB::select($sql);
        // dd($result);
        $res = [];
        foreach ($result as $key => $v) {
            // dd($key);
            // if($v->juli){
            $res[] = $v->id;
            // }
        }
        // dd($res);
        return $res;
    }

    // 数据是否为插件返回
    public function isPluginData($apiName)
    {
        $request = request();
        $pluginUsages = FresnsPluginUsages::where('type', 4)->where('is_enable', 1)->first();
        //  $status = false;
        if (! $pluginUsages || empty($pluginUsages['data_sources'])) {
            return;
        }
        $data_source = json_decode($pluginUsages['data_sources'], true);
        // dd($data_source);
        if (! $data_source) {
            return;
        }
        foreach ($data_source as $key => $d) {
            if ($key == $apiName) {
                if (! isset($d['pluginUnikey'])) {
                    return;
                } else {
                    if (empty($d['pluginUnikey'])) {
                        return;
                    }
                    // 获取接口 sortNumber 参数，
                    // $sortNumber = $d['sortNumber'][0]['id'];
                    // dd($sortNumber);
                    // 插件返回数据
                    $pluginUnikey = $d['pluginUnikey'];
                    // 执行上传
                    $pluginClass = PluginHelper::findPluginClass($pluginUnikey);
                    // dd($pluginClass);
                    if (empty($pluginClass)) {
                        LogService::error('未找到插件类');
                        $this->error(ErrorCodeService::PLUGINS_CLASS_ERROR);
                    }
                    $cmd = BasePluginConfig::PLG_CMD_DEFAULT;
                    $pluginClass = PluginHelper::findPluginClass($pluginUnikey);
                    $input = [
                        'type' => $apiName,
                        'header' => $this->getHeader($request->header()),
                        'body' => $request->all(),
                    ];
                    // dd($input);
                    $resp = PluginRpcHelper::call($pluginClass, $cmd, $input);
                    $this->success($resp['output']);
                }
            }
        }
    }

    public function getHeader($header)
    {
        $arr = [];
        foreach ($header as $key => $h) {
            foreach ($h as $v) {
                $arr[$key] = $v;
            }
        }

        return $arr;
    }
}
