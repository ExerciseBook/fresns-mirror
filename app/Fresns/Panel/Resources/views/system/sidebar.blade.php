<!--菜单 开始-->
<div class="col-lg-2 fresns-sidebar mt-3 mt-lg-0">
  <nav class="navbar navbar-expand-lg navbar-light flex-lg-column shadow" style="background-color:#e3f2fd;">
    <div class="container-fluid d-lg-flex flex-lg-column">
      <span class="navbar-brand">系统</span>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav d-flex flex-column">
          <li class="nav-item"><a class="nav-link  {{ \Route::is('panel.languageMenus.*') ? 'active' : ''}}" href="{{ route('panel.languageMenus.index') }}">语言设置</a></li>
          <li class="nav-item"><a class="nav-link {{ \Route::is('panel.site.*') ? 'active' : ''}}" href="{{ route('panel.site.show') }}">站点设置</a></li>
          <li class="nav-item"><a class="nav-link" href="system-policies.html">政策设置</a></li>
          <li class="nav-item"><a class="nav-link" href="system-send.html">发信设置</a></li>
          <li class="nav-item"><a class="nav-link" href="system-user.html">用户设置</a></li>
          <li class="nav-item"><a class="nav-link" href="system-wallet.html">钱包设置</a></li>
          <li class="nav-item"><a class="nav-link" href="system-storage-image.html">存储设置</a></li>
          <li class="nav-item"><a class="nav-link" href="system-map.html">地图设置</a></li>
          <li class="nav-item d-block d-lg-none my-3 text-secondary">Powered by Fresns</li>
        </ul>
      </div>
    </div>
    <div class="fresns-copyright d-none d-lg-block">Powered by Fresns</div>
  </nav>
</div>
<!--菜单 结束-->

