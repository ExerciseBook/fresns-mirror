@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::manage.sidebar')
@endsection

@section('content')
  <!--设置区域 开始-->
  <div class="row mb-4 border-bottom">
    <div class="col-lg-7">
      <h3>接口密钥</h3>
      <p class="text-secondary">密钥凭证很重要，请勿轻易透露给其他人。</p>
    </div>
    <div class="col-lg-5">
      <div class="input-group mt-2 mb-4 justify-content-lg-end">
        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#createKey"><i class="bi bi-plus-circle-dotted"></i> 新增密钥</button>
        <a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
      </div>
    </div>
  </div>
  <!--密钥列表-->
  <div class="table-responsive">
    <table class="table table-hover align-middle text-nowrap">
      <thead>
        <tr class="table-info">
          <th scope="col">平台</th>
          <th scope="col">名称</th>
          <th scope="col">App ID</th>
          <th scope="col">App Key</th>
          <th scope="col">密钥类型</th>
          <th scope="col">启用状态</th>
          <th scope="col">操作</th>
        </tr>
      </thead>
      <tbody>
        @foreach($sessionKeys as $key)
        <tr>
          <th scope="row" class="py-3">{{ $key->platformName($platforms) }}</th></th>
          <td>{{ $key->name }}</td>
          <td>{{ $key->app_id }}</td>
          <td>{{ $key->app_secret }}</td>
          <td>{{ $typeLabels[$key->type] ?? '' }}</td>
          <td><i class="bi {{ $key->is_enable ? 'bi-check-lg text-success' : 'bi-dash-lg text-secondary' }}"></i></td>
          <td>
            <button type="button" class="btn btn-outline-success btn-sm"
              data-bs-toggle="modal"
              data-bs-target="#updateKey"
              data-id="{{ $key->id }}"
              data-name="{{ $key->name }}"
              data-type="{{ $key->type }}"
              data-platform_id="{{ $key->platform_id }}"
              data-plugin_unikey ="{{ $key->plugin_unikey }}"
              data-is_enable ="{{ $key->is_enable }}"
              data-action="{{ route('panel.sessionKeys.update', ['sessionKey' => $key]) }}"
              >编辑</button>
            <button type="button" class="btn btn-outline-primary btn-sm mx-2"
              data-bs-toggle="modal"
              data-app_id="{{ $key->app_id }}"
              data-name="{{ $key->name }}"
              data-action="{{ route('panel.sessionKeys.reset', ['sessionKey' => $key]) }}"
              data-bs-target="#resetKey">重置 Key</button>
            <button type="button" class="btn btn-link btn-sm text-danger fresns-link"
              data-bs-toggle="modal"
              data-app_id="{{ $key->app_id }}"
              data-action="{{ route('panel.sessionKeys.destroy', ['sessionKey' => $key]) }}"
              data-bs-target="#deleteKey">删除</button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <!--设置区域 结束-->


  <!-- Modal -->
  <div class="modal fade" id="createKey" tabindex="-1" aria-labelledby="createKeyLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createKeyLabel">创建密钥</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!--创建密钥表单 开始-->
          <form action="{{ route('panel.sessionKeys.store')}}" method="post">
            @csrf
            <div class="input-group mb-3">
              <span class="input-group-text">平台</span>
              <select name="platform_id" class="form-select" required id="key_platform">
                <option selected disabled>选择密钥应用平台</option>
                @foreach($platforms as $platform)
                  <option value="{{ $platform['id'] }}">{{ $platform['name'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="input-group mb-3">
              <span class="input-group-text">{{ __('panel::panel.name') }}</span>
              <input type="text" name="name" required class="form-control" id="key_name">
            </div>
            <div class="input-group mb-3">
              <span class="input-group-text">{{ __('panel::panel.type' )}}</span>
              <div class="form-control bg-white">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="type" value="1" id="fresns_key"  data-bs-toggle="collapse" data-bs-target="#key_plugin_setting.show" aria-expanded="false" aria-controls="key_plugin_setting" checked>
                  <label class="form-check-label" for="fresns_key">{{ __('panel::panel.mainApi')}}</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="type" value="2" id="admin_key"  data-bs-toggle="collapse" data-bs-target="#key_plugin_setting.show" aria-expanded="false" aria-controls="key_plugin_setting">
                  <label class="form-check-label" for="admin_key">{{ __('panel::panel.manageApi') }}</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="type" value="3" id="plugin_key" data-bs-toggle="collapse" data-bs-target="#key_plugin_setting:not(.show)" aria-expanded="false" aria-controls="key_plugin_setting">
                  <label class="form-check-label" for="plugin_key">{{ __('panel::panel.pluginApi') }}</label>
                </div>
              </div>
            </div>
            <!--类型设置 开始-->
            <div class="input-group mb-3 collapse" id="key_plugin_setting">
              <span class="input-group-text">关联插件<i class="bi bi-info-circle ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="该密钥不允许请求主程序 API"></i></span>
              <select class="form-select" name="plugin_unikey" id="key_plugin">
                <option selected disabled>选择密钥用于哪个插件</option>
                @foreach($plugins as $plugin)
                  <option value="{{ $plugin->unikey }}">{{ $plugin->name }}</option>
                @endforeach
              </select>
            </div>
            <!--类型设置 结束-->
            <div class="input-group mb-3">
              <span class="input-group-text">状态</span>
              <div class="form-control bg-white">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="is_enable" id="inlineRadio1" value="1">
                  <label class="form-check-label" for="inlineRadio1">启用</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" checked type="radio" name="is_enable" id="inlineRadio2" value="0">
                  <label class="form-check-label" for="inlineRadio2">停用</label>
                </div>
              </div>
            </div>

            <div class="text-center">
              <button type="submit" class="btn btn-primary">提交创建</button>
            </div>
          </form>
          <!--创建密钥表单 结束-->
        </div>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="updateKey" tabindex="-1" aria-labelledby="updateKeyLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="updateKeyLabel">编辑密钥</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!--创建密钥表单 开始-->
          <form action="" method="post">
            @csrf
            @method('put')
            <div class="input-group mb-3">
              <span class="input-group-text">平台</span>
              <select name="platform_id" class="form-select" required id="key_platform">
                <option selected disabled>选择密钥应用平台</option>
                @foreach($platforms as $platform)
                  <option value="{{ $platform['id'] }}">{{ $platform['name'] }}</option>
                @endforeach
              </select>
            </div>
            <div class="input-group mb-3">
              <span class="input-group-text">{{ __('panel::panel.name') }}</span>
              <input type="text" name="name" required class="form-control" id="key_name">
            </div>
            <div class="input-group mb-3">
              <span class="input-group-text">{{ __('panel::panel.type' )}}</span>
              <div class="form-control bg-white">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="type" value="1" id="fresns_key"  data-bs-toggle="collapse" data-bs-target="#key_plugin_setting.show" aria-expanded="false" aria-controls="key_plugin_setting" checked>
                  <label class="form-check-label" for="fresns_key">{{ __('panel::panel.mainApi')}}</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="type" value="2" id="admin_key"  data-bs-toggle="collapse" data-bs-target="#key_plugin_setting.show" aria-expanded="false" aria-controls="key_plugin_setting">
                  <label class="form-check-label" for="admin_key">{{ __('panel::panel.manageApi') }}</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="type" value="3" id="plugin_key" data-bs-toggle="collapse" data-bs-target="#key_plugin_setting:not(.show)" aria-expanded="false" aria-controls="key_plugin_setting">
                  <label class="form-check-label" for="plugin_key">{{ __('panel::panel.pluginApi') }}</label>
                </div>
              </div>
            </div>
            <!--类型设置 开始-->
            <div class="input-group mb-3 collapse" id="key_plugin_setting">
              <span class="input-group-text">关联插件<i class="bi bi-info-circle ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="该密钥不允许请求主程序 API"></i></span>
              <select class="form-select" name="plugin_unikey" id="key_plugin">
                <option selected disabled>选择密钥用于哪个插件</option>
                @foreach($plugins as $plugin)
                  <option value="{{ $plugin->unikey }}">{{ $plugin->name }}</option>
                @endforeach
              </select>
            </div>
            <!--类型设置 结束-->
            <div class="input-group mb-3">
              <span class="input-group-text">状态</span>
              <div class="form-control bg-white">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="is_enable" id="inlineRadio1" value="1">
                  <label class="form-check-label" for="inlineRadio1">启用</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" checked type="radio" name="is_enable" id="inlineRadio2" value="0">
                  <label class="form-check-label" for="inlineRadio2">停用</label>
                </div>
              </div>
            </div>

            <div class="text-center">
              <button type="submit" class="btn btn-primary">提交编辑</button>
            </div>
          </form>
          <!--创建密钥表单 结束-->
        </div>
      </div>
    </div>
  </div>

  <!-- Reset Modal -->
  <div class="modal fade" id="resetKey" tabindex="-1" aria-labelledby="resetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
      <form action="" method="post">
        @csrf
        @method('put')
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">该名称名称</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>App ID: <span class="app-id"></span></p>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary" data-bs-toggle="modal" data-bs-dismiss="modal">重置 Key</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Delete Modal -->
  <div class="modal fade" id="deleteKey" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
      <div class="modal-content">
        <form action="" method="post">
          @csrf
          @method('delete')
          <div class="modal-header">
            <h5 class="modal-title">该名称名称</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <p>App ID: <span class="app-id"></span></p>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-danger" data-bs-toggle="modal" data-bs-dismiss="modal">确认删除</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
