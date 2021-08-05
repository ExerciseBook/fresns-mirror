<!doctype html>
<html lang="{{ $lang }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
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
                <h3>@lang('fresns.appsTitle')</h3>
                <p class="text-secondary mb-4">@lang('fresns.appsIntro')</p>

                <div class="row">
                    <!--List-->
                    @if(empty($location))
                        <div class="p-5 text-center">
                            <i class="bi bi-view-list"></i> @lang('fresns.appsNull')
                        </div>
                    @else
                        @foreach ($location as $item)
                            <div class="col-sm-6 col-xl-3 mb-4">
                                <div class="card">
                                    <div class="position-relative">
                                        <img src="/views/{{$item['unikey']}}/fresns.png" class="card-img-top" alt="{{$item['name']}}">
                                        @if ($item['is_upgrade'] == 1)
                                            <div class="position-absolute top-0 start-100 translate-middle"><a href="/fresns/dashboard" unikey="{{$item['unikey']}}" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('fresns.newVersionInfo')"><span class="badge rounded-pill bg-danger">@lang('fresns.newVersion')</span></a></div>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <h5 class="text-nowrap overflow-hidden">{{$item['name']}} <span class="badge bg-secondary align-middle plugin-version">{{$item['version']}}</span></h5>
                                        <p class="card-text text-height">{{$item['description']}}</p>
                                        <div>
                                        @if ($item['is_enable'] == 1)
                                            <button type="button" class="btn btn-outline-success btn-sm btn_enable1" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('fresns.deactivateInfo')" data_id="{{$item['id']}}">@lang('fresns.deactivate')</button>
                                            @if ($item['setting_path'] == '')
                                                <a href="#" class="btn btn-primary btn-sm disabled">@lang('fresns.setting')</a>
                                            @else
                                                <a href="/fresns/iframe?url={{$item['setting_path']}}" class="btn btn-primary btn-sm"  title="@lang('fresns.settingInfo')" data-bs-toggle="tooltip" data-bs-placement="top">@lang('fresns.setting')</a>
                                            @endif
                                        @else
                                            <button type="button" class="btn btn-outline-secondary btn-sm btn_enable2" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('fresns.activateInfo')" data_id="{{$item['id']}}">@lang('fresns.activate')</button>
                                            <button type="button" class="btn btn-outline-danger btn-sm uninstallUnikey" data-bs-toggle="modal" data-bs-target="#confirmDele" data-name="{{ $item['name'] }}" unikey="{{$item['unikey']}}" title="@lang('fresns.uninstallInfo')">@lang('fresns.uninstall')</button>
                                        @endif
                                        </div>
                                    </div>
                                    <div class="card-footer fs-8">@lang('fresns.author'): <a href="{{$item['author_link']}}" target="_blank" class="link-info fresns-link">{{$item['author']}}</a></div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    
                    <!--List End-->
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="confirmDele" tabindex="-1" aria-labelledby="confirmDele" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Name</h5>
                    <button type="button" id="deleteClose" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-check">
                            <input class="form-check-input" name="clear_plugin_data" type="checkbox" id="is-delete-data">
                            <label class="form-check-label" for="is-delete-data">@lang('fresns.uninstallOption')</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-dismiss="modal">@lang('fresns.confirmUninstall')</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('fresns.cancel')</button>
                </div>
            </div>
        </div>
    </div>

@include('common.footer')

<script>
    $(".btn_enable1").click(function(){
        var id = $(this).attr('data_id');
        $.ajax({
             async: false,    //设置为同步
             type: "post",
             url: "/enableUnikeyStatus",
             data: {'data_id':id,'is_enable':0},
             beforeSend: function (request) {
                     return request.setRequestHeader('X-CSRF-Token', "{{ csrf_token() }}");
                 },
             success: function (data) {
                if(data.code == 0){
                    window.location.reload();
                }else{
                    alert(data.message)
                }
             }
         });
    })
    $(".btn_enable2").click(function(){
        var id = $(this).attr('data_id');
        $.ajax({
             async: false,    //设置为同步
             type: "post",
             url: "/enableUnikeyStatus",
             data: {'data_id':id,'is_enable':1},
             beforeSend: function (request) {
                     return request.setRequestHeader('X-CSRF-Token', "{{ csrf_token() }}");
                 },
             success: function (data) {
                if(data.code == 0){
                    window.location.reload();
                }else{
                    alert(data.message)
                }
             }
         });
    })
    $('.uninstallUnikey').on('click', function() {
        $('#confirmDele').addClass('show');
        $('#confirmDele').css({
            'display': 'block'
        })
        var name = $(this).attr('data-name');
        $('#confirmDele .modal-title').text(name);
        var unikey = $(this).attr('unikey');
        $(".btn-danger").attr('unikey', unikey);
    })
    $('#confirmDele .btn-close').on('click', function() {
        $('#confirmDele').removeClass('show');
        $('#confirmDele').css({
            'display': 'none'
        })
    })
    $('#confirmDele .btn-secondary').on('click', function() {
        $('#confirmDele').removeClass('show');
        $('#confirmDele').css({
            'display': 'none'
        })
    })
    // 卸载
    $(".btn-danger").click(function(){
        var unikey = $(this).attr('unikey');
        var clear_plugin_data = $('#is-delete-data').is(':checked') ? 1 : 0;
        $.ajax({
             async: false,    //设置为同步
             type: "post",
             url: "/uninstall",
             data: {'unikey':unikey,'clear_plugin_data': clear_plugin_data},
             beforeSend: function (request) {
                     return request.setRequestHeader('X-CSRF-Token', "{{ csrf_token() }}");
                 },
             success: function (data) {
                if(data.code == 0){
                    window.location.reload();
                }else{
                    alert(data.message)
                }
             }
         });
    })
</script>

</body>
</html>