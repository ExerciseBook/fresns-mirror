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
      <li class="nav-item"><a class="nav-link active" href="{{ route('panel.postConfigs.show') }}">发表帖子</a></li>
      <li class="nav-item"><a class="nav-link" href="{{ route('panel.commentConfigs.show') }}">发表评论</a></li>
    </ul>
  </div>
  <!--操作列表-->
  <form action="{{ route('panel.postConfigs.update') }}" method="post">
    @csrf
    @method('put')
    <!--发表帖子要求-->
    <div class="row mb-3">
      <label class="col-lg-2 col-form-label text-lg-end">发表帖子要求：</label>
      <div class="col-lg-6 pt-2">
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="post_email_verify" id="comment_email_verify" value="true" {{ $params['post_email_verify']=='true' ? 'checked' : ''}}>
          <label class="form-check-label" for="comment_email_verify">已绑定邮箱</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="post_phone_verify" id="comment_phone_verify" value="true" {{ $params['post_phone_verify']=='true' ? 'checked' : ''}}>
          <label class="form-check-label" for="post_phone_verify">已绑定手机号</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="post_prove_verify" id="comment_prove_verify" value="true" {{ $params['post_prove_verify']=='true' ? 'checked' : ''}}>
          <label class="form-check-label" for="post_prove_verify">已实名认证</label>
        </div>
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 用户必须满足所选条件才能发表内容</div>
    </div>
    <!--发表帖子特殊规则-->
    <div class="row mb-3">
      <label class="col-lg-2 col-form-label text-lg-end">发表帖子特殊规则：</label>
      <div class="col-lg-6 pt-2">
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="post_limit_status" id="post_limit_status_0" data-bs-toggle="collapse" data-bs-target="#post_limit_setting.show" aria-expanded="false" aria-controls="post_limit_setting" value="false" {{ $params['post_limit_status']=='false' ? 'checked' : ''}}>
          <label class="form-check-label" for="post_limit_status_0">关闭特殊规则</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="post_limit_status" id="post_limit_status_1" data-bs-toggle="collapse" data-bs-target="#post_limit_setting:not(.show)" aria-expanded="false" aria-controls="post_limit_setting" value="true" {{ $params['post_limit_status']=='true' ? 'checked' : ''}}>
          <label class="form-check-label" for="post_limit_status_1">开启特殊规则</label>
        </div>
        <!--发表帖子特殊规则配置 开始-->
        <div class="collapse mt-3 {{ $params['post_limit_status']=='false' ? '' : 'show'}}" id="post_limit_setting">
          <div class="input-group mb-3">
            <label class="input-group-text fresns-label">规则类型</label>
            <select class="form-select" id="post_limit_type" name="post_limit_type">
              <option value="1" {{ $params['post_limit_type']=='1' ? 'selected' : ''}}>指定日期范围内全天生效</option>
              <option value="2"  {{ $params['post_limit_type']=='2' ? 'selected' : ''}}>指定每天的某个时间段范围内循环生效</option>
            </select>
          </div>
          <div class="input-group mb-3" id="post_date_setting">
            <label class="input-group-text fresns-label">日期范围</label>
            <input type="date" name="post_limit_period_start" value="{{$params['post_limit_period_start']}}" class="form-control" placeholder="2021/01/01">
            <input type="date" name="post_limit_period_end" value="{{$params['post_limit_period_end']}}" class="form-control" placeholder="2021/01/05">
          </div>
          <div class="input-group mb-3" id="post_time_setting" style="display:none;" >
            <label class="input-group-text fresns-label">时间范围</label>
            <input type="time" name="post_limit_cycle_start" value="{{$params['post_limit_cycle_start']}}" class="form-control" placeholder="22:30:00">
            <input type="time" name="post_limit_cycle_end" value="{{$params['post_limit_cycle_end']}}" class="form-control" placeholder="08:30:00">
          </div>
          <div class="input-group mb-3">
            <label class="input-group-text fresns-label">规则要求</label>
            <div class="form-control bg-white">
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="post_limit_rule" id="post.limit.rule.0" value="1" {{ $params['post_limit_rule']=='1' ? 'checked' : ''}}>
                <label class="form-check-label" for="post.limit.rule.0">可以发表，但是需要审核</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="post_limit_rule" id="post.limit.rule.1" value="2" {{ $params['post_limit_rule']=='2' ? 'checked' : ''}}>
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
            <select class="form-select" name="post_limit_whitelist">
              <option selected disabled>这是 liveSearch 多选框，暂未加载样式组件，所以原型显示为单选下拉框</option>
              <option value="4">角色名</option>
              <option value="1">角色名</option>
              <option value="2">角色名</option>
            </select>
          </div>
        </div>
        <!--发表帖子特殊规则配置 结束-->
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 全员有效，优先级大于角色规则要求</div>
    </div>
    <!--帖子编辑权限-->
    <div class="row mb-5">
      <label class="col-lg-2 col-form-label text-lg-end">帖子编辑权限：</label>
      <div class="col-lg-6 pt-2">
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="post_edit" id="post_edit_status_0" data-bs-toggle="collapse" data-bs-target="#post_edit_setting.show" aria-expanded="false" aria-controls="post_edit_setting" value="false"  {{ $params['post_edit']=='false' ? 'checked' : ''}}>
          <label class="form-check-label" for="post_edit_status_0">不可编辑</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="post_edit" id="post_edit_status_1" data-bs-toggle="collapse" data-bs-target="#post_edit_setting:not(.show)" aria-expanded="true" aria-controls="post_edit_setting" value="true"  {{ $params['post_edit']=='true' ? 'checked' : ''}}>
          <label class="form-check-label" for="post_edit_status_1">可以编辑</label>
        </div>
        <!--发表帖子特殊规则配置 开始-->
        <div class="collapse mt-3 {{ $params['post_edit']=='true' ? 'show' : ''}}" id="post_edit_setting">
          <div class="input-group mb-3">
            <label class="input-group-text">多长时间内可以编辑</label>
            <input type="number" name="post_edit_timelimit" value="{{$params['post_edit_timelimit']}}" class="form-control input-number" id="post_edit_timelimit" value="30">
            <span class="input-group-text">分钟以内</span>
          </div>
          <div class="input-group mb-3">
            <label class="input-group-text">帖子置顶后编辑权限</label>
            <div class="form-control bg-white">
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="post_edit_sticky" id="post_edit_sticky_false" value="false" {{ $params['post_edit_sticky']=='false' ? 'checked' : ''}}>
                <label class="form-check-label" for="post_edit_sticky_false">不可编辑</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="post_edit_sticky" id="post_edit_sticky_true" value="true" {{ $params['post_edit_sticky']=='true' ? 'checked' : ''}}>
                <label class="form-check-label" for="post_edit_sticky_true">可以编辑</label>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <label class="input-group-text">帖子加精后编辑权限</label>
            <div class="form-control bg-white">
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="post_edit_essence" id="post_edit_essence_false" value="false"  {{ $params['post_edit_essence']=='false' ? 'checked' : ''}}>
                <label class="form-check-label" for="post_edit_essence_false">不可编辑</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="post_edit_essence" id="post_edit_essence_true" value="true"  {{ $params['post_edit_essence']=='true' ? 'checked' : ''}}>
                <label class="form-check-label" for="post_edit_essence_true">可以编辑</label>
              </div>
            </div>
          </div>
        </div>
        <!--发表帖子特殊规则配置 结束-->
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 帖子发表后是否可以编辑</div>
    </div>
    <!--编辑器选择-->
    <div class="row mb-5">
      <label class="col-lg-2 col-form-label text-lg-end">编辑器选择：</label>
      <div class="col-lg-6">
        <select class="form-select" name="post_editor_service" id="post_editor">
          <option value="1" selected>默认编辑器</option>
          <option value="2">zz编辑器</option>
          <option value="3">xx编辑器</option>
        </select>
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 发表帖子的编辑器</div>
    </div>
    <!--编辑器功能-->
    <div class="row mb-5">
      <label class="col-lg-2 col-form-label text-lg-end">编辑器功能开启：</label>
      <div class="col-lg-10">
        <ul class="list-group">
          <li class="list-group-item">
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" id="editor_group" value="true" name="post_editor_group" {{ $params['post_editor_group']=='true' ? 'checked' : ''}}>
              <label class="form-check-label" for="editor_group">小组</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" id="editor_title" value="true" name="post_editor_title" {{ $params['post_editor_title']=='true' ? 'checked' : ''}}>
              <label class="form-check-label" for="editor_title">标题</label>
            </div>
          </li>
          <li class="list-group-item">
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" id="editor_emoji" value="true" name="post_editor_emoji" {{ $params['post_editor_emoji']=='true' ? 'checked' : ''}}>
              <label class="form-check-label" for="editor_emoji">表情</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" id="editor_image" value="true" name="post_editor_image" {{ $params['post_editor_image']=='true' ? 'checked' : ''}}>
              <label class="form-check-label" for="editor_image">图片</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" id="editor_video" value="true" name="post_editor_video" {{ $params['post_editor_video']=='true' ? 'checked' : ''}}>
              <label class="form-check-label" for="editor_video">视频</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" id="editor_audio" value="true" name="post_editor_audio" {{ $params['post_editor_audio']=='true' ? 'checked' : ''}}>
              <label class="form-check-label" for="editor_audio">音频</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" id="editor_file" value="true" name="post_editor_doc" {{ $params['post_editor_doc']=='true' ? 'checked' : ''}}>
              <label class="form-check-label" for="editor_file">文档</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" id="editor_at" value="true" name="post_editor_mention" {{ $params['post_editor_mention']=='true' ? 'checked' : ''}}>
              <label class="form-check-label" for="editor_at">艾特</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" id="editor_topic" value="true" name="post_editor_hashtag" {{ $params['post_editor_hashtag']=='true' ? 'checked' : ''}}>
              <label class="form-check-label" for="editor_topic">话题</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" id="editor_expand" value="true" name="post_editor_expand" {{ $params['post_editor_expand']=='true' ? 'checked' : ''}}>
              <label class="form-check-label" for="editor_expand">扩展功能</label>
            </div>
          </li>
          <li class="list-group-item">
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" id="editor_lbs" value="true" name="post_editor_lbs" {{ $params['post_editor_lbs']=='true' ? 'checked' : ''}}>
              <label class="form-check-label" for="editor_lbs">定位</label>
            </div>
            <div class="form-check form-check-inline">
              <input type="checkbox" class="form-check-input" id="editor_anonymous" value="true" name="post_editor_anonymous" {{ $params['post_editor_anonymous']=='true' ? 'checked' : ''}}>
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
          <label class="input-group-text">小组是否必选</label>
          <div class="form-control bg-white">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="post_editor_group_required" id="post_editor_group_required_false" value="false" {{ $params['post_editor_group_required']=='false' ? 'checked' : ''}}>
              <label class="form-check-label" for="post_editor_group_required_false">否</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="post_editor_group_required" id="post_editor_group_required_true" value="true" {{ $params['post_editor_group_required']=='true' ? 'checked' : ''}}>
              <label class="form-check-label" for="post_editor_group_required_true">是</label>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 发表帖子的时候，小组是否必须选择</div>
    </div>
    <div class="row mb-3">
      <label class="col-lg-2 col-form-label text-lg-end"></label>
      <div class="col-lg-6">
        <div class="input-group">
          <label class="input-group-text">标题输入框显示</label>
          <div class="form-control bg-white">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="post_editor_title_view" id="post_editor_title_view_false" value="2" {{ $params['post_editor_title_view']=='2' ? 'checked' : ''}}>
              <label class="form-check-label" for="post_editor_title_view_false">弱显示</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="post_editor_title_view" id="post_editor_title_view_true" value="1" {{ $params['post_editor_title_view']=='1' ? 'checked' : ''}}>
              <label class="form-check-label" for="post_editor_title_view_true">强显示</label>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 弱显示需用户手动选择后才会显示标题输入框</div>
    </div>
    <div class="row mb-3">
      <label class="col-lg-2 col-form-label text-lg-end"></label>
      <div class="col-lg-6">
        <div class="input-group">
          <label class="input-group-text">标题是否必填</label>
          <div class="form-control bg-white">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="post_editor_title_required" id="post_editor_title_required_false" value="false" {{ $params['post_editor_title_required']=='false' ? 'checked' : ''}}>
              <label class="form-check-label" for="post_editor_title_required_false">否</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="post_editor_title_required" id="post_editor_title_required_true" value="true" {{ $params['post_editor_title_required']=='true' ? 'checked' : ''}}>
              <label class="form-check-label" for="post_editor_title_required_true">是</label>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 发表帖子的时候，标题是否必填</div>
    </div>
    <div class="row mb-3">
      <label class="col-lg-2 col-form-label text-lg-end"></label>
      <div class="col-lg-6">
        <div class="input-group">
          <label class="input-group-text">标题字数限制</label>
          <input type="number" class="form-control input-number" id="post_editor_title_word_count" name="post_editor_title_word_count" value="{{$params['post_editor_title_word_count']}}">
          <span class="input-group-text">字符</span>
        </div>
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 标题字数不得超过设置数，最大上限 255 个字符。</div>
    </div>
    <div class="row mb-3">
      <label class="col-lg-2 col-form-label text-lg-end"></label>
      <div class="col-lg-6">
        <div class="input-group">
          <label class="input-group-text">帖子字数限制</label>
          <input type="number" class="form-control input-number" id="post_editor_word_count" name="post_editor_word_count" value="{{$params['post_editor_word_count']}}">
          <span class="input-group-text">字符</span>
        </div>
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 帖子字数不得超过设置数，最小上限 140 个字符。</div>
    </div>
    <div class="row mb-3">
      <label class="col-lg-2 col-form-label text-lg-end"></label>
      <div class="col-lg-6">
        <div class="input-group">
          <label class="input-group-text">帖子摘要字数</label>
          <input type="number" class="form-control input-number" id="post_editor_brief_count" name="post_editor_brief_count" value="{{$params['post_editor_brief_count']}}">
          <span class="input-group-text">字符</span>
        </div>
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 帖子超过该设定值将采用摘要，超长内容摘要字数。</div>
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
                        <td>{{ $lang['langTag'] }}</td>
                        <td>{{$lang['langName']}} @if($lang['areaCode'])({{ optional($areaCodes->where('code', $lang['areaCode'])->first())['localName']}}) @endif</td>
                        <td><textarea class="form-control" name="post_limit_prompt[{{ $lang['langTag'] }}]" rows="3">{{ $languages->where('lang_tag', $lang['langTag'])->first()['lang_content'] ?? '' }}</textarea></td>
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
