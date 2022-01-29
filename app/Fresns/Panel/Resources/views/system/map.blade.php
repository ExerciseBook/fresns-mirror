@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::system.sidebar')
@endsection

@section('content')

  <div class="row mb-4 border-bottom">
    <div class="col-lg-7">
      <h3>地图设置</h3>
      <p class="text-secondary">一个服务商只能关联一个插件，同一个插件可以关联多个服务商。</p>
    </div>
    <div class="col-lg-5">
      <div class="input-group mt-2 mb-4 justify-content-lg-end">
        <button class="btn btn-primary" type="button"
                                        data-bs-toggle="modal"
                                        data-action="{{ route('panel.mapConfigs.store') }}"
                                        data-bs-target="#createMap"><i class="bi bi-plus-circle-dotted"></i> 新增服务商</button>
        <a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
      </div>
    </div>
  </div>
  <!--操作列表-->
  <div class="table-responsive">
    <table class="table table-hover align-middle text-nowrap">
      <thead>
        <tr class="table-info">
          <th scope="col" style="width:6rem;">显示顺序</th>
          <th scope="col">关联插件</th>
          <th scope="col">显示名称</th>
          <th scope="col">服务商</th>
          <th scope="col">App ID</th>
          <th scope="col">App Key</th>
          <th scope="col">启用状态</th>
          <th scope="col" style="width:8rem;">操作</th>
        </tr>
      </thead>
      <tbody>
        @foreach($pluginUsages as $item)
        <tr>
          <td><input type="number" class="form-control input-number rank-num" data-action="{{ route('panel.pluginUsages.rank.update', $item->id)}}" value="{{ $item['rank_num']}}"></td>
          <td>{{ optional($item->plugin)->name }}</td>
          <td><img src=" {{ asset('static/images/placeholder_icon.png') }} " width="24" height="24">{{ $item->name }}</td>
          <td>{{ $mapServices[$item->parameter]['name'] ?? '' }}</td>
          <td>{{ $mapConfigs['map_' .$item->parameter]['appId'] ?? ''}}</td>
          <td>{{ $mapConfigs['map_' .$item->parameter]['appKey'] ?? ''}}</td>
          <td>
            @if($item['is_enable'])
              <i class="bi bi-check-lg text-success"></i>
            @else
              <i class="bi bi-dash-lg text-secondary"></i>
            @endif
          </td>
          <td>
            <form action="{{ route('panel.pluginUsages.destroy', ['pluginUsage' => $item->id])}}" method="post">
              @csrf
              @method('delete')
              <button type="button" class="btn btn-outline-primary btn-sm"
                data-bs-toggle="modal"
                data-bs-target="#createMap"
                data-action="{{ route('panel.mapConfigs.update', ['mapConfig' => $item->id])}}"
                data-params="{{ $item->toJson() }}"
                data-names="{{ $item->names->toJson() }}"
                data-config_params="{{ json_encode($mapConfigs['map_' .$item->parameter] ?? []) }}"
              >修改</button>
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
    <!-- Modal -->
    <div class="modal fade name-lang-parent" id="createMap" tabindex="-1" aria-labelledby="createModal" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">新增服务商</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">显示顺序</label>
              <div class="col-sm-9">
                <input type="number" class="form-control input-number" name="rank_num" required>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">关联插件</label>
              <div class="col-sm-9">
                <select class="form-select" name="plugin_unikey" required>
                  <option selected disabled value="">请选择插件</option>
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
  				  <input type="file" class="form-control inputFile" name="icon_file_url_file">
  			   <input type="text" class="form-control inputUrl"     name="icon_file_url" value="" style="display:none;">
  			  </div>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">显示名称</label>
              <div class="col-sm-9">
                <button type="button"
                        class="btn btn-outline-secondary btn-modal w-100 text-start"
                        data-bs-toggle="modal"
                        data-bs-target="#mapLangModal"
                        data-parent="#createMap">显示名称</button>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">地图服务商</label>
              <div class="col-sm-9">
                <select class="form-select" name="parameter" required>
                  <option selected disabled value="">请选择</option>
                  @foreach($mapServices as $service)
                    <option value="{{ $service['id'] }}">{{ $service['name'] }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">API ID</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" name="app_id">
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">API Key</label>
              <div class="col-sm-9">
                <input type="text" class="form-control" name="app_key">
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
          </div>
        </div>
      </div>
    </div>

    <!-- Language Modal -->
    <div class="modal fade name-lang-modal" id="mapLangModal" tabindex="-1" aria-labelledby="mapLangModal" aria-hidden="true">
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
                    <?php
                      $langName = $lang['langName'];
                      if ($lang['areaCode']) {
                          $langName .= '('. optional($areaCodes->where('code', $lang['areaCode'])->first())['localName'] .')';
                      }
                    ?>
                  <tr>
                    <td>
                      {{ $lang['langTag'] }}
                      @if($lang['langTag'] == $defaultLanguage)
                        <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="默认语言" aria-label="默认语言"></i>
                      @endif
                    </td>
                    <td>{{ $langName }}</td>
                    <td><input type="text" class="form-control" name="names[{{ $lang['langTag'] }}]" value=""></td>
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
