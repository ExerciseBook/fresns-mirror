<!doctype html>
<html lang="zh-Hans">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="Fresns" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fresns {{__('panel::panel.panelControl')}}</title>
    <link rel="stylesheet" href="{{@asset('/assets/panel/css/bootstrap.min.css')}}">
    <!-- 语言 writingDirection 为 rtl 时，将上面 css 替换为这个
    <link rel="stylesheet" href="../assets/css/bootstrap.rtl.min.css">
    -->
    <link rel="stylesheet" href="{{@asset('/assets/panel/css/bootstrap-icons.css')}}">
    <link rel="stylesheet" href="{{@asset('/assets/panel/css/backend.css')}}">
</head>

<body>
    @yield('content')
    <script src="{{@asset('/assets/panel/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{@asset('/assets/panel/js/backend.js')}}"></script>
</body>

</html>
