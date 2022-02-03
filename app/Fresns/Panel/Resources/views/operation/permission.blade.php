@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::operation.sidebar')
@endsection

@section('content')
  <div class="row mb-4 border-bottom">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('panel.dashboard') }}">仪表盘</a></li>
        <li class="breadcrumb-item"><a href="{{ route('panel.renameConfigs.show') }}">运营配置</a></li>
        <li class="breadcrumb-item"><a href="{{ route('panel.memberRoles.index' )}}">用户角色</a></li>
        <li class="breadcrumb-item active" aria-current="page">设置权限<span class="badge bg-secondary ms-2">普通会员</span></li>
      </ol>
    </nav>
  </div>
  <!--表单 开始-->
  <form action="{{ route('panel.memberRoles.permissions.update', $memberRole->id) }}" method="post">
    @csrf
    @method('put')
    <div class="row mb-4">
      <label class="col-lg-2 col-form-label text-lg-end">基础设置：</label>
      <div class="col-lg-6">
        <!--浏览权限-->
        <div class="input-group mb-3">
          <label class="input-group-text">浏览权限</label>
          <div class="form-control bg-white">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" {{ ($permission['content_view']['permValue'] ?? '') ? 'checked' : ''}} name="permission[content_view]" id="content.view.0" value="0">
              <label class="form-check-label" for="content.view.0">允许浏览</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" {{ !($permission['content_view']['permValue'] ?? '') ? 'checked' : ''}} name="permission[content_view]" id="content.view.1" value="1">
              <label class="form-check-label" for="content.view.1">禁止浏览</label>
            </div>
          </div>
        </div>
        <!--会话权限-->
        <div class="input-group">
          <label class="input-group-text">会话权限</label>
          <div class="form-control bg-white">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" {{ ($permission['dialog']['permValue'] ?? '') ? 'checked' : ''}} name="permission[dialog]" id="dialog.0" value="0">
              <label class="form-check-label" for="dialog.0">允许</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" {{ !($permission['dialog']['permValue'] ?? '') ? 'checked' : ''}} name="permission[dialog]" id="dialog.1" value="1">
              <label class="form-check-label" for="dialog.1">禁用发送私信会话</label>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row mb-4">
      <label class="col-lg-2 col-form-label text-lg-end">发表帖子设置：</label>
      <div class="col-lg-6">
        <!--发表帖子权限-->
        <div class="input-group mb-3">
          <label class="input-group-text">发表帖子权限</label>
          <div class="form-control bg-white">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" {{ ($permission['post_publish']['permValue'] ?? '') ? 'checked' : ''}} name="permission[post_publish]" id="publish.post.0" value="0">
              <label class="form-check-label" for="publish.post.0">允许</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" {{ !($permission['post_publish']['permValue'] ?? '') ? 'checked' : ''}} name="permission[post_publish]" id="publish.post.1" value="1">
              <label class="form-check-label" for="publish.post.1">禁止</label>
            </div>
          </div>
        </div>
        <!--发表帖子要求-->
        <div class="input-group mb-3">
          <label class="input-group-text">发表帖子要求</label>
          <div class="form-control bg-white">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" {{ ($permission['post_email_verify']['permValue'] ?? '') ? 'checked' : ''}} name="permission[post_email_verify]" id="publish.post.verify.email" value="1">
              <label class="form-check-label" for="publish.post.verify.email">已绑定邮箱</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" {{ ($permission['post_phone_verify']['permValue'] ?? '') ? 'checked' : ''}} name="permission[post_phone_verify]" id="publish.post.verify.phone" value="1">
              <label class="form-check-label" for="publish.post.verify.phone">已绑定手机号</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" {{ ($permission['post_prove_verify']['permValue'] ?? '') ? 'checked' : ''}} name="permission[post_prove_verify]" id="publish.post.verify.prove" value="1">
              <label class="form-check-label" for="publish.post.verify.prove">已实名认证</label>
            </div>
          </div>
        </div>
        <!--发表帖子是否需要审核-->
        <div class="input-group mb-3">
          <label class="input-group-text">发表帖子规则</label>
          <div class="form-control bg-white">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" {{ !($permission['post_review']['permValue'] ?? '') ? 'checked' : ''}} name="permission[post_review]" id="publish.post.review.0" value="0">
              <label class="form-check-label" for="publish.post.review.0">直接通过</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" {{ ($permission['post_review']['permValue'] ?? '') ? 'checked' : ''}} name="permission[post_review]" id="publish.post.review.1" value="1">
              <label class="form-check-label" for="publish.post.review.1">需要审核</label>
            </div>
          </div>
        </div>
        <!--发表帖子特殊规则-->
        <div class="input-group mb-3">
          <label class="input-group-text">发表帖子特殊规则</label>
          <div class="form-control bg-white">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" {{ !($permission['post_limit_status']['permValue'] ?? '') ? 'checked' : ''}} name="post.limit.status" id="post.limit.status.0" value="0" data-bs-toggle="collapse" data-bs-target="#post_limit_setting.show" aria-expanded="false" aria-controls="post_limit_setting">
              <label class="form-check-label" for="post.limit.status.0">关闭特殊规则</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" {{ ($permission['post_limit_status']['permValue'] ?? '') ? 'checked' : ''}} name="post.limit.status" id="post.limit.status.1" value="1" data-bs-toggle="collapse" data-bs-target="#post_limit_setting:not(.show)" aria-expanded="false" aria-controls="post_limit_setting">
              <label class="form-check-label" for="post.limit.status.1">开启特殊规则</label>
            </div>
          </div>
        </div>
        <!--发表帖子特殊规则配置 开始-->
        <div class="collapse" id="post_limit_setting">
          <div class="input-group mb-3">
            <label class="input-group-text">规则类型</label>
            <select class="form-select" id="post_limit_type" name="permission[post_limit_type]">
              <option value="1" id="post_date" {{ ($permission['post_limit_type']['permValue'] ?? '') == 1 ? 'selected' : '' }} >指定日期范围内全天生效</option>
              <option value="2" id="post_datetime" {{ ($permission['post_limit_type']['permValue'] ?? '') == 2 ? 'selected' : '' }}>指定某个时间段范围内生效</option>
            </select>
          </div>
          <div class="input-group mb-3 collapse {{ $permission['post_limit_type']['permValue'] == 1 ? 'show' : ''}}" id="post_date_setting">
            <label class="input-group-text">日期范围</label>
            <input type="date" class="form-control" value="{{ ($permission['post_limit_period_start']['permValue'] ?? '') }}" name="permission[post_limit_period_start]" placeholder="2021/01/01">
            <input type="date" class="form-control" value="{{ ($permission['post_limit_period_end']['permValue'] ?? '') }}" name="permission[post_limit_period_end]" placeholder="2021/01/05">
          </div>
          <div class="input-group mb-3 collapse {{ $permission['post_limit_type']['permValue'] == 2 ? 'show' : ''}}" id="post_datetime_setting">
            <label class="input-group-text">时间段范围</label>
            <input type="datetime-local" class="form-control" value="{{ ($permission['post_limit_cycle_start']['permValue'] ?? '') }}" name="permission[post_limit_cycle_start]" placeholder="2021/01/01 22:00:00">
            <input type="datetime-local" class="form-control" value="{{ ($permission['post_limit_cycle_end']['permValue'] ?? '') }}" name="permission[post_limit_cycle_end]" placeholder="2021/01/05 09:00:00">
          </div>
          {{--<div class="input-group mb-3 collapse show" id="post_time_setting">--}}
            {{--<label class="input-group-text">时间范围</label>--}}
            {{--<input type="time" class="form-control" value="{{ ($permission[]['permValue'] ?? '') }}" placeholder="22:30:00">--}}
            {{--<input type="time" class="form-control" value="{{ ($permission[]['permValue'] ?? '') }}" placeholder="08:30:00">--}}
          {{--</div>--}}
          <div class="input-group mb-3">
            <label class="input-group-text">规则要求</label>
            <div class="form-control bg-white">
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" {{ ($permission['post_limit_rule']['permValue'] ?? '') ? 'checked' : ''}} name="permission[post_limit_rule]" id="post.limit.rule.0" value="0">
                <label class="form-check-label" for="post.limit.rule.0">可以发表，但是需要审核</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" {{ !($permission['post_limit_rule']['permValue'] ?? '') ? 'checked' : ''}} name="permission[post_limit_rule]" id="post.limit.rule.1" value="1">
                <label class="form-check-label" for="post.limit.rule.1">禁止发表</label>
              </div>
            </div>
          </div>
        </div>
        <!--发表帖子特殊规则配置 结束-->
      </div>
    </div>

    <div class="row mb-4">
      <label class="col-lg-2 col-form-label text-lg-end">发表评论设置：</label>
      <div class="col-lg-6">
        <!--发表评论权限-->
        <div class="input-group mb-3">
          <label class="input-group-text">发表评论权限</label>
          <div class="form-control bg-white">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" {{ ($permission['comment_publish']['permValue'] ?? '') ? 'checked' : ''}} name="permission[comment_publish]" id="publish.comment.0" value="0">
              <label class="form-check-label" for="publish.comment.0">允许</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio"{{ !($permission['comment_publish']['permValue'] ?? '') ? 'checked' : ''}}  name="permission[comment_publish]" id="publish.comment.1" value="1">
              <label class="form-check-label" for="publish.comment.1">禁止</label>
            </div>
          </div>
        </div>
        <!--发表评论要求-->
        <div class="input-group mb-3">
          <label class="input-group-text">发表评论要求</label>
          <div class="form-control bg-white">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" {{ ($permission['comment_email_verify']['permValue'] ?? '') ? 'checked' : ''}} name="permission[comment_email_verify]" id="publish.comment.verify.email" value="1">
              <label class="form-check-label" for="publish.comment.verify.email">已绑定邮箱</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" {{ ($permission['comment_phone_verify']['permValue'] ?? '') ? 'checked' : ''}} name="permission[comment_phone_verify]"  id="publish.comment.verify.phone" value="1">
              <label class="form-check-label" for="publish.comment.verify.phone">已绑定手机号</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" {{ ($permission['comment_prove_verify']['permValue'] ?? '') ? 'checked' : ''}} name="permission[comment_prove_verify]"  id="publish.comment.verify.prove" value="1">
              <label class="form-check-label" for="publish.comment.verify.prove">已实名认证</label>
            </div>
          </div>
        </div>
        <!--发表评论是否需要审核-->
        <div class="input-group mb-3">
          <label class="input-group-text">发表评论规则</label>
          <div class="form-control bg-white">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" {{ !($permission['comment_review']['permValue'] ?? '') ? 'checked' : ''}} name="permission[comment_review]" id="publish.comment.review.0" value="0">
              <label class="form-check-label" for="publish.comment.review.0">直接通过</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" {{ ($permission['comment_review']['permValue'] ?? '') ? 'checked' : ''}} name="permission[comment_review]" id="publish.comment.review.1" value="1">
              <label class="form-check-label" for="publish.comment.review.1">需要审核</label>
            </div>
          </div>
        </div>
        <!--发表评论特殊规则-->
        <div class="input-group mb-3">
          <label class="input-group-text">发表评论特殊规则</label>
          <div class="form-control bg-white">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" {{ !($permission['comment_limit_status']['permValue'] ?? '') ? 'checked' : ''}} name="permission[comment_limit_status]" id="comment.limit.status.0" value="0" data-bs-toggle="collapse" data-bs-target="#comment_limit_setting.show" aria-expanded="false" aria-controls="comment_limit_setting" checked>
              <label class="form-check-label" for="comment.limit.status.0">关闭特殊规则</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" {{ ($permission['comment_limit_status']['permValue'] ?? '') ? 'checked' : ''}} name="psermission[comment_limit_status]" id="comment.limit.status.1" value="1" data-bs-toggle="collapse" data-bs-target="#comment_limit_setting:not(.show)" aria-expanded="false" aria-controls="comment_limit_setting">
              <label class="form-check-label" for="comment.limit.status.1">开启特殊规则</label>
            </div>
          </div>
        </div>
        <!--发表评论特殊规则配置 开始-->
        <div class="collapse" id="comment_limit_setting">
          <div class="input-group mb-3">
            <label class="input-group-text">规则类型</label>
            <select class="form-select" id="comment_limit_status">
              <option value="1" id="comment_date" selected>指定日期范围内全天生效</option>
              <option value="2" id="comment_datetime">指定某个时间段范围内生效</option>
              {{--<option value="3" id="comment_time">指定每天的某个时间段范围内循环生效</option>--}}
            </select>
          </div>
          <div class="input-group mb-3 collapse" id="comment_date_setting">
            <label class="input-group-text">日期范围</label>
            <input type="date" class="form-control" value="{{ ($permission['comment_limit_period_start']['permValue'] ?? '') }}" name="permission[comment_limit_period_start]" placeholder="2021/01/01">
            <input type="date" class="form-control" value="{{ ($permission['comment_limit_period_end']['permValue'] ?? '') }}" name="permission[comment_limit_period_end]" placeholder="2021/01/05">
          </div>
          <div class="input-group mb-3 collapse" id="comment_datetime_setting">
            <label class="input-group-text">时间段范围</label>
            <input type="datetime-local" class="form-control" value="{{ ($permission['comment_limit_cycle_start']['permValue'] ?? '') }}" name="permission[comment_limit_cycle_start]" placeholder="2021/01/01 22:00:00">
            <input type="datetime-local" class="form-control" value="{{ ($permission['comment_limit_cycle_end']['permValue'] ?? '') }}" name="permission[comment_limit_cycle_end]" placeholder="2021/01/05 09:00:00">
          </div>
          {{--<div class="input-group mb-3 collapse" id="comment_time_setting">--}}
            {{--<label class="input-group-text">时间范围</label>--}}
            {{--<input type="time" class="form-control" placeholder="22:30:00">--}}
            {{--<input type="time" class="form-control" placeholder="08:30:00">--}}
          {{--</div>--}}
          <div class="input-group mb-3">
            <label class="input-group-text">规则要求</label>
            <div class="form-control bg-white">
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" {{ ($permission['comment_limit_rule']['permValue'] ?? '') ? 'checked' : ''}}  name="permission[comment_limit_rule]" id="comment.limit.rule.0" value="0">
                <label class="form-check-label" for="comment.limit.rule.0">可以发表，但是需要审核</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" {{ !($permission['comment_limit_rule']['permValue'] ?? '') ? 'checked' : ''}}  name="permission[comment_limit_rule]" id="comment.limit.rule.1" value="1">
                <label class="form-check-label" for="comment.limit.rule.1">禁止发表</label>
              </div>
            </div>
          </div>
        </div>
        <!--发表帖子特殊规则配置 结束-->
      </div>
    </div>

    <div class="row mb-4">
      <label class="col-lg-2 col-form-label text-lg-end">上传设置：</label>
      <div class="col-lg-10">
        <!--上传图片-->
        <div class="input-group mb-3">
          <label class="input-group-text">上传图片</label>
          <div class="input-group-text">
            <input class="form-check-input mt-0" type="checkbox" {{ ($permission['post_editor_image']['permValue'] ?? '') ? 'checked' : ''}} name="permission[post_editor_image]" id="post_editor_image" value="0">
            <label class="form-check-label ms-1" for="post_editor_image">帖子</label>
          </div>
          <div class="input-group-text">
            <input class="form-check-input mt-0" type="checkbox" {{ ($permission['comment_editor_image']['permValue'] ?? '') ? 'checked' : ''}} name="permission[comment_editor_image]" id="comment_editor_image" value="0">
            <label class="form-check-label ms-1" for="comment_editor_image">评论</label>
          </div>
          <input type="number" class="form-control input-number" value="{{ ($permission['images_max_size']['permValue'] ?? '') }}" name="permission[images_max_size]" placeholder="上传图片最大尺寸">
          <span class="input-group-text">MB</span>
        </div>
        <!--上传视频-->
        <div class="input-group mb-3">
          <label class="input-group-text">上传视频</label>
          <div class="input-group-text">
            <input class="form-check-input mt-0" type="checkbox" {{ ($permission['post_editor_video']['permValue'] ?? '') ? 'checked' : ''}} name="permission[post_editor_video]" id="post_editor_video" value="0">
            <label class="form-check-label ms-1" for="post_editor_video">帖子</label>
          </div>
          <div class="input-group-text">
            <input class="form-check-input mt-0" type="checkbox" {{ ($permission['comment_editor_video']['permValue'] ?? '') ? 'checked' : ''}} name="permission[comment_editor_video]" id="comment_editor_video" value="0">
            <label class="form-check-label ms-1" for="comment_editor_video">评论</label>
          </div>
          <input type="number" class="form-control input-number" value="{{ ($permission['videos_max_size']['permValue'] ?? '') }}" name="permission[videos_max_size]" placeholder="上传视频最大尺寸">
          <span class="input-group-text">MB</span>
          <input type="number" class="form-control input-number" value="{{ ($permission['videos_max_time']['permValue'] ?? '') }}" name="permission[videos_max_time]" placeholder="上传视频最大时长">
          <span class="input-group-text">秒</span>
        </div>
        <!--上传音频-->
        <div class="input-group mb-3">
          <label class="input-group-text">上传音频</label>
          <div class="input-group-text">
            <input class="form-check-input mt-0" type="checkbox" {{ ($permission['post_editor_audio']['permValue'] ?? '') ? 'checked' : ''}} name="permission[post_editor_audio]" id="post_editor_audio" value="0">
            <label class="form-check-label ms-1" for="post_editor_audio">帖子</label>
          </div>
          <div class="input-group-text">
            <input class="form-check-input mt-0" type="checkbox" {{ ($permission['comment_editor_audio']['permValue'] ?? '') ? 'checked' : ''}} name="permission[comment_editor_audio]" id="comment_editor_audio" value="0">
            <label class="form-check-label ms-1" for="comment_editor_audio">评论</label>
          </div>
          <input type="number" class="form-control input-number" value="{{ ($permission['audios_max_size']['permValue'] ?? '') }}" name="permission[audios_max_size]" placeholder="上传音频最大尺寸">
          <span class="input-group-text">MB</span>
          <input type="number" class="form-control input-number" value="{{ ($permission['audios_max_time']['permValue'] ?? '') }}" name="permission[audios_max_time]" placeholder="上传音频最大时长">
          <span class="input-group-text">秒</span>
        </div>
        <!--上传文档-->
        <div class="input-group mb-3">
          <label class="input-group-text">上传文档</label>
          <div class="input-group-text">
            <input class="form-check-input mt-0" type="checkbox" {{ ($permission['post_editor_doc']['permValue'] ?? '') ? 'checked' : ''}} name="permission[post_editor_doc]" id="post_editor_doc" value="0">
            <label class="form-check-label ms-1" for="post_editor_doc">帖子</label>
          </div>
          <div class="input-group-text">
            <input class="form-check-input mt-0" type="checkbox" {{ ($permission['comment_editor_doc']['permValue'] ?? '') ? 'checked' : ''}} name="permission[comment_editor_doc]" id="comment_editor_doc" value="0">
            <label class="form-check-label ms-1" for="comment_editor_doc">评论</label>
          </div>
          <input type="number" class="form-control input-number" value="{{ ($permission['docs_max_size']['permValue'] ?? '') }}" name="permission[docs_max_size]" placeholder="上传文档最大尺寸">
          <span class="input-group-text">MB</span>
        </div>
        <div class="form-text"><i class="bi bi-info-circle"></i> 勾选代表有权上传，输入框留空则使用<a href="system-storage-image.html">存储配置</a>的设置值作为默认参数。</div>
      </div>
    </div>

    <div class="row mb-4">
      <label class="col-lg-2 col-form-label text-lg-end">下载设置：</label>
      <div class="col-lg-10">
        <!--下载计数-->
        <div class="input-group mb-3">
          <label class="input-group-text">24 小时内下载上限</label>
          <input type="number" class="form-control input-number" value="{{ ($permission['download_file_count']['permValue'] ?? '') }}" name="permission[download_file_count]" placeholder="10">
          <span class="input-group-text">次数</span>
        </div>
      </div>
    </div>

    <div class="row mb-4">
      <label class="col-lg-2 col-form-label text-lg-end">自定义设置：</label>
      <div class="col-lg-10">
        <!--操作列表-->
        <div class="table-responsive">
          <table class="table table-hover align-middle text-nowrap">
            <thead>
              <tr class="table-info">
                <th scope="col">权限名</th>
                <th scope="col">权限值</th>
                <th scope="col" style="width:6rem;">操作</th>
              </tr>
            </thead>
            <tbody id="customPermBox">
              @foreach($customPermission as $permission)
                <tr>
                  <td><input type="text" class="form-control" name="custom_permissions[permKey][]" value="{{ $permission['permKey'] ?? ''}}" readonly></td>
                  <td><input type="text" class="form-control" name="custom_permissions[permValue][]" value="{{ $permission['permValue'] ?? ''}}" readonly></td>
                  <td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7 delete-custom-perm">删除</button></td>
                </tr>
              @endforeach
              <tr id="addCustomPermTr">
                <td colspan="3" class="text-center">
                  <button class="btn btn-outline-success btn-sm px-3" id="addCustomPerm" type="button">
                    <i class="bi bi-plus-circle-dotted"></i> 新增
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <!--操作列表 结束-->
      </div>
    </div>

    <!--保存按钮-->
    <div class="row my-5">
      <div class="col-lg-2"></div>
      <div class="col-lg-5">
        <button type="submit" class="btn btn-primary">提交保存</button>
      </div>
    </div>
  </form>
  <!--表单 结束-->

  <template id="customPerm">
    <tr>
      <td><input type="text" class="form-control" required name="custom_permissions[permKey][]"></td>
      <td><input type="text" class="form-control" required name="custom_permissions[permValue][]"></td>
      <td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button></td>
    </tr>
  </template>

@endsection
