<!doctype html>
<html lang="{{ $lang }}">

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
                        <span class="badge bg-success rounded-pill">@lang('install.step2CheckStatusSuccess')</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>@lang('install.step2CheckHttps')</span>
                        <span class="badge bg-warning rounded-pill">@lang('install.step2CheckStatusWarning')</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>@lang('install.step2CheckFolderOwnership')</span>
                        <span class="badge bg-success rounded-pill">@lang('install.step2CheckStatusSuccess')</span>
                    </li>
                    <!--Extensions: fileinfo-->
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>@lang('install.step2CheckPhpExtensions')</span>
                        <span class="badge bg-success rounded-pill">@lang('install.step2CheckStatusSuccess')</span>
                    </li>
                    <!--Functions: putenv,symlink,readlink,proc_open-->
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>@lang('install.step2CheckPhpFunctions') <small class="text-muted">@lang('install.step2StatusNotEnabled'): symlink</small></span>
                        <span class="badge bg-danger rounded-pill">@lang('install.step2CheckStatusFailure')</span>
                    </li>
                </ul>
                <a href="/install/step3" class="btn btn-outline-primary ms-3">@lang('install.step2Btn')</a>
                <!-- 不满足条件，点击「重试」按钮重新检测，符合条件则是「确认」按钮
                <button type="button" class="btn btn-outline-info ms-3">@lang('install.step2CheckBtn')</button>
                -->
            </div>
        </div>
    </main>

    <script src="assets/javascript/bootstrap.bundle.min.js"></script>
</body>

</html>