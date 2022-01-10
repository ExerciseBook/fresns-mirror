<!--菜单 开始-->
<div class="col-lg-2 fresns-sidebar mt-3 mt-lg-0">
  <nav class="navbar navbar-expand-lg navbar-light flex-lg-column shadow" style="background-color:#e3f2fd;">
    <div class="container-fluid d-lg-flex flex-lg-column">
      <span class="navbar-brand">客户端</span>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav d-flex flex-column">
          <li class="nav-item"><a class="nav-link  {{ \Route::is('panel.clientMenus.*') ? 'active' : ''}}" href="{{ route('panel.clientMenus.index') }}">菜单配置</a></li>
          <li class="nav-item"><a class="nav-link {{ \Route::is('panel.columns.*') ? 'active' : ''}}" href="{{ route('panel.columns.index') }}">栏目配置</a></li>
		  <li class="nav-item"><a class="nav-link  {{ \Route::is('panel.languagePack.*') ? 'active' : ''}}" href="{{ route('panel.languagePack.index') }}">语言包配置</a></li>
		  <li><hr class="dropdown-divider"></li>
          <li class="nav-item"><a class="nav-link  {{ \Route::is('panel.engines.*') ? 'active' : ''}}" href="{{ route('panel.engines.index') }}">网站引擎</a></li>
          <li class="nav-item"><a class="nav-link {{ \Route::is('panel.themes.*') ? 'active' : ''}}" href="{{ route('panel.themes.index') }}">主题模板</a></li>
          <li class="nav-item"><a class="nav-link {{ \Route::is('panel.apps.*') ? 'active' : ''}}" href="{{ route('panel.apps.index') }}">移动应用</a></li>
        </ul>
      </div>
    </div>
    <div class="fresns-copyright d-none d-lg-block">Powered by Fresns</div>
  </nav>
</div>
<!--菜单 结束-->
