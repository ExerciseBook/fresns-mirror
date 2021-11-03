<!doctype html>
<html lang="{{ App::getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="Fresns" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fresns &rsaquo; @lang('install.title')</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/static/css/bootstrap.min.css">
    <link rel="stylesheet" href="/static/css/bootstrap-icons.css">
</head>

<body>

    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <div class="navbar-brand">
                    <img src="/static/images/fresns-logo.png" alt="Fresns" height="30" class="d-inline-block align-text-top">
                    <span class="ms-2">@lang('install.desc')</span>
                </div>
            </div>
        </nav>
    </header>

    <main class="container">
        <div class="card mx-auto my-5" style="max-width:800px;">
            <div class="card-body p-5">
                <h3 class="card-title">@lang('install.step2Title')</h3>
                <ul class="list-group list-group-flush my-4">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>@lang('install.step2CheckPhpVersion')</span>
                        <span id="php_version_status">-</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>@lang('install.step2CheckHttps')</span>
                        <span id="https_status">-</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>@lang('install.step2CheckFolderOwnership')</span>
                        <span id="folder_status">-</span>
                    </li>
                    <!--Extensions: fileinfo-->
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>@lang('install.step2CheckPhpExtensions')</span>
                        <span id="extensions_status">-</span>
                    </li>
                    <!--Functions: putenv,symlink,readlink,proc_open-->
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>@lang('install.step2CheckPhpFunctions') </span>
                        <span id="functions_status">-</span>
                    </li>
                </ul>
                <a href="{{ route('install.step3') }}" class="btn btn-outline-primary ms-3">@lang('install.step2Btn')</a>
                <!-- 不满足条件，点击「重试」按钮重新检测，符合条件则是「确认」按钮-->
                <button type="button" class="btn btn-outline-info ms-3" onclick="window.location.reload()">@lang('install.step2CheckBtn')</button>
            </div>
        </div>
    </main>

    <script src="/static/js/bootstrap.bundle.min.js"></script>
    <script src="/static/js/jquery-3.6.0.min.js"></script>
    <script>
        var items = [
            "php_version",
            "https",
            "folder",
            "extensions",
            "functions",
        ];

        //检测
        (function detect() {
            var name = items[0];
            $.ajax({
                type: "POST",
                dataType: "json",
                cache: false,
                url: '<?php echo route('install.env'); ?>',
                data: {name: name},
                success: function (data) {
                    if ($('#' + name + '_status').length && data.result !== undefined) {
                        $('#' + name + '_status').html(data.result);
                    }
                },
                complete: function () {
                    items.shift();
                    if (items.length) {
                        setTimeout(function () {detect();}, 20);
                    }
                }
            });
        })();
    </script>
</body>
</html>
