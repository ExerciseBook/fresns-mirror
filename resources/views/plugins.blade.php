<!doctype html>
<html lang="{{ $lang }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="Fresns" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no,viewport-fit=cover">
    <title>Fresns Console</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/console.css">
</head>

<body>

@include('common.header')

    <main>
        <div class="container-lg p-0 p-lg-3">
            <div class="bg-white shadow-sm mt-4 mt-lg-2 p-3 p-lg-5">
                <h3>@lang('fresns.pluginsTitle')</h3>
                <p class="text-secondary mb-4">@lang('fresns.pluginsIntro')</p>

                <ul class="nav nav-tabs mb-3 pluginList">
                    <li class="nav-item"><a class="nav-link active" href="javascript:;" data-type = 2>@lang('fresns.pluginsTabAll')</a></li>
                    <li class="nav-item"><a class="nav-link" href="javascript:;" data-type = 1>@lang('fresns.pluginsTabActive')<span class="enableCount"> ({{$enableCount}})</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="javascript:;" data-type = 0>@lang('fresns.pluginsTabInactive')<span class="unEnableCount">({{$unEnableCount}})</span> </a></li>
                </ul>

                <div class="table-responsive">
                    <table class="table table-hover align-middle text-nowrap fs-7">
                        <thead>
                            <tr class="table-info fs-6">
                                <th scope="col">@lang('fresns.pluginsTableName')</th>
                                <th scope="col">@lang('fresns.pluginsTableDesc')</th>
                                <th scope="col">@lang('fresns.pluginsTableAuthor')</th>
                                <th scope="col">@lang('fresns.pluginsTableOptions')</th>
                            </tr>
                        </thead>
                        <!--List-->
                        <tbody>
                            @if(empty($location))
                                <tr>
                                    <td colspan="4" class="p-5 text-center"><i class="bi bi-view-list"></i> @lang('fresns.pluginsNull')</td>
                                </tr>
                            @else
                                @foreach ($location as $item)
                                    <tr class="pluginLists" isEnable="{{$item['is_enable'] == 1 ? 1 :0}}">
                                        <td class="py-3">
                                            <img src="/views/{{$item['unikey']}}/fresns.png" class="me-2" width="44" height="44">
                                            <span class="fs-6">{{$item['name']}}</span>
                                            <span class="badge bg-secondary plugin-version">{{$item['version']}}</span>
                                            @if($item['is_upgrade'] == 1)
                                                <a href="/fresns/dashboard" unikey="{{$item['unikey']}}" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('fresns.newVersionInfo')"><span class="badge rounded-pill bg-danger plugin-version">@lang('fresns.newVersion')</span></a>
                                            @endif
                                        </td>
                                        <td>{{$item['description']}}</td>
                                        <td><a href="{{$item['author_link']}}" target="_blank" class="link-info fresns-link fs-7">{{$item['author']}}</a></td>
                                        <td>
                                            @if ($item['is_enable'] == 1)
                                                <button type="button" class="btn btn-outline-success btn-sm btn_enable1" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('fresns.deactivateInfo')" data_id="{{$item['id']}}">@lang('fresns.deactivate')</button>
                                                @if ($item['setting_path'] == '')
                                                    <a href="#" class="btn btn-primary btn-sm disabled">@lang('fresns.setting')</a>
                                                @else
                                                    <a href="/fresns/iframe?url={{$item['setting_path']}}?lang={{$lang}}" class="btn btn-primary btn-sm"  title="@lang('fresns.settingInfo')" data-bs-toggle="tooltip" data-bs-placement="top">@lang('fresns.setting')</a>
                                                @endif
                                            @else
                                                <button type="button" class="btn btn-outline-secondary btn-sm btn_enable2" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('fresns.activateInfo')" data_id="{{$item['id']}}" >@lang('fresns.activate')</button>
                                                <button type="button"  class="btn btn-outline-danger btn-sm uninstallUnikey" data-bs-toggle="modal" data-bs-target="#confirmDele" data-name="{{ $item['name'] }}" unikey="{{$item['unikey']}}" title="@lang('fresns.uninstallInfo')">@lang('fresns.uninstall')</button> 
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif  
                        </tbody>
                        <!--List End-->
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!--Uninstall Modal-->
    <div class="modal fade" id="confirmDele" tabindex="-1" aria-labelledby="confirmDele" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Plugin Name</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
    //Deactivate
    $(".btn_enable1").click(function(){
        var id = $(this).attr('data_id');
        $.ajax({
            async: false,
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
        })
    });

    //Activate
    $(".btn_enable2").click(function(){
        var id = $(this).attr('data_id');
        $.ajax({
            async: false,
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
        })
    });

    //Uninstall
    $(".uninstallUnikey").click(function(){
        var name = $(this).attr('data-name');
        $('#confirmDele .modal-title').text(name);
        var unikey = $(this).attr('unikey');
        console.log(unikey);
        $(".btn-danger").attr('unikey', unikey);
    });
    $(".btn-danger").click(function() {
        var unikey = $(this).attr('unikey');
        var clear_plugin_data = $('#is-delete-data').is(':checked') ? 1 : 0;
        $.ajax({
            async: false,
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
        })
    });
    
    //List Tab
    $(".pluginList li").click(function(){
        var type = $(this).find('a').attr('data-type');
        $(".pluginList").find('li a').removeClass('active');
        $(this).find('a').addClass('active');
        if(type == 2){
            $(".pluginLists").show();
            return;
        }
        $(".pluginLists").each(function(){
            var that = $(this);
            var enableStatus = that.attr('isEnable');
            if(type != enableStatus){
                that.hide();
            }else{
                that.show();
            }
        })
    })
</script>

</body>
</html>