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
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">@lang('install.step3DatabaseHost')</label>
                        <div class="col-sm-5"><input type="text" class="form-control" name="db_host" placeholder="" required></div>
                        <div class="col-sm-4 form-text">@lang('install.step3DatabaseHostIntro')</div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">@lang('install.step3DatabaseHost')</label>
                        <div class="col-sm-5"><input type="text" class="form-control" name="db_port" placeholder="" required></div>
                        <div class="col-sm-4 form-text">@lang('install.step3DatabaseHostIntro')</div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">@lang('install.step3DatabaseName')</label>
                        <div class="col-sm-5"><input type="text" class="form-control" name="db_name" placeholder="" required></div>
                        <div class="col-sm-4 form-text">@lang('install.step3DatabaseNameIntro')</div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">@lang('install.step3DatabaseUsername')</label>
                        <div class="col-sm-5"><input type="text" class="form-control" name="db_user" placeholder="" required></div>
                        <div class="col-sm-4 form-text pt-1">@lang('install.step3DatabaseUsernameIntro')</div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">@lang('install.step3DatabasePassword')</label>
                        <div class="col-sm-5"><input type="text" class="form-control" name="db_pwd" placeholder="" required></div>
                        <div class="col-sm-4 form-text pt-1">@lang('install.step3DatabasePasswordIntro')</div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">@lang('install.step3DatabaseTablePrefix')</label>
                        <div class="col-sm-5"><input type="text" class="form-control" name="db_prefix" placeholder="" required></div>
                        <div class="col-sm-4 form-text">@lang('install.step3DatabaseTablePrefixIntro')</div>
                    </div>
                    <div class="alert alert-danger" role="alert" id="install_error_msg" style="display: none;">
                        @lang('install.step3CheckDatabaseFailure')
                    </div>
                    <div class="row mt-4">
                        <label class="col-sm-3 col-form-label"></label>
                        <div class="col-sm-9">
                            <input type="hidden" id="install_mysql" value="{{ route('install.mysql') }}" >
                            <input type="hidden" id="install_step4" value="{{ route('install.step4') }}" >
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
        $("#submit").click(function() {
            var db_host = $('input[name="db_host"]').val();
            var db_port = $('input[name="db_port"]').val();
            var db_name = $('input[name="db_name"]').val();
            var db_user = $('input[name="db_user"]').val();
            var db_pwd = $('input[name="db_pwd"]').val();
            var db_prefix = $('input[name="db_prefix"]').val();

            var submit_url = $('#install_mysql').val();
            var next_url = $('#install_step4').val();
            $.ajax({
                async: false,
                type: "post",
                url: submit_url,
                data: {
                    'db_host': db_host,
                    'db_port': db_port,
                    'db_name': db_name,
                    'db_user': db_user,
                    'db_pwd': db_pwd,
                    'db_prefix': db_prefix,
                },
                beforeSend: function(request) {
                    return request.setRequestHeader('X-CSRF-Token', "{{ csrf_token() }}");
                },
                success: function(data) {
                    if (data.code == '000000') {
                        $('#install_error_msg').hide();
                        location.href = next_url;
                    } else {
                        $('#install_error_msg').show();
                    }
                }
            })
        });
    </script>
</body>
</html>
