@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::client.sidebar')
@endsection

@section('content')
<div class="row mb-4 border-bottom">
	<div class="col-lg-7">
		<h3>语言包配置</h3>
		<p class="text-secondary">为多语言客户端配置各项文本的多语言内容。</p>
	</div>
	<div class="col-lg-5">
		<div class="input-group mt-2 mb-4 justify-content-lg-end">
			<a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
		</div>
	</div>
</div>
<!--操作列表-->
<div class="table-responsive">
	<table class="table table-hover align-middle text-nowrap">
		<thead>
			<tr class="table-info">
				<th scope="col">语言标签</th>
				<th scope="col">语言名称</th>
				<th scope="col">书写方向</th>
				<th scope="col">启用状态</th>
				<th scope="col">操作</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>zh-Hans</td>
				<td>简体中文</td>
				<td>ltr</td>
				<td><i class="bi bi-check-lg text-success"></i></td>
				<td>
					<a class="btn btn-outline-primary btn-sm text-decoration-none" href="{{ route('panel.languagePack.show', ['id' => 1]) }}" role="button">配置语言包</a>
				</td>
			</tr>
			<tr>
				<td>zh-Hans-SG</td>
				<td>简体中文(新加坡)</td>
				<td>ltr</td>
				<td><i class="bi bi-dash-lg text-secondary"></i></td>
				<td>
					<a class="btn btn-outline-primary btn-sm text-decoration-none" href="{{ route('panel.languagePack.show', ['id' => 1]) }}" role="button">配置语言包</a>
				</td>
			</tr>
			<tr>
				<td>zh-Hant-HK</td>
				<td>繁體中文(香港)</td>
				<td>ltr</td>
				<td><i class="bi bi-check-lg text-success"></i></td>
				<td>
					<a class="btn btn-outline-primary btn-sm text-decoration-none" href="{{ route('panel.languagePack.show', ['id' => 1]) }}" role="button">配置语言包</a>
				</td>
			</tr>
			<tr>
				<td>en-US <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="默认语言" aria-label="默认语言"></i></td>
				<td>English(United States)</td>
				<td>ltr</td>
				<td><i class="bi bi-check-lg text-success"></i></td>
				<td>
					<a class="btn btn-outline-primary btn-sm text-decoration-none" href="{{ route('panel.languagePack.show', ['id' => 1]) }}" role="button">配置语言包</a>
				</td>
			</tr>
			<tr>
				<td>ja</td>
				<td>日本語</td>
				<td>ltr</td>
				<td><i class="bi bi-check-lg text-success"></i></td>
				<td>
					<a class="btn btn-outline-primary btn-sm text-decoration-none" href="{{ route('panel.languagePack.show', ['id' => 1]) }}" role="button">配置语言包</a>
				</td>
			</tr>
			<tr>
				<td>ko-KR</td>
				<td>한국어(대한민국)</td>
				<td>ltr</td>
				<td><i class="bi bi-dash-lg text-secondary"></i></td>
				<td>
					<a class="btn btn-outline-primary btn-sm text-decoration-none" href="{{ route('panel.languagePack.show', ['id' => 1]) }}" role="button">配置语言包</a>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<!--操作列表 结束-->

@endsection
