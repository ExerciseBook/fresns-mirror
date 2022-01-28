@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::system.sidebar')
@endsection

@section('content')

  <div class="row mb-4 border-bottom">
    <div class="col-lg-7">
      <h3>语言设置</h3>
      <p class="text-secondary">多语言需要先在这里配置语言选项，然后才能在对应配置项时录入多语言内容。</p>
    </div>
    <div class="col-lg-5">
      <div class="input-group mt-2 mb-4 justify-content-lg-end">
        <span class="input-group-text" data-bs-toggle="tooltip" data-bs-placement="top" title="多语言状态"><i class="bi bi-translate me-1"></i>{{ $status ? '启用' : '停用' }} </span>
        <form action="{{ route('panel.languageMenus.status.switch') }}" method="post">
          @csrf
          @method('put')
          <button class="btn btn-warning" type="submit" data-bs-toggle="tooltip" data-bs-placement="top" title="点击停用">{{ $status ? '停用' : '启用' }}</button>
        </form>

        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#createLanguage"><i class="bi bi-plus-circle-dotted"></i> 新增语言</button>
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
            <th scope="col">默认语言</th>
            <th scope="col">语言代码</th>
            <th scope="col">地区代码</th>
            <th scope="col">语言名称</th>
            <th scope="col">地区名称</th>
            <th scope="col">长度单位</th>
            <th scope="col">书写方向</th>
            <th scope="col">启用状态</th>
            <th scope="col">编辑操作</th>
          </tr>
        </thead>
        <tbody>
          @foreach($languages as $language)
            <tr>
              <td>
                <input type="number"
                       data-action="{{ route('panel.languageMenus.rank.update', ['langTag' => $language['langTag']])}}"
                       class="form-control input-number rank-num"
                       value="{{ $language['rankNum'] }}"></td>
              <td>
                  <input
                       data-action="{{ route('panel.languageMenus.default.update')}}"
                        class="form-check-input" i
                        type="radio"
                        name="default_language"
                        value="{{ $language['langTag'] }}" {{ $language['langTag'] == $defaultLanguage ? 'checked' : ''}}>
              </td>
              <td>{{ $language['langCode'] }}</td>
              <td>{{ $language['areaCode'] }}</td>
              <td>{{ $language['langName'] }}</td>
              <td>{{ $language['areaName'] }}</td>
              <td>{{ $language['lengthUnits'] }}</td>
              <td>{{ $language['writingDirection'] }}</td>
              <td><i class="bi {{ $language['isEnable'] ? 'bi-check-lg text-success' : 'bi-dash-lg text-secondary'}} "></i></td>
              <td>
                <form action="{{ route('panel.languageMenus.destroy', ['langTag' => $language['langTag']]) }}" method="post">
                  @csrf
                  @method('delete')
                  <button type="button" class="btn btn-outline-primary btn-sm"
                    data-language="{{ json_encode($language) }}"
                    data-action="{{ route('panel.languageMenus.update', ['langTag' => $language['langTag']]) }}"
                    data-bs-toggle="modal"
                    data-bs-target="#updateLanguageMenu">修改</button>
                  <button type="submit" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
  </div>
  <!--操作列表 结束-->

  <!-- Modal -->
  <div class="modal fade" id="createLanguage" tabindex="-1" aria-labelledby="createModal" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">语言设置</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="{{ route('panel.languageMenus.store') }}" method="post">
            @csrf
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">显示顺序</label>
              <div class="col-sm-9">
                <input type="number" name="rank_num" required class="form-control input-number">
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">语言代码</label>
              <div class="col-sm-9">
                <select class="form-select" name="lang_code" required>
                  <option selected>请选择语言代码</option>
                  @foreach($codes as $code)
                    <option value={{ $code['code'] }}>{{ $code['name'] }}- {{ $code['localName']}} > {{ $code['code']}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">语言地区</label>
              <div class="col-sm-9 pt-2">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="area_status" id="create_area_status_false" value="0" data-bs-toggle="collapse" data-bs-target="#area_setting.show" aria-expanded="false" aria-controls="area_setting" checked>
                  <label class="form-check-label" for="create_area_status_false">不启用</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="area_status" id="create_area_status_true" value="1" data-bs-toggle="collapse" data-bs-target="#area_setting:not(.show)" aria-expanded="false" aria-controls="area_setting">
                  <label class="form-check-label" for="create_area_status_true">启用</label>
                </div>
              </div>
            </div>
            <div class="collapse" id="area_setting">
              <div class="mb-3 row">
                <label class="col-sm-3 col-form-label">地区代码</label>
                <div class="col-sm-9">
                  <div class="input-group">
                    <select class="form-select select-continent" data-children="{{ json_encode($areaCodes) }}" name="continent_id">
                      <option selected disabled>Choose...</option>
                      @foreach($continents as $continent)
                      <option value="{{ $continent['id'] }}">{{ $continent['name']}}</option>
                      @endforeach
                    </select>
                    <select class="form-select" name="area_code">
                      <option selected disabled>Choose...</option>
                      {{--@foreach($areaCodes as $areaCode)--}}
                        {{--<option value={{ $areaCode['code'] }}>{{ $areaCode['name'] }}- {{ $areaCode['localName']}} > {{ $areaCode['code']}}</option>--}}
                      {{--@endforeach--}}
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">长度单位</label>
              <div class="col-sm-9">
                <select class="form-select" name="length_units">
                  <option value="km" selected>公里 km</option>
                  <option value="mi">英里 mi</option>
                </select>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">日期格式</label>
              <div class="col-sm-9">
                <select class="form-select" name="date_format">
                  <option value="yyyy-mm-dd" selected>yyyy-mm-dd</option>
                  <option value="yyyy/mm/dd">yyyy/mm/dd</option>
                  <option value="yyyy.mm.dd">yyyy.mm.dd</option>
                  <option value="mm-dd-yyyy">mm-dd-yyyy</option>
                  <option value="mm/dd/yyyy">mm/dd/yyyy</option>
                  <option value="mm.dd.yyyy">mm.dd.yyyy</option>
                  <option value="dd-mm-yyyy">dd-mm-yyyy</option>
                  <option value="dd/mm/yyyy">dd/mm/yyyy</option>
                  <option value="dd.mm.yyyy">dd.mm.yyyy</option>
                </select>
                <div class="form-text">yyyy 表示年，mm 表示月，dd 表示天。</div>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">人性化时间</label>
              <div class="col-sm-9">
                <div class="input-group mb-1">
                  <span class="input-group-text">{n} minute ago</span>
                  <input type="text" class="form-control" name="time_format_minute" required>
                </div>
                <div class="input-group mb-1">
                  <span class="input-group-text">{n} hour ago</span>
                  <input type="text" class="form-control" name="time_format_hour" required>
                </div>
                <div class="input-group mb-1">
                  <span class="input-group-text">{n} day ago</span>
                  <input type="text" class="form-control" name="time_format_day" required>
                </div>
                <div class="input-group mb-1">
                  <span class="input-group-text">{n} month ago</span>
                  <input type="text" class="form-control" name="time_format_month" required>
                </div>
                <div class="form-text">时间变量名 {n}</div>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">是否启用</label>
              <div class="col-sm-9 pt-2">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="is_enable" id="create_language_status_true" value="1" checked>
                  <label class="form-check-label" for="language_status_true">启用</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="is_enable" id="create_language_status_false" value="0">
                  <label class="form-check-label" for="language_status_false">停用</label>
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

  <!-- Modal -->
  <div class="modal fade" id="updateLanguageMenu" tabindex="-1" aria-labelledby="updateModal" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">语言设置</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="" method="post">
            @csrf
            @method('put')
            <input type="hidden" name="old_lang_tag" value="">
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">显示顺序</label>
              <div class="col-sm-9">
                <input type="number" name="rank_num" required class="form-control input-number">
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">语言代码</label>
              <div class="col-sm-9">
                <select class="form-select" name="lang_code" required>
                  <option selected>请选择语言代码</option>
                  @foreach($codes as $code)
                    <option value={{ $code['code'] }}>{{ $code['name'] }}- {{ $code['localName']}} > {{ $code['code']}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">语言地区</label>
              <div class="col-sm-9 pt-2">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="area_status" id="area_status_false" value="0" data-bs-toggle="collapse" data-bs-target="#area_setting.show" aria-expanded="false" aria-controls="area_setting" checked>
                  <label class="form-check-label" for="area_status_false">不启用</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="area_status" id="area_status_true" value="1" data-bs-toggle="collapse" data-bs-target="#area_setting:not(.show)" aria-expanded="false" aria-controls="area_setting">
                  <label class="form-check-label" for="area_status_true">启用</label>
                </div>
              </div>
            </div>
            <div class="collapse" id="area_setting">
              <div class="mb-3 row">
                <label class="col-sm-3 col-form-label">地区代码</label>
                <div class="col-sm-9">
                  <div class="input-group">
                    <select class="form-select select-continent" data-children="{{ json_encode($areaCodes) }}" name="continent_id">
                      <option selected disabled>Choose...</option>
                      @foreach($continents as $continent)
                      <option value="{{ $continent['id'] }}">{{ $continent['name']}}</option>
                      @endforeach
                    </select>
                    <select class="form-select" name="area_code">
                      <option selected disabled>Choose...</option>
                      {{--@foreach($areaCodes as $areaCode)--}}
                        {{--<option value={{ $areaCode['code'] }}>{{ $areaCode['name'] }}- {{ $areaCode['localName']}} > {{ $areaCode['code']}}</option>--}}
                      {{--@endforeach--}}
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">长度单位</label>
              <div class="col-sm-9">
                <select class="form-select" name="length_units">
                  <option value="km" selected>公里 km</option>
                  <option value="mi">英里 mi</option>
                </select>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">日期格式</label>
              <div class="col-sm-9">
                <select class="form-select" name="date_format">
                  <option value="yyyy-mm-dd" selected>yyyy-mm-dd</option>
                  <option value="yyyy/mm/dd">yyyy/mm/dd</option>
                  <option value="yyyy.mm.dd">yyyy.mm.dd</option>
                  <option value="mm-dd-yyyy">mm-dd-yyyy</option>
                  <option value="mm/dd/yyyy">mm/dd/yyyy</option>
                  <option value="mm.dd.yyyy">mm.dd.yyyy</option>
                  <option value="dd-mm-yyyy">dd-mm-yyyy</option>
                  <option value="dd/mm/yyyy">dd/mm/yyyy</option>
                  <option value="dd.mm.yyyy">dd.mm.yyyy</option>
                </select>
                <div class="form-text">yyyy 表示年，mm 表示月，dd 表示天。</div>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">人性化时间</label>
              <div class="col-sm-9">
                <div class="input-group mb-1">
                  <span class="input-group-text">{n} minute ago</span>
                  <input type="text" class="form-control" name="time_format_minute" required>
                </div>
                <div class="input-group mb-1">
                  <span class="input-group-text">{n} hour ago</span>
                  <input type="text" class="form-control" name="time_format_hour" required>
                </div>
                <div class="input-group mb-1">
                  <span class="input-group-text">{n} day ago</span>
                  <input type="text" class="form-control" name="time_format_day" required>
                </div>
                <div class="input-group mb-1">
                  <span class="input-group-text">{n} month ago</span>
                  <input type="text" class="form-control" name="time_format_month" required>
                </div>
                <div class="form-text">时间变量名 {n}</div>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">是否启用</label>
              <div class="col-sm-9 pt-2">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="is_enable" id="language_status_true" value="1" checked>
                  <label class="form-check-label" for="language_status_true">启用</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="is_enable" id="language_status_false" value="0">
                  <label class="form-check-label" for="language_status_false">停用</label>
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

@endsection
