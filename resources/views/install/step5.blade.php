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
                <h3 class="card-title mt-3">@lang('install.step5Title')</h3>
                <p class="mt-4">@lang('install.step5Desc')</p>
                <ul class="list-group">
                    <li class="list-group-item">@lang('install.step5Account')</li>
                    <li class="list-group-item">@lang('install.step5Password')</li>
                </ul>
                <p class="mt-4"><a href="/fresns/admin" class="btn btn-outline-primary">@lang('install.step5Btn')</a></p>
            </div>
        </div>
    </main>

    <script src="assets/javascript/bootstrap.bundle.min.js"></script>
</body>

</html>