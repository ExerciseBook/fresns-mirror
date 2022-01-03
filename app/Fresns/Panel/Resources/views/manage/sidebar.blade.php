<!--菜单 开始-->
<div class="col-lg-2 fresns-sidebar mt-3 mt-lg-0">
  <nav class="navbar navbar-expand-lg navbar-light flex-lg-column shadow" style="background-color:#e3f2fd;">
    <div class="container-fluid d-lg-flex flex-lg-column">
      <span class="navbar-brand">{{ __('panel::panel.manage') }}</span>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav d-flex flex-column">
          <li class="nav-item"><a class="nav-link {{ \Route::is('panel.sessionKeys.*') ? 'active' : ''}} " href="{{ route('panel.sessionKeys.index') }}">{{ __('panel::panel.manageKey') }}</a></li>
          <li class="nav-item"><a class="nav-link {{ \Route::is('panel.configs.*') ? 'active' : '' }}" href="{{ route('panel.configs.show')}}">{{ __('panel::panel.manageConfig') }}</a></li>
          <li class="nav-item"><a class="nav-link {{ \Route::is('panel.admins.*') ? 'active' : '' }}" href="{{ route('panel.admins.index') }}">{{ __('panel::panel.admin') }}</a></li>
          <li class="nav-item d-block d-lg-none my-3 text-secondary">Powered by Fresns</li>
        </ul>
      </div>
    </div>
    <div class="fresns-copyright d-none d-lg-block">Powered by Fresns</div>
  </nav>
</div>
<!--菜单 结束-->

