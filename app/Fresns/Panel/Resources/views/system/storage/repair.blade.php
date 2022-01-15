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
<form action="{{ route('panel.storage.repair.update') }}" method="post">
	@csrf
	@method('put')
	<!--存储配置-->
	<div class="row mb-4">
		<label for="account_policies" class="col-lg-2 col-form-label text-lg-end">无效状态补位图：</label>
		<div class="col-lg-6">
			<div class="input-group mb-3">
				<label class="input-group-text">图片无效提示图</label>
				<input type="file" class="form-control" id="repair_image" name="repair_image" value="{{ $params['repair_image'] }}">
				<button class="btn btn-outline-secondary" type="button" id="view_repair_image">查看</button>
			</div>
			<div class="input-group mb-3">
				<label class="input-group-text">视频无效提示图</label>
				<input type="file" class="form-control" id="repair_video" name="repair_video" value="{{ $params['repair_video'] }}">
				<button class="btn btn-outline-secondary" type="button" id="view_repair_video">查看</button>
			</div>
			<div class="input-group mb-3">
				<label class="input-group-text">音频无效提示图</label>
				<input type="file" class="form-control" id="repair_audio" name="repair_audio" value="{{ $params['repair_audio'] }}">
				<button class="btn btn-outline-secondary" type="button" id="view_repair_audio">查看</button>
			</div>
			<div class="input-group mb-3">
				<label class="input-group-text">文档无效提示图</label>
				<input type="file" class="form-control" id="repair_doc" name="repair_doc" value="{{ $params['repair_doc'] }}">
				<button class="btn btn-outline-secondary" type="button" id="view_repair_doc">查看</button>
			</div>
			<div class="form-text"><i class="bi bi-info-circle"></i> 当资源文件的状态为“无效”时输出的补位提示用图</div>
		</div>
	</div>

	<!--保存按钮-->
	<div class="row my-3">
		<div class="col-lg-2"></div>
		<div class="col-lg-8">
			<button type="submit" class="btn btn-primary">提交上传</button>
		</div>
	</div>
</form>

@endsection
