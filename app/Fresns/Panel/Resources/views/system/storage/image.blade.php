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
    <ul class="nav nav-tabs nav-fill">
      <li class="nav-item"><a class="nav-link active" href="system-storage-image.html">图片存储设置</a></li>
      <li class="nav-item"><a class="nav-link" href="system-storage-video.html">视频存储设置</a></li>
      <li class="nav-item"><a class="nav-link" href="system-storage-audio.html">音频存储设置</a></li>
      <li class="nav-item"><a class="nav-link" href="system-storage-doc.html">文档存储设置</a></li>
      <li class="nav-item"><a class="nav-link" href="system-storage-repair.html">补位图设置</a></li>
    </ul>
  </div>
  <!--表单 开始-->
  <form action="{{ route('panel.storage.image.update') }}" method="post">
    @csrf
    @method('put')
    <!--存储配置-->
    <div class="row mb-4">
      <label for="account_policies" class="col-lg-2 col-form-label text-lg-end">存储配置：</label>
      <div class="col-lg-6">
        <div class="input-group mb-3">
          <label class="input-group-text w-25">存储服务商</label>
          <select class="form-select" id="images_service" name="images_service">
            <option value="">不启用</option>
            @foreach($pluginParams['storage'] as $plugin)
              <option value="{{ $plugin->unikey }}" {{ $params['images_service'] == $plugin->unikey ? 'selected' : '' }}>{{ $plugin->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="input-group mb-3">
          <label class="input-group-text w-25">Secret ID</label>
          <input type="text" class="form-control" name="images_secret_id" value="{{ $params['images_secret_id'] }}">
        </div>
        <div class="input-group mb-3">
          <label class="input-group-text w-25">Secret Key</label>
          <input type="text" class="form-control" name="images_secret_key" value="{{ $params['images_secret_key'] }}">
        </div>
        <div class="input-group mb-3">
          <label class="input-group-text w-25">存储配置名称</label>
          <input type="text" class="form-control" name="images_bucket_name" value="{{ $params['images_bucket_name'] }}">
        </div>
        <div class="input-group mb-3">
          <label class="input-group-text w-25">存储配置地域</label>
          <input type="text" class="form-control" name="images_bucket_area" value="{{ $params['images_bucket_area'] }}">
        </div>
        <div class="input-group">
          <label class="input-group-text w-25">存储配置域名</label>
          <input type="url" class="form-control" name="images_bucket_domain" value="{{ $params['images_bucket_domain']}}">
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
          <input type="text" class="form-control" name="images_ext" value="{{ $params['images_ext'] }}" placeholder="png,gif,jpg,jpeg,bmp,heic">
        </div>
        <div class="input-group mb-3">
          <label class="input-group-text w-25">支持的最大尺寸</label>
          <input type="text" class="form-control" name="images_max_size" value="{{ $params['images_max_size'] }}">
          <span class="input-group-text">MB</span>
        </div>
        <div class="input-group mb-3">
          <label class="input-group-text w-25">防盗链功能</label>
          <div class="form-control bg-white">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="images_url_status" id="images_url_status_false" value="false" data-bs-toggle="collapse" data-bs-target="#images_url_status_setting.show" aria-expanded="false" aria-controls="images_url_status_setting" {{ $params['images_url_status'] == 'false' ? 'checked' : '' }}>
              <label class="form-check-label" for="images_url_status_false">关闭</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="images_url_status" id="images_url_status_true" value="true" data-bs-toggle="collapse" data-bs-target="#images_url_status_setting:not(.show)" aria-expanded="false" aria-controls="images_url_status_setting" {{ $params['images_url_status'] == 'true' ? 'checked' : ''}}>
              <label class="form-check-label" for="images_url_status_true">开启</label>
            </div>
          </div>
        </div>
        <!--防盗链功能 开始-->
        <div class="collapse {{ $params['images_url_status'] == 'true' ? 'show' : '' }}" id="images_url_status_setting">
          <div class="input-group mb-3">
            <label class="input-group-text w-25">防盗链 Key</label>
            <input type="text" class="form-control" name="images_url_key" value="{{ $params['images_url_key'] }}">
          </div>
          <div class="input-group mb-3">
            <label class="input-group-text">防盗链签名有效期</label>
            <input type="text" class="form-control" name="images_url_expire" value="{{ $params['images_url_expire'] }}">
            <span class="input-group-text">分钟</span>
          </div>
        </div>
        <!--防盗链功能 结束-->
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 如果插件不支持防盗链功能，请勿开启，否则将导致资源无法访问。</div>
    </div>
    <!--图片处理功能配置-->
    <div class="row mb-4">
      <label for="account_policies" class="col-lg-2 col-form-label text-lg-end">图片处理功能配置：</label>
      <div class="col-lg-6">
        <div class="input-group mb-3">
          <label class="input-group-text w-25">配置图</label>
          <input type="text" class="form-control" name="images_thumb_config" value="{{ $params['images_thumb_config'] }}">
        </div>
        <div class="input-group mb-3">
          <label class="input-group-text w-25">头像图</label>
          <input type="text" class="form-control" name="images_thumb_avatar" value="{{ $params['images_thumb_avatar'] }}">
        </div>
        <div class="input-group mb-3">
          <label class="input-group-text w-25">等比例缩略图</label>
          <input type="text" class="form-control" name="images_thumb_ratio" value="{{ $params['images_thumb_ratio'] }}">
        </div>
        <div class="input-group mb-3">
          <label class="input-group-text w-25">正方形缩略图</label>
          <input type="text" class="form-control" name="images_thumb_square" value="{{ $params['images_thumb_square'] }}">
        </div>
        <div class="input-group mb-3">
          <label class="input-group-text w-25">原图压缩图</label>
          <input type="text" class="form-control" name="images_thumb_big" value="{{ $params['images_thumb_big'] }}">
        </div>
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 可以配置样式名或高级参数。可实时处理图片压缩、缩放、打水印等各种功能，常见的“间隔标识符”为半角字符 “!”、“-”、“_” 三种。支持高级参数，写法见存储服务商的开发者文档。</div>
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
