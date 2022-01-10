@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::system.sidebar')
@endsection

@section('content')

  <div class="row mb-4">
    <div class="col-lg-7">
      <h3>政策设置</h3>
      <p class="text-secondary">根据目标市场所在国的相关数据法律条例配置功能。</p>
    </div>
    <div class="col-lg-5">
      <div class="input-group mt-2 mb-4 justify-content-lg-end">
        <a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
      </div>
    </div>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="function-tab" data-bs-toggle="tab" data-bs-target="#function" type="button" role="tab" aria-controls="function" aria-selected="true">政策功能</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="policies-tab" data-bs-toggle="tab" data-bs-target="#policies" type="button" role="tab" aria-controls="policies" aria-selected="false">政策内容</button>
      </li>
    </ul>
  </div>
  <!--操作列表-->
  <div class="tab-content table-responsive" id="myTabContent">
    <div class="tab-pane fade show active" id="function" role="tabpanel" aria-labelledby="function-tab">
      <form action="{{ route('panel.policy.update')}}" method="post">
        @csrf
        @method('put')
        <div class="row mb-3">
          <label for="delete_account" class="col-lg-2 col-form-label text-lg-end">服务条款：</label>
          <div class="col-lg-6 pt-2">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="account_terms_close" id="account_terms_false" value="false" {{ $params['account_terms_close'] == 'true' ? 'checked' : ''}}>
              <label class="form-check-label" for="account_terms_false">隐藏</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="account_terms_close" id="account_terms_true" value="true" {{ $params['account_terms_close'] == 'false' ? 'checked' : ''}}>
              <label class="form-check-label" for="account_terms_true">显示</label>
            </div>
          </div>
        </div>
        <div class="row mb-3">
          <label for="delete_account" class="col-lg-2 col-form-label text-lg-end">隐私权政策：</label>
          <div class="col-lg-6 pt-2">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="account_privacy_close" id="account_privacy_false" value="false" {{ $params['account_privacy_close'] == 'true' ? 'checked' : ''}}>
              <label class="form-check-label" for="account_privacy_false">隐藏</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="account_privacy_close" id="account_privacy_true" value="true" {{ $params['account_privacy_close'] == 'false' ? 'checked' : ''}}>
              <label class="form-check-label" for="account_privacy_true">显示</label>
            </div>
          </div>
        </div>
        <div class="row mb-3">
          <label for="delete_account" class="col-lg-2 col-form-label text-lg-end">Cookie 政策：</label>
          <div class="col-lg-6 pt-2">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="account_cookie_close" id="account_cookie_false" value="false" {{ $params['account_cookie_close'] == 'true' ? 'checked' : ''}}>
              <label class="form-check-label" for="account_cookie_false">隐藏</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="account_cookie_close" id="account_cookie_true" value="true" {{ $params['account_cookie_close'] == 'false' ? 'checked' : ''}}>
              <label class="form-check-label" for="account_cookie_true">显示</label>
            </div>
          </div>
        </div>
        <div class="row mb-3">
          <label for="delete_account" class="col-lg-2 col-form-label text-lg-end">注销说明：</label>
          <div class="col-lg-6 pt-2">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="account_delete_close" id="account_delete_false" value="false" {{ $params['account_delete_close'] == 'true' ? 'checked' : ''}}>
              <label class="form-check-label" for="account_delete_false">隐藏</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="account_delete_close" id="account_delete_true" value="true" {{ $params['account_delete_close'] == 'false' ? 'checked' : ''}}>
              <label class="form-check-label" for="account_delete_true">显示</label>
            </div>
          </div>
        </div>
        <div class="row mb-3">
          <label for="delete_account" class="col-lg-2 col-form-label text-lg-end">注销功能：</label>
          <div class="col-lg-6 pt-2">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="delete_account" id="delete_account" value="0" data-bs-toggle="collapse" data-bs-target="#delete_account_todo_setting.show" aria-expanded="false" aria-controls="delete_account_todo_setting" {{ $params['delete_account'] == 0 ? 'checked' : ''}}>
              <label class="form-check-label" for="delete_account">不启用注销功能</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="delete_account" id="delete_account_1" value="1" data-bs-toggle="collapse" data-bs-target="#delete_account_todo_setting:not(.show)" aria-expanded="false" aria-controls="delete_account_todo_setting" {{ $params['delete_account'] == 1 ? 'checked' : ''}}>
              <label class="form-check-label" for="delete_account_1">软注销</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="delete_account" id="delete_account_2" value="2" data-bs-toggle="collapse" data-bs-target="#delete_account_todo_setting:not(.show)" aria-expanded="false" aria-controls="delete_account_todo_setting" {{ $params['delete_account'] == 2 ? 'checked' : ''}}>
              <label class="form-check-label" for="delete_account_2">硬注销</label>
            </div>
            <div class="collapse {{ $params['delete_account'] ? 'show' : ''}}" id="delete_account_todo_setting">
              <div class="input-group mt-2">
                <span class="input-group-text">注销缓冲期</span>
                <input type="number" class="form-control input-number" name="delete_account_todo" value="{{ $params['delete_account_todo'] }}">
                <span class="input-group-text">天</span>
              </div>
              <div class="form-text">用户在缓冲期天数内可以撤销注销（恢复账号），到期未撤销将执行注销流程。</div>
              <div class="form-text"><i class="bi bi-info-circle"></i> 软注销：仅在数据库中标注账号为注销，并不真实删除数据。</div>
              <div class="form-text"><i class="bi bi-info-circle"></i> 硬注销：物理删除数据，用户资料和发表的内容将会被真实删除。</div>
            </div>
          </div>
        </div>
        <!--保存按钮-->
        <div class="row my-3">
          <div class="col-lg-2"></div>
          <div class="col-lg-6">
            <button type="submit" class="btn btn-primary">提交保存</button>
          </div>
        </div>
      </form>
    </div>

    <div class="tab-pane fade" id="policies" role="tabpanel" aria-labelledby="policies-tab">
      <table class="table table-hover align-middle text-nowrap">
        <thead>
          <tr class="table-info">
            <th scope="col">语言标签</th>
            <th scope="col">语言名称</th>
            <th scope="col">服务条款</th>
            <th scope="col">隐私权政策</th>
            <th scope="col">Cookie 政策</th>
            <th scope="col">注销说明</th>
          </tr>
        </thead>
        <tbody>
          @foreach($optionalLanguages as $lang)
          <?php
            $langName = $lang['langName'];
            if ($lang['areaCode']) {
              $langName .= '('. optional($areaCodes->where('code', $lang['areaCode'])->first())['localName'] .')';
            }
          ?>
          <tr>
            <td>{{ $lang['langTag'] }}</td>
            <td>{{ $langName }}</td>
            <td><button type="button" class="btn btn-outline-primary btn-sm"
                                      data-bs-toggle="modal"
                                      data-bs-target="#updateLanguage"
                                      data-action="{{ route('panel.languages.update', ['itemKey' => 'account_terms'] )}}"
                                      data-lang_tag_desc="{{ $langName }}"
                                      data-lang_tag="{{ $lang['langTag'] }}"
                                      data-content="{{ $langParams['account_terms'][$lang['langTag']] ?? '' }}"
                >编辑</button></td>
            <td><button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateLanguage">编辑</button></td>
            <td><button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateLanguage">编辑</button></td>
            <td><button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateLanguage">编辑</button></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="updateLanguage" tabindex="-1" aria-labelledby="createModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">服务条款</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form method="post">
            @csrf
            @method('put')
            <input type="hidden" name="lang_tag">
            <div class="form-floating">
              <textarea class="form-control" placeholder="Leave a comment here" id="floatingTextarea2" name="content" style="height: 150px"></textarea>
              <label for="floatingTextarea2" class="lang-label"></label>
            </div>
            <div class="form-text">内容支持 Markdown 语法，但是输入框不支持预览，请保存后到前端查看效果。</div>
            <button type="submit" class="btn btn-primary mt-3">保存</button>
          </form>
        </div>
      </div>
    </div>
  </div>

@endsection
