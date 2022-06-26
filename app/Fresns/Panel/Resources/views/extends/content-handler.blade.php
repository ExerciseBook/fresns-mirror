@extends('FsView::commons.sidebarLayout')

@section('sidebar')
    @include('FsView::extends.sidebar')
@endsection

@section('content')
    <!--content_handler header-->
    <div class="row mb-5 border-bottom">
        <div class="col-lg-9">
            <h3>{{ __('FsLang::panel.sidebar_extend_content_handler') }}</h3>
            <p class="text-secondary">{{ __('FsLang::panel.sidebar_extend_content_handler_intro') }}</p>
        </div>
        <div class="col-lg-3">
            <div class="input-group mt-2 mb-4 justify-content-lg-end">
                <a class="btn btn-outline-secondary" href="#" role="button">{{ __('FsLang::panel.button_support') }}</a>
            </div>
        </div>
    </div>

    <!--content_handler config-->
    <form action="{{ route('panel.content-handler.update') }}" method="post">
        @csrf
        @method('put')
        <!--content handler-->
        <div class="row mb-4">
            <label class="col-lg-2 col-form-label text-lg-end">内容处理:</label>
            <div class="col-lg-6">
                <div class="input-group mb-3">
                    <label class="input-group-text">IP 服务商</label>
                    <select class="form-select" name="ip_service">
                        <option value="" {{ !$params['ip_service'] ? 'selected' : '' }}>🚫 {{ __('FsLang::panel.option_deactivate') }}</option>
                        @foreach ($pluginParams['extendIp'] as $plugin)
                            <option value="{{ $plugin->unikey }}" {{ $params['ip_service'] == $plugin->unikey ? 'selected' : '' }}> {{ $plugin->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group mb-3">
                    <label class="input-group-text">内容审核服务商</label>
                    <select class="form-select" name="content_review_service">
                        <option value="" {{ !$params['content_review_service'] ? 'selected' : '' }}>🚫 {{ __('FsLang::panel.option_deactivate') }}</option>
                        @foreach ($pluginParams['extendData'] as $plugin)
                            <option value="{{ $plugin->unikey }}" {{ $params['content_review_service'] == $plugin->unikey ? 'selected' : '' }}> {{ $plugin->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!--content list-->
        <div class="row mb-4">
            <label class="col-lg-2 col-form-label text-lg-end">内容列表:</label>
            <div class="col-lg-6">
                <div class="input-group mb-3">
                    <label class="input-group-text">全部帖子</label>
                    <select class="form-select" name="post_list_service">
                        <option value="" {{ !$params['post_list_service'] ? 'selected' : '' }}>{{ __('FsLang::panel.option_default') }}</option>
                        @foreach ($pluginParams['extendData'] as $plugin)
                            <option value="{{ $plugin->unikey }}" {{ $params['post_list_service'] == $plugin->unikey ? 'selected' : '' }}>{{ $plugin->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group mb-3">
                    <label class="input-group-text">关注对象的帖子</label>
                    <select class="form-select" name="post_follow_service">
                        <option value="" {{ !$params['post_follow_service'] ? 'selected' : '' }}>{{ __('FsLang::panel.option_default') }}</option>
                        @foreach ($pluginParams['extendData'] as $plugin)
                            <option value="{{ $plugin->unikey }}" {{ $params['post_follow_service'] == $plugin->unikey ? 'selected' : '' }}>{{ $plugin->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group mb-3">
                    <label class="input-group-text">附近范围的帖子</label>
                    <select class="form-select" name="post_nearby_service">
                        <option value="" {{ !$params['post_nearby_service'] ? 'selected' : '' }}>{{ __('FsLang::panel.option_default') }}</option>
                        @foreach ($pluginParams['extendData'] as $plugin)
                            <option value="{{ $plugin->unikey }}" {{ $params['post_nearby_service'] == $plugin->unikey ? 'selected' : '' }}>{{ $plugin->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 该配置的优先级大于内容类型配置，当配置后，内容类型指定的数据来源将被取代。</div>
        </div>

        <!--content detail-->
        <div class="row mb-4">
            <label class="col-lg-2 col-form-label text-lg-end">内容详情:</label>
            <div class="col-lg-6">
                <div class="input-group mb-3">
                    <label class="input-group-text">帖子详情</label>
                    <select class="form-select" name="post_detail_service">
                        <option value="" {{ !$params['post_detail_service'] ? 'selected' : '' }}>🚫 {{ __('FsLang::panel.option_deactivate') }}</option>
                        @foreach ($pluginParams['extendData'] as $plugin)
                            <option value="{{ $plugin->unikey }}" {{ $params['post_detail_service'] == $plugin->unikey ? 'selected' : '' }}>{{ $plugin->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('FsLang::panel.send_wechat_desc') }}</div>
        </div>

        <!--content search-->
        <div class="row mb-4">
            <label class="col-lg-2 col-form-label text-lg-end">内容搜索:</label>
            <div class="col-lg-6">
                <!--users-->
                <div class="input-group mb-3">
                    <label class="input-group-text">搜索用户</label>
                    <select class="form-select" name="search_users_service">
                        <option value="" {{ !$params['search_users_service'] ? 'selected' : '' }}>{{ __('FsLang::panel.option_default') }}</option>
                        @foreach ($pluginParams['searchUsers'] as $plugin)
                            <option value="{{ $plugin->unikey }}" {{ $params['search_users_service'] == $plugin->unikey ? 'selected' : '' }}>{{ $plugin->name }}</option>
                        @endforeach
                    </select>
                </div>
                <!--groups-->
                <div class="input-group mb-3">
                    <label class="input-group-text">搜索小组</label>
                    <select class="form-select" name="search_groups_service">
                        <option value="" {{ !$params['search_groups_service'] ? 'selected' : '' }}>{{ __('FsLang::panel.option_default') }}</option>
                        @foreach ($pluginParams['searchUsers'] as $plugin)
                            <option value="{{ $plugin->unikey }}" {{ $params['search_groups_service'] == $plugin->unikey ? 'selected' : '' }}>{{ $plugin->name }}</option>
                        @endforeach
                    </select>
                </div>
                <!--hashtags-->
                <div class="input-group mb-3">
                    <label class="input-group-text">搜索话题</label>
                    <select class="form-select" name="search_hashtags_service">
                        <option value="" {{ !$params['search_hashtags_service'] ? 'selected' : '' }}>{{ __('FsLang::panel.option_default') }}</option>
                        @foreach ($pluginParams['searchUsers'] as $plugin)
                            <option value="{{ $plugin->unikey }}" {{ $params['search_hashtags_service'] == $plugin->unikey ? 'selected' : '' }}>{{ $plugin->name }}</option>
                        @endforeach
                    </select>
                </div>
                <!--posts-->
                <div class="input-group mb-3">
                    <label class="input-group-text">搜索帖子</label>
                    <select class="form-select" name="search_posts_service">
                        <option value="" {{ !$params['search_posts_service'] ? 'selected' : '' }}>{{ __('FsLang::panel.option_default') }}</option>
                        @foreach ($pluginParams['searchUsers'] as $plugin)
                            <option value="{{ $plugin->unikey }}" {{ $params['search_posts_service'] == $plugin->unikey ? 'selected' : '' }}>{{ $plugin->name }}</option>
                        @endforeach
                    </select>
                </div>
                <!--comments-->
                <div class="input-group mb-3">
                    <label class="input-group-text">搜索评论</label>
                    <select class="form-select" name="search_comments_service">
                        <option value="" {{ !$params['search_comments_service'] ? 'selected' : '' }}>{{ __('FsLang::panel.option_default') }}</option>
                        @foreach ($pluginParams['searchUsers'] as $plugin)
                            <option value="{{ $plugin->unikey }}" {{ $params['search_comments_service'] == $plugin->unikey ? 'selected' : '' }}>{{ $plugin->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('FsLang::panel.send_wechat_desc') }}</div>
        </div>

        <!--button_save-->
        <div class="row my-3">
            <div class="col-lg-2"></div>
            <div class="col-lg-8">
                <button type="submit" class="btn btn-primary">{{ __('FsLang::panel.button_save') }}</button>
            </div>
        </div>
    </form>
@endsection
