@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::operation.sidebar')
@endsection

@section('content')
  <div class="row mb-4 border-bottom">
    <div class="col-lg-7">
      <h3>过滤配置</h3>
      <p class="text-secondary">根据运营需求配置处理词，过滤交互内容。</p>
    </div>
    <div class="col-lg-5">
      <div class="input-group mt-2 mb-4 justify-content-lg-end">
        <button class="btn btn-primary" type="button"
          data-bs-toggle="modal"
          data-action="{{ route('panel.stopWords.store') }}"
          data-bs-target="#createStopWordModal"><i class="bi bi-plus-circle-dotted"></i> 新增处理词</button>
        <a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
      </div>
    </div>
  </div>
  <div class="row mb-3">
    <div class="col-lg-4">
      <form action="{{ route('panel.stopWords.index') }}">
        <div class="input-group">
          <input type="text" class="form-control" name="keyword" value="{{ $keyword }}" placeholder="搜索处理词">
          <button class="btn btn-outline-secondary" type="submit">搜索</button>
        </div>
      </form>
    </div>
    <div class="col-lg-8">
      <div class="input-group justify-content-lg-end">
        <button class="btn btn-outline-info" type="button">批量导入</button>
        <button class="btn btn-outline-info" type="button">批量导出</button>
      </div>
    </div>
  </div>
  <!--操作列表-->
  <div class="table-responsive">
    <table class="table table-hover align-middle text-nowrap">
      <thead>
        <tr class="table-info">
          <th scope="col">处理词</th>
          <th scope="col">内容处理方式（帖子和评论）</th>
          <th scope="col">用户处理方式（昵称和简介）</th>
          <th scope="col">消息处理方式（会话私信）</th>
          <th scope="col">处理词替换词</th>
          <th scope="col">操作</th>
        </tr>
      </thead>
      <tbody>
        @foreach($words as $word)
          <tr>
            <td><input type="text" class="form-control" disabled value="{{ $word->word }}"></td>
            <td>{{ $contentModeLabels[$word->content_mode] ?? ''}}</td>
            <td>{{ $memberModeLabels[$word->member_mode] ?? ''}}</td>
            <td>{{ $dialogModeLabels[$word->dialog_mode] ?? '' }}</td>
            <td><input type="text" class="form-control" disabled value="{{ $word->replace_word }}"></td>
            <td>
              <form action="{{ route('panel.stopWords.destroy', ['stopWord' => $word->id])}}" method="post">
                @csrf
                @method('delete')
                <button type="button" class="btn btn-outline-primary btn-sm"
                                      data-bs-toggle="modal"
                                      data-action="{{ route('panel.stopWords.update', ['stopWord' => $word->id]) }}"
                                      data-params="{{ $word->toJson() }}"
                                      data-bs-target="#createStopWordModal">修改</button>
                <button type="submit" class="btn btn-link link-danger ms-1 fresns-link fs-7">删除</button>
              </form>
            </td>
          </tr>
        @endforeach
        </tr>
      </tbody>
    </table>
  </div>
  {{ $words->links() }}

  <!-- Modal -->
  <div class="modal fade" id="createStopWordModal" tabindex="-1" aria-labelledby="createStopWordModal" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">新建处理词</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action=""  method="post">
            @csrf
            @method('post')
            <div class="input-group mb-3">
              <span class="input-group-text">处理词</span>
              <input type="text" name="word" class="form-control">
            </div>
            <div class="input-group mb-3">
              <span class="input-group-text">替换词</span>
              <input type="text" name="replace_word" class="form-control">
            </div>
            <div class="input-group mb-3">
              <label class="input-group-text">内容处理方式</label>
              <select class="form-select" aria-label="Default select example" name="content_mode">
                @foreach($contentModeLabels as $key => $label)
                  <option value="{{ $key }}" >{{ $label }}</option>
                @endforeach
              </select>
            </div>
            <div class="input-group mb-3">
              <label class="input-group-text">用户处理方式</label>
              <select class="form-select" aria-label="Default select example" name="member_mode">
                @foreach($memberModeLabels as $key => $label)
                  <option value="{{ $key }}" >{{ $label }}</option>
                @endforeach
              </select>
            </div>
            <div class="input-group mb-3">
              <label class="input-group-text">消息处理方式</label>
              <select class="form-select" aria-label="Default select example" name="dialog_mode">
                @foreach($dialogModeLabels as $key => $label)
                <option value="{{ $key }}" >{{ $label }}</option>
                @endforeach
              </select>
            </div>
            <button type="submit" class="btn btn-primary">确认提交</button>
          </form>
        </div>
      </div>
    </div>
  </div>

@endsection
