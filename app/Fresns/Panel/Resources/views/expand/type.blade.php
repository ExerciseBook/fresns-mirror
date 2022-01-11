@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::expand.sidebar')
@endsection

@section('content')
<div class="row mb-4 border-bottom">
	<div class="col-lg-9">
		<h3>内容类型扩展</h3>
		<p class="text-secondary">将呈现在「帖子」列表页面中，用于输出指定「类型」的帖子，以及定义数据结果来源。</p>
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
				<th scope="col">数据来源</th>
				<th scope="col">启用状态</th>
				<th scope="col" style="width:8rem;">操作</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><input type="number" class="form-control input-number" value="1"></td>
				<td>全部</td>
				<td>全部</td>
				<td></td>
				<td><i class="bi bi-check-lg text-success"></i></td>
				<td>
					<button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">修改</button>
				</td>
			</tr>
			<tr>
				<td><input type="number" class="form-control input-number" value="2"></td>
				<td>文本</td>
				<td>文本</td>
				<td></td>
				<td><i class="bi bi-check-lg text-success"></i></td>
				<td>
					<button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">修改</button>
				</td>
			</tr>
			<tr>
				<td><input type="number" class="form-control input-number" value="3"></td>
				<td>附件：图片</td>
				<td>图片</td>
				<td>
					<button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#sortNumberModal">帖子总列表</button>
					<button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#sortNumberModal">关注对象的帖子</button>
				</td>
				<td><i class="bi bi-check-lg text-success"></i></td>
				<td>
					<button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">修改</button>
				</td>
			</tr>
			<tr>
				<td><input type="number" class="form-control input-number" value="4"></td>
				<td>附件：视频</td>
				<td>视频</td>
				<td>
					<button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#sortNumberModal">帖子总列表</button>
					<button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#sortNumberModal">关注对象的帖子</button>
					<button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#sortNumberModal">附近范围的帖子</button>
				</td>
				<td><i class="bi bi-check-lg text-success"></i></td>
				<td>
					<button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">修改</button>
				</td>
			</tr>
			<tr>
				<td><input type="number" class="form-control input-number" value="5"></td>
				<td>附件：音频</td>
				<td>音频</td>
				<td></td>
				<td><i class="bi bi-check-lg text-success"></i></td>
				<td>
					<button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">修改</button>
				</td>
			</tr>
			<tr>
				<td><input type="number" class="form-control input-number" value="6"></td>
				<td>附件：文档</td>
				<td>文档</td>
				<td>
					<button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#sortNumberModal">帖子总列表</button>
					<button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#sortNumberModal">关注对象的帖子</button>
					<button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#sortNumberModal">附近范围的帖子</button>
				</td>
				<td><i class="bi bi-check-lg text-success"></i></td>
				<td>
					<button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">修改</button>
				</td>
			</tr>
			<tr>
				<td><input type="number" class="form-control input-number" value="7"></td>
				<td>仿拼多多商城</td>
				<td>商品</td>
				<td>
					<button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#sortNumberModal">帖子总列表</button>
				</td>
				<td><i class="bi bi-check-lg text-success"></i></td>
				<td>
					<button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">修改</button>
					<button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button>
				</td>
			</tr>
			<tr>
				<td><input type="number" class="form-control input-number" value="8"></td>
				<td>投票插件</td>
				<td>投票</td>
				<td>
					<button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#sortNumberModal">帖子总列表</button>
				</td>
				<td><i class="bi bi-check-lg text-success"></i></td>
				<td>
					<button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">修改</button>
					<button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button>
				</td>
			</tr>
			<tr>
				<td><input type="number" class="form-control input-number" value="9"></td>
				<td>知乎问答索引插件</td>
				<td>问答</td>
				<td>
					<button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#sortNumberModal">帖子总列表</button>
				</td>
				<td><i class="bi bi-check-lg text-success"></i></td>
				<td>
					<button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">修改</button>
					<button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button>
				</td>
			</tr>
			<tr>
				<td><input type="number" class="form-control input-number" value="10"></td>
				<td>豆瓣电影</td>
				<td>电影</td>
				<td>
					<button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#sortNumberModal">帖子总列表</button>
				</td>
				<td><i class="bi bi-dash-lg text-secondary"></i></td>
				<td>
					<button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">修改</button>
					<button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button>
				</td>
			</tr>
			<tr>
				<td><input type="number" class="form-control input-number" value="11"></td>
				<td>你画我猜</td>
				<td>游戏</td>
				<td></td>
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

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">内容类型扩展</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form>
					<div class="mb-3 row">
						<label class="col-sm-3 col-form-label">显示顺序</label>
						<div class="col-sm-9">
							<input type="number" class="form-control input-number">
						</div>
					</div>
					<div class="mb-3 row">
						<label class="col-sm-3 col-form-label">关联插件</label>
						<div class="col-sm-9">
							<select class="form-select">
								<option selected disabled>请选择插件</option>
								<option value="1">One</option>
								<option value="2">Two</option>
								<option value="3">Three</option>
							</select>
						</div>
					</div>
					<div class="mb-3 row">
						<label class="col-sm-3 col-form-label">显示名称</label>
						<div class="col-sm-9">
							<button type="button" class="btn btn-outline-secondary btn-modal w-100 text-start" data-bs-toggle="modal" data-bs-target="#langModal">显示名称</button>
						</div>
					</div>
					<div class="mb-3 row">
						<label class="col-sm-3 col-form-label">数据来源</label>
						<div class="col-sm-9">
							<div class="form-floating mb-3">
								<select class="form-select" id="floatingSelect" aria-label="Floating label select example">
									<option disabled>选择提供数据的插件</option>
									<option value="" selected>默认</option>
									<option value="1">One</option>
									<option value="2">Two</option>
									<option value="3">Three</option>
								</select>
								<label for="floatingSelect">/api/fresns/post/lists</label>
							</div>
							<div class="form-floating mb-3">
								<select class="form-select" id="floatingSelect" aria-label="Floating label select example">
									<option disabled>选择提供数据的插件</option>
									<option value="" selected>默认</option>
									<option value="1">One</option>
									<option value="2">Two</option>
									<option value="3">Three</option>
								</select>
								<label for="floatingSelect">/api/fresns/post/follows</label>
							</div>
							<div class="form-floating">
								<select class="form-select" id="floatingSelect" aria-label="Floating label select example">
									<option disabled>选择提供数据的插件</option>
									<option value="" selected>默认</option>
									<option value="1">One</option>
									<option value="2">Two</option>
									<option value="3">Three</option>
								</select>
								<label for="floatingSelect">/api/fresns/post/nearbys</label>
							</div>
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
									<td><input type="text" class="form-control" value="显示名称"></td>
								</tr>
								<tr>
									<td>zh-Hans-SG</td>
									<td>简体中文(新加坡)</td>
									<td><input type="text" class="form-control" value="显示名称"></td>
								</tr>
								<tr>
									<td>zh-Hans-HK</td>
									<td>繁體中文(香港)</td>
									<td><input type="text" class="form-control" value="顯示名稱"></td>
								</tr>
								<tr>
									<td>en-US <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="默认语言"></i></td>
									<td>English(United States)</td>
									<td><input type="text" class="form-control" value="display name"></td>
								</tr>
								<tr>
									<td>ja</td>
									<td>日本語</td>
									<td><input type="text" class="form-control" value="表示名"></td>
								</tr>
								<tr>
									<td>ko-KR</td>
									<td>한국어(대한민국)</td>
									<td><input type="text" class="form-control" value="이름 표시하기"></td>
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


