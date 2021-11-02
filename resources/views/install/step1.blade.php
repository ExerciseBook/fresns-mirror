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
                <h3 class="card-title">@lang('install.step1Title')</h3>
                <p class="mt-4">@lang('install.step1Desc')</p>
                <ul>
                    <li>@lang('install.step1DatabaseName')</li>
                    <li>@lang('install.step1DatabaseUsername')</li>
                    <li>@lang('install.step1DatabasePassword')</li>
                    <li>@lang('install.step1DatabaseHost')</li>
                    <li>@lang('install.step1DatabaseTablePrefix')</li>
                </ul>
                <p>@lang('install.step1DatabaseDesc')</p>
                <a href="/install/step2" class="btn btn-outline-primary mt-2">@lang('install.step1Btn')</a>
            </div>
        </div>
    </main>

    <script src="assets/javascript/bootstrap.bundle.min.js"></script>
</body>

</html>