@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::manage.sidebar')
@endsection

@section('content')
  <div class="row mb-4 border-bottom">
    <div class="col-lg-7">
      <h3>后端配置</h3>
      <p class="text-secondary">Fresns 后端系统配置信息，非常重要</p>
    </div>
    <div class="col-lg-5">
      <div class="input-group mt-2 mb-4 justify-content-lg-end">
        <a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
      </div>
    </div>
  </div>
  <!--配置 开始-->
  <form>
    <div class="row mb-3">
      <label for="backend_url" class="col-lg-2 col-form-label text-lg-end">后端网址：</label>
      <div class="col-lg-6"><input type="url" class="form-control" id="backend_url" placeholder="https://"></div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 主程序 API 和插件默认访问地址，结尾不带 /</div>
    </div>
    <div class="row mb-3">
      <label for="backend_url" class="col-lg-2 col-form-label text-lg-end">安全入口：</label>
      <div class="col-lg-6"><input type="url" class="form-control" id="backend_url" placeholder="admin"></div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 设置后只能通过指定安全入口登录控制台</div>
    </div>
    <div class="row mb-3">
      <label for="backend_url" class="col-lg-2 col-form-label text-lg-end">入口完整地址：</label>
      <div class="col-lg-6">
        <div class="input-group">
          <span class="form-control bg-light">https://abc.com/fresns/admin</span>
          <button class="btn btn-outline-secondary" type="button">复制</button>
        </div>
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 设置后只能通过指定安全入口登录控制台</div>
    </div>

    <!--保存按钮-->
    <div class="row my-3">
      <div class="col-lg-2"></div>
      <div class="col-lg-6">
        <button type="submit" class="btn btn-primary">提交保存</button>
      </div>
    </div>
  </form>
  <!--配置 结束-->
@endsection
