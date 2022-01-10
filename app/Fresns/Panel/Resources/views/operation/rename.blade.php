@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::operation.sidebar')
@endsection

@section('content')

  <div class="row mb-4 border-bottom">
    <div class="col-lg-7">
      <h3>命名配置</h3>
      <p class="text-secondary">通过自定义命名改变用户的感知，实现各种运营场景。</p>
    </div>
    <div class="col-lg-5">
      <div class="input-group mt-2 mb-4 justify-content-lg-end">
        <a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
      </div>
    </div>
  </div>
  <!--成员命名设置-->
  <div class="row mb-3">
    <label class="col-lg-2 col-form-label text-lg-end">成员命名设置：</label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">身份自定义名称</label>
        <button class="btn btn-outline-secondary text-start rename-btn" type="button"
          data-bs-toggle="modal"
          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'member_name'])}}"
          data-languages="{{ json_encode($langParams['member_name'] ?? [])}}"
          data-item_key="member_name"
          data-bs-target="#langModal">{{ $params['member_name'] ?? '' }}</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“账号”、“用户”、“会员”等命名</div>
  </div>
  <div class="row mb-3">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">ID 自定义名称</label>
        <button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">UID</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“mid”、“uid”、“aid”等命名</div>
  </div>
  <div class="row mb-3">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">名称自定义名称</label>
        <button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">用户名</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“用户名”、“成员名”、“账号”等命名</div>
  </div>
  <div class="row mb-3">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">昵称自定义名称</label>
        <button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">用户昵称</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“用户昵称”、“花名”、“代号”等命名</div>
  </div>
  <div class="row mb-5">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">角色自定义名称</label>
        <button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">用户角色</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“用户角色”、“用户组”、“用户群”等命名</div>
  </div>
  <!--内容命名设置-->
  <div class="row mb-3">
    <label class="col-lg-2 col-form-label text-lg-end">内容命名设置：</label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">小组自定义名称</label>
        <button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">小组</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“版区”、“圈子”、“分类”等命名</div>
  </div>
  <div class="row mb-3">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">话题自定义名称</label>
        <button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">话题</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“超话”、“标签”、“话题”等命名</div>
  </div>
  <div class="row mb-3">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">帖子自定义名称</label>
        <button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">帖子</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“帖子”、“动态”、“说说”等命名</div>
  </div>
  <div class="row mb-5">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">评论自定义名称</label>
        <button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">评论</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“评论”、“回复”、“互动”等命名</div>
  </div>
  <!--发表行为命名设置-->
  <div class="row mb-3">
    <label class="col-lg-2 col-form-label text-lg-end">发表行为命名设置：</label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">发表帖子行为名称</label>
        <button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">发表</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“发表”、“投稿”、“反馈”等命名</div>
  </div>
  <div class="row mb-5">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">发表评论行为名称</label>
        <button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">评论</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“回复”、“回帖”、“跟帖”等命名</div>
  </div>
  <!--点赞行为命名设置-->
  <div class="row mb-3">
    <label class="col-lg-2">点赞行为命名设置：</label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">点赞用户行为</label>
        <button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">喜欢</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“点赞”、“喜欢”、“投一票”等命名</div>
  </div>
  <div class="row mb-3">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">点赞小组行为</label>
        <button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">点赞</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“点赞”、“喜欢”、“投一票”等命名</div>
  </div>
  <div class="row mb-3">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">点赞话题行为</label>
        <button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">点赞</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“点赞”、“喜欢”、“投一票”等命名</div>
  </div>
  <div class="row mb-3">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">点赞帖子行为</label>
        <button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">点赞</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“点赞”、“喜欢”、“顶一顶”等命名</div>
  </div>
  <div class="row mb-5">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">点赞评论行为</label>
        <button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">点赞</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“点赞”、“喜欢”、“顶一顶”等命名</div>
  </div>
  <!--关注行为命名设置-->
  <div class="row mb-3">
    <label class="col-lg-2">关注行为命名设置：</label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">关注用户行为</label>
        <button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">关注</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“关注”、“喜欢”、“跟进”等命名</div>
  </div>
  <div class="row mb-3">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">关注小组行为</label>
        <button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">加入</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“关注”、“加入”、“订阅”等命名</div>
  </div>
  <div class="row mb-3">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">关注话题行为</label>
        <button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">订阅</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“关注”、“订阅”、“跟进”等命名</div>
  </div>
  <div class="row mb-3">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">关注帖子行为</label>
        <button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">收藏</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“收藏”、“喜欢”、“保存”等命名</div>
  </div>
  <div class="row mb-5">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">关注评论行为</label>
        <button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">收藏</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“收藏”、“喜欢”、“保存”等命名</div>
  </div>
  <!--屏蔽行为命名设置-->
  <div class="row mb-3">
    <label class="col-lg-2">屏蔽行为命名设置：</label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">屏蔽用户行为</label>
        <button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">拉黑</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“拉黑”、“屏蔽”、“讨厌”等命名</div>
  </div>
  <div class="row mb-3">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">屏蔽小组行为</label>
        <button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">屏蔽</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“不喜欢”、“不感兴趣”、“屏蔽”等命名</div>
  </div>
  <div class="row mb-3">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">屏蔽话题行为</label>
        <button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">不感兴趣</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“不喜欢”、“不感兴趣”、“屏蔽”等命名</div>
  </div>
  <div class="row mb-3">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">屏蔽帖子行为</label>
        <button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">不喜欢</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“不喜欢”、“不感兴趣”、“踩一踩”等命名</div>
  </div>
  <div class="row mb-5">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">屏蔽评论行为</label>
        <button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">不喜欢</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“不喜欢”、“不感兴趣”、“踩一踩”等命名</div>
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
          <form action="" method="post">
            @csrf
            @method('put')

            <input type="hidden" name="update_config" value="">
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
                      <td><input type="text" name="languages[{{ $lang['langTag'] }}]" class="form-control" value="{{ $langParams['site_name'][$lang['langTag']] ?? '' }}"></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <!--保存按钮-->
            <div class="text-center">
              <button type="submit" class="btn btn-success" data-bs-dismiss="modal" aria-label="Close">确认</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
