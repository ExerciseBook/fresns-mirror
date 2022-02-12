@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::system.sidebar')
@endsection

@section('content')
<div class="row mb-4">
	<div class="col-lg-7">
		<h3>存储设置</h3>
		<p class="text-secondary">四种资源文件可分开存储，也可存储在同一处，只需将存储配置信息填写一致即可。</p>
	</div>
	<div class="col-lg-5">
		<div class="input-group mt-2 mb-4 justify-content-lg-end">
			<a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
		</div>
	</div>
	@include('panel::system.storage.tab')
</div>
<!--表单 开始-->
<form action="{{ route('panel.storage.audio.update') }}" method="post">
	@csrf
	@method('put')
	<!--存储配置-->
	<div class="row mb-4">
		<label for="account_policies" class="col-lg-2 col-form-label text-lg-end">存储配置：</label>
		<div class="col-lg-6">
			<div class="input-group mb-3">
				<span class="input-group-text w-25">存储服务商</span>
				<select class="form-select" id="audios_service" name="audios_service">
					<option value="">不启用</option>
					@foreach($pluginParams['storage'] as $plugin)
					  <option value="{{ $plugin->unikey }}" {{ $params['audios_service'] == $plugin->unikey ? 'selected' : '' }}>{{ $plugin->name }}</option>
					@endforeach
				</select>
			</div>
			<div class="input-group mb-3">
				<label class="input-group-text w-25">Secret ID</label>
				<input type="text" class="form-control" id="audios_secret_id"  name="audios_secret_id" value="{{ $params['audios_secret_id'] }}">
			</div>
			<div class="input-group mb-3">
				<label class="input-group-text w-25">Secret Key</label>
				<input type="text" class="form-control" id="audios_secret_key"  name="audios_secret_key" value="{{ $params['audios_secret_key'] }}">
			</div>
			<div class="input-group mb-3">
				<label class="input-group-text w-25">存储配置名称</label>
				<input type="text" class="form-control" id="audios_bucket_name"  name="audios_bucket_name" value="{{ $params['audios_bucket_name'] }}">
			</div>
			<div class="input-group mb-3">
				<label class="input-group-text w-25">存储配置地域</label>
				<input type="text" class="form-control" id="audios_bucket_area"  name="audios_bucket_area" value="{{ $params['audios_bucket_area'] }}">
			</div>
			<div class="input-group">
				<label class="input-group-text w-25">存储配置域名</label>
				<input type="url" class="form-control" id="audios_bucket_domain"  name="audios_bucket_domain" value="{{ $params['audios_bucket_domain'] }}">
			</div>
		</div>
		<div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 存储服务商可在应用商店安装更多选择；<br><i class="bi bi-info-circle"></i> 存储配置地域，用不到则留空；<br><i class="bi bi-info-circle"></i> 存储配置域名以 http:// 或 https:// 开头，结尾不带 /</div>
	</div>
	<!--功能配置-->
	<div class="row mb-4">
		<label for="account_policies" class="col-lg-2 col-form-label text-lg-end">功能配置：</label>
		<div class="col-lg-6">
			<div class="input-group mb-3">
				<label class="input-group-text w-25">支持的扩展名</label>
				<input type="text" class="form-control" id="audios_ext" placeholder="mp3,wav,m4a" name="audios_ext" value="{{ $params['audios_ext']??'' }}">
			</div>
			<div class="input-group mb-3">
				<label class="input-group-text w-25">支持的最大尺寸</label>
				<input type="number" class="form-control" id="audios_max_size" name="audios_max_size" value="{{ $params['audios_max_size'] }}">
				<span class="input-group-text">MB</span>
			</div>
			<div class="input-group mb-3">
				<label class="input-group-text w-25">支持的最大时长</label>
				<input type="number" class="form-control" id="audios_max_time" name="audios_max_time" value="{{ $params['audios_max_time'] }}">
				<span class="input-group-text">秒</span>
			</div>
			<div class="input-group mb-3">
				<label class="input-group-text w-25">防盗链功能</label>
				<div class="form-control bg-white">
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="audios_url_status" id="audios_url_status_false" value="false" data-bs-toggle="collapse" data-bs-target="#audios_url_status_setting.show" aria-expanded="false" aria-controls="audios_url_status_setting"   {{ !$params['audios_url_status'] ? 'checked' : '' }}>
						<label class="form-check-label" for="audios_url_status_false">关闭</label>
					</div>
					<div class="form-check form-check-inline">
						<input class="form-check-input" type="radio" name="audios_url_status" id="audios_url_status_true" value="true" data-bs-toggle="collapse" data-bs-target="#audios_url_status_setting:not(.show)" aria-expanded="false" aria-controls="audios_url_status_setting"  {{ $params['audios_url_status'] ? 'checked' : '' }}>
						<label class="form-check-label" for="audios_url_status_true">开启</label>
					</div>
				</div>
			</div>
			<!--防盗链功能 开始-->
			<div class="collapse  {{ $params['audios_url_status'] ? 'show' : '' }}" id="audios_url_status_setting">
				<div class="input-group mb-3">
					<label class="input-group-text w-25">防盗链 Key</label>
					<input type="text" class="form-control" id="audios_url_key"  name="audios_url_key" value="{{ $params['audios_url_key'] }}">
				</div>
				<div class="input-group mb-3">
					<label class="input-group-text">防盗链签名有效期</label>
					<input type="number" class="form-control" id="audios_url_expire"  name="audios_url_expire" value="{{ $params['audios_url_expire'] }}">
					<span class="input-group-text">分钟</span>
				</div>
			</div>
			<!--防盗链功能 结束-->
		</div>
		<div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 如果插件不支持防盗链功能，请勿开启，否则将导致资源无法访问。</div>
	</div>
	<!--音频处理功能配置-->
	<div class="row mb-4">
		<label for="account_policies" class="col-lg-2 col-form-label text-lg-end">音频处理功能配置：</label>
		<div class="col-lg-6">
			<div class="input-group mb-3">
				<label class="input-group-text w-25">音频转码参数</label>
				<input type="text" class="form-control" id="audios_transcode"  name="audios_transcode" value="{{ $params['audios_transcode'] }}">
			</div>
		</div>
		<div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 可以配置模板 ID，详情请查阅关联插件的说明。</div>
	</div>

	<!--保存按钮-->
	<div class="row my-3">
		<div class="col-lg-2"></div>
		<div class="col-lg-8">
			<button type="submit" class="btn btn-primary">提交保存</button>
		</div>
	</div>
</form>

@endsection
