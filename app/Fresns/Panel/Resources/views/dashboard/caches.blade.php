@extends('FsView::commons.sidebarLayout')

@section('sidebar')
    @include('FsView::dashboard.sidebar')
@endsection

@section('content')
    <div class="row mb-4 border-bottom">
        <div class="col-lg-7">
            <h3>{{ __('FsLang::panel.sidebar_caches') }}</h3>
            <p class="text-secondary">{{ __('FsLang::panel.sidebar_caches_intro') }}</p>
        </div>
        <div class="col-lg-5">
            <div class="input-group mt-2 mb-4 justify-content-lg-end">
                <a class="btn btn-outline-danger" href="{{ route('panel.cache.all.clear') }}"><i class="bi bi-trash3"></i> {{ __('FsLang::panel.button_clear_cache') }}</a>
                <a class="btn btn-outline-secondary" href="#" role="button">{{ __('FsLang::panel.button_support') }}</a>
            </div>
        </div>
    </div>
    <!--form-->
    <form action="{{ route('panel.cache.select.clear') }}" method="post">
        @csrf

        <div class="mx-4 ps-lg-5">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="fresnsSystems" name="fresnsSystems" checked>
                <label class="form-check-label" for="fresnsSystems">系统信息</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="fresnsConfigs" name="fresnsConfigs" checked>
                <label class="form-check-label" for="fresnsConfigs">站点配置</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="fresnsLanguages" name="fresnsLanguages">
                <label class="form-check-label" for="fresnsLanguages">多语言内容</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="fresnsModels" name="fresnsModels" disabled>
                <label class="form-check-label" for="fresnsModels">数据模型</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="fresnsUserInteraction" name="fresnsUserInteraction" disabled>
                <label class="form-check-label" for="fresnsUserInteraction">用户互动信息</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="fresnsApiData" name="fresnsApiData" disabled>
                <label class="form-check-label" for="fresnsApiData">API 数据内容（用户、小组、话题、帖子、评论）</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="fresnsApiExtensions" name="fresnsApiExtensions" disabled>
                <label class="form-check-label" for="fresnsApiExtensions">API 扩展内容</label>
            </div>

            <button type="submit" class="btn btn-primary">{{ __('FsLang::panel.button_clear_cache') }}</button>
        </div>
    </form>
@endsection
