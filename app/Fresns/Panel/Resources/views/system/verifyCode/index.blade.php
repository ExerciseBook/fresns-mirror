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
      <li class="nav-item"><a class="nav-link" href="{{ route('panel.send.show') }}">发信服务商</a></li>
      <li class="nav-item"><a class="nav-link active" href="{{ route('panel.verifyCodes.index')}}">验证码模板</a></li>
    </ul>
  </div>

  <!--操作列表-->
  <div class="table-responsive">
    <table class="table table-hover align-middle text-nowrap">
      <thead>
        <tr class="table-info">
          <th scope="col" class="w-25">模板编号</th>
          <th scope="col">用途说明</th>
          <th scope="col">已支持方式</th>
          <th scope="col" style="width:8rem;">操作</th>
        </tr>
      </thead>
      <tbody>

        <?php $index = 0; ?>
        @foreach($configKeys as $name => $key)
        <?php $index ++ ?>
        <tr>
          <td>{{ $index }}</td>
          <td>{{ $name }}</td>
          <td>
            @if ($params[$key]['email']['isEnable'] ?? false)
              <span class="badge bg-success me-3">邮件</span>
            @endif
            @if ($params[$key]['sms']['isEnable'] ?? false)
              <span class="badge bg-success">短信</span>
            @endif
          </td>
          <td><a class="btn btn-outline-primary btn-sm text-decoration-none" href="{{ route('panel.verifyCodes.edit', ['itemKey' => $key])}}" role="button">配置模板</a></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

@endsection
