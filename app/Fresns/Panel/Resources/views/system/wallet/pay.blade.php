@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::system.sidebar')
@endsection

@section('content')

  <div class="row mb-4">
    <div class="col-lg-7">
      <h3>钱包设置</h3>
      <p class="text-secondary">充值服务商配置将呈现在「钱包」功能列表中，用于支付订单或者充值钱包。</p>
    </div>
    <div class="col-lg-5">
      <div class="input-group mt-2 mb-4 justify-content-lg-end">
        <button class="btn btn-primary"
                type="button"
                data-bs-toggle="modal"
                data-action="{{ route('panel.walletPayConfigs.store') }}"
                data-bs-target="#createPayModal">
          <i class="bi bi-plus-circle-dotted"></i> 新增服务商
        </button>
        <a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
      </div>
    </div>
    <ul class="nav nav-tabs">
      <li class="nav-item"><a class="nav-link {{ \Route::is('panel.walletConfigs.*') ? 'active' : ''}}" href="{{ route('panel.walletConfigs.show') }}">功能设置</a></li>
      <li class="nav-item"><a class="nav-link {{ \Route::is('panel.walletPayConfigs.*') ? 'active' : ''}}" href="{{ route('panel.walletPayConfigs.index')}}">充值服务商</a></li>
      <li class="nav-item"><a class="nav-link {{ \Route::is('panel.walletWithdrawConfigs.*') ? 'active' : ''}}" href="{{ route('panel.walletWithdrawConfigs.index')}}">提现服务商</a></li>
    </ul>
  </div>
  <!--操作列表-->
  <div class="table-responsive">
    <table class="table table-hover align-middle text-nowrap">
      <thead>
        <tr class="table-info">
          <th scope="col" style="width:6rem;">显示顺序</th>
          <th scope="col">关联插件</th>
          <th scope="col">显示名称</th>
          <th scope="col">自定义参数</th>
          <th scope="col">启用状态</th>
          <th scope="col" style="width:8rem;">操作</th>
        </tr>
      </thead>
      <tbody>
        @foreach($pluginUsages as $item)
          <tr>
            <td><input type="number" class="form-control input-number rank-num" data-action="{{ route('panel.pluginUsages.rank.update', $item->id)}}" value="{{ $item['rank_num']}}"></td>
            <td>{{ optional($item->plugin)->name }}</td>
            <td>
              @if($item->icon_file_url)
              <img src="{{ $item->icon_file_url }}" width="24" height="24">
              @endif
              {{ $item['name'] }}
            </td>
            <td>{{ $item->parameter }}</td>
            <td>
              @if($item['is_enable'])
                <i class="bi bi-check-lg text-success"></i>
              @else
                <i class="bi bi-dash-lg text-secondary"></i>
              @endif
            </td>
            <td>
              <form method="post" action="{{ route('panel.pluginUsages.destroy', $item->id) }}">
                @csrf
                @method('delete')
                <button type="button" class="btn btn-outline-primary btn-sm"
                                      data-bs-toggle="modal"
                                      data-names="{{ $item->names->toJson() }}"
                                      data-params="{{ json_encode($item->attributesToArray()) }}"
                                      data-action="{{ route('panel.walletPayConfigs.update', ['pluginUsage' => $item->id]) }}"
                                      data-bs-target="#createPayModal">修改</button>
                <button type="submit" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <form action="" method="post">
    @csrf
    @method('post')
    <input type="hidden" name="update_name" value="0">
    <!-- Update Modal -->
    <div class="modal fade name-lang-parent wallet-modal" id="createPayModal" tabindex="-1" aria-labelledby="createPayModal" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">充值服务商设置</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">显示顺序</label>
              <div class="col-sm-9">
                <input type="number" class="form-control input-number" required name="rank_num">
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">关联插件</label>
              <div class="col-sm-9">
                <select class="form-select" name="plugin_unikey" required>
                  <option selected disabled>请选择插件</option>
                  @foreach($plugins as $plugin)
                    <option value="{{ $plugin->unikey }}">{{ $plugin->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">显示图标</label>
              <div class="col-sm-9">
                <div class="input-group">
                  <button class="btn btn-outline-secondary dropdown-toggle showSelectTypeName" type="button" data-bs-toggle="dropdown" aria-expanded="false">上传图片</button>
                  <ul class="dropdown-menu selectImageTyle">
                    <li data-name="inputFile"><a class="dropdown-item" href="#">上传图片</a></li>
                    <li data-name="inputUrl"><a class="dropdown-item" href="#">图片地址</a></li>
                  </ul>
                  <input type="file" class="form-control inputFile" name="icon_file_id">
                  <input type="text" class="form-control inputUrl"  name="icon_file_url" value="" style="display:none;">
                </div>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">显示名称</label>
              <div class="col-sm-9">
                <button type="button"
                        class="name-button btn btn-outline-secondary btn-modal w-100 text-start"
                        data-parent="#createPayModal"

                        data-bs-toggle="modal"
                        data-bs-target="#langModal">显示名称</button>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">自定义参数</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" name="parameter">
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">启用状态</label>
              <div class="col-sm-9 pt-2">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="is_enable" id="status_true" value="1" checked>
                  <label class="form-check-label" for="status_true">启用</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="is_enable" id="status_false" value="0">
                  <label class="form-check-label" for="status_false">不启用</label>
                </div>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label"></label>
              <div class="col-sm-9"><button type="submit" class="btn btn-primary">提交</button></div>
            </div>
          </div>
        </div>
      </div>
    </div>


    <!-- Language Modal -->
    <div class="modal fade name-lang-modal" id="langModal" tabindex="-1" aria-labelledby="langModal" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">多语言设置</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
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
                      <td><input type="text" name="names[{{ $lang['langTag'] }}]" class="form-control" value="{{ $langParams['site_name'][$lang['langTag']] ?? '' }}"></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <!--保存按钮-->
            <div class="text-center">
              <button type="button" class="btn btn-success" data-bs-dismiss="modal" aria-label="Close">确认</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
@endsection
