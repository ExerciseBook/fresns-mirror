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
                <p class="mt-2">@lang('install.step2Desc')</p>
                <form class="my-4">
                    <div class="alert alert-danger" role="alert" id="install_error_msg" style="display: none;">
                        @lang('install.step3CheckDatabaseFailure')
                    </div>
                    <div class="row mt-4">
                        <label class="col-sm-3 col-form-label"></label>
                        <div class="col-sm-9">
                            <button type="button" id="submit" class="btn btn-outline-primary">@lang('install.step3Btn')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="/static/js/bootstrap.bundle.min.js"></script>
    <script src="/static/js/jquery-3.6.0.min.js"></script>
    <script>
        var items = [
            "mysql_version",
            "mysql_db",
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
