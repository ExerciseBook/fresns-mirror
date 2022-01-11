@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::expand.sidebar')
@endsection

@section('content')
<div class="row mb-4 border-bottom">
	<div class="col-lg-9">
		<h3>用户资料扩展</h3>
		<p class="text-secondary">将呈现在「用户资料」管理功能列表中，例如「修改用户名插件」可以让修改资料功能支持修改用户名。</p>
	</div>
	<div class="col-lg-3">
		<div class="input-group mt-2 mb-4 justify-content-lg-end">
			<button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#createModal"><i class="bi bi-plus-circle-dotted"></i> 新增扩展</button>
			<a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
		</div>
	</div>
</div>
<!--操作列表-->
<div class="table-responsive">
	<table class="table table-hover align-middle text-nowrap">
		<thead>
			<tr class="table-info">
				<th scope="col" style="width:6rem;">显示顺序</th>
				<th scope="col">关联插件</th>
				<th scope="col">显示名称</th>
				<th scope="col">有权使用的用户角色 <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="留空代表所有用户角色均有权使用"></i></th>
				<th scope="col">自定义参数</th>
				<th scope="col">启用状态</th>
				<th scope="col" style="width:8rem;">操作</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><input type="number" class="form-control input-number" value="1"></td>
				<td>修改用户名插件</td>
				<td><img src="../assets/images/temp/placeholder_icon.png" width="24" height="24"> 修改用户名</td>
				<td></td>
				<td></td>
				<td><i class="bi bi-check-lg text-success"></i></td>
				<td>
					<button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">修改</button>
					<button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button>
				</td>
			</tr>
			<tr>
				<td><input type="number" class="form-control input-number" value="2"></td>
				<td>头像挂件</td>
				<td><img src="../assets/images/temp/placeholder_icon.png" width="24" height="24"> 挂件道具</td>
				<td><span class="badge bg-light text-dark">管理员</span></td>
				<td>test</td>
				<td><i class="bi bi-dash-lg text-secondary"></i></td>
				<td>
					<button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">修改</button>
					<button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<!--操作列表 结束-->
<nav aria-label="Page navigation example">
	<ul class="pagination">
		<li class="page-item disabled"><a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>
		<li class="page-item active"><a class="page-link" href="#">1</a></li>
		<li class="page-item"><a class="page-link" href="#">2</a></li>
		<li class="page-item"><a class="page-link" href="#">3</a></li>
		<li class="page-item"><a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>
	</ul>
</nav>



@endsection
