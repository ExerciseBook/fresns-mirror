@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::manage.sidebar')
@endsection

@section('content')
  <div class="row mb-4 border-bottom">
    <div class="col-lg-7">
      <h3>更新</h3>
      <p class="text-secondary">最后检查于 2021-03-10 20:16:10</p>
    </div>
    <div class="col-lg-5">
      <div class="input-group mt-2 mb-4 justify-content-lg-end">
        <button class="btn btn-primary" type="button"><i class="bi bi-arrow-clockwise"></i> 检查更新</button>
        <a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
      </div>
    </div>
  </div>
  <!--更新 开始-->
  <div class="card mb-4">
    <div class="card-header">主程序</div>
    <div class="card-body">
      <h5 class="card-title">有新的 Fresns 版本可供升级。</h5>
      <p class="card-text">您可以升级到 Fresns v1.0.1</p>
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#upgrade">更新</button>
    </div>
  </div>
  {{--<div class="row">--}}
    {{--<div class="col-md-6 mb-4">--}}
      {{--<div class="card">--}}
        {{--<div class="card-header">扩展插件</div>--}}
        {{--<div class="card-body">--}}
          {{--<ul class="list-group list-group-flush">--}}
            {{--<li class="list-group-item d-flex justify-content-between align-items-center">--}}
              {{--<div><i class="bi bi-journal-code"></i> 小程序助手 <span class="badge bg-secondary">1.0.9</span> to <span class="badge bg-danger">1.1.0</span></div>--}}
              {{--<div><button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#upgrade">更新</button></div>--}}
            {{--</li>--}}
            {{--<li class="list-group-item d-flex justify-content-between align-items-center">--}}
              {{--<div><i class="bi bi-journal-code"></i> 每日榜单 <span class="badge bg-secondary">1.0.9</span> to <span class="badge bg-danger">1.1.0</span></div>--}}
              {{--<div><button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#upgrade">更新</button></div>--}}
            {{--</li>--}}
          {{--</ul>--}}
        {{--</div>--}}
      {{--</div>--}}
    {{--</div>--}}
    {{--<div class="col-md-6 mb-4">--}}
      {{--<div class="card">--}}
        {{--<div class="card-header">移动应用</div>--}}
        {{--<div class="card-body">--}}
          {{--<ul class="list-group list-group-flush">--}}
            {{--<li class="list-group-item d-flex justify-content-between align-items-center">--}}
              {{--<div><i class="bi bi-phone"></i> Fresns for iOS <span class="badge bg-secondary">1.0.9</span> to <span class="badge bg-danger">1.1.0</span></div>--}}
              {{--<div><button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#upgrade">更新</button></div>--}}
            {{--</li>--}}
          {{--</ul>--}}
        {{--</div>--}}
      {{--</div>--}}
    {{--</div>--}}
    {{--<div class="col-md-6 mb-4">--}}
      {{--<div class="card">--}}
        {{--<div class="card-header">网站引擎</div>--}}
        {{--<div class="card-body">--}}
          {{--<ul class="list-group list-group-flush">--}}
            {{--<div class="p-5 text-center">--}}
              {{--<i class="bi bi-view-list"></i> 暂无更新--}}
            {{--</div>--}}
          {{--</ul>--}}
        {{--</div>--}}
      {{--</div>--}}
    {{--</div>--}}
    {{--<div class="col-md-6 mb-4">--}}
      {{--<div class="card">--}}
        {{--<div class="card-header">主题模板</div>--}}
        {{--<div class="card-body">--}}
          {{--<ul class="list-group list-group-flush">--}}
            {{--<li class="list-group-item d-flex justify-content-between align-items-center">--}}
              {{--<div><i class="bi bi-laptop"></i> BBS 主题 <span class="badge bg-secondary">Beta</span> to <span class="badge bg-danger">1.0.0</span></div>--}}
              {{--<div><button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#upgrade">更新</button></div>--}}
            {{--</li>--}}
          {{--</ul>--}}
        {{--</div>--}}
      {{--</div>--}}
    {{--</div>--}}
  {{--</div>--}}

  <!-- 插件升级 Modal -->
  <div class="modal fade" id="upgrade" tabindex="-1" aria-labelledby="upgrade" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-laptop"></i> 小程序助手 <span class="badge bg-secondary">1.0.9</span> to <span class="badge bg-danger">1.1.0</span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body ps-5">
          <p><i class="bi bi-x-lg text-danger me-2"></i>初始化验证 <span class="badge bg-secondary">报错信息</span></p>
          <p><i class="bi bi-check-lg text-success me-2"></i>下载应用包</p>
          <p><i class="bi bi-check-lg text-success me-2"></i>解压应用包</p>
          <p><i class="spinner-border spinner-border-sm me-2" role="status"></i>安装应用</p>
          <p><i class="bi bi-hourglass text-secondary me-2"></i>清空缓存</p>
          <p><i class="bi bi-hourglass text-secondary me-2"></i>完成</p>
        </div>
      </div>
    </div>
  </div>
@endsection
