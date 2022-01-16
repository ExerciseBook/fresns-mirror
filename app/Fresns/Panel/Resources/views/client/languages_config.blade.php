@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::client.sidebar')
@endsection

@section('content')

<div class="row mb-4 border-bottom">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="{{ route('panel.clientMenus.index')}}">客户端</a></li>
			<li class="breadcrumb-item"><a href="{{ route('panel.languagePack.index')}}">语言包配置</a></li>
      <li class="breadcrumb-item active" aria-current="page">
        <?php
          $lang = $optionalLanguages->where('langTag', $langTag)->first() ?: [];
          $langName = $lang['langName'] ?? '';
          if ($lang['areaCode']) {
            $langName .= '('. optional($areaCodes->where('code', $lang['areaCode'])->first())['localName'] .')';
          }
        ?>

        {{ $langTag }}
        <span class="badge bg-secondary ms-2">{{ $langName }}</span>
      </li>
		</ol>
	</nav>
</div>
<!--表单 开始-->
<form action="{{ route('panel.languagePack.update', ['langTag' => $langTag])}}" method="post">
  @csrf
  @method('put')
	<div class="table-responsive">
		<table class="table table-hover align-middle text-nowrap">
			<thead>
				<tr class="table-info">
					<th scope="col">标识名</th>
					<th scope="col">默认语言内容 <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="默认语言配置的内容，供参考"></i></th>
					<th scope="col">当前语言内容</th>
					<th scope="col" style="width:6rem;">操作</th>
				</tr>
			</thead>
			<tbody class="lang-pack-box">
        @foreach($languageKeys as $key)
				<tr>
					<td><input type="text" class="form-control" name="keys[]" value="{{ $key['name'] }}" readonly></td>
					<td><input type="text" class="form-control" value="{{ $defaultLanguages[$key['name']] ?? '' }}" readonly></td>
					<td><input type="text" class="form-control" name="contents[]" value="{{ $languages[$key['name']] ?? ''}}"></td>
					<td>
            @if ($key['canDelete'])
              <button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7 delete-lang-pack">删除</button>
            @endif
          </td>
				</tr>
        @endforeach
			</tbody>
		</table>
    <div class="text-center">
      <button class="btn btn-outline-success btn-sm px-3" id="addLangPack" type="button"><i class="bi bi-plus-circle-dotted"></i> 新增</button>
      <button class="btn btn-primary btn-sm" type="submit">保存全部</button>
    </div>
	</div>
</form>
<!--表单 结束-->


<template id="languagePackTemplate">
  <tr>
    <td><input type="text" class="form-control" name="keys[]" value=""></td>
    <td><input type="text" class="form-control" value="" readonly></td>
    <td><input type="text" class="form-control" name="contents[]" value=""></td>
    <td>
        <button type="button" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button>
    </td>
  </tr>
</template>

@endsection
