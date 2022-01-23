@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::expand.sidebar')
@endsection

@section('content')
  <div class="row mb-4 border-bottom">
    <div class="col-lg-9">
      <h3>内容类型扩展</h3>
      <p class="text-secondary">将呈现在「帖子」列表页面中，用于输出指定「类型」的帖子，以及定义数据结果来源。</p>
    </div>
    <div class="col-lg-3">
      <div class="input-group mt-2 mb-4 justify-content-lg-end">
        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#createTypeModal"><i class="bi bi-plus-circle-dotted"></i> 新增扩展</button>
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
          <th scope="col">数据来源</th>
          <th scope="col">启用状态</th>
          <th scope="col" style="width:8rem;">操作</th>
        </tr>
      </thead>
      <tbody>
        @foreach($pluginUsages as $item)
          <tr>
            <td><input type="number" class="form-control input-number" value="{{ $item->rank_num }}"></td>
            <td>{{ optional($item->plugin)->name}}</td>
            <td>{{ $item->name }}</td>
            <td>
              @if (!empty($item->data_sources['postLists']['pluginUnikey']))
                <button type="button"
                        class="btn btn-outline-secondary btn-sm update-data-source"
                        data-bs-toggle="modal"
                        data-action="{{ route('panel.expandType.source', ['id' => $item->id, 'key' => 'postLists'])}}"
                        data-params="{{ json_encode($item->data_sources['postLists']['sortNumber'] ?? []) }} "
                        data-bs-target="#sortNumberModal">帖子总列表</button>
              @endif
              @if (!empty($item->data_sources['postFollows']['pluginUnikey']))
                <button type="button"
                        class="btn btn-outline-secondary btn-sm update-data-source"
                        data-bs-toggle="modal"
                        data-action="{{ route('panel.expandType.source', ['id' => $item->id, 'key' => 'postFollows'])}}"
                        data-params="{{ json_encode($item->data_sources['postFollows']['sortNumber'] ?? []) }} "
                        data-bs-target="#sortNumberModal">关注对象的帖子</button>
              @endif
              @if (!empty($item->data_sources['postNearbys']['pluginUnikey']))
                <button type="button"
                        class="btn btn-outline-secondary btn-sm update-data-source"
                        data-bs-toggle="modal"
                        data-action="{{ route('panel.expandType.source', ['id' => $item->id, 'key' => 'postNearbys'])}}"
                        data-params="{{ json_encode($item->data_sources['postNearbys']['sortNumber'] ?? []) }} "
                        data-bs-target="#sortNumberModal">附近范围的帖子</button>
                      @endif
            </td>
            <td>
              @if($item->is_enable)
                <i class="bi bi-check-lg text-success"></i>
              @else
                <i class="bi bi-dash-lg text-secondary"></i>
              @endif
            </td>
            <td>
              <form method="post" action="{{ route('panel.pluginUsages.destroy', $item->id) }}">
                @csrf
                <button type="button"
                        class="btn btn-outline-primary btn-sm"
                        data-names="{{ $item->names->toJson() }}"
                        data-params="{{ json_encode($item->attributesToArray()) }}"
                        data-action="{{ route('panel.expandType.update', $item->id) }}"
                        data-bs-toggle="modal"
                        data-bs-target="#createTypeModal">修改</button>
                <button type="submit" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  {{ $pluginUsages->links()}}
  <!--操作列表 结束-->

  <form action="" method="post">
    @csrf
    @method('post')
    <input type="hidden" name="update_name" value="0">
    <!-- Create Modal -->
    <div class="modal fade name-lang-parent" id="createTypeModal" tabindex="-1" aria-labelledby="createTypeModal" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">内容类型扩展</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">显示顺序</label>
              <div class="col-sm-9">
                <input type="number" name="rank_num" required class="form-control input-number">
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">关联插件</label>
              <div class="col-sm-9">
                <select class="form-select" name="plugin_unikey" required>
                  <option selected disabled>请选择插件</option>
                  @foreach($plugins as $plugin)
                    <option value="{{ $plugin->unikey }}">{{$plugin->name}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">显示名称</label>
              <div class="col-sm-9">
                <button type="button"
                        class="btn btn-outline-secondary btn-modal w-100 text-start"
                        data-parent="#createTypeModal"
                        data-bs-toggle="modal"
                        data-bs-target="#langModal">显示名称</button>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">数据来源</label>
              <div class="col-sm-9">
                <div class="form-floating mb-3">
                  <select class="form-select" id="floatingSelect" name="post_list" aria-label="Floating label select example">
                    <option disabled>选择提供数据的插件</option>
                    <option value="" selected>默认</option>
                    @foreach($plugins as $plugin)
                      <option value="{{ $plugin->unikey }}">{{$plugin->name}}</option>
                    @endforeach
                  </select>
                  <label for="floatingSelect">/api/fresns/post/lists</label>
                </div>
                <div class="form-floating mb-3">
                  <select class="form-select" id="floatingSelect" name="post_follow" aria-label="Floating label select example">
                    <option disabled>选择提供数据的插件</option>
                    <option value="" selected>默认</option>
                    @foreach($plugins as $plugin)
                      <option value="{{ $plugin->unikey }}">{{$plugin->name}}</option>
                    @endforeach
                  </select>
                  <label for="floatingSelect">/api/fresns/post/follows</label>
                </div>
                <div class="form-floating">
                  <select class="form-select" id="floatingSelect" name="post_nearby" aria-label="Floating label select example">
                    <option disabled>选择提供数据的插件</option>
                    <option value="" selected>默认</option>
                    @foreach($plugins as $plugin)
                      <option value="{{ $plugin->unikey }}">{{$plugin->name}}</option>
                    @endforeach
                  </select>
                  <label for="floatingSelect">/api/fresns/post/nearbys</label>
                </div>
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
                      <td>{{ $lang['langTag'] }}</td>
                      <td>{{$lang['langName']}} @if($lang['areaCode'])({{ optional($areaCodes->where('code', $lang['areaCode'])->first())['localName']}}) @endif</td>
                      <td><input type="text" name="names[{{ $lang['langTag'] }}]" class="form-control" value=""></td>
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


  <!-- sortNumber Modal -->
  <div class="modal fade name-lang-modal" id="sortNumberModal" tabindex="-1" aria-labelledby="sortNumberModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">排序编号配置</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="sortNumberForm" method="post">
            @csrf
            @method('put')
            <div class="table-responsive">
              <table class="table table-hover align-middle text-nowrap">
                <thead>
                  <tr class="table-info">
                    <th scope="col" style="width:10rem;">编号 <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="如何填写请参见「数据来源」关联插件的说明文档"></i></th>
                    <th scope="col">标题</th>
                    <th scope="col">描述</th>
                    <th scope="col" style="width:6rem;">操作</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="add-sort-tr">
                    <td colspan="4"><button class="btn btn-outline-success btn-sm px-3 add-sort" type="button"><i class="bi bi-plus-circle-dotted"></i> 新增编号</button></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!--保存按钮-->
            <div class="text-center">
              <button type="submit" class="btn btn-primary">提交保存</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <template id="sortTemplate">
    <tr class="sort-item">
      <td><input required type="number" name="ids[]" class="form-control input-number"></td>
      <td>
        <button type="button"
                  class="btn btn-outline-secondary btn-modal w-100 text-start sort-title"
                  data-bs-toggle="modal"
                  data-bs-target="#sortNumberTitleLangModal">标题</button>
        <input type="hidden" name="titles[]">
      </td>
      <td>
        <button type="button"
                  class="btn btn-outline-secondary btn-modal w-100 text-start sort-description"
                  data-bs-toggle="modal"
                  data-bs-target="#sortNumberDescLangModal">描述</button>

        <input type="hidden" name="descriptions[]">
      </td>
      <td><button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7 delete-sort-number">删除</button></td>
    </tr>
  </template>

  <!-- sortNumber Language Modal -->
  <div class="modal fade" id="sortNumberTitleLangModal" tabindex="-1" aria-labelledby="sortNumberTitleLangModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">排序编号多语言设置</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="sortTitleForm">
            <div class="table-responsive">
              <table class="table table-hover align-middle text-nowrap">
                <thead>
                  <tr class="table-info">
                    <th scope="col">语言标签</th>
                    <th scope="col">语言名称</th>
                    <th scope="col" class="w-50">标题</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($optionalLanguages as $lang)
                    <tr>
                      <td>{{ $lang['langTag'] }}
                        @if($lang['langTag'] == $defaultLanguage)
                          <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="默认语言" aria-label="默认语言"></i>
                        @endif
                      </td>
                      <td>{{$lang['langName']}} @if($lang['areaCode'])({{ optional($areaCodes->where('code', $lang['areaCode'])->first())['localName']}}) @endif</td>
                      <td><input type="text" name="{{ $lang['langTag'] }}" class="form-control" value=""></td>
                    </tr>
                  @endforeach
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

  <!-- sortNumber Language Modal -->
  <div class="modal fade" id="sortNumberDescLangModal" tabindex="-1" aria-labelledby="sortNumberDescLangModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">排序编号多语言设置</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form>
            <div class="table-responsive">
              <table class="table table-hover align-middle text-nowrap">
                <thead>
                  <tr class="table-info">
                    <th scope="col">语言标签</th>
                    <th scope="col">语言名称</th>
                    <th scope="col" class="w-50">描述</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($optionalLanguages as $lang)
                    <tr>
                      <td>{{ $lang['langTag'] }}
                        @if($lang['langTag'] == $defaultLanguage)
                          <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="默认语言" aria-label="默认语言"></i>
                        @endif
                      </td>
                      <td>{{$lang['langName']}} @if($lang['areaCode'])({{ optional($areaCodes->where('code', $lang['areaCode'])->first())['localName']}}) @endif</td>
                      <td><input type="text" name="{{ $lang['langTag'] }}" class="form-control" value=""></td>
                    </tr>
                  @endforeach
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
