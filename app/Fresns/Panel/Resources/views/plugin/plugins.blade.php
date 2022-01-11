@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::plugin.sidebar')
@endsection

@section('content')
<div class="row mb-4">
	<div class="col-lg-7">
		<h3>插件管理</h3>
		<p class="text-secondary">灵活的功能，强大的扩展，助您自由发挥心中所想。</p>
	</div>
	<div class="col-lg-5">
		<div class="input-group mt-2 mb-4 justify-content-lg-end">
			<a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
		</div>
	</div>
	<ul class="nav nav-tabs" id="myTab" role="tablist">
		<li class="nav-item" role="presentation">
			<button class="nav-link active" type="button">全部</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link" type="button">已启用 (3)</button>
		</li>
		<li class="nav-item" role="presentation">
			<button class="nav-link" type="button">未启用 (6)</button>
		</li>
	</ul>
</div>
<!--插件列表-->
<div class="table-responsive">
	<table class="table table-hover align-middle text-nowrap fs-7">
		<thead>
			<tr class="table-info fs-6">
				<th scope="col">名称</th>
				<th scope="col">描述</th>
				<th scope="col">开发者</th>
				<th scope="col">操作</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="py-3">
					<img src="../assets/images/temp/placeholder_icon.png" class="me-2" width="44" height="44">
					<span class="fs-6">投票</span>
					<span class="badge bg-secondary fs-9">1.0.9</span>
					<a href="dashboard.html" class="badge rounded-pill bg-danger fs-9 fresns-link">有新版</a>
				</td>
				<td>让帖子支持投票功能</td>
				<td><a href="#" class="link-info fresns-link fs-7">唐杰</a></td>
				<td>
					<button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="点击停用">已启用</button>
					<a href="iframe.html" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="进入插件设置">设置</a>
				</td>
			</tr>
			<tr>
				<td class="py-3">
					<img src="../assets/images/fresns-icon.png" class="me-2" width="44" height="44">
					<span class="fs-6">附近的人</span>
					<span class="badge bg-secondary fs-9">1.0.9</span>
				</td>
				<td>微信发现频道“附近的人”功能，让你的社区也能实现附近交友</td>
				<td><a href="#" class="link-info fresns-link fs-7">唐杰</a></td>
				<td>
					<button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="点击启用">启用</button>
					<button type="button" class="btn btn-link btn-sm ms-2 text-danger fresns-link">卸载</button>
				</td>
			</tr>
			<tr>
				<td class="py-3">
					<img src="../assets/images/temp/placeholder_icon.png" class="me-2" width="44" height="44">
					<span class="fs-6">商城</span>
					<span class="badge bg-secondary fs-9">2.0.0</span>
				</td>
				<td>仿拼多多商城</td>
				<td><a href="#" class="link-info fresns-link fs-7">唐杰</a></td>
				<td>
					<button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="点击启用">启用</button>
					<button type="button" class="btn btn-link btn-sm ms-2 text-danger fresns-link">卸载</button>
				</td>
			</tr>
			<tr>
				<td class="py-3">
					<img src="../assets/images/temp/placeholder_icon.png" class="me-2" width="44" height="44">
					<span class="fs-6">微信快捷登录</span>
					<span class="badge bg-secondary fs-9">2.0.0</span>
				</td>
				<td>支持微信快捷注册登录，支持微信小程序参数</td>
				<td><a href="#" class="link-info fresns-link fs-7">唐杰</a></td>
				<td>
					<button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="点击启用">启用</button>
					<button type="button" class="btn btn-link btn-sm ms-2 text-danger fresns-link">卸载</button>
				</td>
			</tr>
			<tr>
				<td class="py-3">
					<img src="../assets/images/temp/placeholder_icon.png" class="me-2" width="44" height="44">
					<span class="fs-6">每日榜单</span>
					<span class="badge bg-secondary fs-9">1.0</span>
				</td>
				<td>独立频道，可以调用整站或指定某个小组的数据，以榜单模式展示。效果参考 Product Hunt</td>
				<td><a href="#" class="link-info fresns-link fs-7">唐杰</a></td>
				<td>
					<button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="点击启用">启用</button>
					<button type="button" class="btn btn-link btn-sm ms-2 text-danger fresns-link">卸载</button>
				</td>
			</tr>
			<tr>
				<td class="py-3">
					<img src="../assets/images/temp/placeholder_icon.png" class="me-2" width="44" height="44">
					<span class="fs-6">图片内容安全</span>
					<span class="badge bg-secondary fs-9">1.0</span>
				</td>
				<td>检测涉黄、涉恐、涉政等违规图片，节省人工审核成本，提升审核效率</td>
				<td><a href="#" class="link-info fresns-link fs-7">唐杰</a></td>
				<td>
					<button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="点击启用">启用</button>
					<button type="button" class="btn btn-link btn-sm ms-2 text-danger fresns-link">卸载</button>
				</td>
			</tr>
			<tr>
				<td class="py-3">
					<img src="../assets/images/temp/placeholder_icon.png" class="me-2" width="44" height="44">
					<span class="fs-6">文本内容安全</span>
					<span class="badge bg-secondary fs-9">1.0</span>
				</td>
				<td>精准识别涉黄、涉政、涉恐文本，释放业务风险，节省审核人力</td>
				<td><a href="#" class="link-info fresns-link fs-7">唐杰</a></td>
				<td>
					<button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="点击启用">启用</button>
					<button type="button" class="btn btn-link btn-sm ms-2 text-danger fresns-link">卸载</button>
				</td>
			</tr>
			<tr>
				<td class="py-3">
					<img src="../assets/images/temp/placeholder_icon.png" class="me-2" width="44" height="44">
					<span class="fs-6">邀请有奖</span>
					<span class="badge bg-secondary fs-9">1.0</span>
				</td>
				<td>可以实现裂变邀请注册</td>
				<td><a href="#" class="link-info fresns-link fs-7">唐杰</a></td>
				<td>
					<button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="点击启用">启用</button>
					<button type="button" class="btn btn-link btn-sm ms-2 text-danger fresns-link">卸载</button>
				</td>
			</tr>
			<tr>
				<td class="py-3">
					<img src="../assets/images/temp/placeholder_icon.png" class="me-2" width="44" height="44">
					<span class="fs-6">个性化互动图标</span>
					<span class="badge bg-secondary fs-9">1.0</span>
				</td>
				<td>可以为单个帖子自定义互动图标，比如“赞”的图标换样式</td>
				<td><a href="#" class="link-info fresns-link fs-7">唐杰</a></td>
				<td>
					<button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="点击启用">启用</button>
					<button type="button" class="btn btn-link btn-sm ms-2 text-danger fresns-link">卸载</button>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<!--插件列表 结束-->

@endsection
