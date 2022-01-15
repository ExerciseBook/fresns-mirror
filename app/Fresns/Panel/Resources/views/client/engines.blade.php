@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::client.sidebar')
@endsection

@section('content')


<div class="row mb-4 border-bottom">
	<div class="col-lg-7">
		<h3>网站引擎</h3>
		<p class="text-secondary">选用不同的引擎，实现更个性化功能和服务。</p>
	</div>
	<div class="col-lg-5">
		<div class="input-group mt-2 mb-4 justify-content-lg-end">
			<a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
		</div>
	</div>
</div>
<!--列表-->
<div class="table-responsive">
	<table class="table table-hover align-middle text-nowrap">
		<thead>
			<tr class="table-info">
				<th scope="col">引擎 <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="如果你希望独立部署网站，或者是不需要网站的移动应用。只需“停用”或“卸载”网站引擎，这样 Fresns 将只是一个 API 和插件运行的后端系统。"></i></th>
				<th scope="col">开发者</th>
				<th scope="col">主题模板</th>
				<th scope="col" class="text-center">操作 <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="支持启用多个引擎，只需要彼此路径不冲突即可，详情请咨询引擎开发者。"></i></th>
			</tr>
		</thead>
		<tbody>
      @foreach($engines as $engine)
			<tr>
        <th scope="row" class="py-3">{{ $engine->name }}<span class="badge bg-secondary plugin-version">{{ $engine->version }}</span>
          @if ($engine->is_upgrade)
          <a href="{{ route('panel.dashboard')}}"><span class="badge rounded-pill bg-danger plugin-version">有新版</span></a>
          @endif
        </th>
        <td><a href="#" class="link-info fresns-link fs-7">{{ $engine->author }}</a></td>
				<td>
					<span class="badge bg-light text-dark"><i class="bi bi-laptop"></i> Demo</span>
					<span class="badge bg-light text-dark"><i class="bi bi-phone"></i> Demo</span>
				</td>
				<td class="text-end">
          @if ($engine->is_enable)
            <button type="button"
                    data-action="{{ route('panel.plugins.update', ['plugin' => $engine->id]) }}"
                    data-enable="0"
                    class="btn btn-outline-success btn-sm plugin-update"
                    title="点击停用">已启用</button>
            <button type="button" class="btn btn-warning btn-sm"
                                  data-bs-toggle="modal"
                                  data-action="{{ route('panel.plugins.engines.theme.update', ['engine' => $engine->id])}}"
                                  data-params="{{ $engine->toJson() }}"
                                  data-pc_plugin="{{ optional($configs->where('item_key', $engine->unikey.'_Pc')->first())->item_value }}"
                                  data-mobile_plugin="{{ optional($configs->where('item_key', $engine->unikey.'_Mobile')->first())->item_value }}"
                                  data-bs-target="#themeSetting">关联主题</button>
            @if ($engine->setting_path)
              <a href="{{ url($engine->setting_path) }}" class="btn btn-primary btn-sm" title="进入插件设置">设置</a>
            @endif
          @else
            <button type="button"
              class="btn btn-outline-secondary btn-sm plugin-update"
              data-action="{{ route('panel.plugins.update', ['plugin' => $engine->id]) }}"
              data-enable="1"
              title="点击启用">启用</button>
            <button type="button"
                    data-action="{{ route('panel.plugins.destroy', ['plugin' => $engine->id]) }}"
                    class="btn btn-link btn-sm ms-2 text-danger fresns-link uninstall-plugin"
              >卸载</button>
          @endif
				</td>
			</tr>
      @endforeach
		</tbody>
	</table>
</div>
<!--列表 结束-->
<!-- Modal -->
<div class="modal fade" id="themeSetting" tabindex="-1" aria-labelledby="themeSetting" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">关联主题模板</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<!--网站设置 开始-->
				<form method="post">
          @csrf
          @method('put')
					<div class="form-floating mb-3">
						<select class="form-select" id="pcTheme" aria-label="Floating label select example" name="">
							<option value="" selected>不使用</option>
              @foreach($themes as $theme)
                <option value="{{ $theme->unikey }}">{{ $theme->name }}</option>
              @endforeach
						</select>
						<label for="PCtheme"><i class="bi bi-laptop"></i> 电脑端主题</label>
					</div>
					<div class="form-floating mb-4">
						<select class="form-select" id="mobileTheme" aria-label="Floating label select example">
							<option value="" selected>不使用</option>
              @foreach($themes as $theme)
                <option value="{{ $theme->unikey }}">{{ $theme->name }}</option>
              @endforeach
						</select>
						<label for="mobileTheme"><i class="bi bi-phone"></i> 手机端主题</label>
					</div>
					<div class="text-center">
						<button type="submit" class="btn btn-primary">保存</button>
					</div>
				</form>
				<!--网站设置 结束-->
			</div>
		</div>
	</div>
</div>

@endsection
