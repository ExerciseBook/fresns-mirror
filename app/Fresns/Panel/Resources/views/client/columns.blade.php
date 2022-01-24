@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::client.sidebar')
@endsection

@section('content')
<div class="row mb-4 border-bottom">
	<div class="col-lg-7">
		<h3>栏目配置</h3>
		<p class="text-secondary">统一为客户端各个栏目配置命名</p>
	</div>
	<div class="col-lg-5">
		<div class="input-group mt-2 mb-4 justify-content-lg-end">
			<a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
		</div>
	</div>
</div>
<!--成员栏目命名-->
<div class="row mb-4">
	<label class="col-lg-2">成员栏目命名:</label>
	<div class="col-lg-6">
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">点赞的成员</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_like_members'])}}"
	          data-languages="{{ optional($configs['menu_like_members'])->languages->toJson() }}"
	          data-item_key="menu_like_members"
	          data-bs-target="#configLangModal">{{ $configs['menu_like_members']['item_value'] ?? '点赞的成员' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">关注的成员</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_follow_members'])}}"
	          data-languages="{{ optional($configs['menu_follow_members'])->languages->toJson() }}"
	          data-item_key="menu_follow_members"
	          data-bs-target="#configLangModal">{{ $configs['menu_follow_members']['item_value'] ?? '我的喜欢' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">屏蔽的成员</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_shield_members'])}}"
	          data-languages="{{ optional($configs['menu_shield_members'])->languages->toJson() }}"
	          data-item_key="menu_shield_members"
	          data-bs-target="#configLangModal">{{ $configs['menu_shield_members']['item_value'] ?? '黑名单' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">关注成员的帖子</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_post_from_follow_members'])}}"
	          data-languages="{{ optional($configs['menu_post_from_follow_members'])->languages->toJson() }}"
	          data-item_key="menu_post_from_follow_members"
	          data-bs-target="#configLangModal">{{ $configs['menu_post_from_follow_members']['item_value'] ?? '关注' }}
		  	</button>
		</div>
	</div>
</div>
<!--小组栏目命名-->
<div class="row mb-4">
	<label class="col-lg-2">小组栏目命名:</label>
	<div class="col-lg-6">
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">点赞的小组</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_like_groups'])}}"
	          data-languages="{{ optional($configs['menu_like_groups'])->languages->toJson() }}"
	          data-item_key="menu_like_groups"
	          data-bs-target="#configLangModal">{{ $configs['menu_like_groups']['item_value'] ?? '我点赞的小组' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">关注的小组</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_follow_groups'])}}"
	          data-languages="{{ optional($configs['menu_follow_groups'])->languages->toJson() }}"
	          data-item_key="menu_follow_groups"
	          data-bs-target="#configLangModal">{{ $configs['menu_follow_groups']['item_value'] ?? '我的小组' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">屏蔽的小组</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_shield_groups'])}}"
	          data-languages="{{ optional($configs['menu_shield_groups'])->languages->toJson() }}"
	          data-item_key="menu_shield_groups"
	          data-bs-target="#configLangModal">{{ $configs['menu_shield_groups']['item_value'] ?? '我屏蔽的' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">关注小组的帖子</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_post_from_follow_groups'])}}"
	          data-languages="{{ optional($configs['menu_post_from_follow_groups'])->languages->toJson() }}"
	          data-item_key="menu_post_from_follow_groups"
	          data-bs-target="#configLangModal">{{ $configs['menu_post_from_follow_groups']['item_value'] ?? '关注' }}
		  	</button>
		</div>
	</div>
</div>
<!--话题栏目命名-->
<div class="row mb-4">
	<label class="col-lg-2">话题栏目命名:</label>
	<div class="col-lg-6">
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">点赞的话题</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_like_hashtags'])}}"
	          data-languages="{{ optional($configs['menu_like_hashtags'])->languages->toJson() }}"
	          data-item_key="menu_like_hashtags"
	          data-bs-target="#configLangModal">{{ $configs['menu_like_hashtags']['item_value'] ?? '我点赞的话题' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">关注的话题</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_follow_hashtags'])}}"
	          data-languages="{{ optional($configs['menu_follow_hashtags'])->languages->toJson() }}"
	          data-item_key="menu_follow_hashtags"
	          data-bs-target="#configLangModal">{{ $configs['menu_follow_hashtags']['item_value'] ?? '我的订阅' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">屏蔽的话题</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_shield_hashtags'])}}"
	          data-languages="{{ optional($configs['menu_shield_hashtags'])->languages->toJson() }}"
	          data-item_key="menu_shield_hashtags"
	          data-bs-target="#configLangModal">{{ $configs['menu_shield_hashtags']['item_value'] ?? '我不感兴趣的' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">关注话题的帖子</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_post_from_follow_hashtags'])}}"
	          data-languages="{{ optional($configs['menu_post_from_follow_hashtags'])->languages->toJson() }}"
	          data-item_key="menu_post_from_follow_hashtags"
	          data-bs-target="#configLangModal">{{ $configs['menu_post_from_follow_hashtags']['item_value'] ?? '关注' }}
		  	</button>
		</div>
	</div>
</div>
<!--帖子栏目命名-->
<div class="row mb-4">
	<label class="col-lg-2">帖子栏目命名:</label>
	<div class="col-lg-6">
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">点赞的帖子</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_like_posts'])}}"
	          data-languages="{{ optional($configs['menu_like_posts'])->languages->toJson() }}"
	          data-item_key="menu_like_posts"
	          data-bs-target="#configLangModal">{{ $configs['menu_like_posts']['item_value'] ?? '我点赞的帖子' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">关注的帖子</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_follow_posts'])}}"
	          data-languages="{{ optional($configs['menu_follow_posts'])->languages->toJson() }}"
	          data-item_key="menu_follow_posts"
	          data-bs-target="#configLangModal">{{ $configs['menu_follow_posts']['item_value'] ?? '帖子收藏夹' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">屏蔽的帖子</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_shield_posts'])}}"
	          data-languages="{{ optional($configs['menu_shield_posts'])->languages->toJson() }}"
	          data-item_key="menu_shield_posts"
	          data-bs-target="#configLangModal">{{ $configs['menu_shield_posts']['item_value'] ?? '我不喜欢的' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">全部关注的帖子</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_post_from_follow_all'])}}"
	          data-languages="{{ optional($configs['menu_post_from_follow_all'])->languages->toJson() }}"
	          data-item_key="menu_post_from_follow_all"
	          data-bs-target="#configLangModal">{{ $configs['menu_post_from_follow_all']['item_value'] ?? '关注' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">附近范围的帖子</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_post_from_nearby'])}}"
	          data-languages="{{ optional($configs['menu_post_from_nearby'])->languages->toJson() }}"
	          data-item_key="menu_post_from_nearby"
	          data-bs-target="#configLangModal">{{ $configs['menu_post_from_nearby']['item_value'] ?? '附近' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">指定位置的周边帖子</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_post_from_location'])}}"
	          data-languages="{{ optional($configs['menu_post_from_location'])->languages->toJson() }}"
	          data-item_key="menu_post_from_location"
	          data-bs-target="#configLangModal">{{ $configs['menu_post_from_location']['item_value'] ?? '周边' }}
		  	</button>
		</div>
	</div>
</div>
<!--评论栏目命名-->
<div class="row mb-4">
	<label class="col-lg-2">评论栏目命名:</label>
	<div class="col-lg-6">
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">点赞的评论</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_like_comments'])}}"
	          data-languages="{{ optional($configs['menu_like_comments'])->languages->toJson() }}"
	          data-item_key="menu_like_comments"
	          data-bs-target="#configLangModal">{{ $configs['menu_like_comments']['item_value'] ?? '我点赞的评论' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">关注的评论</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_follow_comments'])}}"
	          data-languages="{{ optional($configs['menu_follow_comments'])->languages->toJson() }}"
	          data-item_key="menu_follow_comments"
	          data-bs-target="#configLangModal">{{ $configs['menu_follow_comments']['item_value'] ?? '评论收藏夹' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">屏蔽的评论</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_shield_comments'])}}"
	          data-languages="{{ optional($configs['menu_shield_comments'])->languages->toJson() }}"
	          data-item_key="menu_shield_comments"
	          data-bs-target="#configLangModal">{{ $configs['menu_shield_comments']['item_value'] ?? '我不喜欢的' }}
		  	</button>
		</div>
	</div>
</div>
<!--个人中心命名设置-->
<div class="row mb-4">
	<label class="col-lg-2">个人中心栏目命名:</label>
	<div class="col-lg-6">
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">个人中心</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_user'])}}"
	          data-languages="{{ optional($configs['menu_user'])->languages->toJson() }}"
	          data-item_key="menu_user"
	          data-bs-target="#configLangModal">{{ $configs['menu_user']['item_value'] ?? '我' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">注册</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_user_signup'])}}"
	          data-languages="{{ optional($configs['menu_user_signup'])->languages->toJson() }}"
	          data-item_key="menu_user_signup"
	          data-bs-target="#configLangModal">{{ $configs['menu_user_signup']['item_value'] ?? '注册' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">登录</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_user_signin'])}}"
	          data-languages="{{ optional($configs['menu_user_signin'])->languages->toJson() }}"
	          data-item_key="menu_user_signin"
	          data-bs-target="#configLangModal">{{ $configs['menu_user_signin']['item_value'] ?? '登录' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">找回密码</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_user_reset'])}}"
	          data-languages="{{ optional($configs['menu_user_reset'])->languages->toJson() }}"
	          data-item_key="menu_user_reset"
	          data-bs-target="#configLangModal">{{ $configs['menu_user_reset']['item_value'] ?? '找回密码' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">成员</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_user_members'])}}"
	          data-languages="{{ optional($configs['menu_user_members'])->languages->toJson() }}"
	          data-item_key="menu_user_members"
	          data-bs-target="#configLangModal">{{ $configs['menu_user_members']['item_value'] ?? '成员' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">钱包</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_user_wallet'])}}"
	          data-languages="{{ optional($configs['menu_user_wallet'])->languages->toJson() }}"
	          data-item_key="menu_user_wallet"
	          data-bs-target="#configLangModal">{{ $configs['menu_user_wallet']['item_value'] ?? '钱包' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">设置</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_user_settings'])}}"
	          data-languages="{{ optional($configs['menu_user_settings'])->languages->toJson() }}"
	          data-item_key="menu_user_settings"
	          data-bs-target="#configLangModal">{{ $configs['menu_user_settings']['item_value'] ?? '设置' }}
		  	</button>
		</div>
	</div>
</div>
<!--消息命名设置-->
<div class="row mb-4">
	<label class="col-lg-2">消息栏目命名:</label>
	<div class="col-lg-6">
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">会话</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_dialogs'])}}"
	          data-languages="{{ optional($configs['menu_dialogs'])->languages->toJson() }}"
	          data-item_key="menu_dialogs"
	          data-bs-target="#configLangModal">{{ $configs['menu_dialogs']['item_value'] ?? '私信' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">消息</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_messages'])}}"
	          data-languages="{{ optional($configs['menu_messages'])->languages->toJson() }}"
	          data-item_key="menu_messages"
	          data-bs-target="#configLangModal">{{ $configs['menu_messages']['item_value'] ?? '消息' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">通知</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_notifies'])}}"
	          data-languages="{{ optional($configs['menu_notifies'])->languages->toJson() }}"
	          data-item_key="menu_notifies"
	          data-bs-target="#configLangModal">{{ $configs['menu_notifies']['item_value'] ?? '通知' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">通知-推荐</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_notify_recommends'])}}"
	          data-languages="{{ optional($configs['menu_notify_recommends'])->languages->toJson() }}"
	          data-item_key="menu_notify_recommends"
	          data-bs-target="#configLangModal">{{ $configs['menu_notify_recommends']['item_value'] ?? '推荐' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">通知-系统</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_notify_systems'])}}"
	          data-languages="{{ optional($configs['menu_notify_systems'])->languages->toJson() }}"
	          data-item_key="menu_notify_systems"
	          data-bs-target="#configLangModal">{{ $configs['menu_notify_systems']['item_value'] ?? '系统' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">通知-关注</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_notify_follows'])}}"
	          data-languages="{{ optional($configs['menu_notify_follows'])->languages->toJson() }}"
	          data-item_key="menu_notify_follows"
	          data-bs-target="#configLangModal">{{ $configs['menu_notify_follows']['item_value'] ?? '关注' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">通知-点赞</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_notify_likes'])}}"
	          data-languages="{{ optional($configs['menu_notify_likes'])->languages->toJson() }}"
	          data-item_key="menu_notify_likes"
	          data-bs-target="#configLangModal">{{ $configs['menu_notify_likes']['item_value'] ?? '点赞' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">通知-评论</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_notify_comments'])}}"
	          data-languages="{{ optional($configs['menu_notify_comments'])->languages->toJson() }}"
	          data-item_key="menu_notify_comments"
	          data-bs-target="#configLangModal">{{ $configs['menu_notify_comments']['item_value'] ?? '评论' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">通知-提及</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_notify_mentions'])}}"
	          data-languages="{{ optional($configs['menu_notify_mentions'])->languages->toJson() }}"
	          data-item_key="menu_notify_mentions"
	          data-bs-target="#configLangModal">{{ $configs['menu_notify_mentions']['item_value'] ?? '提及' }}
		  	</button>
		</div>
	</div>
</div>
<!--搜索命名设置-->
<div class="row mb-4">
	<label class="col-lg-2">搜索栏目命名:</label>
	<div class="col-lg-6">
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">搜索</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_search'])}}"
	          data-languages="{{ optional($configs['menu_search'])->languages->toJson() }}"
	          data-item_key="menu_search"
	          data-bs-target="#configLangModal">{{ $configs['menu_search']['item_value'] ?? '搜索' }}
		  	</button>
		</div>
	</div>
</div>
<!--编辑器命名设置-->
<div class="row mb-4">
	<label class="col-lg-2">编辑器栏目命名:</label>
	<div class="col-lg-6">
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">编辑器</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_editor_functions'])}}"
	          data-languages="{{ optional($configs['menu_editor_functions'])->languages->toJson() }}"
	          data-item_key="menu_editor_functions"
	          data-bs-target="#configLangModal">{{ $configs['menu_editor_functions']['item_value'] ?? '编辑器' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">草稿箱</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_editor_drafts'])}}"
	          data-languages="{{ optional($configs['menu_editor_drafts'])->languages->toJson() }}"
	          data-item_key="menu_editor_drafts"
	          data-bs-target="#configLangModal">{{ $configs['menu_editor_drafts']['item_value'] ?? '草稿箱' }}
		  	</button>
		</div>
	</div>
</div>
<!--成员主页命名-->
<div class="row mb-4">
	<label class="col-lg-2">成员主页命名:</label>
	<div class="col-lg-6">
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">点赞 TA 的成员</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_profile_likes'])}}"
	          data-languages="{{ optional($configs['menu_profile_likes'])->languages->toJson() }}"
	          data-item_key="menu_profile_likes"
	          data-bs-target="#configLangModal">{{ $configs['menu_profile_likes']['item_value'] ?? '被喜欢' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">关注 TA 的成员</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_profile_followers'])}}"
	          data-languages="{{ optional($configs['menu_profile_followers'])->languages->toJson() }}"
	          data-item_key="menu_profile_followers"
	          data-bs-target="#configLangModal">{{ $configs['menu_profile_followers']['item_value'] ?? '粉丝' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">屏蔽 TA 的成员</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_profile_shielders'])}}"
	          data-languages="{{ optional($configs['menu_profile_shielders'])->languages->toJson() }}"
	          data-item_key="menu_profile_shielders"
	          data-bs-target="#configLangModal">{{ $configs['menu_profile_shielders']['item_value'] ?? '被拉黑' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">TA 点赞的成员</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_profile_like_members'])}}"
	          data-languages="{{ optional($configs['menu_profile_like_members'])->languages->toJson() }}"
	          data-item_key="menu_profile_like_members"
	          data-bs-target="#configLangModal">{{ $configs['menu_profile_like_members']['item_value'] ?? 'TA 点赞的成员' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">TA 点赞的小组</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_profile_like_groups'])}}"
	          data-languages="{{ optional($configs['menu_profile_like_groups'])->languages->toJson() }}"
	          data-item_key="menu_profile_like_groups"
	          data-bs-target="#configLangModal">{{ $configs['menu_profile_like_groups']['item_value'] ?? 'TA 点赞的小组' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">TA 点赞的话题</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_profile_like_hashtags'])}}"
	          data-languages="{{ optional($configs['menu_profile_like_hashtags'])->languages->toJson() }}"
	          data-item_key="menu_profile_like_hashtags"
	          data-bs-target="#configLangModal">{{ $configs['menu_profile_like_hashtags']['item_value'] ?? 'TA 点赞的话题' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">TA 点赞的帖子</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_profile_like_posts'])}}"
	          data-languages="{{ optional($configs['menu_profile_like_posts'])->languages->toJson() }}"
	          data-item_key="menu_profile_like_posts"
	          data-bs-target="#configLangModal">{{ $configs['menu_profile_like_posts']['item_value'] ?? 'TA 点赞的帖子' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">TA 点赞的评论</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_profile_like_comments'])}}"
	          data-languages="{{ optional($configs['menu_profile_like_comments'])->languages->toJson() }}"
	          data-item_key="menu_profile_like_comments"
	          data-bs-target="#configLangModal">{{ $configs['menu_profile_like_comments']['item_value'] ?? 'TA 点赞的评论' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">TA 关注的成员</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_profile_follow_members'])}}"
	          data-languages="{{ optional($configs['menu_profile_follow_members'])->languages->toJson() }}"
	          data-item_key="menu_profile_follow_members"
	          data-bs-target="#configLangModal">{{ $configs['menu_profile_follow_members']['item_value'] ?? '正在关注' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">TA 关注的小组</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_profile_follow_groups'])}}"
	          data-languages="{{ optional($configs['menu_profile_follow_groups'])->languages->toJson() }}"
	          data-item_key="menu_profile_follow_groups"
	          data-bs-target="#configLangModal">{{ $configs['menu_profile_follow_groups']['item_value'] ?? '正在其中' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">TA 关注的话题</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_profile_follow_hashtags'])}}"
	          data-languages="{{ optional($configs['menu_profile_follow_hashtags'])->languages->toJson() }}"
	          data-item_key="menu_profile_follow_hashtags"
	          data-bs-target="#configLangModal">{{ $configs['menu_profile_follow_hashtags']['item_value'] ?? '正在订阅' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">TA 关注的帖子</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_profile_follow_posts'])}}"
	          data-languages="{{ optional($configs['menu_profile_follow_posts'])->languages->toJson() }}"
	          data-item_key="menu_profile_follow_posts"
	          data-bs-target="#configLangModal">{{ $configs['menu_profile_follow_posts']['item_value'] ?? '收藏夹' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">TA 关注的评论</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_profile_follow_comments'])}}"
	          data-languages="{{ optional($configs['menu_profile_follow_comments'])->languages->toJson() }}"
	          data-item_key="menu_profile_follow_comments"
	          data-bs-target="#configLangModal">{{ $configs['menu_profile_follow_comments']['item_value'] ?? '收藏夹' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">TA 屏蔽的成员</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_profile_shield_members'])}}"
	          data-languages="{{ optional($configs['menu_profile_shield_members'])->languages->toJson() }}"
	          data-item_key="menu_profile_shield_members"
	          data-bs-target="#configLangModal">{{ $configs['menu_profile_shield_members']['item_value'] ?? 'TA 的黑名单' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">TA 屏蔽的小组</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_profile_shield_groups'])}}"
	          data-languages="{{ optional($configs['menu_profile_shield_groups'])->languages->toJson() }}"
	          data-item_key="menu_profile_shield_groups"
	          data-bs-target="#configLangModal">{{ $configs['menu_profile_shield_groups']['item_value'] ?? 'TA 屏蔽的小组' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">TA 屏蔽的话题</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_profile_shield_hashtags'])}}"
	          data-languages="{{ optional($configs['menu_profile_shield_hashtags'])->languages->toJson() }}"
	          data-item_key="menu_profile_shield_hashtags"
	          data-bs-target="#configLangModal">{{ $configs['menu_profile_shield_hashtags']['item_value'] ?? 'TA 屏蔽的话题' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">TA 屏蔽的帖子</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_profile_shield_posts'])}}"
	          data-languages="{{ optional($configs['menu_profile_shield_posts'])->languages->toJson() }}"
	          data-item_key="menu_profile_shield_posts"
	          data-bs-target="#configLangModal">{{ $configs['menu_profile_shield_posts']['item_value'] ?? 'TA 屏蔽的帖子' }}
		  	</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">TA 屏蔽的评论</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button"
	          data-bs-toggle="modal"
	          data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_profile_shield_comments'])}}"
	          data-languages="{{ optional($configs['menu_profile_shield_comments'])->languages->toJson() }}"
	          data-item_key="menu_profile_shield_comments"
	          data-bs-target="#configLangModal">{{ $configs['menu_profile_shield_comments']['item_value'] ?? 'TA 屏蔽的评论' }}
		  	</button>
		</div>
	</div>
</div>
<!--结束-->
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
