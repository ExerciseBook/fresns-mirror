@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::operation.sidebar')
@endsection

@section('content')

  <div class="row mb-4 border-bottom">
    <div class="col-lg-7">
      <h3>互动配置</h3>
      <p class="text-secondary">基础的互动功能配置，更多功能配置请参见网站引擎或移动应用设置页。</p>
    </div>
    <div class="col-lg-5">
      <div class="input-group mt-2 mb-4 justify-content-lg-end">
        <a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
      </div>
    </div>
  </div>
  <!--操作列表-->
  <form action="{{ route('panel.interactiveConfigs.update')}}" method="post">
    @csrf
    @method('put')
    <!--内容功能设置-->
    <div class="row mb-3">
      <label class="col-lg-2 col-form-label text-lg-end">内容功能设置：</label>
      <div class="col-lg-6">
        <div class="input-group">
          <label class="input-group-text">话题功能形式</label>
          <div class="form-control bg-white">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="hashtag_show" id="hashtag_show_1" value="1" {{ $params['hashtag_show'] == 1 ? 'checked' : '' }}>
              <label class="form-check-label" for="hashtag_show_1">单 # 号</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="hashtag_show" id="hashtag_show_2" value="2" {{ $params['hashtag_show'] == 2 ? 'checked' : '' }}>
              <label class="form-check-label" for="hashtag_show_2">双 # 号</label>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> <a href="#">#话题</a> 和 <a href="#">#话题#</a> 的区别</div>
    </div>
    <div class="row mb-3">
      <label class="col-lg-2 col-form-label text-lg-end"></label>
      <div class="col-lg-6">
        <div class="input-group">
          <label class="input-group-text">帖子热评条件</label>
          <input type="number" class="form-control input-number" name="post_hot" value="{{ $params['post_hot'] }}">
        </div>
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 评论点赞总数达到多少开启热评功能，0 代表不启用</div>
    </div>
    <div class="row mb-3">
      <label class="col-lg-2 col-form-label text-lg-end"></label>
      <div class="col-lg-6">
        <div class="input-group">
          <label class="input-group-text">评论预览数量</label>
          <input type="number" class="form-control input-number" name="comment_preview" value="{{ $params['comment_preview'] }}" min="0" max="3">
        </div>
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 评论列表子级评论预览，0 代表不启用，最大数字 3</div>
    </div>
    <div class="row mb-5">
      <label class="col-lg-2 col-form-label text-lg-end"></label>
      <div class="col-lg-6">
        <div class="input-group">
          <label class="input-group-text">附近帖子默认范围</label>
          <input type="number" class="form-control input-number" name="nearby_length" value="{{ $params['nearby_length'] }}">
        </div>
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 范围单位以多语言配置为准，默认是公里 km</div>
    </div>
    <!--会话功能设置-->
    <div class="row mb-3">
      <label class="col-lg-2 col-form-label text-lg-end">会话功能设置：</label>
      <div class="col-lg-6">
        <div class="input-group">
          <label class="input-group-text">私信会话功能</label>
          <div class="form-control bg-white">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="dialog_status" id="dialog_status_true" value="true" data-bs-toggle="collapse" data-bs-target="#dialog_setting:not(.show)" aria-expanded="false" aria-controls="dialog_setting" {{ $params['dialog_status'] ? 'checked' : ''}}>
              <label class="form-check-label" for="dialog_status_true">开启</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="dialog_status" id="dialog_status_false" value="false" data-bs-toggle="collapse" data-bs-target="#dialog_setting.show" aria-expanded="false" aria-controls="dialog_setting" {{ !$params['dialog_status'] ? 'checked' : ''}}>
              <label class="form-check-label" for="dialog_status_false">关闭</label>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 关闭对全员有效，开启后以用户角色权限为准</div>
    </div>
    <div class="collapse {{ $params['dialog_status'] ? 'show' : ''}}" id="dialog_setting">
      <div class="row">
        <label class="col-lg-2 col-form-label text-lg-end"></label>
        <div class="col-lg-6">
          <div class="input-group">
            <label class="input-group-text">会话附件功能</label>
            <div class="form-control bg-white">
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox"  id="dialog_file_image" name="dialog_files[]" value="1" {{ in_array(1, explode(',', $params['dialog_files'] ?? '')) ? 'checked' : '' }}>
                <label class="form-check-label" for="dialog_file_image">图片</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox"  id="dialog_file_video" name="dialog_files[]" value="2" {{ in_array(2, explode(',', $params['dialog_files'] ?? '')) ? 'checked' : '' }}>
                <label class="form-check-label" for="dialog_file_video">视频</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox"  id="dialog_file_audio" name="dialog_files[]" value="3" {{ in_array(3, explode(',', $params['dialog_files'] ?? '')) ? 'checked' : '' }}>
                <label class="form-check-label" for="dialog_file_audio">音频</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox"  id="dialog_file_doc" name="dialog_files[]" value="4" {{ in_array(4, explode(',', $params['dialog_files'] ?? '')) ? 'checked' : '' }}>
                <label class="form-check-label" for="dialog_file_doc">文档</label>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 如果全部不勾选则代表只支持纯文字的对话</div>
      </div>
    </div>
    <!--互动行为设置-->
    <div class="row mt-5">
      <label class="col-lg-2 col-form-label text-lg-end">互动行为设置：</label>
      <div class="col-lg-10">
        <ul class="list-group">
          <li class="list-group-item">
            <div class="form-check form-check-inline">
              <input type="checkbox" id="like_member" name="like_member_setting" value="true" class="form-check-input" {{ $params['like_member_setting'] ? 'checked' : '' }} >
              <label class="form-check-label" for="like_member">点赞成员</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" id="like_group" name="like_group_setting" value="true" class="form-check-input" {{ $params['like_group_setting'] ? 'checked' : '' }}>
              <label class="form-check-label" for="like_group">点赞小组</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" id="like_hashtag" name="like_hashtag_setting" value="true" class="form-check-input" {{ $params['like_hashtag_setting'] ? 'checked' : '' }}>
              <label class="form-check-label" for="like_hashtag">点赞话题</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" id="like_post" name="like_post_setting" value="true" class="form-check-input" {{ $params['like_post_setting'] ? 'checked' : '' }}>
              <label class="form-check-label" for="like_post">点赞帖子</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" id="like_comment" name="like_comment_setting" value="true" class="form-check-input" {{ $params['like_comment_setting'] ? 'checked' : '' }}>
              <label class="form-check-label" for="like_comment">点赞评论</label>
            </div>
          </li>
          <li class="list-group-item">
            <div class="form-check form-check-inline">
              <input type="checkbox" id="follow_member" name="follow_member_setting" value="true" class="form-check-input" {{ $params['follow_member_setting'] ? 'checked' : '' }}>
              <label class="form-check-label" for="follow_member">关注成员</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" id="follow_group" name="follow_group_setting" value="true" class="form-check-input" {{ $params['follow_group_setting'] ? 'checked' : '' }}>
              <label class="form-check-label" for="follow_group">关注小组</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" id="follow_hashtag" name="follow_hashtag_setting" value="true" class="form-check-input" {{ $params['follow_hashtag_setting'] ? 'checked' : '' }}>
              <label class="form-check-label" for="follow_hashtag">关注话题</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" id="follow_post" name="follow_post_setting" value="true" class="form-check-input" {{ $params['follow_post_setting'] ? 'checked' : '' }}>
              <label class="form-check-label" for="follow_post">关注帖子</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" id="follow_comment" name="follow_comment_setting" value="true" class="form-check-input" {{ $params['follow_comment_setting'] ? 'checked' : '' }}>
              <label class="form-check-label" for="follow_comment">关注评论</label>
            </div>
          </li>
          <li class="list-group-item">
            <div class="form-check form-check-inline">
              <input type="checkbox" id="shield_member" name="shield_member_setting" value="true" class="form-check-input" {{ $params['shield_member_setting'] ? 'checked' : '' }}>
              <label class="form-check-label" for="shield_member">屏蔽成员</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" id="shield_group" name="shield_group_setting" value="true" class="form-check-input" {{ $params['shield_group_setting'] ? 'checked' : '' }}>
              <label class="form-check-label" for="shield_group">屏蔽小组</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" id="shield_hashtag" name="shield_hashtag_setting" value="true" class="form-check-input" {{ $params['shield_hashtag_setting'] ? 'checked' : '' }}>
              <label class="form-check-label" for="shield_hashtag">屏蔽话题</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" id="shield_post" name="shield_post_setting" value="true" class="form-check-input" {{ $params['shield_post_setting'] ? 'checked' : '' }}>
              <label class="form-check-label" for="shield_post">屏蔽帖子</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" id="shield_comment" name="shield_comment_setting" value="true" class="form-check-input" {{ $params['shield_comment_setting'] ? 'checked' : '' }}>
              <label class="form-check-label" for="shield_comment">屏蔽评论</label>
            </div>
          </li>
        </ul>
      </div>
      <div class="col-lg-2"></div>
      <div class="col-lg-10 form-text pt-1"><i class="bi bi-info-circle"></i> 勾选则代表开启对应的互动功能</div>
    </div>
    <!--查看别人内容设置-->
    <div class="row mt-5">
      <label class="col-lg-2 col-form-label text-lg-end">查看别人内容设置：</label>
      <div class="col-lg-10 mb-3">
        <ul class="list-group">
          <li class="list-group-item list-group-item-secondary">哪些内容可以被别人查看？</li>
          <li class="list-group-item">
            <div class="form-check form-check-inline">
              <input type="checkbox" id="tac-1" name="it_publish_posts" value="true" class="form-check-input" {{ $params['it_publish_posts'] ? 'checked' : '' }}>
              <label class="form-check-label" for="tac-1">TA 发表的帖子</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" id="tac-2" name="it_publish_comments" value="true" class="form-check-input" {{ $params['it_publish_comments'] ? 'checked' : '' }}>
              <label class="form-check-label" for="tac-2">TA 发表的评论</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" id="tac-111" name="it_likers" value="true" class="form-check-input" {{ $params['it_likers'] ? 'checked' : '' }}>
              <label class="form-check-label" for="tac-111">点赞 TA 的成员</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" id="tac-112" name="it_followers" value="true" class="form-check-input" {{ $params['it_followers'] ? 'checked' : '' }}>
              <label class="form-check-label" for="tac-112">关注 TA 的成员</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" id="tac-113" name="it_shielders" value="true" class="form-check-input" {{ $params['it_shielders'] ? 'checked' : '' }}>
              <label class="form-check-label" for="tac-113">屏蔽 TA 的成员</label>
            </div>
          </li>
          <li class="list-group-item">
            <div class="form-check form-check-inline">
              <input type="checkbox" id="tac-3" name="it_like_members" value="true" class="form-check-input" {{ $params['it_like_members'] ? 'checked' : '' }}>
              <label class="form-check-label" for="tac-3">TA 点赞的成员</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" id="tac-4" name="it_like_groups" value="true" class="form-check-input" {{ $params['it_like_groups'] ? 'checked' : '' }}>
              <label class="form-check-label" for="tac-4">TA 点赞的小组</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" id="tac-5" name="it_like_hashtags" value="true" class="form-check-input" {{ $params['it_like_hashtags'] ? 'checked' : '' }}>
              <label class="form-check-label" for="tac-5">TA 点赞的话题</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" id="tac-6" name="it_like_posts" value="true" class="form-check-input" {{ $params['it_like_posts'] ? 'checked' : '' }}>
              <label class="form-check-label" for="tac-6">TA 点赞的帖子</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" id="tac-7" name="it_like_comments" value="true" class="form-check-input" {{ $params['it_like_comments'] ? 'checked' : '' }}>
              <label class="form-check-label" for="tac-7">TA 点赞的评论</label>
            </div>
          </li>
          <li class="list-group-item">
            <div class="form-check form-check-inline">
              <input type="checkbox" id="tac-8" name="it_follow_members" value="true" class="form-check-input" {{ $params['it_follow_members'] ? 'checked' : '' }}>
              <label class="form-check-label" for="tac-8">TA 关注的成员</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" id="tac-9" name="it_follow_groups" value="true" class="form-check-input" {{ $params['it_follow_groups'] ? 'checked' : '' }}>
              <label class="form-check-label" for="tac-9">TA 关注的小组</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" id="tac-10" name="it_follow_hashtags" value="true" class="form-check-input" {{ $params['it_follow_hashtags'] ? 'checked' : '' }}>
              <label class="form-check-label" for="tac-10">TA 关注的话题</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" id="tac-11" name="it_follow_posts" value="true" class="form-check-input" {{ $params['it_follow_posts'] ? 'checked' : '' }}>
              <label class="form-check-label" for="tac-11">TA 关注的帖子</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" id="tac-12" name="it_follow_comments" value="true" class="form-check-input" {{ $params['it_follow_comments'] ? 'checked' : '' }}>
              <label class="form-check-label" for="tac-12">TA 关注的评论</label>
            </div>
          </li>
          <li class="list-group-item">
            <div class="form-check form-check-inline">
              <input type="checkbox" id="tac-13" name="it_shield_members" value="true" class="form-check-input" {{ $params['it_shield_members'] ? 'checked' : '' }}>
              <label class="form-check-label" for="tac-13">TA 屏蔽的成员</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" id="tac-14" name="it_shield_groups" value="true" class="form-check-input" {{ $params['it_shield_groups'] ? 'checked' : '' }}>
              <label class="form-check-label" for="tac-14">TA 屏蔽的小组</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" id="tac-15" name="it_shield_hashtags" value="true" class="form-check-input" {{ $params['it_shield_hashtags'] ? 'checked' : '' }}>
              <label class="form-check-label" for="tac-15">TA 屏蔽的话题</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" id="tac-16" name="it_shield_posts" value="true" class="form-check-input" {{ $params['it_shield_posts'] ? 'checked' : '' }}>
              <label class="form-check-label" for="tac-16">TA 屏蔽的帖子</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" id="tac-17" name="it_shield_comments" value="true" class="form-check-input" {{ $params['it_shield_comments'] ? 'checked' : '' }}>
              <label class="form-check-label" for="tac-17">TA 屏蔽的评论</label>
            </div>
          </li>
        </ul>
      </div>
      <label class="col-lg-2"></label>
      <div class="col-lg-10 mb-3">
        <ul class="list-group">
          <li class="list-group-item list-group-item-secondary">访问 TA 的主页时，默认内容列表</li>
          <li class="list-group-item">
            <div class="form-check form-check-inline">
              <input type="radio" id="ta-1" name="it_home_list" value="it_publish_posts" class="form-check-input" {{ $params['it_home_list'] == 'it_publish_posts' ? 'checked' : '' }}>
              <label class="form-check-label" for="ta-1">TA 发表的帖子</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" id="ta-2" name="it_home_list" value="it_publish_comments" class="form-check-input" {{ $params['it_home_list'] == 'it_publish_comments' ? 'checked' : '' }}>
              <label class="form-check-label" for="ta-2">TA 发表的评论</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" id="ta-111" name="it_home_list" value="it_likers" class="form-check-input" {{ $params['it_home_list'] == 'it_likers' ? 'checked' : '' }}>
              <label class="form-check-label" for="ta-111">点赞 TA 的成员</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" id="ta-112" name="it_home_list" value="it_followers" class="form-check-input" {{ $params['it_home_list'] == 'it_followers' ? 'checked' : '' }}>
              <label class="form-check-label" for="ta-112">关注 TA 的成员</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" id="ta-113" name="it_home_list" value="it_shielders" class="form-check-input" {{ $params['it_home_list'] == 'it_shielders' ? 'checked' : '' }}>
              <label class="form-check-label" for="ta-113">屏蔽 TA 的成员</label>
            </div>
          </li>
          <li class="list-group-item">
            <div class="form-check form-check-inline">
              <input type="radio" id="ta-3" name="it_home_list" value="it_like_members" class="form-check-input" {{ $params['it_home_list'] == 'it_like_members' ? 'checked' : '' }}>
              <label class="form-check-label" for="ta-3">TA 点赞的成员</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" id="ta-4" name="it_home_list" value="it_like_groups" class="form-check-input" {{ $params['it_home_list'] == 'it_like_groups' ? 'checked' : '' }}>
              <label class="form-check-label" for="ta-4">TA 点赞的小组</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" id="ta-5" name="it_home_list" value="it_like_hashtags" class="form-check-input" {{ $params['it_home_list'] == 'it_like_hashtags' ? 'checked' : '' }}>
              <label class="form-check-label" for="ta-5">TA 点赞的话题</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" id="ta-6" name="it_home_list" value="it_like_posts" class="form-check-input" {{ $params['it_home_list'] == 'it_like_posts' ? 'checked' : '' }}>
              <label class="form-check-label" for="ta-6">TA 点赞的帖子</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" id="ta-7" name="it_home_list" value="it_like_comments" class="form-check-input" {{ $params['it_home_list'] == 'it_like_comments' ? 'checked' : '' }}>
              <label class="form-check-label" for="ta-7">TA 点赞的评论</label>
            </div>
          </li>
          <li class="list-group-item">
            <div class="form-check form-check-inline">
              <input type="radio" id="ta-8" name="it_home_list" value="it_follow_members" class="form-check-input" {{ $params['it_home_list'] == 'it_follow_members' ? 'checked' : '' }}>
              <label class="form-check-label" for="ta-8">TA 关注的成员</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" id="ta-9" name="it_home_list" value="it_follow_groups" class="form-check-input" {{ $params['it_home_list'] == 'it_follow_groups' ? 'checked' : '' }}>
              <label class="form-check-label" for="ta-9">TA 关注的小组</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" id="ta-10" name="it_home_list" value="it_follow_hashtags" class="form-check-input" {{ $params['it_home_list'] == 'it_follow_hashtags' ? 'checked' : '' }}>
              <label class="form-check-label" for="ta-10">TA 关注的话题</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" id="ta-11" name="it_home_list" value="it_follow_posts" class="form-check-input" {{ $params['it_home_list'] == 'it_follow_posts' ? 'checked' : '' }}>
              <label class="form-check-label" for="ta-11">TA 关注的帖子</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" id="ta-12" name="it_home_list" value="it_follow_comments" class="form-check-input" {{ $params['it_home_list'] == 'it_follow_comments' ? 'checked' : '' }}>
              <label class="form-check-label" for="ta-12">TA 关注的评论</label>
            </div>
          </li>
          <li class="list-group-item">
            <div class="form-check form-check-inline">
              <input type="radio" id="ta-13" name="it_home_list" value="it_shield_members" class="form-check-input" {{ $params['it_home_list'] == 'it_shield_members' ? 'checked' : '' }}>
              <label class="form-check-label" for="ta-13">TA 屏蔽的成员</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" id="ta-14" name="it_home_list" value="it_shield_groups" class="form-check-input" {{ $params['it_home_list'] == 'it_shield_groups' ? 'checked' : '' }}>
              <label class="form-check-label" for="ta-14">TA 屏蔽的小组</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" id="ta-15" name="it_home_list" value="it_shield_hashtags" class="form-check-input" {{ $params['it_home_list'] == 'it_shield_hashtags' ? 'checked' : '' }}>
              <label class="form-check-label" for="ta-15">TA 屏蔽的话题</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" id="ta-16" name="it_home_list" value="it_shield_posts" class="form-check-input" {{ $params['it_home_list'] == 'it_shield_posts' ? 'checked' : '' }}>
              <label class="form-check-label" for="ta-16">TA 屏蔽的帖子</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="radio" id="ta-17" name="it_home_list" value="it_shield_comments" class="form-check-input" {{ $params['it_home_list'] == 'it_shield_comments' ? 'checked' : '' }}>
              <label class="form-check-label" for="ta-17">TA 屏蔽的评论</label>
            </div>
          </li>
        </ul>
      </div>
    </div>
    <!--保存按钮-->
    <div class="row my-3">
      <div class="col-lg-2"></div>
      <div class="col-lg-8">
        <button type="submit" class="btn btn-primary">提交保存</button>
      </div>
    </div>
  </form>
@endsection
