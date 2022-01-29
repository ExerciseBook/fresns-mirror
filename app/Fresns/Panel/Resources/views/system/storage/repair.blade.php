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
        <div class="input-group">
          <label class="input-group-text">图片无效提示图</label>
          <button class="btn btn-outline-secondary dropdown-toggle showSelectTypeName" type="button" data-bs-toggle="dropdown" aria-expanded="false">图片地址</button>
          <ul class="dropdown-menu selectImageTyle">
            <li data-name="inputUrl"><a class="dropdown-item" href="#">图片地址</a></li>
            <li data-name="inputFile"><a class="dropdown-item" href="#">上传图片</a></li>
          </ul>
          <input type="file" class="form-control inputFile hidden" name="repair_image_id" style="display:none">
          <input type="text" class="form-control inputUrl"  name="repair_image" value="{{ $params['repair_image'] }}">
          <button class="btn btn-outline-secondary preview-image" type="button">查看</button>
        </div>
      </div>

      <div class="input-group mb-3">
        <div class="input-group">
          <label class="input-group-text">视频无效提示图</label>
          <button class="btn btn-outline-secondary dropdown-toggle showSelectTypeName" type="button" data-bs-toggle="dropdown" aria-expanded="false">图片地址</button>
          <ul class="dropdown-menu selectImageTyle">
            <li data-name="inputUrl"><a class="dropdown-item" href="#">图片地址</a></li>
            <li data-name="inputFile"><a class="dropdown-item" href="#">上传图片</a></li>
          </ul>
          <input type="file" class="form-control inputFile" name="repair_video_id" style="display:none;">
          <input type="text" class="form-control inputUrl"  name="repair_video" value="{{ $params['repair_video'] }}">
          <button class="btn btn-outline-secondary preview-image" type="button">查看</button>
        </div>
      </div>

      <div class="input-group mb-3">
        <div class="input-group">
          <label class="input-group-text">音频无效提示图</label>
          <button class="btn btn-outline-secondary dropdown-toggle showSelectTypeName" type="button" data-bs-toggle="dropdown" aria-expanded="false">图片地址</button>
          <ul class="dropdown-menu selectImageTyle">
            <li data-name="inputUrl"><a class="dropdown-item" href="#">图片地址</a></li>
            <li data-name="inputFile"><a class="dropdown-item" href="#">上传图片</a></li>
          </ul>
          <input type="file" class="form-control inputFile" name="repair_audio_id" style="display:none;">
          <input type="text" class="form-control inputUrl"  name="repair_audio" value="{{ $params['repair_audio'] }}">
          <button class="btn btn-outline-secondary preview-image" type="button">查看</button>
        </div>
      </div>

      <div class="input-group mb-3">
        <div class="input-group">
          <label class="input-group-text">文档无效提示图</label>
          <button class="btn btn-outline-secondary dropdown-toggle showSelectTypeName" type="button" data-bs-toggle="dropdown" aria-expanded="false">图片地址</button>
          <ul class="dropdown-menu selectImageTyle">
            <li data-name="inputUrl"><a class="dropdown-item" href="#">图片地址</a></li>
            <li data-name="inputFile"><a class="dropdown-item" href="#">上传图片</a></li>
          </ul>
          <input type="file" class="form-control inputFile" name="repair_doc_id" style="display:none;">
          <input type="text" class="form-control inputUrl"  name="repair_doc" value="{{ $params['repair_doc'] }}">
          <button class="btn btn-outline-secondary preview-image" type="button">查看</button>
        </div>
      </div>
      <div class="form-text"><i class="bi bi-info-circle"></i> 当资源文件的状态为“无效”时输出的补位提示用图</div>
		</div>
	</div>

  <div class="modal fade image-zoom" id="imageZoom" tabindex="-1" aria-labelledby="imageZoomLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="position-relative image-box">
        <img class="img-fluid" src="">
      </div>
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
