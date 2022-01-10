@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::client.sidebar')
@endsection

@section('content')

<div class="row mb-4 border-bottom">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="client-menus.html">客户端</a></li>
			<li class="breadcrumb-item"><a href="client-language-pack.html">语言包配置</a></li>
			<li class="breadcrumb-item active" aria-current="page">zh-Hans-CN<span class="badge bg-secondary ms-2">简体中文(新加坡)</span></li>
		</ol>
	</nav>
</div>
<!--表单 开始-->
<form>
	<div class="table-responsive">
		<table class="table table-hover align-middle text-nowrap">
			<thead>
				<tr class="table-info">
					<th scope="col">标识名</th>
					<th scope="col">默认语言内容 <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="默认语言配置的内容，供参考"></i></th>
					<th scope="col">当前语言内容</th>
					<th scope="col" style="width:6rem;">操作</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><input type="text" class="form-control" value="userLogin" readonly></td>
					<td><input type="text" class="form-control" value="Log in" readonly></td>
					<td><input type="text" class="form-control" value="登录"></td>
					<td></td>
				</tr>
				<tr>
					<td><input type="text" class="form-control" value="userRegister" readonly></td>
					<td><input type="text" class="form-control" value="Sign Up" readonly></td>
					<td><input type="text" class="form-control" value="注册"></td>
					<td></td>
				</tr>
				<tr>
					<td><input type="text" class="form-control" value="author" readonly></td>
					<td><input type="text" class="form-control" value="Jarvis Tang" readonly></td>
					<td><input type="text" class="form-control" value="唐杰"></td>
					<td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button></td>
				</tr>
				<tr>
					<td colspan="4" class="text-center"><button class="btn btn-outline-success btn-sm px-3" type="button"  data-bs-toggle="modal" data-bs-target="#createModal"><i class="bi bi-plus-circle-dotted"></i> 新增</button></td>
				</tr>
			</tbody>
		</table>
	</div>
</form>
<!--表单 结束-->

<!-- Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">语言设置</h5>
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
						<label class="col-sm-3 col-form-label">语言代码</label>
						<div class="col-sm-9">
							<select class="form-select">
								<option selected>请选择语言代码</option>
								<option value="zh-Hans">Chinese (Simplified) - 简体中文 > zh-Hans</option>
								<option value="zh-Hant">Chinese (Traditional) - 繁體中文 > zh-Hant</option>
								<option value="en">English - English > en</option>
								<option value="en">Japanese - 日本語 > ja</option>
								<option value="en">Korean - 한국어 > ko</option>
							</select>
						</div>
					</div>
					<div class="mb-3 row">
						<label class="col-sm-3 col-form-label">语言地区</label>
						<div class="col-sm-9 pt-2">
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="area_status" id="area_status_false" value="false" data-bs-toggle="collapse" data-bs-target="#area_setting.show" aria-expanded="false" aria-controls="area_setting" checked>
								<label class="form-check-label" for="area_status_false">不启用</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="area_status" id="area_status_true" value="true" data-bs-toggle="collapse" data-bs-target="#area_setting:not(.show)" aria-expanded="false" aria-controls="area_setting">
								<label class="form-check-label" for="area_status_true">启用</label>
							</div>
						</div>
					</div>
					<div class="collapse" id="area_setting">
						<div class="mb-3 row">
							<label class="col-sm-3 col-form-label">地区代码</label>
							<div class="col-sm-9">
								<div class="input-group">
									<select class="form-select">
										<option selected disabled>Choose...</option>
										<option value="1">Asia</option>
										<option value="2">Africa</option>
										<option value="3">North America</option>
										<option value="4">South America</option>
										<option value="5">Antarctica</option>
										<option value="6">Europe</option>
										<option value="7">Oceania</option>
									</select>
									<select class="form-select">
										<option selected disabled>Choose...</option>
										<option value="CN">China - 中国大陆 > CN</option>
										<option value="HK">Hong Kong - 香港 > HK</option>
										<option value="MO">Macao - 澳門 > MO</option>
										<option value="TW">Taiwan - 台灣 > CN</option>
										<option value="SG">Singapore - 新加坡 > SG</option>
										<option value="JP">Japanese - 日本 > JP</option>
										<option value="KR">Korea, Republic Of - 대한민국 > KR</option>
										<option value="KP">Korea, Democratic People's Republic Of - 북한 > KP</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="mb-3 row">
						<label class="col-sm-3 col-form-label">长度单位</label>
						<div class="col-sm-9">
							<select class="form-select">
								<option value="km" selected>公里 km</option>
								<option value="mi">英里 mi</option>
							</select>
						</div>
					</div>
					<div class="mb-3 row">
						<label class="col-sm-3 col-form-label">日期格式</label>
						<div class="col-sm-9">
							<select class="form-select">
								<option value="yyyy-mm-dd" selected>yyyy-mm-dd</option>
								<option value="yyyy/mm/dd">yyyy/mm/dd</option>
								<option value="yyyy.mm.dd">yyyy.mm.dd</option>
								<option value="mm-dd-yyyy">mm-dd-yyyy</option>
								<option value="mm/dd/yyyy">mm/dd/yyyy</option>
								<option value="mm.dd.yyyy">mm.dd.yyyy</option>
								<option value="dd-mm-yyyy">dd-mm-yyyy</option>
								<option value="dd/mm/yyyy">dd/mm/yyyy</option>
								<option value="dd.mm.yyyy">dd.mm.yyyy</option>
							</select>
							<div class="form-text">yyyy 表示年，mm 表示月，dd 表示天。</div>
						</div>
					</div>
					<div class="mb-3 row">
						<label class="col-sm-3 col-form-label">人性化时间</label>
						<div class="col-sm-9">
							<div class="input-group mb-1">
								<span class="input-group-text">{n} minute ago</span>
								<input type="text" class="form-control">
							</div>
							<div class="input-group mb-1">
								<span class="input-group-text">{n} hour ago</span>
								<input type="text" class="form-control">
							</div>
							<div class="input-group mb-1">
								<span class="input-group-text">{n} day ago</span>
								<input type="text" class="form-control">
							</div>
							<div class="input-group mb-1">
								<span class="input-group-text">{n} month ago</span>
								<input type="text" class="form-control">
							</div>
							<div class="form-text">时间变量名 {n}</div>
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

@endsection
