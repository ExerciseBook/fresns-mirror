@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::system.sidebar')
@endsection

@section('content')

  <div class="row mb-4 border-bottom">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('panel.languageMenus.index') }}">系统</a></li>
        <li class="breadcrumb-item"><a href="{{ route('panel.send.show') }}">发信设置</a></li>
        <li class="breadcrumb-item"><a href="{{ route('panel.verifyCodes.index') }}">验证码模板</a></li>
        <li class="breadcrumb-item active" aria-current="page">模板配置<span class="badge bg-secondary ms-2">通用验证码</span></li>
      </ol>
    </nav>
  </div>
  <!--表单 开始-->
  <form action="{{ route('panel.verifyCodes.update', ['itemKey' => $itemKey])}}" method="post">
    @csrf
    @method('put')
    <div class="input-group mb-3">
      <span class="input-group-text">支持方式</span>
      <div class="form-control">
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="has_email" value="1" data-bs-toggle="collapse" data-bs-target="#email_settings" aria-expanded="false" aria-controls="email_settings" {{ ($template['email']['isEnable'] ?? false) ? 'checked' : ''}}>
          <label class="form-check-label" for="email">邮件</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="has_sms" value="1" data-bs-toggle="collapse" data-bs-target="#sms_settings" aria-expanded="false" aria-controls="sms_settings"{{ ($template['sms']['isEnable'] ?? false) ? 'checked' : ''}} >
          <label class="form-check-label" for="sms">短信</label>
        </div>
      </div>
    </div>
    <div class="table-responsive collapse {{ ($template['email']['isEnable'] ?? false) ? 'show' : ''}} " id="email_settings">
      <table class="table table-hover align-middle text-nowrap">
        <thead>
          <tr class="table-primary">
            <th scope="col" colspan="4" class="text-center">邮件模板配置</th>
          </tr>
          <tr class="table-info">
            <th scope="col">语言标签</th>
            <th scope="col">语言名称</th>
            <th scope="col" class="w-50">标题 <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="支持 HTML 格式"></i></th>
            <th scope="col" class="w-50">内容 <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="支持 HTML 格式"></i></th>
          </tr>
        </thead>
        <tbody>
          @foreach($optionalLanguages as $lang)
          <?php
            $templateData = collect($template['email']['template'] ?? [])->where('langTag', $lang['langTag'])->first() ?: [];
          ?>
          <tr>
            <td>{{ $lang['langTag'] }}</td>
            <td>{{$lang['langName']}} @if($lang['areaCode'])({{ optional($areaCodes->where('code', $lang['areaCode'])->first())['localName']}}) @endif</td>
            <td><input type="text" class="form-control" name="email_templates[{{$lang['langTag']}}][title]" value="{{ $templateData['title'] ?? '' }}"></td>
            <td><textarea class="form-control" rows="3" name="email_templates[{{$lang['langTag']}}][content]">{{ $templateData['content'] ?? '' }}</textarea></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="table-responsive collapse {{ ($template['sms']['isEnable'] ?? false) ? 'show' : ''}} " id="sms_settings">
      <table class="table table-hover align-middle text-nowrap">
        <thead>
          <tr class="table-primary">
            <th scope="col" colspan="5" class="text-center">短信模板配置</th>
          </tr>
          <tr class="table-info">
            <th scope="col">语言标签</th>
            <th scope="col">语言名称</th>
            <th scope="col" class="w-25">短信签名名称</th>
            <th scope="col" class="w-25">模板参数 <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="可以配置模板 ID，详情请查阅关联插件的说明。"></i></th>
            <th scope="col" class="w-25">验证码变量名</th>
          </tr>
        </thead>
        <tbody>
          @foreach($optionalLanguages as $lang)
          <?php
            $templateData = collect($template['sms']['template'] ?? [])->where('langTag', $lang['langTag'])->first() ?: [];
          ?>
          <tr>
            <td>{{ $lang['langTag'] }}</td>
            <td>{{$lang['langName']}} @if($lang['areaCode'])({{ optional($areaCodes->where('code', $lang['areaCode'])->first())['localName']}}) @endif</td>
            <td><input type="text" class="form-control" name="sms_templates[{{$lang['langTag'] }}][signName]" value="{{ $templateData['signName'] ?? ''}}"></td>
            <td><input type="text" class="form-control" name="sms_templates[{{$lang['langTag']}}][templateCode]" value="{{ $templateData['templateCode'] ?? ''}}"></td>
            <td><input type="text" class="form-control" name="sms_templates[{{$lang['langTag']}}][codeParam]" value="{{ $templateData['codeParam'] ?? ''}}"></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <!--保存按钮-->
    <div class="text-center">
      <button type="submit" class="btn btn-primary">提交保存</button>
    </div>
  </form>

@endsection
