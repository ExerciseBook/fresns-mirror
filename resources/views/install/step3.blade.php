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
                <p class="mt-2">@lang('install.step2Desc')</p>
                <form class="my-4">
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">@lang('install.step3DatabaseName')</label>
                        <div class="col-sm-5"><input type="text" class="form-control" placeholder="fresns" required></div>
                        <div class="col-sm-4 form-text">@lang('install.step3DatabaseNameIntro')</div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">@lang('install.step3DatabaseUsername')</label>
                        <div class="col-sm-5"><input type="text" class="form-control" placeholder="username" required></div>
                        <div class="col-sm-4 form-text pt-1">@lang('install.step3DatabaseUsernameIntro')</div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">@lang('install.step3DatabasePassword')</label>
                        <div class="col-sm-5"><input type="text" class="form-control" placeholder="password" required></div>
                        <div class="col-sm-4 form-text pt-1">@lang('install.step3DatabasePasswordIntro')</div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">@lang('install.step3DatabaseHost')</label>
                        <div class="col-sm-5"><input type="text" class="form-control" placeholder="localhost" required></div>
                        <div class="col-sm-4 form-text">@lang('install.step3DatabaseHostIntro')</div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">@lang('install.step3DatabaseTablePrefix')</label>
                        <div class="col-sm-5"><input type="text" class="form-control" placeholder="fs_" value="fs_" required></div>
                        <div class="col-sm-4 form-text">@lang('install.step3DatabaseTablePrefixIntro')</div>
                    </div>
                    <div class="alert alert-danger" role="alert">
                        <!--连接失败提示-->
                        @lang('install.step3CheckDatabaseFailure')
                    </div>
                    <div class="row mt-4">
                        <label class="col-sm-3 col-form-label"></label>
                        <div class="col-sm-9">
                            <button type="submit" class="btn btn-outline-primary">@lang('install.step3Btn')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="assets/javascript/bootstrap.bundle.min.js"></script>
</body>

</html>