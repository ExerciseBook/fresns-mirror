<!--菜单 开始-->
<div class="col-lg-2 fresns-sidebar mt-3 mt-lg-0">
  <nav class="navbar navbar-expand-lg navbar-light flex-lg-column shadow" style="background-color:#e3f2fd;">
    <div class="container-fluid d-lg-flex flex-lg-column">
      <span class="navbar-brand">扩展</span>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav d-flex flex-column">
          <li class="nav-item"><a class="nav-link  {{ \Route::is('panel.expandEditor.*') ? 'active' : ''}}" href="{{ route('panel.expandEditor.index') }}">编辑器扩展</a></li>
		  <li class="nav-item"><a class="nav-link  {{ \Route::is('panel.expandType.*') ? 'active' : ''}}" href="{{ route('panel.expandType.index') }}">内容类型扩展</a></li>
		  <li class="nav-item"><a class="nav-link {{ \Route::is('panel.expandPost.*') ? 'active' : ''}}" href="{{ route('panel.expandPost.index') }}">帖子详情扩展</a></li>
		  <li class="nav-item"><a class="nav-link {{ \Route::is('panel.expandManage.*') ? 'active' : ''}}" href="{{ route('panel.expandManage.index') }}">管理扩展</a></li>
		  <li class="nav-item"><a class="nav-link {{ \Route::is('panel.expandGroup.*') ? 'active' : ''}}" href="{{ route('panel.expandGroup.index') }}">小组扩展</a></li>
		  <li class="nav-item"><a class="nav-link {{ \Route::is('panel.expandFeature.*') ? 'active' : ''}}" href="{{ route('panel.expandFeature.index') }}">用户功能扩展</a></li>
		  <li class="nav-item"><a class="nav-link {{ \Route::is('panel.expandProfile.*') ? 'active' : ''}}" href="{{ route('panel.expandProfile.index') }}">用户资料扩展</a></li>
        </ul>
      </div>
    </div>
    <div class="fresns-copyright d-none d-lg-block">Powered by Fresns</div>
  </nav>
</div>
<!--菜单 结束-->
