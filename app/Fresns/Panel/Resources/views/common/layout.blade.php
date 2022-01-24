<!doctype html>
<html lang="{{ App::getLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="Fresns" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Fresns {{ __('panel::panel.panelControl') }}</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="{{ @asset('/static/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ @asset('/static/css/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ @asset('/static/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ @asset('/static/css/backend.css') }}">
    @yield('css')
  </head>

  <body>
    @yield('body')

    <div class="fresns-tips">
      @include('panel::common.tips')
    </div>

    <script src="{{ @asset('/static/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ @asset('/static/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ @asset('/static/js/select2.min.js') }}"></script>
    <script src="{{ @asset('/static/js/backend.js') }}"></script>
    @yield('js')
  </body>

</html>
