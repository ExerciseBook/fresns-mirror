@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::operation.sidebar')
@endsection

@section('content')

  <div class="row mb-4 border-bottom">
    <div class="col-lg-7">
      <h3>表情配置</h3>
      <p class="text-secondary">自定义配置表情图，在不配置的情况下，用户也可以通过表情键盘输入 Emoji 表情。</p>
    </div>
    <div class="col-lg-5">
      <div class="input-group mt-2 mb-4 justify-content-lg-end">
        <button class="btn btn-primary" type="button"
                                        data-bs-toggle="modal"
                                        data-action="{{ route('panel.emojiGroups.store') }}"
                                        data-bs-target="#emojiGroupCreateModal"><i class="bi bi-plus-circle-dotted"></i> 新增表情组</button>
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
          <th scope="col">表情组名称</th>
          <th scope="col">表情数量</th>
          <th scope="col">启用状态</th>
          <th scope="col" style="width:13rem;">操作</th>
        </tr>
      </thead>
      <tbody>
        @foreach($groups as $group)
          <tr>
			<td><input type="number"  data-action="{{ route('panel.emojiGroups.rank',$group->id) }}" class="form-control input-number rank-num" value="{{  $group->rank_num}}"></td>
            <td><img src="{{ $group->image_file_url }}" width="24" height="24"> {{ $group->name }}</td>
            <td>{{ $group->emojis->count() }}</td>
            <td>
              @if($group->is_enable)
                <i class="bi bi-check-lg text-success"></i>
              @else
                <i class="bi bi-dash-lg text-secondary"></i>
              @endif
            </td>
            <td>
              <form action="{{ route('panel.emojiGroups.destroy', ['emojiGroup' => $group->id]) }}" method="post">
                @csrf
                @method('delete')
                <button type="button" class="btn btn-outline-primary btn-sm"
                                      data-bs-toggle="modal"
                                      data-action="{{ route('panel.emojiGroups.update', ['emojiGroup' => $group->id]) }}"
                                      data-names="{{ $group->names->toJson() }}"
                                      data-params="{{ json_encode($group->attributesToArray()) }}"
                                      data-bs-target="#emojiGroupCreateModal">修改</button>
                <button type="button" class="btn btn-outline-info btn-sm ms-1"
                                      data-bs-toggle="offcanvas"
                                      data-bs-target="#offcanvasEmoji"
                                      data-action="{{ route('panel.emojis.store')}}"
                                      data-emojis="{{ $group->emojis->toJson() }}"
                                      data-parent_id="{{ $group->id }}"
                                      aria-controls="offcanvasEmoji">配置表情图</button>
                <button type="submit" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>


  <form method="post">
    @csrf
    @method('post')
    <!-- Modal -->
    <div class="modal fade name-lang-parent" id="emojiGroupCreateModal" tabindex="-1" aria-labelledby="emojiGroupCreateModal" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">表情组管理</h5>
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
              <label class="col-sm-3 col-form-label">表情组图标</label>
              <div class="col-sm-9">

                <div class="input-group">
                  <button class="btn btn-outline-secondary dropdown-toggle showSelectTypeName" type="button" data-bs-toggle="dropdown" aria-expanded="false">上传图片</button>
                  <ul class="dropdown-menu selectImageTyle">
                    <li data-name="inputFile"><a class="dropdown-item" href="#">上传图片</a></li>
                    <li data-name="inputUrl"><a class="dropdown-item" href="#">图片地址</a></li>
                  </ul>
                  <input type="file" class="form-control inputFile" name="image_file_url_file">
                  <input type="text" class="form-control inputUrl"     name="image_file_url" value="" style="display:none;">
                </div>

              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">表情组标识</label>
              <div class="col-sm-9">
                <input type="text" class="form-control input-number" name="code" required>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">表情组名称</label>
              <div class="col-sm-9">
                <button type="button" class="btn btn-outline-secondary btn-modal w-100 text-start name-button"
                                      data-bs-toggle="modal"
                                      data-parent="#emojiGroupCreateModal"
                                      data-bs-target="#langModal"></button>
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


  <!-- Offcanvas -->
  <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasEmoji" aria-labelledby="offcanvasEmojiLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="offcanvasEmojiLabel">表情管理
        <button class="btn btn-info btn-sm ms-3"
                type="button"
                data-bs-toggle="modal"
                data-bs-target="#emojiModal"><i class="bi bi-plus-circle-dotted"></i> 新增表情图</button>
      </h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle text-nowrap">
            <thead>
              <tr class="table-info">
                <th scope="col" style="width:6rem;">顺序</th>
                <th scope="col">表情图</th>
                <th scope="col">表情符号</th>
                <th scope="col">是否启用</th>
                <th scope="col">操作</th>
              </tr>
            </thead>
            <tbody id="emojiList">
            </tbody>
          </table>
        </div>
        <!--保存按钮-->
        {{--<div class="text-center mb-4">--}}
          {{--<button type="button" class="btn btn-primary" data-bs-dismiss="modal" aria-label="Close">保存</button>--}}
        {{--</div>--}}
    </div>
  </div>

  <template id="emojiData">
    <tr>
	  <td><input type="number" data-action="" name="rank_num" class="form-control input-number rank-num" value="1"></td>
      <td><img class="emoji-img" src="" width="28" height="28"></td>
      <td>[<span class="emoji-code"></span>]</td>
      <td>
		  <form action="" method="post">
			@csrf
			@method('put')
		  <div class="form-check form-switch">
		  <input name="is_enable" class="form-check-input" type="checkbox"></div>
		  </form>
	  </td>
      <td>
		  <form action="" method="post">
			@csrf
			@method('delete')
		  <button type="submit" class="delete-emoji btn btn-link link-danger ms-1 fresns-link fs-7">删除</button>
		  </form>
	  </td>

    </tr>

  </template>


  <!-- Modal -->
  <div class="modal fade" id="emojiModal" tabindex="-1" aria-labelledby="emojiModal" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">新增表情图</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="{{ route('panel.emojis.store')}}" method="post">
            @csrf
            <input type="hidden" name="parent_id">
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">顺序</label>
              <div class="col-sm-9">
                <input type="number" class="form-control input-number" name="rank_num" required>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">表情图</label>
              <div class="col-sm-9">
				  <div class="input-group">
				   <button class="btn btn-outline-secondary dropdown-toggle showSelectTypeName" type="button" data-bs-toggle="dropdown" aria-expanded="false">上传图片</button>
				   <ul class="dropdown-menu selectImageTyle">
					   <li data-name="inputFile"><a class="dropdown-item" href="#">上传图片</a></li>
					   <li data-name="inputUrl"><a class="dropdown-item" href="#">图片地址</a></li>
				   </ul>
				   <input type="file" class="form-control inputFile" name="image_file_url_file">
				<input type="text" class="form-control inputUrl"     name="image_file_url" value="" style="display:none;">
			   </div>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">表情符号</label>
              <div class="col-sm-9">
                <div class="input-group mb-3">
                  <span class="input-group-text">[</span>
                  <input type="text" class="form-control" name="code">
                  <span class="input-group-text">]</span>
                </div>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">是否启用</label>
              <div class="col-sm-9 pt-2">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="is_enable" id="emoji_status_true" value="1" checked>
                  <label class="form-check-label" for="emoji_status_true">启用</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="is_enable" id="emoji_status_false" value="0">
                  <label class="form-check-label" for="emoji_status_false">不启用</label>
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
