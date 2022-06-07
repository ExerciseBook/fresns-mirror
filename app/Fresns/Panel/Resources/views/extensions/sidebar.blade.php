<div class="col-lg-2 fresns-sidebar mt-3 mt-lg-0">
    <nav class="navbar navbar-expand-lg navbar-light flex-lg-column shadow" style="background-color:#e3f2fd;">
        <div class="container-fluid d-lg-flex flex-lg-column">
            <span class="navbar-brand">{{ __('FsLang::panel.menu_app_center') }}</span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav d-flex flex-column">
                    <li class="nav-item"><a class="nav-link {{ \Route::is('panel.plugin.*') ? 'active' : '' }}" href="{{ route('panel.plugin.index') }}"><i class="bi bi-journal-code"></i> {{ __('FsLang::panel.sidebar_plugins') }}</a></li>
                    <li class="nav-item"><a class="nav-link {{ \Route::is('panel.engine.*', 'panel.theme.*') ? 'active' : '' }}" href="{{ route('panel.engine.index') }}"><i class="bi bi-laptop"></i> {{ __('FsLang::panel.sidebar_website') }}</a></li>
                    <li class="nav-item"><a class="nav-link {{ \Route::is('panel.app.*') ? 'active' : '' }}" href="{{ route('panel.app.index') }}"><i class="bi bi-phone"></i> {{ __('FsLang::panel.sidebar_apps') }}</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li class="nav-item"><a class="nav-link {{ \Route::is('panel.iframe.market') ? 'active' : '' }}" href="{{ route('panel.iframe.market', ['url' => 'https://market.fresns.cn']) }}"><i class="bi bi-shop"></i> {{ __('FsLang::panel.menu_market') }}</a></li>
                    <li><hr class="dropdown-divider"></li>
                    @foreach ($enablePlugins as $plugin)
                        @if ($plugin->settings_path)
                            <li class="nav-item"><a href="{{ route('panel.iframe.plugin', ['url' => $plugin->settings_path]) }}" class="nav-link">{{ $plugin->name }}</a></li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="fresns-copyright d-none d-lg-block">Powered by Fresns</div>
    </nav>
</div>
