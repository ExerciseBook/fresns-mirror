<div class="col-lg-2 fresns-sidebar mt-3 mt-lg-0">
    <nav class="navbar navbar-expand-lg navbar-light flex-lg-column shadow" style="background-color:#e3f2fd;">
        <div class="container-fluid d-lg-flex flex-lg-column">
            <span class="navbar-brand"><i class="bi bi-robot"></i> {{ __('FsLang::panel.menu_app_center') }}</span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav d-flex flex-column">
                    <li class="nav-item"><a class="nav-link {{ \Route::is('panel.plugin.*') ? 'active' : '' }}" href="{{ route('panel.plugin.index') }}"><i class="bi bi-journal-code"></i> {{ __('FsLang::panel.sidebar_plugins') }}</a></li>
                    <li class="nav-item"><a class="nav-link {{ \Route::is('panel.engine.*', 'panel.theme.*') ? 'active' : '' }}" href="{{ route('panel.engine.index') }}"><i class="bi bi-laptop"></i> {{ __('FsLang::panel.sidebar_website') }}</a></li>
                    <li class="nav-item"><a class="nav-link {{ \Route::is('panel.app.*') ? 'active' : '' }}" href="{{ route('panel.app.index') }}"><i class="bi bi-phone"></i> {{ __('FsLang::panel.sidebar_apps') }}</a></li>
                    <li><hr style="margin: 0.5rem 0"></li>
                    <li class="nav-item"><a class="nav-link {{ \Route::is('panel.keys.*') ? 'active' : '' }} " href="{{ route('panel.keys.index') }}"><i class="bi bi-key"></i> {{ __('FsLang::panel.sidebar_keys') }}</a></li>
                    <li><hr style="margin: 0.5rem 0"></li>
                    <li class="nav-item"><a class="nav-link {{ \Route::is('panel.iframe.market') ? 'active' : '' }}" href="{{ route('panel.iframe.market', ['url' => 'https://market.fresns.cn']) }}"><i class="bi bi-shop"></i> {{ __('FsLang::panel.menu_market') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="#installModal" data-bs-toggle="modal" role="button"><i class="bi bi-tools"></i> {{ __('FsLang::panel.install_application') }}</a></li>
                    <li><hr style="margin: 0.5rem 0"></li>
                </ul>
            </div>
        </div>
        <div class="fresns-copyright d-none d-lg-block">Powered by Fresns</div>
    </nav>
</div>

<!--install modal-->
<div class="modal fade" id="installModal" tabindex="-1" aria-labelledby="install" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('FsLang::panel.install_application') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('panel.plugin.install') }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('put')
                <div class="modal-body">
                    <div class="input-group mb-3">
                        <span class="input-group-text">{{ __('FsLang::panel.install_type') }}</span>

                        <div class="form-control">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="install_type" id="install_plugin" value="plugin" checked required>
                                <label class="form-check-label" for="install_plugin">{{ __('FsLang::panel.install_type_application') }} <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('FsLang::panel.install_type_desc') }}"></i></label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="install_type" id="install_theme" value="theme" required>
                                <label class="form-check-label" for="install_theme">{{ __('FsLang::panel.install_type_theme') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text">{{ __('FsLang::panel.install_mode') }}</span>

                        <button class="btn btn-outline-secondary dropdown-toggle showSelectTypeName" type="button" data-bs-toggle="dropdown" aria-expanded="false">{{ __('FsLang::panel.install_mode_input') }}</button>
                        <ul class="dropdown-menu selectInputType">
                            <li data-name="inputUnikey"><a class="dropdown-item install_method" href="#">{{ __('FsLang::panel.install_mode_input') }}</a></li>
                            <li data-name="inputFile"><a class="dropdown-item install_method" href="#">{{ __('FsLang::panel.install_mode_upload') }}</a></li>
                            <li data-name="inputDir"><a class="dropdown-item install_method" href="#">{{ __('FsLang::panel.install_mode_dir') }}</a></li>
                        </ul>

                        <input type="hidden" name="install_method" value="inputUnikey" />
                        <input type="text" class="form-control inputUnikey" name="plugin_unikey" maxlength="64">
                        <input type="file" class="form-control inputFile" name="plugin_zipball" accept=".zip" style="display:none;">
                        <input type="text" class="form-control inputDir" name="plugin_dir" maxlength="64" style="display:none;">

                        <div id="inputUnikeyOrInputFile" class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"  id="installSubmit">{{ __('FsLang::panel.button_confirm_install') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!--install artisan output modal-->
<div class="modal fade" id="installStepModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="installStepModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('FsLang::panel.install_application') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <textarea class="form-control" rows="6" id="install_artisan_output" readonly>{{ __('FsLang::tips.install_in_progress') }}</textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="reloadPage()">{{ __('FsLang::panel.button_close') }}</button>
            </div>
        </div>
    </div>
</div>

<!--uninstall modal-->
<div class="modal fade" id="uninstallConfirm" tabindex="-1" aria-labelledby="uninstall" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('FsLang::panel.button_uninstall') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="uninstallData">
                    <label class="form-check-label" for="uninstallData">{{ __('FsLang::panel.option_uninstall_plugin_data') }}</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('FsLang::panel.button_cancel') }}</button>
                <button type="button" class="btn btn-danger uninstall-plugin" data-bs-toggle="modal" data-bs-dismiss="modal" data-bs-target="#uninstallStepModal" id="uninstallSubmit">{{ __('FsLang::panel.button_confirm_uninstall') }}</button>
            </div>
        </div>
    </div>
</div>

<!--uninstall artisan output modal-->
<div class="modal fade" id="uninstallStepModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="uninstallStepModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('FsLang::panel.button_uninstall') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <textarea class="form-control" rows="6" id="uninstall_artisan_output" readonly>{{ __('FsLang::tips.uninstall_in_progress') }}</textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="reloadPage()">{{ __('FsLang::panel.button_close') }}</button>
            </div>
        </div>
    </div>
</div>
