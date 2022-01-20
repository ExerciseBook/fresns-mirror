@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::operation.sidebar')
@endsection

@section('content')
  <div class="row mb-4 border-bottom">
    <div class="col-lg-7">
      <h3>用户角色</h3>
      <p class="text-secondary">角色可以对用户身份和权限进行特定的设置。</p>
    </div>
    <div class="col-lg-5">
      <div class="input-group mt-2 mb-4 justify-content-lg-end">
        <button class="btn btn-primary" type="button"
          data-bs-toggle="modal"
          data-action="{{ route('panel.memberRoles.store') }}"
          data-bs-target="#createRoleModal"><i class="bi bi-plus-circle-dotted"></i> 新增角色</button>
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
          <th scope="col">类型</th>
          <th scope="col">角色图标</th>
          <th scope="col">角色名称</th>
          <th scope="col">显示标识</th>
          <th scope="col">昵称颜色</th>
          <th scope="col">启用状态</th>
          <th scope="col" style="width:13rem;">编辑操作</th>
        </tr>
      </thead>
      <tbody>
        @foreach($roles as $role)
          <tr>
            <td><input type="number" class="form-control input-number" value="{{ $role->rank_num }}"></td>
            <td>{{ $typeLabels[$role->type] }}</td>
            <td>
              @if($role->icon_file_url)
              <img src="{{ $role->icon_file_url }}" width="24" height="24">
              @endif
            </td>
            <td>{{ $role->anme }}</td>
            <td>
              @if ($role->is_display_icon)
                <i class="bi bi-image me-3" data-bs-toggle="tooltip" data-bs-placement="top" title="显示图标"></i>
              @endif
              @if($role->is_display_name)
                <i class="bi bi-textarea-t me-3" data-bs-toggle="tooltip" data-bs-placement="top" title="显示文字"></i>
              @endif
            </td>
            <td>
              @if($role->nickname_color)
                <input type="color" class="form-control form-control-color" value="{{ $role->nickname_color }}" disabled>
              @endif
            </td>
            <td>
              @if ($role->is_enable)
                <i class="bi bi-check-lg text-success"></i>
              @else
                <i class="bi bi-dash-lg text-secondary"></i>
              @endif
            </td>
            <td>
              <button type="button" class="btn btn-outline-primary btn-sm"
                                    data-bs-toggle="modal"
                                    data-names="{{ $role->names->toJson() }}"
                                    data-params="{{ $role->toJson() }}"
                                    data-action="{{ route('panel.memberRoles.update', ['memberRole' => $role->id]) }}"
                data-bs-target="#createRoleModal">修改</button>
              <a class="btn btn-outline-info btn-sm text-decoration-none ms-1" href="operating-role-permission.html" role="button">设置权限</a>
              <button type="butmit" class="btn btn-link link-danger ms-1 fresns-link fs-7"
                data-bs-toggle="modal"
                data-params="{{ $role->toJson() }}"
                data-bs-target="#deleteModal">删除</button>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <form action="" method="post">
    @method('post')
    <!-- Create Modal -->
    <div class="modal fade name-lang-parent" id="createRoleModal" tabindex="-1" aria-labelledby="createRoleModal" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">用户角色设置</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">角色类型</label>
              <div class="col-sm-9">
                <select class="form-select" required name="type">
                  <option selected disabled>请选择角色类型</option>
                  @foreach($typeLabels as $key => $label)
                  <option value="{{ $key }}">{{ $label }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">显示顺序</label>
              <div class="col-sm-9">
                <input type="number" required name="rank_num" class="form-control input-number">
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">角色图标</label>
              <div class="col-sm-9">
				  <div class="input-group">
  					<button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="showIcon">上传图片</button>
  					<ul class="dropdown-menu infoli">
  						<li data-name="inputFile"><a class="dropdown-item" href="#">上传图片</a></li>
  						<li data-name="inputUrl"><a class="dropdown-item" href="#">图片地址</a></li>
  					</ul>
  					<input type="file" class="form-control inputFile" name="file">
  					<input type="text" style="display:none;" class="form-control inputUrl" name="icon_file_url">
  				</div>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">角色名称</label>
              <div class="col-sm-9">
                <button type="button" class="btn btn-outline-secondary btn-modal w-100 text-start"
                                      data-bs-toggle="modal"
                                      data-parent="#createRoleModal"
                                      data-bs-target="#langModal">显示名称</button>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">显示标识</label>
              <div class="col-sm-9 pt-2">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" name="is_display_icon" type="checkbox" id="inlineCheckbox1" value="1">
                  <label class="form-check-label" for="inlineCheckbox1"><i class="bi bi-image"></i> 显示图标</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" name="is_display_name" type="checkbox" id="inlineCheckbox2" value="1">
                  <label class="form-check-label" for="inlineCheckbox2"><i class="bi bi-textarea-t"></i> 显示文字</label>
                </div>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">昵称颜色</label>
              <div class="col-sm-2">
                <input type="color" name="nickname_color" class="form-control form-control-color" value="#6600FF">
              </div>
              <div class="col-sm-7">
                <div class="form-check form-check-inline mt-2">
                  <input class="form-check-input" type="checkbox" id="emptyColor" name="no_color" value="">
                  <label class="form-check-label" for="emptyColor">不使用颜色</label>
                </div>
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
                    <td>{{ $lang['langTag'] }}</td>
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

  <!-- Delete Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModal" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">删除用户角色</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">源角色</label>
              <div class="col-sm-9">
                <input type="text" class="form-control-plaintext" value="普通会员" readonly>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">目标角色</label>
              <div class="col-sm-9">
                <select class="form-select">
                  <option selected disabled>请选择角色</option>
                  <option value="1">管理员</option>
                  <option value="2">版主</option>
                  <option value="3">禁言用户</option>
                  <option value="5">中级会员</option>
                  <option value="6">高级会员</option>
                </select>
                <div class="form-text">角色删除后，该角色下成员合并到所选目标角色名下</div>
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
