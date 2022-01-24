@extends('panel::common.guestLayout')

@section('content')
  <main class="form-signin text-center">
    <form method="post" class="p-3" action="{{route('panel.login')}}">
      @csrf
      <img class="mt-3 mb-2" src="{{ @asset('/static/images/fresns-icon.png') }}" alt="Fresns" width="72" height="72">
      <h2 class="mb-5">Fresns {{ __('panel::panel.panelControl') }}</h2>
      <h4 class="mb-3 fw-normal">{{ __('panel::panel.language') }}</h4>
      <select class="form-select mb-5 change-lang" aria-label=".form-select-lg example">
        @foreach($langs as $code => $lang)
          <option value="{{$code}}" @if ($code == $locale) selected @endif>{{$lang}}</option>
        @endforeach
      </select>
      <h4 class="mb-3 fw-normal">{{ __('panel::panel.login') }}</h4>
      <div class="form-floating">
        <input type="text" class="form-control rounded-bottom-0" name="username" value="{{ old('email') }}" placeholder="name@example.com">
        <label for="account">{{ __('panel::panel.account') }}</label>
      </div>
      <div class="form-floating">
        <input type="password" class="form-control rounded-top-0 border-top-0" name="password" placeholder="Password">
        <label for="password">{{ __('panel::panel.password') }}</label>
      </div>
      <button type="submit" class="w-100 btn btn-lg btn-primary mt-4">{{ __('panel::panel.enter') }}</button>
      <p class="my-5 text-muted">&copy; 2021 Fresns</p>
    </form>
  </main>
@endsection

@section('js')
  <script src="{{@asset('/static/js/jquery-3.6.0.min.js')}}"></script>

  <script>
    $('.change-lang').change(function(){
      var lang = $(this).val();
      let url = new URL(window.location.href);
      url.searchParams.set('lang', lang);
      window.location.href = url.href;
    });
  </script>
@endsection
