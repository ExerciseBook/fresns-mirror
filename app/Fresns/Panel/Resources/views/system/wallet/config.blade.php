@extends('panel::common.sidebarLayout')

@section('sidebar')
  @include('panel::system.sidebar')
@endsection

@section('content')

  <div class="row mb-4">
    <div class="col-lg-7">
      <h3>钱包设置</h3>
      <p class="text-secondary">钱包为真实货币功能，如需虚拟积分功能请安装相应的插件。</p>
    </div>
    <div class="col-lg-5">
      <div class="input-group mt-2 mb-4 justify-content-lg-end">
        <a class="btn btn-outline-secondary" href="#" role="button">帮助说明</a>
      </div>
    </div>
    <ul class="nav nav-tabs">
      <li class="nav-item"><a class="nav-link active" href="system-wallet.html">功能设置</a></li>
      <li class="nav-item"><a class="nav-link" href="system-wallet-pay.html">充值服务商</a></li>
      <li class="nav-item"><a class="nav-link" href="system-wallet-withdraw.html">提现服务商</a></li>
    </ul>
  </div>
  <!--表单 开始-->
  <form action="{{ route('panel.walletConfigs.update') }}" method="post">
    @csrf
    @method('put')
    <!--功能配置-->
    <div class="row mb-3">
      <label for="account_policies" class="col-lg-2 col-form-label text-lg-end">钱包功能：</label>
      <div class="col-lg-6">
        <div class="input-group mb-3">
          <label class="input-group-text">是否启用</label>
          <div class="form-control bg-white">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="wallet_status" id="wallet_false" value="false" data-bs-toggle="collapse" data-bs-target="#wallet_setting.show" aria-expanded="false" aria-controls="wallet_setting" {{ $params['wallet_status'] == 'false' ? 'checked' : ''}}>
              <label class="form-check-label" for="wallet_false">关闭</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="wallet_status" id="wallet_true" value="true" data-bs-toggle="collapse" data-bs-target="#wallet_setting:not(.show)" aria-expanded="false" aria-controls="wallet_setting" {{ $params['wallet_status'] == 'true' ? 'checked' : ''}}>
              <label class="form-check-label" for="wallet_true">开启</label>
            </div>
          </div>
        </div>
        <div class="collapse {{ $params['wallet_status'] == 'true' ? 'show' : ''}}" id="wallet_setting">
          <div class="input-group mb-3">
            <label class="input-group-text">货币代码</label>
            <select class="form-select" name="wallet_currency_code">
              <option disabled>请选择货币代码</option>
              @foreach($params['currency_codes'] as $code)
              <option value="{{ $code['code'] }}" {{ $params['wallet_currency_code'] == $code['code'] ? 'selected' : ''}}>{{ $code['code'] }} ({{ $code['name'] }}) > {{$code['ctryName'] }}</option>
              @endforeach
            </select>
          </div>
          <div class="input-group mb-3">
            <span class="input-group-text">提现功能</span>
            <div class="form-control bg-white">
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="wallet_withdraw_close" id="withdraw_false" value="false" data-bs-toggle="collapse" data-bs-target="#withdraw_setting.show" aria-expanded="false" aria-controls="withdraw_setting" {{ $params['wallet_withdraw_close'] == 'false' ? 'checked' : ''}}>
                <label class="form-check-label" for="withdraw_false">关闭</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="wallet_withdraw_close" id="withdraw_true" value="true" data-bs-toggle="collapse" data-bs-target="#withdraw_setting:not(.show)" aria-expanded="false" aria-controls="withdraw_setting"  {{ $params['wallet_withdraw_close'] == 'true' ? 'checked' : ''}}>
                <label class="form-check-label" for="withdraw_true">开启</label>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 是否启用法币钱包功能</div>
    </div>
    <!--提现功能配置-->
    <div class="collapse {{ $params['wallet_withdraw_close'] == 'true' ? 'show' : ''}}" id="withdraw_setting">
      <div class="row mb-4">
        <label for="account_policies" class="col-lg-2 col-form-label text-lg-end">提现配置：</label>
        <div class="col-lg-6">
          <div class="input-group mb-3">
            <label class="input-group-text">提现是否审核</label>
            <div class="form-control bg-white">
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="wallet_withdraw_review" id="wallet_cash_review_false" value="false" {{ $params['wallet_withdraw_review'] == 'false' ? 'checked' : '' }}>
                <label class="form-check-label" for="wallet_cash_review_false">不需要审核</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="wallet_withdraw_review" id="wallet_cash_review_true" value="true" {{ $params['wallet_withdraw_review'] == 'true' ? 'checked' : '' }}>
                <label class="form-check-label" for="wallet_cash_review_true">需审核</label>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <label class="input-group-text">提现是否验证实名信息</label>
            <div class="form-control bg-white">
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="wallet_withdraw_verify" id="wallet_cash_verify_false" value="false" {{ $params['wallet_withdraw_verify'] == 'false' ? 'checked' : ''}}>
                <label class="form-check-label" for="wallet_cash_verify_false">不验证</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="wallet_withdraw_verify" id="wallet_cash_verify_true" value="true" {{ $params['wallet_withdraw_verify'] == 'true' ? 'checked' : ''}}>
                <label class="form-check-label" for="wallet_cash_verify_true">需验证</label>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <label class="input-group-text">提现间隔时间</label>
            <input type="text" class="form-control" name="wallet_withdraw_interval_time" value="{{ $params['wallet_withdraw_interval_time'] }}">
            <span class="input-group-text">分钟</span>
          </div>
          <div class="input-group mb-3">
            <label class="input-group-text">提现手续费率</label>
            <input type="text" class="form-control" name="wallet_withdraw_rate" value="{{ $params['wallet_withdraw_rate'] }}">
            <span class="input-group-text">%</span>
          </div>

          <?php $currency = collect($params['currency_codes'])->where('code', $params['wallet_currency_code'])->first() ?>
          <div class="input-group mb-3">
            <label class="input-group-text">单次提现最小金额</label>
            <input type="text" class="form-control" name="wallet_withdraw_min_sum" value="{{ $params['wallet_withdraw_min_sum'] }}">
            <span class="input-group-text">{{ $currency['name'] ?? ''}}</span>
          </div>
          <div class="input-group mb-3">
            <label class="input-group-text">单次提现最大金额</label>
            <input type="text" class="form-control" name="wallet_withdraw_max_sum" value="{{ $params['wallet_withdraw_max_sum'] }} ">
            <span class="input-group-text">{{ $currency['name'] ?? ''}}</span>
          </div>
          <div class="input-group mb-3">
            <label class="input-group-text">每日提现总金额上限</label>
            <input type="text" class="form-control" name="wallet_withdraw_sum_limit" value="{{ $params['wallet_withdraw_sum_limit'] }}">
            <span class="input-group-text">{{ $currency['name'] ?? ''}}</span>
          </div>
        </div>
        <div class="col-lg-4 form-text pt-1"><i class="bi bi-info-circle"></i> 提现相关配置需插件支持，详细请咨询插件开发者。</div>
      </div>
    </div>
    <!--提现功能配置-->

    <!--保存按钮-->
    <div class="row my-3">
      <div class="col-lg-2"></div>
      <div class="col-lg-8">
        <button type="submit" class="btn btn-primary">提交保存</button>
      </div>
    </div>
  </form>

@endsection
