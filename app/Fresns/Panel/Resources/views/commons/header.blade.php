<header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fresns-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('panel.dashboard') }}"><img src="{{ @asset('/static/images/panel-logo.png') }}" alt="Fresns" height="30"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#headerNavbar" aria-controls="headerNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="headerNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ \Route::is([
                            'panel.dashboard*',
                            'panel.upgrades*',
                            'panel.admins*',
                            'panel.settings*',
                        ]) ? 'active' : '' }}" href="{{ route('panel.dashboard') }}">{{ __('FsLang::panel.menu_dashboard') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ \Route::is([
                            'panel.languages.*',
                            'panel.general.*',
                            'panel.policy.*',
                            'panel.send.*',
                            'panel.user.*',
                            'panel.wallet.*',
                            'panel.storage.*',
                            'panel.maps.*',
                        ]) ? 'active' : ''}}" href="{{ route('panel.languages.index') }}">{{ __('FsLang::panel.menu_systems') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ \Route::is([
                            'panel.rename.*',
                            'panel.interactive.*',
                            'panel.stickers.*',
                            'panel.publish.post.*',
                            'panel.publish.comment.*',
                            'panel.block-words.*',
                            'panel.groups.*',
                            'panel.roles.*'
                        ]) ? 'active' : ''}}" href="{{ route('panel.rename.index' )}}">{{ __('FsLang::panel.menu_operations') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ \Route::is([
                            'panel.editor.*',
                            'panel.content-type.*',
                            'panel.post-detail.*',
                            'panel.manage.*',
                            'panel.group.*',
                            'panel.user-feature.*',
                            'panel.user-profile.*'
                        ]) ? 'active' : '' }}" href="{{ route('panel.editor.index') }}">{{ __('FsLang::panel.menu_extends') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ \Route::is([
                            'panel.menus.*',
                            'panel.columns.*',
                            'panel.language.packs.*',
                            'panel.keys.*',
                        ]) ? 'active' : '' }}" href="{{ route('panel.menus.index') }}">{{ __('FsLang::panel.menu_clients') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ \Route::is([
                            'panel.plugin.*',
                            'panel.engine.*',
                            'panel.theme.*',
                            'panel.app.*',
                            'panel.iframe.*',
                        ]) ? 'active' : '' }} " href="{{ route('panel.plugin.index') }}">{{ __('FsLang::panel.menu_app_center') }}</a>
                    </li>
                </ul>
                <div class="navbar-nav">
                    <!--lang-->
                    <div class="btn-group d-flex flex-column">
                        <button type="button" class="btn btn-outline-light btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-translate"></i> {{ $langs[\App::getLocale()] ?? '' }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @foreach($langs as $code => $lang)
                                <li><a class="dropdown-item" href="?lang={{$code}}">{{ $lang }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    <!--logout-->
                    <div class="ms-lg-3 mt-3 mt-lg-0 mb-2 mb-lg-0">
                        <form action="{{route('panel.logout')}}" method="POST">
                            @csrf
                            <button class="btn btn-outline-warning btn-sm" type="subbmit">{{ __('FsLang::panel.logout') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>
