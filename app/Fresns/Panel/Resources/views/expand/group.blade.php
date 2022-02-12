@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::expand.sidebar')
@endsection

@section('content')
  <div class="row mb-4 border-bottom">
    <div class="col-lg-9">
      <h3>小组扩展</h3>
      <p class="text-secondary">将呈现在「小组」主页中，例如「纯链接扩展插件」可以在「小组」主页显示一个超链接。</p>
    </div>
    <div class="col-lg-3">
      <div class="input-group mt-2 mb-4 justify-content-lg-end">
        <button class="btn btn-primary" type="button"
                                        data-bs-toggle="modal"
                                        data-action="{{ route('panel.expandGroup.store') }}"
                                        data-bs-target="#createModal"><i class="bi bi-plus-circle-dotted"></i> 新增扩展</button>
        <a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
      </div>
    </div>
  </div>
  <div class="row mb-3">
    <div class="col-lg-8">

      <form action="" method="get">
        @csrf
        <div class="input-group">
          <span class="input-group-text">小组筛选</span>
          <select class="form-select" id="search_group_id">
            <option>父级小组</option>
            @foreach($groups as $group)
              <option @if($groupSelect && $groupSelect->parent_id == $group->id) selected @endif value="{{ $group->id }}">{{ $group->name }}</option>
            @endforeach
          </select>

          <select  class="form-select groupallsearch" name="group_id" >
            <option value="">选择小组</option>
            @foreach($groups as $group)
              @foreach($group->groups as $res)
                <option @if($groupId == $res->id) selected  @else  style="display:none;" @endif class="childsearch{{$group->id}} alloption" value="{{ $res->id }}">{{ $res->name }}</option>
              @endforeach
            @endforeach
          </select>
          <button class="btn btn-outline-secondary" type="submit">确认</button>
        </div>
      </form>
    </div>
    <div class="col-lg-6">
    </div>
  </div>
  <!--操作列表-->
  <div class="table-responsive">
    <table class="table table-hover align-middle text-nowrap">
      <thead>
        <tr class="table-info">
          <th scope="col" style="width:6rem;">显示顺序</th>
          <th scope="col">所属小组</th>
          <th scope="col">关联插件</th>
          <th scope="col">显示名称</th>
          <th scope="col">有权使用的用户角色 <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="留空代表所有用户角色均有权使用"></i></th>
          <th scope="col">自定义参数</th>
          <th scope="col">启用状态</th>
          <th scope="col" style="width:8rem;">操作</th>
        </tr>
      </thead>
      <tbody>
        @foreach($pluginUsages as $item)
          <tr>
            <td><input type="number"  data-action="{{ route('panel.expandGroup.rank',$item->id) }}" class="form-control input-number rank-num" value="{{ $item['rank_num']}}"></td>
            <td>{{ $item->group?$item->group->name:'' }}</td>
            <td>{{ optional($item->plugin)->name }}</td>
            <td>
              @if($item->icon_file_url)
                <img src="{{ $item->icon_file_url }}" width="24" height="24">
              @endif
              {{ $item['name'] }}
            </td>
            <td>
              @foreach($memberRoles as $role)
                @if(in_array($role->id, explode(',',$item->member_roles)))
                  <span class="badge bg-light text-dark">{{ $role->name }}</span>
                @endif
              @endforeach
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
              <form action="{{ route('panel.expandGroup.destroy',$item->id) }}" method="post">
                @csrf
                @method('delete')
                <button type="button" class="btn btn-outline-primary btn-sm"
                                      data-bs-toggle="modal"
                                      data-names="{{ $item->names->toJson() }}"
                                      data-params="{{ json_encode($item->attributesToArray()) }}"
                                      data-action="{{ route('panel.expandGroup.update', $item->id) }}"
                                      data-bs-target="#createModal">修改</button>
                <button type="submit" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <!--操作列表 结束-->
  <nav aria-label="Page navigation example">
    <ul class="pagination">
      {!! $pluginUsages->render() !!}
    </ul>
  </nav>
  <!-- Create Modal -->
  <form action="" method="post">
    @csrf
    @method('post')
    <input type="hidden" name="update_name" value="0">
    <div class="modal fade name-lang-parent expend-group-modal" id="createModal" tabindex="-1" aria-labelledby="createModal" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">小组扩展</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">所属小组</label>
              <div class="col-sm-9" id="selectGroup">
                <select class="form-select mb-1" id="parent_group_id" name="parent_group_id">
                  <option>父级小组</option>
                  @foreach($groups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                  @endforeach
                </select>
                @foreach($groups as $group)
                  @if($group->groups->toArray())
                    <select data-parent-id={{$group->id}} class="form-select groupall" name="group_id[]" style="display:none" id='child{{$group->id}}'>
                      <option value="">选择小组</option>
                      @foreach($group->groups as $res)
                        <option value="{{ $res->id }}">{{ $res->name }}</option>
                      @endforeach
                    </select>
                  @endif
                @endforeach
              </div>
            </div>
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
                <button type="button" class="btn btn-outline-secondary btn-modal w-100 text-start" data-bs-toggle="modal"
                                                                                                   data-parent="#createModal"
                                                                                                   data-bs-target="#langModal">显示名称</button>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">角色使用权</label>
              <div class="col-sm-9">
                <select class="form-select select2" multiple name="member_roles[]" id='member_roles'>
                  <option selected value="0">全部</option>
                  @foreach($memberRoles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                  @endforeach
                </select>
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
  @endsection
