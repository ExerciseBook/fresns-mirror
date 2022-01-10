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
<!--个人中心命名设置-->
<div class="row mb-4">
	<label class="col-lg-2">个人中心栏目命名：</label>
	<div class="col-lg-6">
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">个人中心</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">我</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">注册</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">注册</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">登录</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">登录</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">找回密码</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">找回密码</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">成员</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">成员</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">钱包</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">钱包</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">设置</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">设置</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">设置-资料</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">个人资料</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">设置-偏好</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">偏好设置</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">设置-账号</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">账号设置</button>
		</div>
	</div>
</div>
<!--消息命名设置-->
<div class="row mb-4">
	<label class="col-lg-2">消息栏目命名：</label>
	<div class="col-lg-6">
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">会话</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">私信</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">消息</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">消息</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">通知</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">通知</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">通知-推荐</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">推荐</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">通知-系统</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">系统</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">通知-关注</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">关注</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">通知-点赞</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">点赞</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">通知-评论</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">评论</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">通知-提及</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">提及</button>
		</div>
	</div>
</div>
<!--搜索命名设置-->
<div class="row mb-4">
	<label class="col-lg-2">搜索栏目命名：</label>
	<div class="col-lg-6">
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">搜索</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">搜索</button>
		</div>
	</div>
</div>
<!--编辑器命名设置-->
<div class="row mb-4">
	<label class="col-lg-2">编辑器栏目命名：</label>
	<div class="col-lg-6">
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">编辑器</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">编辑器</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">草稿箱</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">草稿箱</button>
		</div>
	</div>
</div>
<!--点赞命名设置-->
<div class="row mb-4">
	<label class="col-lg-2">点赞栏目命名：</label>
	<div class="col-lg-6">
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">点赞的成员</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">我的喜欢</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">点赞的小组</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">我点赞的小组</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">点赞的话题</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">我点赞的话题</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">点赞的帖子</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">我点赞的帖子</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">点赞的评论</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">我点赞的评论</button>
		</div>
	</div>
</div>
<!--关注命名设置-->
<div class="row mb-4">
	<label class="col-lg-2">关注栏目命名：</label>
	<div class="col-lg-6">
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">关注的成员</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">我的关注</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">关注的小组</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">我的小组</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">关注的话题</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">我的订阅</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">关注的帖子</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">帖子收藏夹</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">关注的评论</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">评论收藏夹</button>
		</div>
	</div>
</div>
<!--屏蔽命名设置-->
<div class="row mb-4">
	<label class="col-lg-2">屏蔽栏目命名：</label>
	<div class="col-lg-6">
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">屏蔽的成员</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">黑名单</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">屏蔽的小组</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">我屏蔽的小组</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">屏蔽的话题</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">我不感兴趣的话题</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">屏蔽的帖子</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">我不喜欢的帖子</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">屏蔽的评论</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">我不喜欢的评论</button>
		</div>
	</div>
</div>
<!--帖子栏目命名设置-->
<div class="row mb-4">
	<label class="col-lg-2">帖子栏目命名：</label>
	<div class="col-lg-6">
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">关注成员的帖子</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">关注</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">关注小组的帖子</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">关注</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">关注话题的帖子</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">关注</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">全部关注的帖子</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">关注</button>
		</div>
		<div class="input-group mb-3">
			<label class="input-group-text rename-label">附近范围的帖子</label>
			<button class="btn btn-outline-secondary text-start rename-btn" type="button" data-bs-toggle="modal" data-bs-target="#langModal">附近</button>
		</div>
	</div>
</div>
<!--结束-->

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
								<tr>
									<td>zh-Hans-CN</td>
									<td>简体中文(内地)</td>
									<td><input type="text" class="form-control" value="名称"></td>
								</tr>
								<tr>
									<td>zh-Hans-SG</td>
									<td>简体中文(新加坡)</td>
									<td><input type="text" class="form-control" value="名称"></td>
								</tr>
								<tr>
									<td>zh-Hans-HK</td>
									<td>繁體中文(香港)</td>
									<td><input type="text" class="form-control" value="名稱"></td>
								</tr>
								<tr>
									<td>en-US <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="默认语言"></i></td>
									<td>English(United States)</td>
									<td><input type="text" class="form-control" value="name"></td>
								</tr>
								<tr>
									<td>ja</td>
									<td>日本語</td>
									<td><input type="text" class="form-control" value="名"></td>
								</tr>
								<tr>
									<td>ko-KR</td>
									<td>한국어(대한민국)</td>
									<td><input type="text" class="form-control" value="이름"></td>
								</tr>
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


@endsection
