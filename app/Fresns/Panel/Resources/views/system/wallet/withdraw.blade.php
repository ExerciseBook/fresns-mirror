@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::system.sidebar')
@endsection

@section('content')

  <div class="row mb-4">
    <div class="col-lg-7">
      <h3>钱包设置</h3>
      <p class="text-secondary">提现服务商配置将呈现在「钱包」功能列表中，用于钱包余额提现到指定账户中。</p>
    </div>
    <div class="col-lg-5">
      <div class="input-group mt-2 mb-4 justify-content-lg-end">
        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#createModal"><i class="bi bi-plus-circle-dotted"></i> 新增服务商</button>
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
            <td><input type="number" class="form-control input-number" value="{{ $item['rank_num']}}"></td>
            <td>{{ optional($item->plugin)->name }}</td>
            <td><img src="../assets/images/temp/placeholder_icon.png" width="24" height="24">{{ $item['name'] }}</td>
            <td>{{ $item['parameter'] }}</td>
            <td>
              @if($item['is_enable'])
                <i class="bi bi-check-lg text-success"></i>
              @else
                <i class="bi bi-dash-lg text-secondary"></i>
              @endif
            </td>
            <td>
              <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createModal">修改</button>
              <button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>


  <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModal" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">提现服务商设置</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">显示顺序</label>
              <div class="col-sm-9">
                <input type="number" class="form-control input-number">
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">关联插件</label>
              <div class="col-sm-9">
                <select class="form-select">
                  <option selected disabled>请选择插件</option>
                  <option value="1">One</option>
                  <option value="2">Two</option>
                  <option value="3">Three</option>
                </select>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">显示图标</label>
              <div class="col-sm-9">
                <input type="file" class="form-control" id="inputGroupFile01">
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">显示名称</label>
              <div class="col-sm-9">
                <button type="button" class="btn btn-outline-secondary btn-modal w-100 text-start" data-bs-toggle="modal" data-bs-target="#langModal">显示名称</button>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">自定义参数</label>
              <div class="col-sm-9">
                <input type="text" class="form-control">
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">启用状态</label>
              <div class="col-sm-9 pt-2">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="status" id="status_true" value="true" checked>
                  <label class="form-check-label" for="status_true">启用</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="status" id="status_false" value="false">
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
  <div class="modal fade" id="langModal" tabindex="-1" aria-labelledby="langModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">多语言设置</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form>
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
                  <tr>
                    <td>zh-Hans-CN</td>
                    <td>简体中文(内地)</td>
                    <td><input type="text" class="form-control" value="显示名称"></td>
                  </tr>
                  <tr>
                    <td>zh-Hans-SG</td>
                    <td>简体中文(新加坡)</td>
                    <td><input type="text" class="form-control" value="显示名称"></td>
                  </tr>
                  <tr>
                    <td>zh-Hans-HK</td>
                    <td>繁體中文(香港)</td>
                    <td><input type="text" class="form-control" value="顯示名稱"></td>
                  </tr>
                  <tr>
                    <td>en-US <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="默认语言"></i></td>
                    <td>English(United States)</td>
                    <td><input type="text" class="form-control" value="display name"></td>
                  </tr>
                  <tr>
                    <td>ja</td>
                    <td>日本語</td>
                    <td><input type="text" class="form-control" value="表示名"></td>
                  </tr>
                  <tr>
                    <td>ko-KR</td>
                    <td>한국어(대한민국)</td>
                    <td><input type="text" class="form-control" value="이름 표시하기"></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!--保存按钮-->
            <div class="text-center">
              <button type="button" class="btn btn-success" data-bs-dismiss="modal" aria-label="Close">确认</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
