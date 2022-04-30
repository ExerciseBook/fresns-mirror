@extends('FsView::commons.sidebarLayout')

@section('sidebar')
    @include('FsView::dashboard.sidebar')
@endsection

@section('content')
    <!--Dashboard-->
    <div class="row mb-4 ">
        <div class="col-lg-7">
            <h1 class="fs-3 fw-normal">{{ __('FsLang::panel.welcome') }}</h1>
            <p class="text-secondary">
                {{ __('FsLang::panel.current_version') }} v{{$currentVersion['version'] ?? ''}}
                @if ($checkVersion)
                    <a href="{{ route('panel.upgrades') }}" class="badge rounded-pill bg-danger ms-2 text-decoration-none">{{ __('FsLang::panel.new_version') }}</a>
                @endif
            </p>
        </div>
        <div class="col-lg-5">
            <div class="input-group mt-2 mb-4 justify-content-lg-end">
                <a class="btn btn-outline-success" href="{{ route('panel.cache.clear') }}" role="button">{{ __('FsLang::panel.button_clear_cache') }}</a>
            </div>
        </div>
    </div>
    <!--Dashboard data-->
    <div class="row mb-3">
        <div class="col-md mb-4 pe-lg-5">
            <h3 class="h6">{{ __('FsLang::panel.overview') }}</h3>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <i class="bi bi-person-fill"></i> {{ __('FsLang::panel.overview_accounts') }}
                    <span class="badge bg-success">{{ $overview['accountCount'] }}</span>
                </li>
                <li class="list-group-item">
                    <i class="bi bi-people"></i> {{ __('FsLang::panel.overview_users') }}
                    <span class="badge bg-success">{{ $overview['userCount'] }}</span>
                </li>
                <li class="list-group-item">
                    <i class="bi bi-collection"></i> {{ __('FsLang::panel.overview_groups') }}
                    <span class="badge bg-success">{{ $overview['groupCount'] }}</span>
                </li>
                <li class="list-group-item">
                    <i class="bi bi-hash"></i> {{ __('FsLang::panel.overview_hashtags') }}
                    <span class="badge bg-success">{{ $overview['hashtagCount'] }}</span>
                </li>
                <li class="list-group-item">
                    <i class="bi bi-postcard"></i> {{ __('FsLang::panel.overview_posts') }}
                    <span class="badge bg-success">{{ $overview['postCount'] }}</span>
                </li>
                <li class="list-group-item">
                    <i class="bi bi-chat-right-dots"></i> {{ __('FsLang::panel.overview_comments') }}
                    <span class="badge bg-success">{{ $overview['commentCount'] }}</span>
                </li>
            </ul>
        </div>
        <div class="col-md mb-4 pe-lg-5">
            <h3 class="h6">{{ __('FsLang::panel.extensions') }}</h3>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <i class="bi bi-key"></i> {{ __('FsLang::panel.extensions_admins') }}
                    <span class="badge bg-info">{{ $adminCount }}</span>
                </li>
                <li class="list-group-item">
                    <i class="bi bi-person"></i> {{ __('FsLang::panel.extensions_keys') }}
                    <span class="badge bg-info">{{ $keyCount }}</span>
                </li>
                <li class="list-group-item">
                    <i class="bi bi-journal-code"></i> {{ __('FsLang::panel.extensions_plugins') }}
                    <span class="badge bg-info">{{ $plugins->where('type', 1)->count() }}</span>
                </li>
                <li class="list-group-item">
                    <i class="bi bi-phone"></i> {{ __('FsLang::panel.extensions_apps') }}
                    <span class="badge bg-info">{{ $plugins->where('type', 2)->count() }}</span>
                </li>
                <li class="list-group-item">
                    <i class="bi bi-laptop"></i> {{ __('FsLang::panel.extensions_engines') }}
                    <span class="badge bg-info">{{ $plugins->where('type', 3)->count() }}</span>
                </li>
                <li class="list-group-item">
                    <i class="bi bi-brush"></i> {{ __('FsLang::panel.extensions_themes') }}
                    <span class="badge bg-info">{{ $plugins->where('type', 4)->count() }}</span>
                </li>
            </ul>
        </div>
        <div class="col-md mb-4 pe-lg-5">
            <h3 class="h6">{{ __('FsLang::panel.support') }}</h3>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <a class="fresns-link" href="https://fresns.cn" target="_blank">{{ __('FsLang::panel.support_website') }}</a>
                </li>
                <li class="list-group-item">
                    <a class="fresns-link" href="https://fresns.cn/community/teams.html" target="_blank">{{ __('FsLang::panel.support_teams') }}</a>
                </li>
                <li class="list-group-item">
                    <a class="fresns-link" href="https://fresns.cn/community/partners.html" target="_blank">{{ __('FsLang::panel.support_partners') }}</a>
                </li>
                <li class="list-group-item">
                    <a class="fresns-link" href="https://fresns.cn/community/join.html" target="_blank">{{ __('FsLang::panel.support_join') }}</a>
                </li>
                <li class="list-group-item">
                    <a class="fresns-link" href="https://discuss.fresns.cn" target="_blank">{{ __('FsLang::panel.support_community') }}</a>
                </li>
                <li class="list-group-item">
                    <a class="fresns-link" href="https://market.fresns.cn" target="_blank">{{ __('FsLang::panel.support_market') }}</a>
                </li>
            </ul>
        </div>
    </div>
    <!--row-->
    <div class="row">
        <!--system info-->
        <div class="col-md mb-4">
            <div class="card">
                <div class="card-header">{{ __('FsLang::panel.system_info') }}</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            {{ __('FsLang::panel.system_info_server') }}: <span>{{ $systemInfo['server'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            {{ __('FsLang::panel.system_info_web') }}: <span>{{ $systemInfo['web'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            {{ __('FsLang::panel.system_info_php_version') }}: <span>{{ $systemInfo['php']['version'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            {{ __('FsLang::panel.system_info_php_upload_max_filesize') }}: <span>{{ $systemInfo['php']['uploadMaxFileSize'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            {{ __('FsLang::panel.system_info_database_version') }}: <span>{{ $databaseInfo['version'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            {{ __('FsLang::panel.system_info_database_timezone') }}: <span><a data-bs-toggle="modal" href="#timezoneListModal" role="button">{{ $databaseInfo['timezone'] }}</a></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            {{ __('FsLang::panel.system_info_env_timezone') }}:
                            <span @if ($databaseInfo['timezone'] != $databaseInfo['envTimezoneToUtc']) data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('FsLang::tips.timezone_error') }}" @endif>
                                @if ($databaseInfo['timezone'] !== $databaseInfo['envTimezoneToUtc'])
                                    <span class="spinner-grow spinner-grow-sm text-danger" role="status" aria-hidden="true"></span>
                                @endif
                                {{ $databaseInfo['envTimezone'] }}
                                <span class="badge rounded-pill bg-secondary ms-2 fs-9">{{ $databaseInfo['envTimezoneToUtc'] }}</span>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            {{ __('FsLang::panel.system_info_database_collation') }}: <span>{{ $databaseInfo['collation'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            {{ __('FsLang::panel.system_info_database_size') }}: <span>{{ $databaseInfo['sizeMb'].' MB' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!--news-->
        <div class="col-md mb-4">
            <div class="card">
                <div class="card-header">{{ __('FsLang::panel.news') }}</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @foreach ($newsList as $news)
                            <li class="list-group-item">
                                <span class="badge bg-warning text-dark">{{ $news['date'] }}</span>
                                <a class="fresns-link ms-2" href="{{ $news['link'] }}" target="_blank" {{ 'style=color:'.$news['color'] }}>{{ $news['title'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="timezoneListModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <span class="badge bg-primary">{{ $databaseInfo['timezone'] }}</span>
                        {{ __('FsLang::panel.system_info_env_timezone_list') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group list-group-flush">
                        @foreach ($timezones as $timezone)
                            <li class="list-group-item">{{ $timezone }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
