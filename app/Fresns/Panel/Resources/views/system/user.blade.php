@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::system.sidebar')
@endsection

@section('content')

  <div class="row mb-4 border-bottom">
    <div class="col-lg-7">
      <h3>用户设置</h3>
      <p class="text-secondary">用户基础功能设置。</p>
    </div>
    <div class="col-lg-5">
      <div class="input-group mt-2 mb-4 justify-content-lg-end">
        <a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
      </div>
    </div>
  </div>
  <!--表单 开始-->
  <form action="{{ route('panel.userConfigs.update') }}" method="post">
    @csrf
    @method('put')
    <div class="row mb-4">
      <label for="site_name" class="col-lg-2 col-form-label text-lg-end">
        第三方互联支持：
        <button type="button" class="btn btn-outline-primary btn-sm mt-2 me-3" id="addConnect">新增</button>
      </label>
      <div class="col-lg-6 connect-box">
        @foreach($params['account_connect_services'] ?? [] as $connectService)
          <div class="input-group mb-3">
            <label class="input-group-text" for="inputGroupSelect01">互联平台</label>
            <select class="form-select" name="connects[]">
              @foreach($params['connects'] as $connect)
                <option value="{{ $connect['id'] }}" @if ($connectService['code'] == $connect['id']) selected @endif>{{ $connect['name']}}</option>
              @endforeach
            </select>
            <label class="input-group-text" for="inputGroupSelect02">关联插件</label>
            <select class="form-select" name="connect_plugins[]">
              @foreach($pluginParams['connect'] as $plugin)
                <option value="{{ $plugin->unikey }}" {{ $connectService['unikey'] == $plugin->unikey ? 'selected' : '' }}>{{ $plugin->name }}</option>
              @endforeach
            </select>
            <button class="btn btn-outline-secondary delete-connect" type="button">删除</button>
          </div>
        @endforeach

      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 选择互联平台并关联插件，可实现快捷注册或登录</div>
    </div>

    <div class="row mb-4">
      <label for="site_name" class="col-lg-2 col-form-label text-lg-end">实名认证支持：</label>
      <div class="col-lg-6">
        <select class="form-select" name="account_prove_service">
          <option value="" selected>不启用</option>
          @foreach($pluginParams['prove'] as $plugin)
            <option value="{{ $plugin->unikey }}" {{ $params['account_prove_service'] == $plugin->unikey ? 'selected' : '' }}>{{ $plugin->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 安装认证服务插件后，用户可以实名制认证。</div>
    </div>

    <div class="row mb-4">
      <label for="site_copyright" class="col-lg-2 col-form-label text-lg-end">多用户模式支持：</label>
      <div class="col-lg-6 pt-2">
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="member_multiple" id="member_multiple_false" value="false" data-bs-toggle="collapse" data-bs-target="#member_multiple_setting.show" aria-expanded="false" aria-controls="member_multiple_setting" {{ !$params['member_multiple'] ? 'checked': ''}}>
          <label class="form-check-label" for="member_multiple_false">关闭</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="member_multiple" id="member_multiple_true" value="true" data-bs-toggle="collapse" data-bs-target="#member_multiple_setting:not(.show)" aria-expanded="false" aria-controls="member_multiple_setting" {{ $params['member_multiple'] ? 'checked': ''}}>
          <label class="form-check-label" for="member_multiple_true">开启</label>
        </div>
        <!--多用户模式配置 开始-->
        <div class="collapse {{ $params['member_multiple'] == 'true' ? 'show' : ''}}" id="member_multiple_setting">
          <div class="card mt-2">
            <div class="card-header">多用户模式配置</div>
            <div class="card-body">
              <!--配置 开始-->
              <div class="input-group mb-3">
                <label class="input-group-text" for="multi_member_service">多用户功能插件</label>
                <select class="form-select" name="multi_member_service">
                  <option value="">暂停多用户管理</option>
                  @foreach($pluginParams['multiple'] as $plugin)
                    <option value="{{ $plugin->unikey }}" {{ $params['multi_member_service'] == $plugin->unikey ? 'selected' : '' }}>{{ $plugin->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="input-group mb-1">
                <label class="input-group-text" for="multi_member_roles">有权使用的角色</label>
                <select class="form-select select2" multiple name="multi_member_roles[]">
                  <option value="0" {{ !$params['multi_member_roles'] ? 'selected' : '' }}>全部角色</option>
                  @foreach($memberRoles as $role)
                    <option value="{{ $role->id }}" {{ in_array($role->id, $params['multi_member_roles']) ? 'selected' : '' }}>{{ $role->name }}</option>
                  @endforeach
                </select>
              </div>
              <!--配置 结束-->
            </div>
          </div>
        </div>
        <!--多用户模式配置 结束-->
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 前端使用多用户功能需要安装相应的插件。</div>
    </div>

    <!--默认设置-->
    <div class="row mb-2">
      <label class="col-lg-2 col-form-label text-lg-end">默认设置：</label>
      <div class="col-lg-6">
        <div class="input-group">
          <label class="input-group-text">默认用户角色</label>
          <select class="form-select select2" name="default_role">
            @foreach($memberRoles as $role)
              <option value="{{ $role->id }}" {{ $params['default_role'] == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 注册后默认用户角色</div>
    </div>

	<div class="row mb-2">
		<label class="col-lg-2 col-form-label text-lg-end"></label>
		<div class="col-lg-6">
			<div class="input-group">
				<label class="input-group-text">默认用户头像</label>
				<button class="btn btn-outline-secondary dropdown-toggle showSelectTypeName" type="button" data-bs-toggle="dropdown" aria-expanded="false">@if($params['default_avatar']) 图片地址 @else 上传图片 @endif</button>
				<ul class="dropdown-menu selectImageTyle">
					<li data-name="inputFile"><a class="dropdown-item" href="#">上传图片</a></li>
					<li data-name="inputUrl"><a class="dropdown-item" href="#">图片地址</a></li>
				</ul>
				<input type="file" class="form-control inputFile" name="default_avatar_file" @if($params['default_avatar']) style="display:none;" @endif>
			 <input type="text" class="form-control inputUrl" name="default_avatar" value="{{ $params['default_avatar'] }}" @if(!$params['default_avatar']) style="display:none;" @endif>
				<button class="btn btn-outline-secondary" type="button">查看</button>
			</div>
		</div>
		<div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 用户未设置头像时的默认头像图</div>
	</div>
	<div class="row mb-2">
		<label class="col-lg-2 col-form-label text-lg-end"></label>
		<div class="col-lg-6">
			<div class="input-group">
				<label class="input-group-text">匿名用户头像</label>
				<button class="btn btn-outline-secondary dropdown-toggle showSelectTypeName" type="button" data-bs-toggle="dropdown" aria-expanded="false">@if($params['anonymous_avatar']) 图片地址 @else 上传图片 @endif</button>
				<ul class="dropdown-menu selectImageTyle">
					<li data-name="inputFile"><a class="dropdown-item" href="#">上传图片</a></li>
					<li data-name="inputUrl"><a class="dropdown-item" href="#">图片地址</a></li>
				</ul>
				<input type="file" class="form-control inputFile" name="default_avatar_file" @if($params['anonymous_avatar']) style="display:none;" @endif>
			 <input type="text" class="form-control inputUrl" name="anonymous_avatar" value="{{ $params['anonymous_avatar'] }}" @if(!$params['anonymous_avatar']) style="display:none;" @endif>
				<button class="btn btn-outline-secondary" type="button">查看</button>

			</div>
		</div>
		<div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 内容发表匿名者的头像图</div>
	</div>
	<div class="row mb-4">
		<label class="col-lg-2 col-form-label text-lg-end"></label>
		<div class="col-lg-6">
			<div class="input-group">
				<label class="input-group-text">已停用用户头像</label>
				<button class="btn btn-outline-secondary dropdown-toggle showSelectTypeName" type="button" data-bs-toggle="dropdown" aria-expanded="false">@if($params['deactivate_avatar']) 图片地址 @else 上传图片 @endif</button>
				<ul class="dropdown-menu selectImageTyle">
					<li data-name="inputFile"><a class="dropdown-item" href="#">上传图片</a></li>
					<li data-name="inputUrl"><a class="dropdown-item" href="#">图片地址</a></li>
				</ul>
				<input type="file" class="form-control inputFile" name="default_avatar_file" @if($params['deactivate_avatar']) style="display:none;" @endif>
			 <input type="text" class="form-control inputUrl" name="deactivate_avatar" value="{{ $params['deactivate_avatar'] }}" @if(!$params['deactivate_avatar']) style="display:none;" @endif>
				<button class="btn btn-outline-secondary" type="button">查看</button>
			</div>
		</div>
		<div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 用户注销或停用后的头像图</div>
	</div>

    <!--密码设置-->
    <div class="row mb-4">
      <label class="col-lg-2 col-form-label text-lg-end">密码设置：</label>
      <div class="col-lg-6">
        <div class="input-group mb-2">
          <label class="input-group-text">长度要求</label>
          <input type="number" class="form-control input-number" name="password_length" value="{{ $params['password_length'] }}">
          <span class="input-group-text">位数</span>
        </div>
        <div class="input-group">
          <label class="input-group-text">强度要求</label>
          <div class="form-control bg-white">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="digital" name="password_strength[]" value="1" {{ in_array(1, $params['password_strength']) ? 'checked' : ''}}>
              <label class="form-check-label" for="digital">数字</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="lowercase_letter" name="password_strength[]" value="2" {{ in_array(2, $params['password_strength']) ? 'checked' : ''}}>
              <label class="form-check-label" for="lowercase_letter">小写字母</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="symbol" value="3" name="password_strength[]" {{ in_array(3, $params['password_strength']) ? 'checked' : ''}}>
              <label class="form-check-label" for="symbol">符号</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" id="capital_letter" value="4" name="password_strength[]" {{ in_array(4, $params['password_strength']) ? 'checked' : ''}}>
              <label class="form-check-label" for="capital_letter">大写字母</label>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 长度要求：密码最小长度，0 或不填为不限制；<br><i class="bi bi-info-circle"></i> 强度要求：密码中必须存在所选字符类型，不选则为无限制。</div>
    </div>

    <div class="row mb-4">
      <label class="col-lg-2 col-form-label text-lg-end">修改设置：</label>
      <div class="col-lg-6">
        <div class="input-group mb-2">
          <label class="input-group-text">用户名长度</label>
          <input type="number" class="form-control input-number" name="mname_min" value="{{ $params['mname_min'] }}" placeholder="最小长度">
          <input type="number" class="form-control input-number" name="mname_max" value="{{ $params['mname_max'] }}" placeholder="最大长度">
        </div>
        <div class="input-group mb-2">
          <label class="input-group-text">用户名修改间隔天数</label>
          <input type="number" class="form-control input-number" name="mname_edit" value="{{ $params['mname_edit'] }}">
          <span class="input-group-text">天</span>
        </div>
        <div class="input-group">
          <label class="input-group-text">用户昵称修改间隔天数</label>
          <input type="number" class="form-control input-number" name="nickname_edit" value="{{ $params['nickname_edit'] }}">
          <span class="input-group-text">天</span>
        </div>
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 用户名最小长度和最大长度</div>
    </div>

    <!--保存按钮-->
    <div class="row my-3">
      <div class="col-lg-2"></div>
      <div class="col-lg-6">
        <button type="submit" class="btn btn-primary">提交保存</button>
      </div>
    </div>
  </form>

  <template id="connectTemplate">
    <div class="input-group mb-3">
      <label class="input-group-text" for="inputGroupSelect01">互联平台</label>
      <select class="form-select" name="connects[]">
        @foreach($params['connects'] as $connect)
          <option value="{{ $connect['id'] }}">{{ $connect['name']}}</option>
        @endforeach
      </select>
      <label class="input-group-text" for="inputGroupSelect02">关联插件</label>
      <select class="form-select" name="connect_plugins[]" id="inputGroupSelect02">
        @foreach($pluginParams['connect'] as $plugin)
          <option value="{{ $plugin->unikey }}">{{ $plugin->name }}</option>
        @endforeach
      </select>
      <button class="btn btn-outline-secondary delete-connect" type="button">删除</button>
    </div>
  </template>

@endsection
