@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::operation.sidebar')
@endsection

@section('content')
  <div class="row mb-4">
    <div class="col-lg-7">
      <h3>发表配置</h3>
      <p class="text-secondary">此处配置对全员有效，权限优先级大于用户角色权限。</p>
    </div>
    <div class="col-lg-5">
      <div class="input-group mt-2 mb-4 justify-content-lg-end">
        <a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
      </div>
    </div>
    <ul class="nav nav-tabs">
      <li class="nav-item"><a class="nav-link" href="{{ route('panel.postConfigs.show') }}">发表帖子</a></li>
      <li class="nav-item"><a class="nav-link active" href="{{ route('panel.commentConfigs.show') }}">发表评论</a></li>
    </ul>
  </div>
  <!--操作列表-->
  <form action="{{ route('panel.commentConfigs.update') }}" method="post">
    @csrf
    @method('put')
    <!--发表评论要求-->
    <div class="row mb-3">
      <label class="col-lg-2 col-form-label text-lg-end">发表评论要求：</label>
      <div class="col-lg-6 pt-2">
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="comment_email_verify" id="comment_email_verify" value="true" {{ $params['comment_email_verify'] ? 'checked' : ''}}>
          <label class="form-check-label" for="comment_email_verify">已绑定邮箱</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="comment_phone_verify" id="comment_phone_verify" value="true" {{ $params['comment_phone_verify'] ? 'checked' : ''}}>
          <label class="form-check-label" for="comment_phone_verify">已绑定手机号</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="comment_prove_verify" id="comment_prove_verify" value="true" {{ $params['comment_prove_verify'] ? 'checked' : ''}}>
          <label class="form-check-label" for="comment_prove_verify">已实名认证</label>
        </div>
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 用户必须满足所选条件才能发表内容</div>
    </div>
    <!--发表评论特殊规则-->
    <div class="row mb-3">
      <label class="col-lg-2 col-form-label text-lg-end">发表评论特殊规则：</label>
      <div class="col-lg-6 pt-2">
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="comment_limit_status" id="comment_limit_status_0" value="false" data-bs-toggle="collapse" data-bs-target="#comment_limit_setting.show" aria-expanded="false" aria-controls="comment_limit_setting" {{ !$params['comment_limit_status'] ? 'checked' : ''}}>
          <label class="form-check-label" for="comment_limit_status_0">关闭特殊规则</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="comment_limit_status" id="comment_limit_status_1" value="true" data-bs-toggle="collapse" data-bs-target="#comment_limit_setting:not(.show)" aria-expanded="false" aria-controls="comment_limit_setting" {{ $params['comment_limit_status'] ? 'checked' : ''}}>
          <label class="form-check-label" for="comment_limit_status_1">开启特殊规则</label>
        </div>
        <!--发表评论特殊规则配置 开始-->
        <div class="collapse mt-3  {{ $params['comment_limit_status'] ? 'show' : ''}}" id="comment_limit_setting">
          <div class="input-group mb-3">
            <label class="input-group-text fresns-label" id="post_limit_status">规则类型</label>
            <select class="form-select" id="comment_limit_type" name="comment_limit_type">
              <option value="1" {{ $params['comment_limit_type']=='1' ? 'selected' : ''}}>指定日期范围内全天生效</option>
              <option value="2" {{ $params['comment_limit_type']=='2' ? 'selected' : ''}}>指定每天的某个时间段范围内循环生效</option>
            </select>
          </div>
          <div class="input-group mb-3" id="comment_date_setting" @if($params['comment_limit_type']=='2') style="display:none;" @endif>
            <label class="input-group-text fresns-label">日期范围</label>
            <input type="date" name="comment_limit_period_start" value="{{$params['comment_limit_period_start']}}" class="form-control" placeholder="2021/01/01">
            <input type="date" name="comment_limit_period_end" value="{{$params['comment_limit_period_end']}}" class="form-control" placeholder="2021/01/05">
          </div>
          <div class="input-group mb-3" id="comment_time_setting" @if($params['comment_limit_type']=='1') style="display:none;" @endif>
            <label class="input-group-text fresns-label">时间范围</label>
            <input type="time" name="comment_limit_cycle_start" value="{{$params['comment_limit_cycle_start']}}" class="form-control" placeholder="22:30:00">
            <input type="time" name="comment_limit_cycle_end" value="{{$params['comment_limit_cycle_end']}}" class="form-control" placeholder="08:30:00">
          </div>
          <div class="input-group mb-3">
            <label class="input-group-text fresns-label">规则要求</label>
            <div class="form-control bg-white">
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="comment_limit_rule" id="post.limit.rule.0" value="1" {{ $params['comment_limit_rule']=='1' ? 'checked' : ''}}>
                <label class="form-check-label" for="post.limit.rule.0">可以发表，但是需要审核</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="comment_limit_rule" id="post.limit.rule.1" value="2" {{ $params['comment_limit_rule']=='2' ? 'checked' : ''}}>
                <label class="form-check-label" for="post.limit.rule.1">禁止发表</label>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <label class="input-group-text fresns-label">规则提示</label>
            <button class="btn btn-outline-secondary text-start fresns-control" type="button" data-bs-toggle="modal" data-bs-target="#langModal">禁止发表或者发表需要审核时，反馈给用户的提示语</button>
          </div>
          <div class="input-group mb-3">
            <label class="input-group-text fresns-label">白名单角色</label>
            <select class="form-select select2" name="comment_limit_whitelist[]">
              <option selected disabled>请选择角色</option>
			  @foreach($roles as $role)
			  	<option value="{{$role->id}}" @if($params['comment_limit_whitelist'] && is_array($params['comment_limit_whitelist']) && in_array($role->id,$params['comment_limit_whitelist'])) selected @endif>
					{{$role->name}}
				</option>
			  @endforeach
            </select>
          </div>
        </div>
        <!--发表评论特殊规则配置 结束-->
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 全员有效，优先级大于角色规则要求</div>
    </div>
    <!--评论编辑权限-->
    <div class="row mb-5">
      <label class="col-lg-2 col-form-label text-lg-end">评论编辑权限：</label>
      <div class="col-lg-6 pt-2">
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="comment_edit" id="comment_edit_status_0" value="false" data-bs-toggle="collapse" data-bs-target="#comment_edit_setting.show" aria-expanded="false" aria-controls="comment_edit_setting"  {{ !$params['comment_edit'] ? 'checked' : ''}}>
          <label class="form-check-label" for="comment_edit_status_0">不可编辑</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="comment_edit" id="comment_edit_status_1" value="true" data-bs-toggle="collapse" data-bs-target="#comment_edit_setting:not(.show)" aria-expanded="false" aria-controls="comment_edit_setting"  {{ $params['comment_edit'] ? 'checked' : ''}}>
          <label class="form-check-label" for="comment_edit_status_1">可以编辑</label>
        </div>
        <!--发表评论特殊规则配置 开始-->
        <div class="collapse mt-3  {{ $params['comment_edit'] ? 'show' : ''}}" id="comment_edit_setting">
          <div class="input-group mb-3">
            <label class="input-group-text">多长时间内可以编辑</label>
            <input type="number" class="form-control input-number" name="comment_edit_timelimit" value="{{$params['comment_edit_timelimit']}}" id="comment_edit_timelimit" value="30">
            <span class="input-group-text">分钟以内</span>
          </div>
          <div class="input-group mb-3">
            <label class="input-group-text">评论置顶后编辑权限</label>
            <div class="form-control bg-white">
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="comment_edit_sticky" id="comment_edit_sticky_false" value="false" {{ !$params['comment_edit_sticky'] ? 'checked' : ''}}>
                <label class="form-check-label" for="comment_edit_sticky_false">不可编辑</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="comment_edit_sticky" id="comment_edit_sticky_true" value="true" {{ $params['comment_edit_sticky'] ? 'checked' : ''}}>
                <label class="form-check-label" for="comment_edit_sticky_true">可以编辑</label>
              </div>
            </div>
          </div>
        </div>
        <!--发表评论特殊规则配置 结束-->
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 评论发表后是否可以编辑</div>
    </div>
    <!--编辑器选择-->
    <div class="row mb-5">
      <label class="col-lg-2 col-form-label text-lg-end">编辑器选择：</label>
      <div class="col-lg-6">
        <select class="form-select" id="comment_editor" name="comment_editor_service">
			@foreach($plugins as $plugin)
			 <option value="{{$plugin->unikey}}" @if($plugin->unikey == $params['comment_editor_service']) selected @endif>{{$plugin->name}}</option>
			@endforeach
        </select>
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 发表评论的编辑器</div>
    </div>
    <!--编辑器功能-->
    <div class="row mb-5">
      <label class="col-lg-2 col-form-label text-lg-end">编辑器功能开启：</label>
      <div class="col-lg-10">
        <ul class="list-group">
          <li class="list-group-item">
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" id="editor_emoji" value="true" name="comment_editor_emoji" {{ $params['comment_editor_emoji'] ? 'checked' : ''}}>
              <label class="form-check-label" for="editor_emoji">表情</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" id="editor_image" value="true" name="comment_editor_image" {{ $params['comment_editor_image'] ? 'checked' : ''}}>
              <label class="form-check-label" for="editor_image">图片</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" id="editor_video" value="true" name="comment_editor_video" {{ $params['comment_editor_video'] ? 'checked' : ''}}>
              <label class="form-check-label" for="editor_video">视频</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" id="editor_audio" value="true" name="comment_editor_audio" {{ $params['comment_editor_audio'] ? 'checked' : ''}}>
              <label class="form-check-label" for="editor_audio">音频</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" id="editor_file" value="true" name="comment_editor_doc" {{ $params['comment_editor_doc'] ? 'checked' : ''}}>
              <label class="form-check-label" for="editor_file">文档</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" id="editor_at" value="true" name="comment_editor_mention" {{ $params['comment_editor_mention'] ? 'checked' : ''}}>
              <label class="form-check-label" for="editor_at">艾特</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" id="editor_topic" value="true" name="comment_editor_hashtag" {{ $params['comment_editor_hashtag'] ? 'checked' : ''}}>
              <label class="form-check-label" for="editor_topic">话题</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" id="editor_expand" value="true" name="comment_editor_expand" {{ $params['comment_editor_expand'] ? 'checked' : ''}}>
              <label class="form-check-label" for="editor_expand">扩展功能</label>
            </div>
          </li>
          <li class="list-group-item">
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" id="editor_lbs" value="true" name="comment_editor_lbs" {{ $params['comment_editor_lbs'] ? 'checked' : ''}}>
              <label class="form-check-label" for="editor_lbs">定位</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" id="editor_anonymous" value="true" name="comment_editor_anonymous" {{ $params['comment_editor_anonymous'] ? 'checked' : ''}}>
              <label class="form-check-label" for="editor_anonymous">匿名</label>
            </div>
          </li>
        </ul>
      </div>
    </div>
    <!--编辑器功能配置-->
    <div class="row mb-3">
      <label class="col-lg-2 col-form-label text-lg-end">编辑器功能配置：</label>
      <div class="col-lg-6">
        <div class="input-group">
          <label class="input-group-text">评论字数限制</label>
          <input type="number" class="form-control input-number" id="comment_editor_word_count" name="comment_editor_word_count" value="{{$params['comment_editor_word_count']}}">
          <span class="input-group-text">字符</span>
        </div>
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 评论字数不得超过设置数，最小上限 140 个字符。</div>
    </div>
    <div class="row mb-3">
      <label class="col-lg-2 col-form-label text-lg-end"></label>
      <div class="col-lg-6">
        <div class="input-group">
          <label class="input-group-text">评论摘要字数</label>
          <input type="number" class="form-control input-number" id="comment_editor_brief_count" name="comment_editor_brief_count" value="{{$params['comment_editor_brief_count']}}">
          <span class="input-group-text">字符</span>
        </div>
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 评论超过该设定值将采用摘要，超长内容摘要字数。</div>
    </div>
    <!--保存按钮-->
    <div class="row mt-5">
      <div class="col-lg-2"></div>
      <div class="col-lg-8">
        <button type="submit" class="btn btn-primary">提交保存</button>
      </div>
    </div>


    <!-- Language Modal -->
    <div class="modal fade" id="langModal" tabindex="-1" aria-labelledby="langModal" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">多语言设置</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form>
              <div class="table-responsive">
                <table class="table table-hover align-middle text-nowrap">
                  <thead>
                    <tr class="table-info">
                      <th scope="col" class="w-25">语言标签</th>
                      <th scope="col" class="w-25">语言名称</th>
                      <th scope="col" class="w-50">内容</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($optionalLanguages as $lang)
                      <tr>
                        <td>
                          {{ $lang['langTag'] }}
                          @if($lang['langTag'] == $defaultLanguage)
                            <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="默认语言" aria-label="默认语言"></i>
                          @endif
                        </td>
                        <td>{{$lang['langName']}} @if($lang['areaCode'])({{ optional($areaCodes->where('code', $lang['areaCode'])->first())['localName']}}) @endif</td>
                        <td><textarea class="form-control" name="comment_limit_prompt[{{ $lang['langTag'] }}]" rows="3">{{ $languages->where('lang_tag', $lang['langTag'])->first()['lang_content'] ?? '' }}</textarea></td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              <!--保存按钮-->
              <div class="text-center">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal" aria-label="Close">确认</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

  </form>
@endsection
