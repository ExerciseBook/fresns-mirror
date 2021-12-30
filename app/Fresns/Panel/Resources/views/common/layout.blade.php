<!doctype html>
<html lang="{{ App::getLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="Fresns" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fresns {{ __('panel::panel.panelControl') }}</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="{{ @asset('/assets/panel/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ @asset('/assets/panel/css/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ @asset('/assets/panel/css/backend.css') }}">
    @yield('css')
  </head>

  <body>
    @include('panel::common.header')
    <main>
      @yield('content')
    </main>

    @include('panel::common.footer')

    <script src="{{ @asset('/assets/panel/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ @asset('/assets/panel/js/backend.js') }}"></script>
    @yield('js')
  </body>

</html>
