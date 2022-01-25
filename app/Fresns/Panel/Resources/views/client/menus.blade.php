@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::client.sidebar')
@endsection

@section('content')

  <!--设置区域 开始-->
  <div class="row mb-4 border-bottom">
    <div class="col-lg-7">
      <h3>菜单配置</h3>
      <p class="text-secondary">统一配置客户端菜单信息</p>
    </div>
    <div class="col-lg-5">
      <div class="input-group mt-2 mb-4 justify-content-lg-end">
        <a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
      </div>
    </div>
  </div>
  <!--列表-->
  <div class="table-responsive">
    <table class="table table-hover align-middle text-nowrap">
      <thead>
        <tr class="table-info">
          <th scope="col">首页</th>
          <th scope="col">菜单</th>
          <th scope="col">路径</th>
          <th scope="col">导航名称</th>
          <th scope="col">SEO 标题</th>
          <th scope="col">SEO 描述</th>
          <th scope="col">SEO 关键词</th>
          <th scope="col">启用状态</th>
          <th scope="col">操作</th>
        </tr>
      </thead>
      <tbody>
        @foreach($menus as $key => $menu)
          <tr>
            <td>
              @if ($menu['select'])
                <input class="form-check-input update-config"
                       type="radio"
                       name="default_homepage"
                       data-action="{{ route('panel.configs.update', ['config' => 'default_homepage']) }}"
                       data-item_value="{{ $menu['url'] }}"
                       value="portal" {{ optional($configs['default_homepage'])->item_value == $menu['url'] ? 'checked' : '' }}>
                     @endif
            </td>
            <td>{{ $menu['name'] }}</td>
            <td>/{{ $menu['url'] ?? '' }}</td>
            <td>
              <button type="button"
                      class="btn btn-outline-primary btn-sm"
                      data-toggle="tooltip"
                      data-placement="top"
                      data-bs-toggle="modal"
                      data-bs-target="#menuLangModal"
                      data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_'.$key.'_name']) }}"
                      data-languages="{{ optional($configs['menu_'.$key.'_name'])->languages->toJson() }}">
                      <span data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="{{ optional($configs['menu_'.$key.'_name'])->item_value }}"
                            >编辑</span>
              </button>
            </td>
            <td>
              <button type="button"
                      class="btn btn-outline-primary btn-sm"
                      data-toggle="tooltip"
                      data-placement="top"
                      data-bs-toggle="modal"
                      data-bs-target="#menuLangModal"
                      data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_'.$key.'_title']) }}"
                      data-languages="{{ optional($configs['menu_'.$key.'_title'])->languages->toJson() }}">
                      <span data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="{{ optional($configs['menu_'.$key.'_title'])->item_value }}"
                            >编辑</span>
              </button>
            </td>
            <td>
              <button type="button"
                      class="btn btn-outline-primary btn-sm"
                      data-toggle="tooltip"
                      data-placement="top"
                      data-bs-toggle="modal"
                      data-bs-target="#menuLangTextareaModal"
                      data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_'.$key.'_description']) }}"
                      data-languages="{{ optional($configs['menu_'.$key.'_description'])->languages->toJson() }}">
                      <span data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="{{ optional($configs['menu_'.$key.'_description'])->item_value }}"
                            >编辑</span>
              </button>
            </td>
            <td>
              <button type="button"
                      class="btn btn-outline-primary btn-sm"
                      data-toggle="tooltip"
                      data-placement="top"
                      data-bs-toggle="modal"
                      data-bs-target="#menuLangTextareaModal"
                      data-action="{{ route('panel.languages.batch.update', ['itemKey' => 'menu_'.$key.'_keywords']) }}"
                      data-languages="{{ optional($configs['menu_'.$key.'_keywords'])->languages->toJson() }}">
                      <span data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="{{ optional($configs['menu_'.$key.'_keywords'])->item_value }}"
                            >编辑</span>
              </button>
            </td>
            <td>
              @if (optional($configs['menu_'.$key.'_status'])->item_value == 'true')
                <i class="bi bi-check-lg text-success"></i>
              @else
                <i class="bi bi-dash-lg text-secondary"></i>
              @endif
            </td>
            <td><button type="button"
                        class="btn btn-outline-primary btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#menuEdit"
                        data-config="{{ json_encode(optional($configs['menu_'.$key.'_config'] ?? [])->item_value) }}"
                        data-no_config="{{ $key == 'portal' ? 1 : 0}}"
                        data-is_enable="{{ optional($configs['menu_'.$key.'_status'])->item_value }}"
                        data-action="{{ route('panel.clientMenus.update', ['key' => $key]) }}"
                        data-bs-whatever="{{ $menu['name'] }}">编辑</button></td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <!--列表 结束-->


  <!-- Modal -->
  <div class="modal fade" id="menuEdit" tabindex="-1" aria-labelledby="menuEdit" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">菜单设置</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action=""  method="post">
            @csrf
            @method('put')
            <div class="mb-3 row default-config">
              <label class="col-sm-3 col-form-label">默认参数</label>
              <div class="col-sm-9">
                <textarea class="form-control" name="config" rows="3"></textarea>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">启用状态</label>
              <div class="col-sm-9 pt-2">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="is_enable" id="status_true" value="true" checked>
                  <label class="form-check-label" for="status_true">启用</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="is_enable" id="status_false" value="false">
                  <label class="form-check-label" for="status_false">不启用</label>
                </div>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label"></label>
              <div class="col-sm-9"><button type="submit" class="btn btn-primary">提交</button></div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Language Modal -->
  <div class="modal fade" id="menuLangModal" tabindex="-1" aria-labelledby="menuLangModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">多语言设置</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form method="post">
            @csrf
            @method('put')
            <input type="hidden" name="sync_config" value="1">
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
                          <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="默认语言"></i>
                        @endif
                      </td>
                      <td>{{$lang['langName']}} @if($lang['areaCode'])({{ optional($areaCodes->where('code', $lang['areaCode'])->first())['localName']}}) @endif</td>
                      <td><input class="form-control" name="languages[{{ $lang['langTag'] }}]" rows="3"></textarea></td>
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
  <div class="modal fade" id="menuLangTextareaModal" tabindex="-1" aria-labelledby="menuLangTextareaModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">多语言设置</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form method="post">
            @csrf
            @method('put')
            <input type="hidden" name="sync_config" value="1">
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
                          <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="默认语言"></i>
                        @endif
                      </td>
                      <td>{{$lang['langName']}} @if($lang['areaCode'])({{ optional($areaCodes->where('code', $lang['areaCode'])->first())['localName']}}) @endif</td>
                      <td><textarea class="form-control" name="languages[{{ $lang['langTag'] }}]" rows="3"></textarea></td>
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
  <!--设置区域 结束-->
@endsection
