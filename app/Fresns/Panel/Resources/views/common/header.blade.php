<header>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fresns-navbar">
    <div class="container-fluid">
      <a class="navbar-brand" href="{{ route('panel.dashboard') }}"><img src="{{ @asset('/static/images/logo.png') }}" alt="Fresns" height="30"></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#headerNavbar" aria-controls="headerNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="headerNavbar">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link {{ \Route::is([
            'panel.dashboard',
            'panel.manageConfigs.*',
            'panel.admins.*',
          ]) ? 'active' : '' }}" href="{{ route('panel.dashboard') }}">仪表盘</a>
          </li>
          <li class="nav-item"><a class="nav-link {{ \Route::is([
            'panel.languageMenus.*',
            'panel.site.*',
            'panel.policy.*',
            'panel.send.*',
            'panel.verifyCodes.*',
            'panel.userConfigs.*',
            'panel.wallet*',
            'panel.storage.*',
            'panel.mapConfigs.*',
          ]) ? 'active' : ''}}" href="{{ route('panel.languageMenus.index') }}">系统</a></li>
          <li class="nav-item"> <a class="nav-link {{ \Route::is([
            'panel.renameConfigs.*',
            'panel.interactiveConfigs.*',
            'panel.emojis.*',
            'panel.emojiGroups.*',
            'panel.postConfigs.*',
            'panel.commentConfigs.*',
            'panel.stopWords.*',
            'panel.groups.*',
            'panel.recommendGroups.*',
            'panel.disableGroups.*',
            'panel.memberRoles.*'
          ]) ? 'active' : ''}}" href="{{ route('panel.renameConfigs.show' )}}">运营</a></li>
          <li class="nav-item"><a class="nav-link {{ \Route::is([
            'panel.expandEditor.*',
            'panel.expandType.*',
            'panel.expandPost.*',
            'panel.expandManage.*',
            'panel.expandGroup.*',
            'panel.expandFeature.*',
            'panel.expandProfile.*'
          ]) ? 'active' : '' }}" href="{{ route('panel.expandEditor.index') }}">扩展</a></li>

          <li class="nav-item"><a class="nav-link {{ \Route::is([
            'panel.plugins.*'
          ]) ? 'active' : '' }} " href="{{ route('panel.plugins.index') }}">插件</a></li>
          <li class="nav-item"><a class="nav-link  {{ \Route::is([
            'panel.clientMenus.*',
            'panel.columns.*',
            'panel.languagePack.*',
            'panel.engines.*',
            'panel.themes.*',
            'panel.apps.*',
            'panel.sessionKeys.*',
          ]) ? 'active' : '' }} " href="{{ route('panel.clientMenus.index') }}">客户端</a></li>
          {{--<li class="nav-item"><a class="nav-link" href="app-store.html">应用商店</a></li>--}}
        </ul>
        <div class="navbar-nav">
          <div class="btn-group d-flex flex-column">
            <button type="button" class="btn btn-outline-light btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-translate"></i> {{ $langs[$locale] ?? '' }}</button>
            <ul class="dropdown-menu dropdown-menu-end">
              @foreach($langs as $code => $lang)
                <li><a class="dropdown-item" href="?lang={{$code}}">{{ $lang }}</a></li>
              @endforeach
            </ul>
          </div>
          <div class="ms-3">
            <form action="{{route('panel.logout')}}" method="POST">
              @csrf
              <button class="btn btn-outline-warning btn-sm" type="subbmit">退出</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </nav>
</header>
