@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::manage.sidebar')
@endsection

@section('content')
  <div class="row mb-4 border-bottom">
    <div class="col-lg-7">
      <h3>管理员</h3>
      <p class="text-secondary">有权登录控制台的管理员</p>
    </div>
    <div class="col-lg-5">
      <div class="input-group mt-2 mb-4 justify-content-lg-end">
        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#newAdmin"><i class="bi bi-plus-circle-dotted"></i> 新增管理员</button>
        <a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
      </div>
    </div>
  </div>
  <!--管理员列表 开始-->
  <div class="table-responsive">
    <table class="table table-hover align-middle text-nowrap">
      <thead>
        <tr class="table-info">
          <th scope="col">UID</th>
          <th scope="col">账号</th>
          <th scope="col">操作</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>1</td>
          <td>
            <span class="badge bg-light text-dark"><i class="bi bi-envelope"></i> jar***@foxmail.com</span>
            <span class="badge bg-light text-dark"><i class="bi bi-phone"></i> +86 139****5678</span>
          </td>
          <td>
            <button type="button" class="btn btn-link btn-sm text-danger fresns-link">删除</button>
          </td>
        </tr>
        <tr>
          <td>18</td>
          <td>
            <span class="badge bg-light text-dark"><i class="bi bi-envelope"></i> jie***@qq.com</span>
            <span class="badge bg-light text-dark"><i class="bi bi-phone"></i> Null</span>
          </td>
          <td>
            <button type="button" class="btn btn-link btn-sm text-danger fresns-link">删除</button>
          </td>
        </tr>
        <tr>
          <td>9527</td>
          <td>
            <span class="badge bg-light text-dark"><i class="bi bi-envelope"></i> Null</span>
            <span class="badge bg-light text-dark"><i class="bi bi-phone"></i> +1 626***9527</span>
          </td>
          <td>
            <button type="button" class="btn btn-link btn-sm text-danger fresns-link">删除</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <!--管理员列表 结束-->

  <!-- Modal -->
  <div class="modal fade" id="newAdmin" tabindex="-1" aria-labelledby="newAdmin" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">新增运营管理员</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form class="mb-3">
            <div class="input-group">
              <span class="input-group-text">账号</span>
              <input type="text" class="form-control" placeholder="邮箱或者手机号">
              <button class="btn btn-outline-secondary" type="submit" id="folderInstall-button">搜索并增加</button>
            </div>
            <div class="form-text"><i class="bi bi-info-circle"></i> 手机号必须为带国际区号的完整号码</div>
          </form>
        </div>
      </div>
    </div>
  </div>

@endsection
