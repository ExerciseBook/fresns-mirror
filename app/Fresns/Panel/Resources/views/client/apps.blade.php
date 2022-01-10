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
	<div class="col-sm-6 col-xl-3 mb-4">
		<div class="card">
			<div class="position-relative">
				<img src="../assets/images/temp/screenshot.png" class="card-img-top" alt="网站主题">
				<div class="position-absolute top-0 start-100 translate-middle"><a href="dashboard.html"><span class="badge rounded-pill bg-danger">更新</span></a></div>
			</div>
			<div class="card-body">
				<h5 class="text-nowrap overflow-hidden">Fresns for iOS <span class="badge bg-secondary align-middle fs-9">1.0.0</span></h5>
				<p class="card-text text-height">Fresns 官方开发的 iOS 端 App</p>
				<div>
					<button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="点击停用">已启用</button>
					<a href="iframe.html" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="进入主题设置">设置</a>
				</div>
			</div>
			<div class="card-footer fs-8">开发者：<a href="#" class="link-info fresns-link">唐杰</a></div>
		</div>
	</div>
	<!--分隔线-->
	<div class="col-sm-6 col-xl-3 mb-4">
		<div class="card">
			<div class="position-relative">
				<img src="../assets/images/temp/screenshot.png" class="card-img-top" alt="网站主题">
			</div>
			<div class="card-body">
				<h5 class="text-nowrap overflow-hidden">Fresns for Android <span class="badge bg-secondary align-middle fs-9">1.0.0</span></h5>
				<p class="card-text text-height">Fresns 官方开发的 Android 端 App</p>
				<div>
					<button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="点击启用">启用</button>
					<button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="点击卸载">卸载</button>
				</div>
			</div>
			<div class="card-footer fs-8">开发者：<a href="#" class="link-info fresns-link">唐杰</a></div>
		</div>
	</div>
	<!--应用列表 结束-->
</div>
<!--应用结束-->


@endsection
