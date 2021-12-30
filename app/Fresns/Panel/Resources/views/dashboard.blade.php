@extends('panel::common.baseLayout')

@section('content')
  <div class="container-lg">
    <!--欢迎内容-->
    <div class="card mt-4 mb-4 p-2 p-lg-4">
      <div class="card-body">
        <h1 class="fs-3 fw-normal">欢迎使用 Fresns</h1>
        <p class="text-secondary pb-4">当前使用的版本是 v1.0.0</p>
        <div class="row">
          <div class="col-md mb-4 pe-lg-5">
            <h3 class="h6">站点数据</h3>
            <ul class="list-group list-group-flush">
              <li class="list-group-item"><i class="bi bi-person-fill"></i> 账号总数 <span class="badge bg-success">100</span></li>
              <li class="list-group-item"><i class="bi bi-person"></i> 用户总数 <span class="badge bg-success">100</span></li>
              <li class="list-group-item"><i class="bi bi-collection"></i> 小组总数 <span class="badge bg-success">100</span></li>
              <li class="list-group-item"><i class="bi bi-hash"></i> 话题总数 <span class="badge bg-success">100</span></li>
              <li class="list-group-item"><i class="bi bi-file-post-fill"></i> 帖子总数 <span class="badge bg-success">100</span></li>
              <li class="list-group-item"><i class="bi bi-chat-right-dots"></i> 评论总数 <span class="badge bg-success">100</span></li>
            </ul>
          </div>
          <div class="col-md mb-4 pe-lg-5">
            <h3 class="h6">应用数量</h3>
            <ul class="list-group list-group-flush">
              <li class="list-group-item"><i class="bi bi-key"></i> 接口密钥 <span class="badge bg-info">2</span></li>
              <li class="list-group-item"><i class="bi bi-person"></i> 管理员 <span class="badge bg-info">3</span></li>
              <li class="list-group-item"><i class="bi bi-journal-code"></i> 扩展插件 <span class="badge bg-info">24</span></li>
              <li class="list-group-item"><i class="bi bi-laptop"></i> 网站引擎 <span class="badge bg-info">3</span></li>
              <li class="list-group-item"><i class="bi bi-brush"></i> 主题模板 <span class="badge bg-info">6</span></li>
              <li class="list-group-item"><i class="bi bi-phone"></i> 移动应用 <span class="badge bg-info">2</span></li>
            </ul>
          </div>
          <div class="col-md mb-4 pe-lg-5">
            <h3 class="h6">帮助手册</h3>
            <ul class="list-group list-group-flush">
              <li class="list-group-item"><a class="fresns-link" href="https://fresns.cn" target="_blank">Fresns 官网</a></li>
              <li class="list-group-item"><a class="fresns-link" href="https://fresns.cn/community/team.html" target="_blank">开源团队</a></li>
              <li class="list-group-item"><a class="fresns-link" href="https://fresns.cn/community/partners.html" target="_blank">合作伙伴</a></li>
              <li class="list-group-item"><a class="fresns-link" href="https://fresns.cn/community/join.html" target="_blank">加入我们</a></li>
              <li class="list-group-item"><a class="fresns-link" href="https://discuss.fresns.cn" target="_blank">支持社区</a></li>
              <li class="list-group-item"><a class="fresns-link" href="https://apps.fresns.cn" target="_blank">应用商店</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <!--两栏信息-->
    <div class="row">
      <!--动态-->
      <div class="col-md mb-4">
        <div class="card">
          <div class="card-header">Fresns 活动和新闻</div>
          <div class="card-body">
            {!! $news['content'] !!}
          </div>
        </div>
      </div>
      <!--更新-->
      <!--
      <div class="col-md">
        <div class="card">
          <div class="card-header">更新管理</div>
          <div class="card-body">
            <ul class="list-group list-group-flush">
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div><i class="bi bi-sliders"></i> 小程序助手 <span class="badge bg-secondary">1.0.9</span> to <span class="badge bg-danger">1.1.0</span></div>
                <div><button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#upgrade">更新</button></div>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div><i class="bi bi-laptop"></i> Discuz X <span class="badge bg-secondary">1.0.0</span> to <span class="badge bg-danger">1.5</span></div>
                <div><button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#upgrade">更新</button></div>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div><i class="bi bi-laptop"></i> BBS 主题 <span class="badge bg-secondary">Beta</span> to <span class="badge bg-danger">1.0.0</span></div>
                <div><button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#upgrade">更新</button></div>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div><i class="bi bi-phone"></i> Fresns for iOS <span class="badge bg-secondary">1.0.9</span> to <span class="badge bg-danger">1.1.0</span></div>
                <div><button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#upgrade">更新</button></div>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div><i class="bi bi-journal-code"></i> 每日榜单 <span class="badge bg-secondary">1.0.9</span> to <span class="badge bg-danger">1.1.0</span></div>
                <div><button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#upgrade">更新</button></div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    -->
    </div>
  </div>
  <!-- 插件升级 Modal   <div class="modal fade" id="upgrade" tabindex="-1" aria-labelledby="upgrade" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-laptop"></i> Discuz X <span class="badge bg-secondary">1.0.0</span> to <span class="badge bg-danger">1.5</span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body ps-5">
          <p><i class="bi bi-x-lg text-danger me-2"></i>初始化验证 <span class="badge bg-secondary">'当前行为名'+'inputError'</span></p>
          <p><i class="bi bi-check-lg text-success me-2"></i>下载应用包</p>
          <p><i class="bi bi-check-lg text-success me-2"></i>解压应用包</p>
          <p><i class="spinner-border spinner-border-sm me-2" role="status"></i>安装应用</p>
          <p><i class="bi bi-hourglass text-secondary me-2"></i>清空缓存</p>
          <p><i class="bi bi-hourglass text-secondary me-2"></i>完成</p>
        </div>
      </div>
    </div>
  </div>
  -->


@endsection
