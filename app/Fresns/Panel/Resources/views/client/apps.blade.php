@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::client.sidebar')
@endsection

@section('content')

<div class="row mb-4 border-bottom">
	<div class="col-lg-7">
		<h3>移动应用</h3>
		<p class="text-secondary">选装不同的应用程序，创造不一样的运营场景和应用模式。</p>
	</div>
	<div class="col-lg-5">
		<div class="input-group mt-2 mb-4 justify-content-lg-end">
			<a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
		</div>
	</div>
</div>
<!--应用 开始-->
<div class="row">
	<!--应用列表 开始-->
  @foreach($apps as $app)
	<div class="col-sm-6 col-xl-3 mb-4">
		<div class="card">
      <div class="position-relative">
        <img src="{{ $app->image }}" class="card-img-top" alt="网站主题">
        @if ( $app->is_upgrade)
          <div class="position-absolute top-0 start-100 translate-middle"><a href="{{ route('panel.dashboard') }}"><span class="badge rounded-pill bg-danger">更新</span></a></div>
        @endif
			</div>
			<div class="card-body">
        <h5 class="text-nowrap overflow-hidden">Demo <span class="badge bg-secondary align-middle fs-9">{{ $app->version }}</span></h5>
        <p class="card-text text-height">{{ $app->description }}</p>
				<div>
          @if ($app->is_enable)
            <button type="button"
                    data-action="{{ route('panel.plugins.update', ['plugin' => $app->id]) }}"
                    data-enable="0"
                    class="btn btn-outline-success btn-sm plugin-update"
                    title="点击停用">已启用</button>
            @if ($app->setting_path)
              <a href="{{ url($app->setting_path) }}" class="btn btn-primary btn-sm" title="进入插件设置">设置</a>
            @endif
          @else
            <button type="button"
                    class="btn btn-outline-secondary btn-sm plugin-update"
                    data-action="{{ route('panel.plugins.update', ['plugin' => $app->id]) }}"
                    data-enable="1"
                    title="点击启用">启用</button>
            <button type="button"
                    data-action="{{ route('panel.plugins.destroy', ['plugin' => $app->id]) }}"
                    class="btn btn-link btn-sm ms-2 text-danger fresns-link uninstall-plugin"
                    >卸载</button>
                  @endif
				</div>
			</div>
			<div class="card-footer fs-8">开发者：<a href="#" class="link-info fresns-link">唐杰</a></div>
		</div>
	</div>
  @endforeach
</div>
<!--应用结束-->


@endsection
