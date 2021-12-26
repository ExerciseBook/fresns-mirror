@extends('panel::common.guestLayout')

@section('content')
  <main class="form-signin text-center">
    <form class="p-3">
      <img class="mt-3 mb-2" src="{{@asset('/assets/panel/images/fresns-icon.png')}}" alt="Fresns" width="72" height="72">
      <h2 class="mb-5">Fresns {{@trans('panel::panel.panelControl')}}</h2>
      <h4 class="mb-3 fw-normal">语言</h4>
      <select class="form-select mb-5" aria-label=".form-select-lg example">
        <option value="en">English - English</option>
        <option value="code">Español - Spanish</option>
        <option value="code">Français - French</option>
        <option value="code">日本語 - Japanese</option>
        <option value="code">한국어 - Korean</option>
        <option value="code">Русский - Russian</option>
        <option value="code">Português - Portuguese</option>
        <option value="code">Bahasa Indonesia - Indonesian</option>
        <option value="zh-Hans" selected>简体中文 - Chinese (Simplified)</option>
        <option value="zh-Hant">繁體中文 - Chinese (Traditional)</option>
      </select>
      <h4 class="mb-3 fw-normal">登录</h4>
      <div class="form-floating">
        <input type="text" class="form-control rounded-bottom-0" id="account" placeholder="name@example.com">
        <label for="account">账号</label>
      </div>
      <div class="form-floating">
        <input type="password" class="form-control rounded-top-0 border-top-0" id="password" placeholder="Password">
        <label for="password">密码</label>
      </div>
      <a href="dashboard.html" class="w-100 btn btn-lg btn-primary mt-4">进入</a>
      <p class="my-5 text-muted">&copy; 2021 Fresns</p>
    </form>
  </main>
@endsection
