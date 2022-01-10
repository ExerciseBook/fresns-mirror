@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::client.sidebar')
@endsection

@section('content')

<!--设置区域 开始-->
<div class="row mb-4 border-bottom">
	<div class="col-lg-7">
		<h3>菜单配置</h3>
		<p class="text-secondary">统一配置客户端菜单信息</p>
	</div>
	<div class="col-lg-5">
		<div class="input-group mt-2 mb-4 justify-content-lg-end">
			<a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
		</div>
	</div>
</div>
<!--列表-->
<div class="table-responsive">
	<table class="table table-hover align-middle text-nowrap">
		<thead>
			<tr class="table-info">
				<th scope="col">首页</th>
				<th scope="col">菜单</th>
				<th scope="col">路径</th>
				<th scope="col">导航名称</th>
				<th scope="col">SEO 标题</th>
				<th scope="col">SEO 描述</th>
				<th scope="col">SEO 关键词</th>
				<th scope="col">启用状态</th>
				<th scope="col">操作</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><input class="form-check-input" type="radio" name="default_homepage" id="portal" value="portal"></td>
				<td>门户</td>
				<td>/portal</td>
				<td>榜单</td>
				<td>SEO 标题</td>
				<td>SEO 描述</td>
				<td>SEO 关键词</td>
				<td><i class="bi bi-check-lg text-success"></i></td>
				<td><button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#menuEdit" data-bs-whatever="门户">编辑</button></td>
			</tr>
			<tr>
				<td><input class="form-check-input" type="radio" name="default_homepage" id="members" value="members"></td>
				<td>成员</td>
				<td>/members</td>
				<td>用户</td>
				<td>SEO 标题</td>
				<td>SEO 描述</td>
				<td>SEO 关键词</td>
				<td><i class="bi bi-dash-lg text-secondary"></i></td>
				<td><button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#menuEdit" data-bs-whatever="成员">编辑</button></td>
			</tr>
			<tr>
				<td><input class="form-check-input" type="radio" name="default_homepage" id="groups" value="groups" checked></td>
				<td>小组</td>
				<td>/groups</td>
				<td>论坛</td>
				<td>SEO 标题</td>
				<td>SEO 描述</td>
				<td>SEO 关键词</td>
				<td><i class="bi bi-check-lg text-success"></i></td>
				<td><button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#menuEdit" data-bs-whatever="小组">编辑</button></td>
			</tr>
			<tr>
				<td><input class="form-check-input" type="radio" name="default_homepage" id="hashtags" value="hashtags"></td>
				<td>话题</td>
				<td>/hashtags</td>
				<td>标签</td>
				<td>SEO 标题</td>
				<td>SEO 描述</td>
				<td>SEO 关键词</td>
				<td><i class="bi bi-check-lg text-success"></i></td>
				<td><button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#menuEdit" data-bs-whatever="话题">编辑</button></td>
			</tr>
			<tr>
				<td><input class="form-check-input" type="radio" name="default_homepage" id="posts" value="posts"></td>
				<td>帖子</td>
				<td>/posts</td>
				<td>广场</td>
				<td>SEO 标题</td>
				<td>SEO 描述</td>
				<td>SEO 关键词</td>
				<td><i class="bi bi-check-lg text-success"></i></td>
				<td><button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#menuEdit" data-bs-whatever="帖子">编辑</button></td>
			</tr>
			<tr>
				<td><input class="form-check-input" type="radio" name="default_homepage" id="comments" value="comments"></td>
				<td>评论</td>
				<td>/comments</td>
				<td>动态</td>
				<td>SEO 标题</td>
				<td>SEO 描述</td>
				<td>SEO 关键词</td>
				<td><i class="bi bi-check-lg text-success"></i></td>
				<td><button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#menuEdit" data-bs-whatever="评论">编辑</button></td>
			</tr>
			<tr>
				<td></td>
				<td>成员列表页</td>
				<td>/members/list</td>
				<td>排行榜</td>
				<td>SEO 标题</td>
				<td>SEO 描述</td>
				<td>SEO 关键词</td>
				<td><i class="bi bi-dash-lg text-secondary"></i></td>
				<td><button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#menuEdit" data-bs-whatever="成员">编辑</button></td>
			</tr>
			<tr>
				<td></td>
				<td>小组列表页</td>
				<td>/groups/list</td>
				<td>全部版区</td>
				<td>SEO 标题</td>
				<td>SEO 描述</td>
				<td>SEO 关键词</td>
				<td><i class="bi bi-check-lg text-success"></i></td>
				<td><button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#menuEdit" data-bs-whatever="小组">编辑</button></td>
			</tr>
			<tr>
				<td></td>
				<td>话题列表页</td>
				<td>/hashtags/list</td>
				<td>全部标签</td>
				<td>SEO 标题</td>
				<td>SEO 描述</td>
				<td>SEO 关键词</td>
				<td><i class="bi bi-check-lg text-success"></i></td>
				<td><button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#menuEdit" data-bs-whatever="话题">编辑</button></td>
			</tr>
			<tr>
				<td></td>
				<td>帖子列表页</td>
				<td>/posts/list</td>
				<td>全部帖子</td>
				<td>SEO 标题</td>
				<td>SEO 描述</td>
				<td>SEO 关键词</td>
				<td><i class="bi bi-check-lg text-success"></i></td>
				<td><button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#menuEdit" data-bs-whatever="帖子">编辑</button></td>
			</tr>
			<tr>
				<td></td>
				<td>评论列表页</td>
				<td>/comments/list</td>
				<td>全部评论</td>
				<td>SEO 标题</td>
				<td>SEO 描述</td>
				<td>SEO 关键词</td>
				<td><i class="bi bi-check-lg text-success"></i></td>
				<td><button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#menuEdit" data-bs-whatever="评论">编辑</button></td>
			</tr>
		</tbody>
	</table>
