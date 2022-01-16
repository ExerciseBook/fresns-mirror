@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::client.sidebar')
@endsection

@section('content')
<div class="row mb-4 border-bottom">
	<div class="col-lg-7">
		<h3>语言包配置</h3>
		<p class="text-secondary">为多语言客户端配置各项文本的多语言内容。</p>
	</div>
	<div class="col-lg-5">
		<div class="input-group mt-2 mb-4 justify-content-lg-end">
			<a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
		</div>
	</div>
</div>
<!--操作列表-->
<div class="table-responsive">
	<table class="table table-hover align-middle text-nowrap">
		<thead>
			<tr class="table-info">
				<th scope="col">语言标签</th>
				<th scope="col">语言名称</th>
				<th scope="col">书写方向</th>
				<th scope="col">启用状态</th>
				<th scope="col">操作</th>
			</tr>
		</thead>
		<tbody>
      @foreach($optionalLanguages as $lang)
        <?php
          $langName = $lang['langName'];
          $langWriting = $lang['writingDirection'];
          $isEnable = $lang['isEnable'];
          if ($lang['areaCode']) {
            $langName .= '('. optional($areaCodes->where('code', $lang['areaCode'])->first())['localName'] .')';
          }
        ?>
			<tr>
        <td>
          {{ $lang['langTag'] }}
          @if($lang['langTag'] == $defaultLanguage)
            <i class="bi bi-info-circle text-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="默认语言" aria-label="默认语言"></i>
          @endif
        </td>
        <td>{{ $langName }}</td>
				<td>{{ $langWriting}}</td>
				<td>
          @if ($isEnable)
            <i class="bi bi-check-lg text-success"></i>
          @else
            <i class="bi bi-dash-lg text-secondary"></i>
          @endif
        </td>
				<td>
					<a class="btn btn-outline-primary btn-sm text-decoration-none" href="{{ route('panel.languagePack.edit', ['langTag' => $lang['langTag']]) }}" role="button">配置语言包</a>
				</td>
			</tr>
      @endforeach
		</tbody>
	</table>
</div>
<!--操作列表 结束-->

@endsection
