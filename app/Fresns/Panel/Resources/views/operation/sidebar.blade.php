<!--菜单 开始-->
<div class="col-lg-2 fresns-sidebar mt-3 mt-lg-0">
  <nav class="navbar navbar-expand-lg navbar-light flex-lg-column shadow" style="background-color:#e3f2fd;">
    <div class="container-fluid d-lg-flex flex-lg-column">
      <span class="navbar-brand">运营</span>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav d-flex flex-column">
          <li class="nav-item"><a class="nav-link {{ \Route::is('panel.renameConfigs.*') ? 'active' : '' }}" href="{{ route('panel.renameConfigs.show') }}">命名配置</a></li>
          <li class="nav-item"><a class="nav-link {{ \Route::is('panel.interactiveConfigs.*') ? 'active' : '' }}" href="{{ route('panel.interactiveConfigs.show') }}">互动配置</a></li>
          <li class="nav-item"><a class="nav-link {{ \Route::is('panel.emojiGroups.*') ? 'active' : '' }}" href="{{ route('panel.emojiGroups.index' )}}">表情配置</a></li>
          <li class="nav-item"><a class="nav-link  {{ \Route::is('panel.postConfigs.*','panel.commentConfigs.*') ? 'active' : ''}}" href="{{ route('panel.postConfigs.show') }}">发表配置</a></li>
          <li class="nav-item"><a class="nav-link {{ \Route::is('panel.stopWords.*') ? 'active' : '' }}" href="{{ route('panel.stopWords.index') }}">过滤配置</a></li>
          <li class="nav-item"><a class="nav-link {{ \Route::is('panel.memberRoles.*') ? 'active' : '' }}" href="{{ route('panel.memberRoles.index') }}">用户角色</a></li>
          <li class="nav-item"><a class="nav-link {{ \Route::is([
            'panel.groups.*',
            'panel.recommendGroups.*',
            'panel.disableGroups.*'
          ]) ? 'active' : '' }}" href="{{ route('panel.groups.index') }}">内容小组</a></li>
          <li class="nav-item d-block d-lg-none my-3 text-secondary">Powered by Fresns</li>
        </ul>
      </div>
    </div>
    <div class="fresns-copyright d-none d-lg-block">Powered by Fresns</div>
  </nav>
</div>
<!--菜单 结束-->
