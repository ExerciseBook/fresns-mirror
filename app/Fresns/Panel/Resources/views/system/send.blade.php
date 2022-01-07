@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::system.sidebar')
@endsection

@section('content')

  <div class="row mb-4">
    <div class="col-lg-7">
      <h3>发信设置</h3>
      <p class="text-secondary">邮件短信和系统通知等消息发送设置。</p>
    </div>
    <div class="col-lg-5">
      <div class="input-group mt-2 mb-4 justify-content-lg-end">
        <a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
      </div>
    </div>
    <ul class="nav nav-tabs">
      <li class="nav-item"><a class="nav-link active" href="system-send.html">发信服务商</a></li>
      <li class="nav-item"><a class="nav-link" href="system-verifycode.html">验证码模板</a></li>
    </ul>
  </div>
  <!--表单 开始-->
  <form action="{{ route('panel.send.update') }}" method="post">
    @csrf
    @method('put')
    <!--邮箱配置-->
    <div class="row mb-4">
      <label class="col-lg-2 col-form-label text-lg-end">邮箱配置：</label>
      <div class="col-lg-6">
        <div class="input-group mb-3">
          <label class="input-group-text">邮件服务商</label>
          <select class="form-select" name="send_email_service">
            <option value="" {{ !$params['send_email_service'] ? 'selected' : ''}}>不启用</option>
            @foreach($pluginParams['email'] as $plugin)
              <option value="{{ $plugin->unikey }}" {{ $params['send_email_service'] == $plugin->unikey ? 'selected' : '' }}>{{ $plugin->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <!--短信配置-->
    <div class="row mb-4">
      <label class="col-lg-2 col-form-label text-lg-end">短信配置：</label>
      <div class="col-lg-6">
        <div class="input-group mb-3">
          <label class="input-group-text">短信服务商</label>
          <select class="form-select" name="send_sms_service">
            <option value="" {{ !$params['send_sms_service'] ? 'selected' : ''}}>不启用</option>
            @foreach($pluginParams['sms'] as $plugin)
              <option value="{{ $plugin->unikey }}" {{ $params['send_sms_service'] == $plugin->unikey ? 'selected' : '' }}>{{ $plugin->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="input-group mb-3">
          <label class="input-group-text">默认国际区号</label>
          <input type="text" class="form-control" name="send_sms_code" placeholder="+86" value="{{ $params['send_sms_code'] }}">
        </div>
        <div class="input-group mb-3">
          <label class="input-group-text">支持更多区号</label>
          <textarea class="form-control" name="send_sms_code_more" aria-label="With textarea">{!! $params['send_sms_code_more'] !!}</textarea>
          <span class="input-group-text w-50 text-start text-wrap fs-7">一行一个，区号带 + 号。<br>留空代表只支持单一国家。<br>配置前请确认服务商支持国际短信功能。</span>
        </div>
      </div>
    </div>
    <!--iOS 配置-->
    <div class="row mb-4">
      <label class="col-lg-2 col-form-label text-lg-end">iOS 配置：</label>
      <div class="col-lg-6">
        <div class="input-group mb-3">
          <label class="input-group-text">通知服务商</label>
          <select class="form-select" name="send_ios_service">
            <option value="" {{ !$params['send_ios_service'] ? 'selected' : ''}}>不启用</option>
            @foreach($pluginParams['ios'] as $plugin)
              <option value="{{ $plugin->unikey }}" {{ $params['send_ios_service'] == $plugin->unikey ? 'selected' : '' }}>{{ $plugin->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> iOS 系统弹窗通知功能。</div>
    </div>
    <!--Android 配置-->
    <div class="row mb-4">
      <label class="col-lg-2 col-form-label text-lg-end">Android 配置：</label>
      <div class="col-lg-6">
        <div class="input-group mb-3">
          <label class="input-group-text">通知服务商</label>
          <select class="form-select" name="send_android_service">
            <option value="" {{ !$params['send_android_service'] ? 'selected' : ''}}>不启用</option>
            @foreach($pluginParams['android'] as $plugin)
              <option value="{{ $plugin->unikey }}" {{ $params['send_android_service'] == $plugin->unikey ? 'selected' : '' }}>{{ $plugin->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> Android 系统弹窗通知功能。</div>
    </div>
    <!--微信配置-->
    <div class="row mb-4">
      <label class="col-lg-2 col-form-label text-lg-end">微信配置：</label>
      <div class="col-lg-6">
        <div class="input-group mb-3">
          <label class="input-group-text">通知服务商</label>
          <select class="form-select" name="send_wechat_service">
            <option value="" {{ !$params['send_wechat_service'] ? 'selected' : ''}}>不启用</option>
            @foreach($pluginParams['wechat'] as $plugin)
              <option value="{{ $plugin->unikey }}" {{ $params['send_wechat_service'] == $plugin->unikey ? 'selected' : '' }}>{{ $plugin->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 微信服务通知或者公众号模板消息。</div>
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
