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
        <a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
      </div>
    </div>
    <ul class="nav nav-tabs">
      <li class="nav-item"><a class="nav-link" href="{{ route('panel.groups.index') }}">全部小组</a></li>
      <li class="nav-item"><a class="nav-link" href="{{ route('panel.recommendGroups.index') }}">推荐小组</a></li>
      <li class="nav-item"><a class="nav-link active" href="{{ route('panel.disableGroups.index') }}">停用小组</a></li>
    </ul>
  </div>
  <!--操作列表-->
  <div class="table-responsive">
    <table class="table table-hover align-middle text-nowrap">
      <thead>
        <tr class="table-info">
          <th scope="col">所属分类</th>
          <th scope="col">小组名称</th>
          <th scope="col">小组模式</th>
          <th scope="col">关注方式</th>
          <th scope="col">小组管理员</th>
          <th scope="col">发布权限</th>
          <th scope="col">评论权限</th>
          <th scope="col" style="width:6rem;">操作</th>
        </tr>
      </thead>
      <tbody>
        @foreach($groups as $group)
          <tr>
            <td><span class="badge rounded-pill bg-secondary">{{ optional($group->category)->name}}</span></td>
            <td>
              @if ($group->cover_file_url)
                <img src="{{ $group->cover_file_url }}" width="24" height="24">
              @endif
              {{ $group->name }}</td>
            <td>
              @if($group->type_follow == 1)
                原生
              @else
                插件 <span class="badge bg-light text-dark">{{ optional($group->plugin)->name }}</span>
              @endif
            </td>
            <td>
              @if($group->type_follow == 1)
                原生
              @else
                插件 <span class="badge bg-light text-dark">{{ optional($group->plugin)->name }}</span>
              @endif
            </td>
            <td><span class="badge bg-light text-dark">{{ optional($group->member)->name }}</span></td>
            <td><span class="badge bg-light text-dark">{{ $permissioLabels[$group->permission['publish_post'] ?? 0] ?? ''}}</span></td>
            <td><span class="badge bg-light text-dark">{{ $permissioLabels[$group->permission['publish_comment'] ?? 0] ?? ''}}</span></td>
            <td>
              <form action="{{ route('panel.groups.enable.update', ['group' => $group->id, 'is_enable' => 1])}}" method="post">
                @csrf
                @method('put')
                <button type="submit" class="btn btn-outline-warning btn-sm">恢复</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  {{ $groups->links() }}
@endsection
