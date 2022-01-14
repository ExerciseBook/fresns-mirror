@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::plugin.sidebar')
@endsection

@section('content')
<div class="row mb-4">
	<div class="col-lg-7">
		<h3>插件管理</h3>
		<p class="text-secondary">灵活的功能，强大的扩展，助您自由发挥心中所想。</p>
	</div>
	<div class="col-lg-5">
		<div class="input-group mt-2 mb-4 justify-content-lg-end">
			<a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
		</div>
	</div>
	<ul class="nav nav-tabs" id="myTab" role="tablist">
		<li class="nav-item" role="presentation">
      <a href="{{ route('panel.plugins.index') }}" class="nav-link {{ is_null($isEnable) ? 'active' : '' }}" type="button">全部</a>
		</li>
		<li class="nav-item" role="presentation">
      <a href="{{ route('panel.plugins.index', ['is_enable' => 1]) }}" class="nav-link {{ $isEnable == 1 ? 'active' : '' }}" type="button">已启用 ({{ $enableCount }})</a>
		</li>
		<li class="nav-item" role="presentation">
      <a href="{{ route('panel.plugins.index', ['is_enable' => 0]) }}" class="nav-link {{ (!is_null($isEnable) && $isEnable == 0) ? 'active' : '' }}" type="button">未启用 ({{ $disableCount }})</a>
		</li>
	</ul>
</div>
<!--插件列表-->
<div class="table-responsive">
	<table class="table table-hover align-middle text-nowrap fs-7">
		<thead>
			<tr class="table-info fs-6">
				<th scope="col">名称</th>
				<th scope="col">描述</th>
				<th scope="col">开发者</th>
				<th scope="col">操作</th>
			</tr>
		</thead>
		<tbody>
      @foreach($plugins as $plugin)
			<tr>
				<td class="py-3">
          @if ($plugin->image)
            <img src="{{ $plugin->image }}" class="me-2" width="44" height="44">
          @endif
          <span class="fs-6">{{ $plugin->name }}</span>
          <span class="badge bg-secondary fs-9">{{ $plugin->version }}</span>
					{{--<a href="dashboard.html" class="badge rounded-pill bg-danger fs-9 fresns-link">有新版</a>--}}
				</td>
        <td>{{ $plugin->description }}</td>
        <td><a href="{{ $plugin->author_link }}" class="link-info fresns-link fs-7">{{ $plugin->author }}</a></td>
				<td>
          @if ($plugin->is_enable)
            <button type="button"
                    data-action="{{ route('panel.plugins.update', ['plugin' => $plugin->id]) }}"
                    data-enable="0"
                    class="btn btn-outline-success btn-sm plugin-update"
                    title="点击停用">已启用</button>
            @if ($plugin->setting_path)
              <a href="{{ url($plugin->setting_path) }}" class="btn btn-primary btn-sm" title="进入插件设置">设置</a>
            @endif
          @else
            <button type="button"
              class="btn btn-outline-secondary btn-sm plugin-update"
              data-action="{{ route('panel.plugins.update', ['plugin' => $plugin->id]) }}"
              data-enable="1"
              title="点击启用">启用</button>
            <button type="button"
                    data-action="{{ route('panel.plugins.destroy', ['plugin' => $plugin->id]) }}"
                    class="btn btn-link btn-sm ms-2 text-danger fresns-link uninstall-plugin"
              >卸载</button>
          @endif
				</td>
			</tr>
      @endforeach
		</tbody>
	</table>
</div>
<!--插件列表 结束-->

@endsection
