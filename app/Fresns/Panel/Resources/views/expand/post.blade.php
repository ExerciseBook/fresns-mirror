@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::expand.sidebar')
@endsection

@section('content')

<div class="row mb-5 border-bottom">
	<div class="col-lg-9">
		<h3>帖子详情扩展</h3>
		<p class="text-secondary">功能与「内容类型扩展」数据来源功能一样，是否将请求分发给插件处理，由插件决定数据逻辑。</p>
	</div>
	<div class="col-lg-3">
		<div class="input-group mt-2 mb-4 justify-content-lg-end">
			<a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
		</div>
	</div>
</div>
<!--操作列表-->
<form action="{{ route('panel.expandPost.update','post_detail_service') }}" method="post">
  @csrf
  @method('put')
	<div class="row mb-5">
		<label class="col-lg-2 col-form-label text-lg-end">关联插件：</label>
		<div class="col-lg-6">
			<select class="form-select" id="post_editor" name="plugin_unikey">
				@foreach($pluginParams['restful'] as $plugin)
				  <option value="{{ $plugin->unikey }}" @if($pluginUsage) {{$pluginUsage->plugin_unikey == $plugin->unikey ? 'selected' : '' }} @endif>{{ $plugin->name }}</option>
				@endforeach
			</select>
		</div>
	</div>
	<!--保存按钮-->
	<div class="row mt-5">
		<div class="col-lg-2"></div>
		<div class="col-lg-6">
			<button type="submit" class="btn btn-primary">提交保存</button>
		</div>
	</div>
</form>
<!--操作列表 结束-->


@endsection
