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
	          data-languages="{{ optional($configs['member_name'])->languages->toJson() }}"
	          data-item_key="member_name"
	          data-bs-target="#configLangModal">{{ $defaultLangParams['member_name'] ?? '' }}
		  	</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“账号”、“用户”、“会员”等命名</div>
  </div>
  <div class="row mb-3">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
			<label class="input-group-text rename-label">ID 自定义名称</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	            data-bs-toggle="modal"
	            data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'member_id_name'])}}"
	            data-languages="{{ optional($configs['member_id_name'])->languages->toJson() }}"
	            data-item_key="member_id_name"
	            data-bs-target="#configLangModal">{{ $defaultLangParams['member_id_name'] ?? '' }}
	  	  	</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“mid”、“uid”、“aid”等命名</div>
  </div>
  <div class="row mb-3">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">名称自定义名称</label>
		<button class="btn btn-outline-secondary text-start rename-btn" type="button"
			data-bs-toggle="modal"
			data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'member_name_name'])}}"
			data-languages="{{ optional($configs['member_name_name'])->languages->toJson() }}"
			data-item_key="member_name_name"
			data-bs-target="#configLangModal">{{ $defaultLangParams['member_name_name'] ?? '' }}
		</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“用户名”、“成员名”、“账号”等命名</div>
  </div>
  <div class="row mb-3">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">昵称自定义名称</label>
		<button class="btn btn-outline-secondary text-start rename-btn" type="button"
			data-bs-toggle="modal"
			data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'member_nickname_name'])}}"
			data-languages="{{ optional($configs['member_nickname_name'])->languages->toJson() }}"
			data-item_key="member_nickname_name"
			data-bs-target="#configLangModal">{{ $defaultLangParams['member_nickname_name'] ?? '' }}
		</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“用户昵称”、“花名”、“代号”等命名</div>
  </div>
  <div class="row mb-5">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">角色自定义名称</label>
		<button class="btn btn-outline-secondary text-start rename-btn" type="button"
			data-bs-toggle="modal"
			data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'member_role_name'])}}"
			data-languages="{{ optional($configs['member_role_name'])->languages->toJson() }}"
			data-item_key="member_role_name"
			data-bs-target="#configLangModal">{{ $defaultLangParams['member_role_name'] ?? '' }}
		</button>
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
		<button class="btn btn-outline-secondary text-start rename-btn" type="button"
			data-bs-toggle="modal"
			data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'group_name'])}}"
			data-languages="{{ optional($configs['group_name'])->languages->toJson() }}"
			data-item_key="group_name"
			data-bs-target="#configLangModal">{{ $defaultLangParams['group_name'] ?? '' }}
		</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“版区”、“圈子”、“分类”等命名</div>
  </div>
  <div class="row mb-3">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">话题自定义名称</label>
		<button class="btn btn-outline-secondary text-start rename-btn" type="button"
			data-bs-toggle="modal"
			data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'hashtag_name'])}}"
			data-languages="{{ optional($configs['hashtag_name'])->languages->toJson() }}"
			data-item_key="hashtag_name"
			data-bs-target="#configLangModal">{{ $defaultLangParams['hashtag_name'] ?? '' }}
		</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“超话”、“标签”、“话题”等命名</div>
  </div>
  <div class="row mb-3">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">帖子自定义名称</label>
		<button class="btn btn-outline-secondary text-start rename-btn" type="button"
			data-bs-toggle="modal"
			data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'post_name'])}}"
			data-languages="{{ optional($configs['post_name'])->languages->toJson() }}"
			data-item_key="post_name"
			data-bs-target="#configLangModal">{{ $defaultLangParams['post_name'] ?? '' }}
		</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“帖子”、“动态”、“说说”等命名</div>
  </div>
  <div class="row mb-5">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">评论自定义名称</label>
		<button class="btn btn-outline-secondary text-start rename-btn" type="button"
			data-bs-toggle="modal"
			data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'comment_name'])}}"
			data-languages="{{ optional($configs['comment_name'])->languages->toJson() }}"
			data-item_key="comment_name"
			data-bs-target="#configLangModal">{{ $defaultLangParams['comment_name'] ?? '' }}
		</button>
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
		<button class="btn btn-outline-secondary text-start rename-btn" type="button"
			data-bs-toggle="modal"
			data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'publish_post_name'])}}"
			data-languages="{{ optional($configs['publish_post_name'])->languages->toJson() }}"
			data-item_key="publish_post_name"
			data-bs-target="#configLangModal">{{ $defaultLangParams['publish_post_name'] ?? '' }}
		</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“发表”、“投稿”、“反馈”等命名</div>
  </div>
  <div class="row mb-5">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">发表评论行为名称</label>
		<button class="btn btn-outline-secondary text-start rename-btn" type="button"
			data-bs-toggle="modal"
			data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'publish_comment_name'])}}"
			data-languages="{{ optional($configs['publish_comment_name'])->languages->toJson() }}"
			data-item_key="publish_comment_name"
			data-bs-target="#configLangModal">{{ $defaultLangParams['publish_comment_name'] ?? '' }}
		</button>
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
		<button class="btn btn-outline-secondary text-start rename-btn" type="button"
			data-bs-toggle="modal"
			data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'like_member_name'])}}"
			data-languages="{{ optional($configs['like_member_name'])->languages->toJson() }}"
			data-item_key="like_member_name"
			data-bs-target="#configLangModal">{{ $defaultLangParams['like_member_name'] ?? '' }}
		</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“点赞”、“喜欢”、“投一票”等命名</div>
  </div>
  <div class="row mb-3">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">点赞小组行为</label>
		<button class="btn btn-outline-secondary text-start rename-btn" type="button"
			data-bs-toggle="modal"
			data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'like_group_name'])}}"
			data-languages="{{ optional($configs['like_group_name'])->languages->toJson() }}"
			data-item_key="like_group_name"
			data-bs-target="#configLangModal">{{ $defaultLangParams['like_group_name'] ?? '' }}
		</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“点赞”、“喜欢”、“投一票”等命名</div>
  </div>
  <div class="row mb-3">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">点赞话题行为</label>
		<button class="btn btn-outline-secondary text-start rename-btn" type="button"
			data-bs-toggle="modal"
			data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'like_hashtag_name'])}}"
			data-languages="{{ optional($configs['like_hashtag_name'])->languages->toJson() }}"
			data-item_key="like_hashtag_name"
			data-bs-target="#configLangModal">{{ $defaultLangParams['like_hashtag_name'] ?? '' }}
		</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“点赞”、“喜欢”、“投一票”等命名</div>
  </div>
  <div class="row mb-3">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">点赞帖子行为</label>
		<button class="btn btn-outline-secondary text-start rename-btn" type="button"
			data-bs-toggle="modal"
			data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'like_post_name'])}}"
			data-languages="{{ optional($configs['like_post_name'])->languages->toJson() }}"
			data-item_key="like_post_name"
			data-bs-target="#configLangModal">{{ $defaultLangParams['like_post_name'] ?? '' }}
		</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“点赞”、“喜欢”、“顶一顶”等命名</div>
  </div>
  <div class="row mb-5">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">点赞评论行为</label>
		<button class="btn btn-outline-secondary text-start rename-btn" type="button"
			data-bs-toggle="modal"
			data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'like_comment_name'])}}"
			data-languages="{{ optional($configs['like_comment_name'])->languages->toJson() }}"
			data-item_key="like_comment_name"
			data-bs-target="#configLangModal">{{ $defaultLangParams['like_comment_name'] ?? '' }}
		</button>
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
		<button class="btn btn-outline-secondary text-start rename-btn" type="button"
			data-bs-toggle="modal"
			data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'follow_member_name'])}}"
			data-languages="{{ optional($configs['follow_member_name'])->languages->toJson() }}"
			data-item_key="follow_member_name"
			data-bs-target="#configLangModal">{{ $defaultLangParams['follow_member_name'] ?? '' }}
		</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“关注”、“喜欢”、“跟进”等命名</div>
  </div>
  <div class="row mb-3">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">关注小组行为</label>
		<button class="btn btn-outline-secondary text-start rename-btn" type="button"
			data-bs-toggle="modal"
			data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'follow_group_name'])}}"
			data-languages="{{ optional($configs['follow_group_name'])->languages->toJson() }}"
			data-item_key="follow_group_name"
			data-bs-target="#configLangModal">{{ $defaultLangParams['follow_group_name'] ?? '' }}
		</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“关注”、“加入”、“订阅”等命名</div>
  </div>
  <div class="row mb-3">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">关注话题行为</label>
		<button class="btn btn-outline-secondary text-start rename-btn" type="button"
			data-bs-toggle="modal"
			data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'follow_hashtag_name'])}}"
			data-languages="{{ optional($configs['follow_hashtag_name'])->languages->toJson() }}"
			data-item_key="follow_hashtag_name"
			data-bs-target="#configLangModal">{{ $defaultLangParams['follow_hashtag_name'] ?? '' }}
		</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“关注”、“订阅”、“跟进”等命名</div>
  </div>
  <div class="row mb-3">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">关注帖子行为</label>
		<button class="btn btn-outline-secondary text-start rename-btn" type="button"
			data-bs-toggle="modal"
			data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'follow_post_name'])}}"
			data-languages="{{ optional($configs['follow_post_name'])->languages->toJson() }}"
			data-item_key="follow_post_name"
			data-bs-target="#configLangModal">{{ $defaultLangParams['follow_post_name'] ?? '' }}
		</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“收藏”、“喜欢”、“保存”等命名</div>
  </div>
  <div class="row mb-5">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">关注评论行为</label>
		<button class="btn btn-outline-secondary text-start rename-btn" type="button"
			data-bs-toggle="modal"
			data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'follow_comment_name'])}}"
			data-languages="{{ optional($configs['follow_comment_name'])->languages->toJson() }}"
			data-item_key="follow_comment_name"
			data-bs-target="#configLangModal">{{ $defaultLangParams['follow_comment_name'] ?? '' }}
		</button>
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
		<button class="btn btn-outline-secondary text-start rename-btn" type="button"
			data-bs-toggle="modal"
			data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'shield_member_name'])}}"
			data-languages="{{ optional($configs['shield_member_name'])->languages->toJson() }}"
			data-item_key="shield_member_name"
			data-bs-target="#configLangModal">{{ $defaultLangParams['shield_member_name'] ?? '' }}
		</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“拉黑”、“屏蔽”、“讨厌”等命名</div>
  </div>
  <div class="row mb-3">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">屏蔽小组行为</label>
		<button class="btn btn-outline-secondary text-start rename-btn" type="button"
			data-bs-toggle="modal"
			data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'shield_group_name'])}}"
			data-languages="{{ optional($configs['shield_group_name'])->languages->toJson() }}"
			data-item_key="shield_group_name"
			data-bs-target="#configLangModal">{{ $defaultLangParams['shield_group_name'] ?? '' }}
		</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“不喜欢”、“不感兴趣”、“屏蔽”等命名</div>
  </div>
  <div class="row mb-3">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">屏蔽话题行为</label>
		<button class="btn btn-outline-secondary text-start rename-btn" type="button"
			data-bs-toggle="modal"
			data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'shield_hashtag_name'])}}"
			data-languages="{{ optional($configs['shield_hashtag_name'])->languages->toJson() }}"
			data-item_key="shield_hashtag_name"
			data-bs-target="#configLangModal">{{ $defaultLangParams['shield_hashtag_name'] ?? '' }}
		</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“不喜欢”、“不感兴趣”、“屏蔽”等命名</div>
  </div>
  <div class="row mb-3">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">屏蔽帖子行为</label>
		<button class="btn btn-outline-secondary text-start rename-btn" type="button"
			data-bs-toggle="modal"
			data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'shield_post_name'])}}"
			data-languages="{{ optional($configs['shield_post_name'])->languages->toJson() }}"
			data-item_key="shield_post_name"
			data-bs-target="#configLangModal">{{ $defaultLangParams['shield_post_name'] ?? '' }}
		</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“不喜欢”、“不感兴趣”、“踩一踩”等命名</div>
  </div>
  <div class="row mb-5">
    <label class="col-lg-2"></label>
    <div class="col-lg-6">
      <div class="input-group">
        <label class="input-group-text rename-label">屏蔽评论行为</label>
		<button class="btn btn-outline-secondary text-start rename-btn" type="button"
			data-bs-toggle="modal"
			data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'shield_comment_name'])}}"
			data-languages="{{ optional($configs['shield_comment_name'])->languages->toJson() }}"
			data-item_key="shield_comment_name"
			data-bs-target="#configLangModal">{{ $defaultLangParams['shield_comment_name'] ?? '' }}
		</button>
      </div>
    </div>
    <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 例如“不喜欢”、“不感兴趣”、“踩一踩”等命名</div>
  </div>


  <!-- Language Modal -->
  <div class="modal fade" id="configLangModal" tabindex="-1" aria-labelledby="configLangModal" aria-hidden="true">
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
                      <td>
                        {{ $lang['langTag'] }}
                        @if($lang['langTag'] == $defaultLanguage)
                          <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="默认语言" aria-label="默认语言"></i>
                        @endif
                      </td>
                      <td>{{$lang['langName']}} @if($lang['areaCode'])({{ optional($areaCodes->where('code', $lang['areaCode'])->first())['localName']}}) @endif</td>
                      <td><input type="text" name="languages[{{ $lang['langTag'] }}]" class="form-control" value=""></td>
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