<!-- sortNumber Modal -->
<div class="modal fade" id="sortNumberModal" tabindex="-1" aria-labelledby="sortNumberModal" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">排序编号配置</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form>
					<div class="table-responsive">
						<table class="table table-hover align-middle text-nowrap">
							<thead>
								<tr class="table-info">
									<th scope="col" style="width:10rem;">编号 <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="如何填写请参见「数据来源」关联插件的说明文档"></i></th>
									<th scope="col">标题</th>
									<th scope="col">描述</th>
									<th scope="col" style="width:6rem;">操作</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><input type="number" class="form-control input-number" value="123"></td>
									<td><button type="button" class="btn btn-outline-secondary btn-modal w-100 text-start" data-bs-toggle="modal" data-bs-target="#sortNumberLangModal">标题</button></td>
									<td><button type="button" class="btn btn-outline-secondary btn-modal w-100 text-start" data-bs-toggle="modal" data-bs-target="#sortNumberLangModal">描述</button></td>
									<td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button></td>
								</tr>
								<tr>
									<td><input type="number" class="form-control input-number" value="2"></td>
									<td><button type="button" class="btn btn-outline-secondary btn-modal w-100 text-start" data-bs-toggle="modal" data-bs-target="#sortNumberLangModal">标题</button></td>
									<td><button type="button" class="btn btn-outline-secondary btn-modal w-100 text-start" data-bs-toggle="modal" data-bs-target="#sortNumberLangModal">描述</button></td>
									<td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button></td>
								</tr>
								<tr>
									<td><input type="number" class="form-control input-number" value="56"></td>
									<td><button type="button" class="btn btn-outline-secondary btn-modal w-100 text-start" data-bs-toggle="modal" data-bs-target="#sortNumberLangModal">标题</button></td>
									<td><button type="button" class="btn btn-outline-secondary btn-modal w-100 text-start" data-bs-toggle="modal" data-bs-target="#sortNumberLangModal">描述</button></td>
									<td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button></td>
								</tr>
								<tr>
									<td colspan="4"><button class="btn btn-outline-success btn-sm px-3" type="button"><i class="bi bi-plus-circle-dotted"></i> 新增编号</button></td>
								</tr>
							</tbody>
						</table>
					</div>
					<!--保存按钮-->
					<div class="text-center">
						<button type="submit" class="btn btn-primary">提交保存</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- sortNumber Language Modal -->
