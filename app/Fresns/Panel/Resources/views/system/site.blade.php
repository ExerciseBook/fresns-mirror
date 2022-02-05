@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::system.sidebar')
@endsection

@section('content')

  <!--设置区域 开始-->
  <div class="row mb-4 border-bottom">
    <div class="col-lg-7">
      <h3>站点设置</h3>
      <p class="text-secondary">保障系统正常运行的设置项。</p>
    </div>
    <div class="col-lg-5">
      <div class="input-group mt-2 mb-4 justify-content-lg-end">
        <a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
      </div>
    </div>
  </div>
  <!--表单 开始-->
  <form action="{{ route('panel.site.update') }}" method="post">
    @csrf
    @method('put')
    <div class="row mb-4">
      <label for="site_url" class="col-lg-2 col-form-label text-lg-end">站点网址：</label>
      <div class="col-lg-6"><input type="url" class="form-control" name="site_domain" value="{{ $params['site_domain'] }}" id="site_url" placeholder="https://"></div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 结尾不带 / 符号</div>
    </div>

    <div class="row mb-4">
      <label for="site_name" class="col-lg-2 col-form-label text-lg-end">站点名称：</label>
      <div class="col-lg-6"><button type="button" class="btn btn-outline-secondary btn-modal w-100 text-start" data-bs-toggle="modal" data-bs-target="#siteNameModal">{{ $defaultLangParams['site_name'] }}</button></div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 你的 Fresns 站点的名称。</div>
    </div>

    <div class="row mb-4">
      <label for="site_desc" class="col-lg-2 col-form-label text-lg-end">站点介绍：</label>
      <div class="col-lg-6"><button type="button" class="btn btn-outline-secondary btn-modal w-100 text-start" data-bs-toggle="modal" data-bs-target="#siteDescModal">{{ $defaultLangParams['site_desc'] }}</button></div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 你的 Fresns 站点的介绍。</div>
    </div>

    <div class="row mb-4">
      <label for="site_img" class="col-lg-2 col-form-label text-lg-end">站点标志：</label>
	  <div class="col-lg-6">
		  <div class="input-group mb-1">
			  <label class="input-group-text font-monospace" for="ICON">ICON</label>
			  <button class="btn btn-outline-secondary dropdown-toggle showSelectTypeName" type="button" data-bs-toggle="dropdown" aria-expanded="false">@if($params['site_icon']) 图片地址 @else 上传图片 @endif</button>
			  <ul class="dropdown-menu selectImageTyle">
				  <li  data-name="inputFile"><a class="dropdown-item" href="#">上传图片</a></li>
				  <li  data-name="inputUrl"><a class="dropdown-item" href="#">图片地址</a></li>
			  </ul>
			  <input type="file" class="form-control inputFile" name="site_icon_file" @if($params['site_icon']) style="display:none;" @endif>
			    <input type="text" class="form-control inputUrl" name="site_icon" value="{{ $params['site_icon'] }}" @if(!$params['site_icon']) style="display:none;" @endif>
			  <button class="btn btn-outline-secondary preview-image" type="button">查看</button>
		  </div>
		  <div class="input-group">
			  <label class="input-group-text font-monospace" for="LOGO">LOGO</label>
			  <button class="btn btn-outline-secondary dropdown-toggle showSelectTypeName" type="button" data-bs-toggle="dropdown" aria-expanded="false">@if($params['site_logo']) 图片地址 @else 上传图片 @endif</button>
			  <ul class="dropdown-menu selectImageTyle">
				  <li  data-name="inputFile"><a class="dropdown-item" href="#">上传图片</a></li>
				  <li  data-name="inputUrl"><a class="dropdown-item" href="#">图片地址</a></li>
			  </ul>
			  <input type="file" class="form-control inputFile" name="site_logo_file" @if($params['site_logo']) style="display:none;" @endif>
			  <input type="text" class="form-control inputUrl" name="site_logo" value="{{ $params['site_logo'] }}" @if(!$params['site_logo']) style="display:none;" @endif>
			  <button class="btn btn-outline-secondary preview-image" type="button">查看</button>
		  </div>
	  </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 图片文件，必须配置了<a href="system-storage-image.html">存储设置</a>中的信息才能上传。</div>
    </div>

    <div class="row mb-4">
      <label for="site_copyright" class="col-lg-2 col-form-label text-lg-end">版权信息：</label>
      <div class="col-lg-6">
        <div class="input-group">
          <span class="input-group-text">&copy;</span>
          <input type="text" class="form-control" id="site_copyright" placeholder="Fresns" name="site_copyright" value="{{ $params['site_copyright']}}">
          <input type="text" class="form-control" id="site_copyright_years" placeholder="2020-2021" name="site_copyright_years" value="{{ $params['site_copyright_years'] }}">
        </div>
      </div>
    </div>

    <div class="row mb-4">
      <label for="site_copyright_years" class="col-lg-2 col-form-label text-lg-end">默认时区：</label>
      <div class="col-lg-6">
        <select class="form-select" name="default_timezone">
          @foreach($params['utc'] as $utcItem)
          <option value="{{ $utcItem['value']}}" {{ $params['default_timezone'] == $utcItem['value'] ? 'selected' : ''}}>{{ $utcItem['name'] }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="row mb-4">
      <label for="site_mode" class="col-lg-2 col-form-label text-lg-end">运行模式：</label>
      <div class="col-lg-6 pt-2" id="accordionSiteMode">
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="site_mode" id="site_mode_public" value="public" data-bs-toggle="collapse" data-bs-target="#public_setting:not(.show)" aria-expanded="true" aria-controls="public_setting" {{ $params['site_mode'] == 'public' ? 'checked' : '' }}>
          <label class="form-check-label" for="site_mode_public" >公开模式</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="site_mode" id="site_mode_private" value="private" data-bs-toggle="collapse" data-bs-target="#private_setting:not(.show)" aria-expanded="false" aria-controls="private_setting" {{ $params['site_mode'] == 'private' ? 'checked' : '' }}>
          <label class="form-check-label" for="site_mode_private">私有模式</label>
        </div>
        <!--运行模式设置 开始-->
        <div class="collapse {{$params['site_mode'] == 'public' ? 'show' : ''}}" id="public_setting" aria-labelledby="site_mode_public" data-bs-parent="#accordionSiteMode">
          <!--公开模式设置 开始-->
          <div class="card mt-2">
            <div class="card-header text-success">公开模式配置</div>
            <div class="card-body">
              <!--配置 开始-->
              <div class="input-group mb-3">
                <label class="input-group-text" for="register_close">是否开放注册</label>
                <select class="form-select" id="register_close" name="site_public_close">
                  <option value="false" {{ $params['site_public_close'] == 'false' ? 'selected' : ''}}>关闭注册</option>
                  <option value="true" {{ $params['site_public_close'] == 'true' ? 'selected' : ''}}>开放注册</option>
                </select>
              </div>
              <div class="input-group mb-3">
                <label class="input-group-text" for="register_plugin">注册功能配置</label>
                <select class="form-select" id="register_plugin" name="site_public_service">
                  <option selected>默认</option>
                  @foreach($registerPlugins as $plugin)
                    <option value="{{ $plugin->unikey }}" {{ $params['site_public_service'] == $plugin->unikey ? 'selected' : '' }}>{{ $plugin->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="input-group mb-1">
                <label class="input-group-text" for="site_private_end">注册账号支持</label>
                <div class="form-control bg-white">
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="account_email" name="site_register_email" value="true" {{ $params['site_register_email'] == 'true' ? 'checked' : ''}}>
                    <label class="form-check-label" for="account_email">邮箱注册</label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="account_phone" name="site_register_phone" value="true" {{ $params['site_register_phone'] == 'true' ? 'checked' : ''}}>
                    <label class="form-check-label" for="account_phone">手机号注册</label>
                  </div>
                </div>
              </div>
              <!--配置 结束-->
            </div>
          </div>
          <!--公开模式设置 结束-->
        </div>
        <div class="collapse {{ $params['site_mode'] == 'private' ? 'show' : ''}}" id="private_setting" aria-labelledby="site_mode_private" data-bs-parent="#accordionSiteMode">
          <!--私有模式设置 开始-->
          <div class="card mt-2">
            <div class="card-header text-danger">私有模式配置</div>
            <div class="card-body">
              <!--配置 开始-->
              <div class="input-group mb-3">
                <label class="input-group-text" for="site_private_type">是否对外开放加入</label>
                <select class="form-select" id="site_private_type" name="site_private_close">
                  <option value="false" {{ $params['site_private_close'] == 'false' ? 'selected' : ''}}>关闭</option>
                  <option value="true" {{ $params['site_private_close'] == 'true' ? 'selected' : ''}}>开放</option>
                </select>
              </div>
              <div class="input-group mb-3">
                <label class="input-group-text" for="site_private_plugin">加入通道支持插件</label>
                <select class="form-select" id="site_private_plugin" name="site_private_service">
                  <option selected disabled>请选择插件关联</option>
                  @foreach($joinPlugins as $plugin)
                    <option value="{{ $plugin->unikey }}" {{ $params['site_private_service'] == $plugin->unikey ? 'selected' : '' }}>{{ $plugin->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="input-group mb-1">
                <label class="input-group-text" for="site_private_end">私有到期后的状态</label>
                <select class="form-select" id="site_private_end" name="site_private_end">
                  <option value="0" {{ $params['site_private_end'] == 0 ? 'selected' : '' }}>站点内容全部不可见</option>
                  <option value="1" {{ $params['site_private_end'] == 1 ? 'selected' : '' }}>到期前内容可见，新内容不可见</option>
                </select>
              </div>
              <!--配置 结束-->
            </div>
          </div>
          <!--私有模式设置 结束-->
        </div>
        <!--运行模式设置 结束-->
      </div>
    </div>

    <div class="row mb-4">
      <label for="site_email" class="col-lg-2 col-form-label text-lg-end">管理员邮箱：</label>
      <div class="col-lg-6"><input type="email" class="form-control" id="site_email" name="site_email" value="{{ $params['site_email'] }}" placeholder="support@fresns.com"></div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 当运行出错时，展示给用户，便于用户反馈问题。</div>
    </div>

    <!--保存按钮-->
    <div class="row my-3">
      <div class="col-lg-2"></div>
      <div class="col-lg-6">
        <button type="submit" class="btn btn-primary">提交保存</button>
      </div>
    </div>
  </form>
  <!--表单 结束-->

  <!-- Language Modal -->
  <div class="modal fade" id="siteNameModal" tabindex="-1" aria-labelledby="siteNameModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">站点名称设置</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="{{ route('panel.languages.batch.update', ['itemKey' => 'site_name']) }}" method="post">
            @csrf
            @method('put')
            <input type="hidden" name="update_config" value="site_name">
            <div class="table-responsive">
              <table class="table table-hover align-middle text-nowrap">
                <thead>
                  <tr class="table-info">
                    <th scope="col" class="w-25">语言标签</th>
                    <th scope="col" class="w-25">语言名称</th>
                    <th scope="col" class="w-50">内容</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($optionalLanguages as $lang)
                  <tr>
                    <td>
                      {{ $lang['langTag'] }}
                      @if($lang['langTag'] == $defaultLanguage)
                        <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="默认语言" aria-label="默认语言"></i>
                      @endif
                    </td>
                    <td>{{$lang['langName']}} @if($lang['areaCode'])({{ optional($areaCodes->where('code', $lang['areaCode'])->first())['localName']}}) @endif</td>
                    <td><input type="text" name="languages[{{ $lang['langTag'] }}]" class="form-control" value="{{ $langParams['site_name'][$lang['langTag']] ?? '' }}"></td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <!--保存按钮-->
            <div class="text-center">
              <button type="submit" class="btn btn-success" data-bs-dismiss="modal" aria-label="Close">确认</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Language Modal -->
  <div class="modal fade" id="siteDescModal" tabindex="-1" aria-labelledby="siteDescModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">站点介绍设置</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="{{ route('panel.languages.batch.update', ['itemKey' => 'site_desc']) }}" method="post">
            @csrf
            @method('put')
            <input type="hidden" name="update_config" value="site_desc">
            <div class="table-responsive">
              <table class="table table-hover align-middle text-nowrap">
                <thead>
                  <tr class="table-info">
                    <th scope="col" class="w-25">语言标签</th>
                    <th scope="col" class="w-25">语言名称</th>
                    <th scope="col" class="w-50">内容</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($optionalLanguages as $lang)
                  <tr>
                    <td>
                      {{ $lang['langTag'] }}
                      @if($lang['langTag'] == $defaultLanguage)
                        <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="默认语言" aria-label="默认语言"></i>
                      @endif
                    </td>
                    <td>{{$lang['langName']}} @if($lang['areaCode'])({{ optional($areaCodes->where('code', $lang['areaCode'])->first())['localName']}}) @endif</td>
                    <td>
                      <textarea name="languages[{{ $lang['langTag'] }}]" class="form-control" rows="3">{{ $langParams['site_desc'][$lang['langTag']] ?? '' }}</textarea>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <!--保存按钮-->
            <div class="text-center">
              <button type="submit" class="btn btn-success" data-bs-dismiss="modal" aria-label="Close">确认</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade image-zoom" id="imageZoom" tabindex="-1" aria-labelledby="imageZoomLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="position-relative image-box">
        <img class="img-fluid" src="">
      </div>
    </div>
  </div>

@endsection
