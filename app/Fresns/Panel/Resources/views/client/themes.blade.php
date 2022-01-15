@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::client.sidebar')
@endsection

@section('content')

<!--设置区域 开始-->
<div class="row mb-4 border-bottom">
	<div class="col-lg-7">
		<h3>主题模板</h3>
		<p class="text-secondary">选用不同的主题，实现更个性化的风格和交互。</p>
	</div>
	<div class="col-lg-5">
		<div class="input-group mt-2 mb-4 justify-content-lg-end">
			<a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
		</div>
	</div>
</div>
<!--主题 开始-->
<div class="row">
	<!--主题列表 开始-->
  @foreach($themes as $theme)
    <div class="col-sm-6 col-xl-3 mb-4">
      <div class="card">
        <div class="position-relative">
          <img src="{{ $theme->image }}" class="card-img-top" alt="网站主题">
          @if ($theme->is_upgrade)
          <div class="position-absolute top-0 start-100 translate-middle"><a href="{{ route('panel.dashboard') }}"><span class="badge rounded-pill bg-danger">更新</span></a></div>
          @endif
        </div>
        <div class="card-body">
          <h5 class="text-nowrap overflow-hidden">Demo <span class="badge bg-secondary align-middle fs-9">{{ $theme->version }}</span></h5>
          <p class="card-text text-height">{{ $theme->description }}</p>
          <div>
            @if ($theme->is_enable)
              <button type="button"
                      data-action="{{ route('panel.plugins.update', ['plugin' => $theme->id]) }}"
                      data-enable="0"
                      class="btn btn-outline-success btn-sm plugin-update"
                      title="点击停用">已启用</button>
              @if ($theme->setting_path)
                <a href="{{ url($theme->setting_path) }}" class="btn btn-primary btn-sm" title="进入插件设置">设置</a>
              @endif
            @else
              <button type="button"
                      class="btn btn-outline-secondary btn-sm plugin-update"
                      data-action="{{ route('panel.plugins.update', ['plugin' => $theme->id]) }}"
                      data-enable="1"
                      title="点击启用">启用</button>
              <button type="button"
                      data-action="{{ route('panel.plugins.destroy', ['plugin' => $theme->id]) }}"
                      class="btn btn-link btn-sm ms-2 text-danger fresns-link uninstall-plugin"
                      >卸载</button>
                    @endif
          </div>
        </div>
        <div class="card-footer fs-8">开发者：<a href="#" class="link-info fresns-link">{{ $theme->author }}</a></div>
      </div>
    </div>
  @endforeach
	<!--主题列表 结束-->
</div>
<!--主题结束-->

@endsection