<div class="modal fade" id="sortNumberLangModal" tabindex="-1" aria-labelledby="sortNumberLangModal" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">排序编号多语言设置</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form>
					<div class="table-responsive">
						<table class="table table-hover align-middle text-nowrap">
							<thead>
								<tr class="table-info">
									<th scope="col">语言标签</th>
									<th scope="col">语言名称</th>
									<th scope="col" class="w-50">标题</th>
									<th scope="col" class="w-50">描述</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>zh-Hans-CN</td>
									<td>简体中文(内地)</td>
									<td><input type="text" class="form-control" value=""></td>
									<td><input type="text" class="form-control" value=""></td>
								</tr>
								<tr>
									<td>zh-Hans-SG</td>
									<td>简体中文(新加坡)</td>
									<td><input type="text" class="form-control" value=""></td>
									<td><input type="text" class="form-control" value=""></td>
								</tr>
								<tr>
									<td>zh-Hans-HK</td>
									<td>繁體中文(香港)</td>
									<td><input type="text" class="form-control" value=""></td>
									<td><input type="text" class="form-control" value=""></td>
								</tr>
								<tr>
									<td>en-US <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="默认语言"></i></td>
									<td>English(United States)</td>
									<td><input type="text" class="form-control" value=""></td>
									<td><input type="text" class="form-control" value=""></td>
								</tr>
								<tr>
									<td>ja</td>
									<td>日本語</td>
									<td><input type="text" class="form-control" value=""></td>
									<td><input type="text" class="form-control" value=""></td>
								</tr>
								<tr>
									<td>ko-KR</td>
									<td>한국어(대한민국)</td>
									<td><input type="text" class="form-control" value=""></td>
									<td><input type="text" class="form-control" value=""></td>
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