</div>
<!--列表 结束-->


<!-- Modal -->
<div class="modal fade" id="menuEdit" tabindex="-1" aria-labelledby="menuEdit" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">菜单设置</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form>
					<div class="mb-3 row">
						<label class="col-sm-3 col-form-label">导航名称</label>
						<div class="col-sm-9">
							<button type="button" class="btn btn-outline-secondary btn-modal w-100 text-start" data-bs-toggle="modal" data-bs-target="#langModal">导航名称</button>
						</div>
					</div>
					<div class="mb-3 row">
						<label class="col-sm-3 col-form-label">SEO 标题</label>
						<div class="col-sm-9">
							<button type="button" class="btn btn-outline-secondary btn-modal w-100 text-start" data-bs-toggle="modal" data-bs-target="#langModal">SEO 标题</button>
						</div>
					</div>
					<div class="mb-3 row">
						<label class="col-sm-3 col-form-label">SEO 描述</label>
						<div class="col-sm-9">
							<button type="button" class="btn btn-outline-secondary btn-modal w-100 text-start" data-bs-toggle="modal" data-bs-target="#langDescModal">SEO 描述</button>
						</div>
					</div>
					<div class="mb-3 row">
						<label class="col-sm-3 col-form-label">SEO 关键词</label>
						<div class="col-sm-9">
							<button type="button" class="btn btn-outline-secondary btn-modal w-100 text-start" data-bs-toggle="modal" data-bs-target="#langDescModal">SEO 关键词</button>
						</div>
					</div>
					<div class="mb-3 row">
						<label class="col-sm-3 col-form-label">默认参数</label>
						<div class="col-sm-9">
							<textarea class="form-control" rows="3"></textarea>
						</div>
					</div>
					<div class="mb-3 row">
						<label class="col-sm-3 col-form-label">启用状态</label>
						<div class="col-sm-9 pt-2">
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="status" id="status_true" value="true" checked>
								<label class="form-check-label" for="status_true">启用</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="status" id="status_false" value="false">
								<label class="form-check-label" for="status_false">不启用</label>
							</div>
						</div>
					</div>
					<div class="mb-3 row">
						<label class="col-sm-3 col-form-label"></label>
						<div class="col-sm-9"><button type="submit" class="btn btn-primary">提交</button></div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<!--设置区域 结束-->
@endsection
