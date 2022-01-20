@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::operation.sidebar')
@endsection

@section('content')

  <div class="row mb-4">
    <div class="col-lg-7">
      <h3>内容小组</h3>
      <p class="text-secondary">使用小组可以实现 BBS 版区、社群圈子、内容分类等各种运营场景。</p>
    </div>
    <div class="col-lg-5">
      <div class="input-group mt-2 mb-4 justify-content-lg-end">
        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#createModal"><i class="bi bi-plus-circle-dotted"></i> 新建小组分类</button>
        <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#groupModal"><i class="bi bi-plus-circle-dotted"></i> 新建小组</button>
        <a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
      </div>
    </div>
    <ul class="nav nav-tabs">
      <li class="nav-item"><a class="nav-link active" href="{{ route('panel.groups.index') }}">全部小组</a></li>
      <li class="nav-item"><a class="nav-link" href="{{ route('panel.recommendGroups.index') }}">推荐小组</a></li>
      <li class="nav-item"><a class="nav-link" href="{{ route('panel.disableGroups.index') }}">停用小组</a></li>
    </ul>
  </div>
  <!--操作列表-->
  <div class="row">
    <div class="col-lg-3">
      <div class="list-group">
        @foreach($categories as $category)
          <a href="{{ route('panel.groups.index', ['parent_id' => $category->id])}}" class="list-group-item list-group-item-action {{ $category->id == $parentId ? 'active' : '' }} d-flex justify-content-between align-items-center">
          <input type="number" class="form-control input-number" value="1" style="width:50px;">
          <span class="ms-2 text-nowrap overflow-hidden">{{ $category->name }}</span>
          <button type="button" class="btn btn-outline-info btn-sm text-nowrap fs-9 ms-auto" data-bs-toggle="modal" data-bs-target="#createModal">编辑</button>
          <button type="button" class="btn btn-outline-secondary btn-sm text-nowrap fs-9 ms-1" data-bs-toggle="tooltip" data-bs-placement="top" title="删除"><i class="bi bi-trash"></i></button>
        </a>
        @endforeach
      </div>
    </div>
    <div class="col-lg-9">
      <div class="table-responsive">
        <table class="table table-hover align-middle text-nowrap">
          <thead>
            <tr class="table-info">
              <th scope="col" style="width:6rem;">显示顺序</th>
              <th scope="col">小组名称</th>
              <th scope="col">小组模式</th>
              <th scope="col">关注方式</th>
              <th scope="col">是否推荐</th>
              <th scope="col">小组管理员</th>
              <th scope="col">发布权限</th>
              <th scope="col">评论权限</th>
              <th scope="col">启用状态</th>
              <th scope="col" style="width:10rem;">操作</th>
            </tr>
          </thead>
          <tbody>
            @foreach($groups as $group)
              <tr>
                <td><input type="number" class="form-control input-number" value="1"></td>
                <td>
                  @if ($group->cover_file_url)
                    <img src="{{ $group->cover_file_url }}" width="24" height="24">
                  @endif
                  {{ $group->name }}
                </td>
                <td>{{ $typeModeLabels[$group->type_mode] ?? '' }}</td>
                <td>
                  @if($group->type_follow == 1)
                    原生
                  @else
                    插件 <span class="badge bg-light text-dark">{{ optional($group->plugin)->name }}</span>
                @endif
                </td>
                <td>
                  @if($group->is_recommend)
                  <i class="bi bi-check-lg text-success"></i>
                  @else
                  <i class="bi bi-dash-lg text-secondary"></i>
                  @endif
                </td>
                <td><span class="badge bg-light text-dark">{{ optional($group->member)->name }}</span></td>
                <td><span class="badge bg-light text-dark">{{ $permissioLabels[$group->permission['publish_post'] ?? 0] ?? ''}}</span></td>
                <td><span class="badge bg-light text-dark">{{ $permissioLabels[$group->permission['publish_comment'] ?? 0] ?? ''}}</span></td>
                <td><i class="bi bi-check-lg text-success"></i></td>
                <td>
                  <form action="{{ route('panel.groups.enable.update', ['group' => $group->id, 'is_enable' => 0])}}" method="post">
                    @csrf
                    @method('put')
                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#groupModal">编辑</button>
                    <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#moveModal">合并</button>
                    <button type="submit" class="btn btn-link link-danger ms-1 fresns-link fs-7">停用</button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      {{ $groups->links() }}
    </div>
  </div>

  <!-- Create Modal -->
  <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModal" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">小组分类</h5>
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
              <label class="col-sm-3 col-form-label">分类名称</label>
              <div class="col-sm-9">
                <button type="button" class="btn btn-outline-secondary btn-modal w-100 text-start" data-bs-toggle="modal" data-bs-target="#langModal">小组多语言名称</button>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">分类描述</label>
              <div class="col-sm-9">
                <button type="button" class="btn btn-outline-secondary btn-modal w-100 text-start" data-bs-toggle="modal" data-bs-target="#langDescModal">小组多语言描述</button>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">分类图标</label>
              <div class="col-sm-9">
                <div class="input-group">
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
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">分类条幅</label>
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
              <label class="col-sm-3 col-form-label">启用状态</label>
              <div class="col-sm-9 pt-2">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="status" id="cat_status_true" value="true" checked>
                  <label class="form-check-label" for="cat_status_true">启用</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="status" id="cat_status_false" value="false">
                  <label class="form-check-label" for="cat_status_false">不启用</label>
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

  <!-- Group Modal -->
  <div class="modal fade" id="groupModal" tabindex="-1" aria-labelledby="groupModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">小组</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form>
            <div class="mb-3 row">
              <label class="col-sm-3 col-md-2 col-form-label">所属分类</label>
              <div class="col-sm-9 col-md-10">
                <select class="form-select">
                  <option selected>小组分类</option>
                  <option value="1">One</option>
                  <option value="2">Two</option>
                  <option value="3">Three</option>
                </select>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-md-2 col-form-label">显示顺序</label>
              <div class="col-sm-9 col-md-10">
                <input type="number" class="form-control input-number">
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-md-2 col-form-label">小组名称</label>
              <div class="col-sm-9 col-md-10">
                <button type="button" class="btn btn-outline-secondary btn-modal w-100 text-start" data-bs-toggle="modal" data-bs-target="#langModal">小组多语言名称</button>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-md-2 col-form-label">小组描述</label>
              <div class="col-sm-9 col-md-10">
                <button type="button" class="btn btn-outline-secondary btn-modal w-100 text-start" data-bs-toggle="modal" data-bs-target="#langDescModal">小组多语言描述</button>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-md-2 col-form-label">小组图标</label>
              <div class="col-sm-9 col-md-10">
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
              <label class="col-sm-3 col-md-2 col-form-label">小组条幅</label>
              <div class="col-sm-9 col-md-10">
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
              <label class="col-sm-3 col-md-2 col-form-label">小组模式</label>
              <div class="col-sm-9 col-md-10 pt-2">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="type_mode" id="type_mode_true" value="true" data-bs-toggle="collapse" data-bs-target="#mode_setting.show" aria-expanded="false" aria-controls="mode_setting" checked>
                  <label class="form-check-label" for="type_mode_true">公开（任何人都能查看小组内帖子）</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="type_mode" id="type_mode_false" value="false" data-bs-toggle="collapse" data-bs-target="#mode_setting:not(.show)" aria-expanded="false" aria-controls="mode_setting">
                  <label class="form-check-label" for="type_mode_false">非公开（只有成员才能查看小组内帖子）</label>
                </div>
                <div class="collapse mt-2" id="mode_setting">
                  <div class="input-group">
                    <span class="input-group-text">是否可发现</span>
                    <div class="form-control">
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="type_find" id="type_find_true" value="true" checked>
                        <label class="form-check-label" for="type_find_true">可发现（任何人都能找到这个小组）</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="type_find" id="type_find_false" value="false">
                        <label class="form-check-label" for="type_find_false">不可发现（只有成员能找到这个小组）</label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-md-2 col-form-label">关注方式</label>
              <div class="col-sm-9 col-md-10 pt-2">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="type_follow" id="type_follow_true" value="true" data-bs-toggle="collapse" data-bs-target="#follow_setting.show" aria-expanded="false" aria-controls="follow_setting" checked>
                  <label class="form-check-label" for="type_follow_true">原生方式</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="type_follow" id="type_follow_false" value="false" data-bs-toggle="collapse" data-bs-target="#follow_setting:not(.show)" aria-expanded="false" aria-controls="follow_setting">
                  <label class="form-check-label" for="type_follow_false">插件方式</label>
                </div>
                <div class="collapse mt-2" id="follow_setting">
                  <div class="input-group">
                    <span class="input-group-text">关联插件</span>
                    <select class="form-select">
                      <option selected>Choose...</option>
                      <option value="1">One</option>
                      <option value="2">Two</option>
                      <option value="3">Three</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-md-2 col-form-label">是否推荐</label>
              <div class="col-sm-9 col-md-10 pt-2">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="is_recommend" id="recommend_false" value="false" checked>
                  <label class="form-check-label" for="recommend_false">不推荐</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="is_recommend" id="recommend_true" value="true">
                  <label class="form-check-label" for="recommend_true">推荐</label>
                </div>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-md-2 col-form-label">小组管理员</label>
              <div class="col-sm-9 col-md-10">
                <select class="form-select">
                  <option selected disabled>这是 liveSearch 多选框，暂未加载样式组件，所以原型显示为单选下拉框</option>
                  <option value="4">成员昵称 (@成员名)</option>
                  <option value="1">Jarvis Tang (@jarvis)</option>
                  <option value="2">唐杰 (@tangjie)</option>
                </select>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-md-2 col-form-label">发布权限</label>
              <div class="col-sm-9 col-md-10 pt-2">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="publish.post" id="publish.post.1" value="1" data-bs-toggle="collapse" data-bs-target="#publish_post_setting.show" aria-expanded="false" aria-controls="publish_post_setting" checked>
                  <label class="form-check-label" for="publish.post.1">所有人</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="publish.post" id="publish.post.2" value="2" data-bs-toggle="collapse" data-bs-target="#publish_post_setting.show" aria-expanded="false" aria-controls="publish_post_setting">
                  <label class="form-check-label" for="publish.post.2">仅关注了小组的成员</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="publish.post" id="publish.post.3" value="3" data-bs-toggle="collapse" data-bs-target="#publish_post_setting:not(.show)" aria-expanded="false" aria-controls="publish_post_setting">
                  <label class="form-check-label" for="publish.post.3">仅指定的角色成员</label>
                </div>
                <div class="collapse mt-2" id="publish_post_setting">
                  <div class="input-group">
                    <span class="input-group-text">有权发表的角色</span>
                    <select class="form-select">
                      <option selected disabled>这是多选框，暂未加载样式组件，所以原型显示为单选下拉框</option>
                      <option value="1">管理员</option>
                      <option value="2">版主</option>
                      <option value="3">普通会员</option>
                      <option value="4">中级会员</option>
                      <option value="5">高级会员</option>
                    </select>
                  </div>
                </div>
                <div class="input-group mt-2">
                  <span class="input-group-text">是否需要审核<i class="bi bi-info-circle ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="小组管理员不受影响"></i></span>
                  <div class="form-control bg-white">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="publish.post.review" id="publish.post.review.0" value="0" checked>
                      <label class="form-check-label" for="publish.post.review.0">不需要</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="publish.post.review" id="publish.post.review.1" value="1">
                      <label class="form-check-label" for="publish.post.review.1">需要</label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-md-2 col-form-label">评论权限</label>
              <div class="col-sm-9 col-md-10 pt-2">
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="publish.comment" id="publish.comment.1" value="1" data-bs-toggle="collapse" data-bs-target="#publish_comment_setting.show" aria-expanded="false" aria-controls="publish_comment_setting" checked>
                  <label class="form-check-label" for="publish.comment.1">所有人</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="publish.comment" id="publish.comment.2" value="2" data-bs-toggle="collapse" data-bs-target="#publish_comment_setting.show" aria-expanded="false" aria-controls="publish_comment_setting">
                  <label class="form-check-label" for="publish.comment.2">仅关注了小组的成员</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="publish.comment" id="publish.comment.3" value="3" data-bs-toggle="collapse" data-bs-target="#publish_comment_setting:not(.show)" aria-expanded="false" aria-controls="publish_comment_setting">
                  <label class="form-check-label" for="publish.comment.3">仅指定的角色成员</label>
                </div>
                <div class="collapse mt-2" id="publish_comment_setting">
                  <div class="input-group">
                    <span class="input-group-text">有权发表的角色</span>
                    <select class="form-select">
                      <option selected disabled>这是多选框，暂未加载样式组件，所以原型显示为单选下拉框</option>
                      <option value="1">管理员</option>
                      <option value="2">版主</option>
                      <option value="3">普通会员</option>
                      <option value="4">中级会员</option>
                      <option value="5">高级会员</option>
                    </select>
                  </div>
                </div>
                <div class="input-group mt-2">
                  <span class="input-group-text">是否需要审核<i class="bi bi-info-circle ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="小组管理员不受影响"></i></span>
                  <div class="form-control bg-white">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="publish.comment.review" id="publish.comment.review.0" value="0" checked>
                      <label class="form-check-label" for="publish.comment.review.0">不需要</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="publish.comment.review" id="publish.comment.review.1" value="1">
                      <label class="form-check-label" for="publish.comment.review.1">需要</label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-md-2 col-form-label">启用状态</label>
              <div class="col-sm-9 col-md-10 pt-2">
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
              <label class="col-sm-3 col-md-2 col-form-label"></label>
              <div class="col-sm-9 col-md-10"><button type="submit" class="btn btn-primary">提交</button></div>
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
                    <td><input type="text" class="form-control" value="名称"></td>
                  </tr>
                  <tr>
                    <td>zh-Hans-SG</td>
                    <td>简体中文(新加坡)</td>
                    <td><input type="text" class="form-control" value="名称"></td>
                  </tr>
                  <tr>
                    <td>zh-Hans-HK</td>
                    <td>繁體中文(香港)</td>
                    <td><input type="text" class="form-control" value="名稱"></td>
                  </tr>
                  <tr>
                    <td>en-US <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="默认语言"></i></td>
                    <td>English(United States)</td>
                    <td><input type="text" class="form-control" value="name"></td>
                  </tr>
                  <tr>
                    <td>ja</td>
                    <td>日本語</td>
                    <td><input type="text" class="form-control" value="名"></td>
                  </tr>
                  <tr>
                    <td>ko-KR</td>
                    <td>한국어(대한민국)</td>
                    <td><input type="text" class="form-control" value="이름"></td>
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

  <!-- Language Modal -->
  <div class="modal fade" id="langDescModal" tabindex="-1" aria-labelledby="langDescModal" aria-hidden="true">
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
                    <td><textarea class="form-control" rows="3"></textarea></td>
                  </tr>
                  <tr>
                    <td>zh-Hans-SG</td>
                    <td>简体中文(新加坡)</td>
                    <td><textarea class="form-control" rows="3"></textarea></td>
                  </tr>
                  <tr>
                    <td>zh-Hans-HK</td>
                    <td>繁體中文(香港)</td>
                    <td><textarea class="form-control" rows="3"></textarea></td>
                  </tr>
                  <tr>
                    <td>en-US <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="默认语言"></i></td>
                    <td>English(United States)</td>
                    <td><textarea class="form-control" rows="3"></textarea></td>
                  </tr>
                  <tr>
                    <td>ja</td>
                    <td>日本語</td>
                    <td><textarea class="form-control" rows="3"></textarea></td>
                  </tr>
                  <tr>
                    <td>ko-KR</td>
                    <td>한국어(대한민국)</td>
                    <td><textarea class="form-control" rows="3"></textarea></td>
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

  <!-- Move Modal -->
  <div class="modal fade" id="moveModal" tabindex="-1" aria-labelledby="moveModal" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">合并小组</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">当前分类</label>
              <div class="col-sm-9">
                <input type="text" class="form-control-plaintext" value="小组分类 1" readonly>
              </div>
            </div>
            <div class="mb-3 row">
              <label class="col-sm-3 col-form-label">目标分类</label>
              <div class="col-sm-9">
                <select class="form-select">
                  <option selected disabled>请选择新分类</option>
                  <option value="2">小组分类 2</option>
                  <option value="3">小组分类 3</option>
                  <option value="5">小组分类 4</option>
                  <option value="6">小组分类 5</option>
                </select>
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
