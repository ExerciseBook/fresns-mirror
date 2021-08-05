<!doctype html>
<html lang="{{ $lang }}">

<head>
    <meta charset="utf-8">
    <meta name="author" content="Fresns" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no,viewport-fit=cover">
    <title>Fresns Console</title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/console.css">
</head>

<body>
    @include('common.header')

    <main>
        <div class="container-lg p-0 p-lg-3">
            <div class="bg-white shadow-sm mt-4 mt-lg-2 p-3 p-lg-5">
                <div class="row">
                    <div class="col-lg-5">
                        <h3>@lang('fresns.consoleTitle')</h3>
                        <p class="text-secondary">@lang('fresns.consoleIntro')</p>
                        <form>
                            <div class="input-group mb-3">
                                <span class="input-group-text">@lang('fresns.backendDomain')</span>

                                <input type="url" class="form-control border-end-0 backend-address" name="backend_url" placeholder="https://abc.com" value={{ $backend_url }}>
                                <span class="input-group-text bg-white border-start-0" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('fresns.backendDomainInfo')"><i class="bi bi-info-circle"></i></span>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text ">@lang('fresns.backendPath')</span>
                                <input type="text" class="form-control border-end-0 safe-entrance" name="admin_path" placeholder="admin" value={{ $admin_path }}>

                                <span class="input-group-text bg-white border-start-0" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('fresns.backendPathInfo')"><i class="bi bi-info-circle"></i></span>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text">@lang('fresns.consoleUrlName')</span>
                                <span class="form-control bg-white" id="copy_info" style="word-break: break-all;">
                                    {{$path}}
                                </span>
                                <button class="btn btn-outline-secondary copy-btn" type="button" id="button-addon1">@lang('fresns.copyConsoleUrl')</button>

                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text">@lang('fresns.siteDomain')</span>
                                <input type="url" class="form-control border-end-0 site-url" name="site_url" placeholder="https://" value="{{ $site_url }}">
                                <span class="input-group-text bg-white border-start-0" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('fresns.siteDomainInfo')"><i class="bi bi-info-circle"></i></span>
                            </div>
                            <button id="submit" class="btn btn-primary">@lang('fresns.consoleSettingBtn')</button>
                        </form>
                        <textarea id="input_textarea" style="opacity: 0;width: 0;height:0;">
                        {{ $path }}
                        </textarea>
                    </div>
                    <div class="col-lg-1 mb-5"></div>
                    <div class="col-lg-5">
                        <h3>@lang('fresns.systemAdminTitle')</h3>
                        <p class="text-secondary">@lang('fresns.systemAdminIntro')</p>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle text-nowrap">
                                <thead>
                                    <tr class="table-info">
                                        <th scope="col">UID</th>
                                        <th scope="col">@lang('fresns.account')</th>
                                        <th scope="col">@lang('fresns.systemAdminOptions')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($user_arr as $v)
                                    <tr>
                                        <td>{{ $v['uuid'] }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                <i class="bi bi-envelope"></i> {{ $v['email_desc'] ?? "@lang('fresns.air')" }}</span>
                                            <span class="badge bg-light text-dark">
                                                <i class="bi bi-phone"></i> {{ $v['phone_desc'] ?? "@lang('fresns.air')" }}</span>
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-link btn-sm text-danger fresns-link delete" data-bs-toggle="modal" data-bs-target="#confirmDele" data-uuid="{{ $v['uuid'] }}">@lang('fresns.deleteSystemAdmin')</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newAdmin"><i class="bi bi-plus-circle-dotted"></i> @lang('fresns.addSystemAdmin')</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal -->
    <div class="modal fade" id="newAdmin" tabindex="-1" aria-labelledby="newAdmin" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('fresns.addSystemAdminTitle')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text">@lang('fresns.account')</span>
                            <input type="text" class="form-control account" placeholder="@lang('fresns.addSystemAdminAccountDesc')">
                            <button class="btn btn-outline-secondary" type="submit" id="folderInstall-button">@lang('fresns.addSystemAdminBtn')</button>
                        </div>
                        <div class="form-text"><i class="bi bi-info-circle"></i> @lang('fresns.addSystemAdminAccountInfo')</div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="confirmDele" tabindex="-1" aria-labelledby="confirmDele" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('fresns.confirmDelete')?</h5>
                    <button type="button" id="deleteClose" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>UID: <span class="app_id">uid</span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-dismiss="modal">@lang('fresns.confirmDelete')</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('fresns.cancel')</button>
                </div>
            </div>
        </div>
    </div>

@include('common.footer')

    <script>
        $('.copy-btn').click(function() {
            var copy_info = document.getElementById('copy_info').innerText;
            var input = document.getElementById("input_textarea");
            input.value = copy_info; // 修改文本框的内容
            input.select();
            // document.execCommand("copy");
            try {
                if (document.execCommand("Copy", "false", null)) {
                    alert("@lang('fresns.copyConsoleUrlSuccess')！");
                } else {
                    alert("复制失败！");
                }
            } catch (err) {
                alert("复制错误！");
            }

        })
        $('.safe-entrance').bind('input propertychange', function() {
            var entrance = $(this).val();
            var address = $('.backend-address').val();
            var text = address+'/fresns/' + entrance;
            $('#copy_info').text(text);
            $('#input_textarea').text(text);
        })
        $('.backend-address').bind('input propertychange', function() {
            var entrance = $(this).val();
            var address = $('.safe-entrance').val();
            var text = entrance+'/fresns/' + address;
            $('#copy_info').text(text);
            $('#input_textarea').text(text);
        })
        // 提交创建
        $("#folderInstall-button").click(function() {
            var account = $('.account').val();
            $.ajax({
                async: false, //设置为同步
                type: "post",
                url: "/addAdmin",
                data: {
                    'account': account
                },
                beforeSend: function(request) {
                    return request.setRequestHeader('X-CSRF-Token', "{{ csrf_token() }}");
                },
                success: function(data) {
                    if (data.code == 0) {
                        // window.location.reload();
                    } else {
                        alert(data.message)
                    }
                }
            });

        })
        $('.delete').on('click', function() {
            $('#confirmDele').addClass('show');
            $('#confirmDele').css({
                'display': 'block'
            })
            
            var uuid = $(this).attr('data-uuid');
            $('#confirmDele .app_id').text(uuid);
            $(".btn-danger").attr('data-uuid', uuid);
        })
        $('#confirmDele .btn-secondary').on('click', function() {
            $('#confirmDele').removeClass('show');
            $('#confirmDele').css({
                'display': 'none'
            })
        })
        $('#deleteClose').click(function(){
            $('#confirmDele').removeClass('show');
            $('#confirmDele').css({
                'display': 'none'
            })
        })
        $(".btn-danger").click(function() {
            var uuid = $(this).attr('data-uuid');
            $.ajax({
                async: false, //设置为同步
                type: "post",
                url: "/delAdmin",
                data: {
                    'uuid': uuid
                },
                beforeSend: function(request) {
                    return request.setRequestHeader('X-CSRF-Token', "{{ csrf_token() }}");
                },
                success: function(data) {
                    if (data.code == 0) {
                        window.location.reload();
                    } else {
                        alert(data.message)
                    }
                }
            });

        })

        //保存设置
        $("#submit").click(function() {
            var admin_path = $('.safe-entrance').val();
            var backend_url = $('.backend-address').val();
            var site_url = $('.site-url').val();
            $.ajax({
                async: false, //设置为同步
                type: "post",
                url: "/updateSetting",
                data: {
                    'admin_path': admin_path,
                    'backend_url': backend_url,
                    'site_url': site_url,
                },
                beforeSend: function(request) {
                    return request.setRequestHeader('X-CSRF-Token', "{{ csrf_token() }}");
                },
                success: function(data) {
                    if (data.code == 0) {
                        window.location.reload();
                    } else {
                        alert(data.message)
                    }
                }
            });

        })
    </script>

</body>
</html>