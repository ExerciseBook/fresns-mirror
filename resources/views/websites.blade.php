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
                <h3>@lang('fresns.enginesTitle')</h3>
                <p class="text-secondary">@lang('fresns.enginesIntro')</p>
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-nowrap">
                        <thead>
                            <tr class="table-info">
                                <th scope="col">@lang('fresns.enginesTableName') <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('fresns.enginesTableNameInfo')"></i></th>
                                <th scope="col">@lang('fresns.enginesTableAuthor')</th>
                                <th scope="col">@lang('fresns.enginesTableTheme')</th>
                                <th scope="col" class="text-center">@lang('fresns.enginesTableOptions') <i class="bi bi-info-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('fresns.enginesTableOptionsInfo')"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(empty($websitePluginArr))
                                <tr>
                                    <td colspan="4" class="p-5 text-center"><i class="bi bi-view-list"></i> @lang('fresns.enginesNull')</td>
                                </tr>
                            @else
                                @foreach ($websitePluginArr as $item)
                                    <tr>
                                        <th scope="row" class="py-3">
                                            {{$item['name']}} <span class="badge bg-secondary plugin-version">{{$item['version']}}</span> 
                                            @if($item['is_upgrade'] == 1)
                                                <a href="/fresns/dashboard" unikey="{{$item['unikey']}}" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('fresns.newVersionInfo')"><span class="badge rounded-pill bg-danger plugin-version">@lang('fresns.newVersion')</span></a>
                                            @endif
                                        </th>
                                        <td><a href="{{$item['author_link']}}" class="link-info fresns-link fs-7">{{$item['author']}}</a></td>
                                        <td>
                                            <span class="badge bg-light text-dark"><i class="bi bi-laptop"></i> 
                                                @if (empty($item['websitePcPlugin']))
                                                    @lang('fresns.enginesTableThemePcNull')
                                                @else
                                                    {{ $item['websitePcPlugin'] }}
                                                @endif
                                                {{-- {{ empty($item['websitePcPlugin']) ? "@lang('fresns.enginesTableThemePcNull')" : $item['websitePcPlugin'] }} --}}
                                            </span>
                                            <span class="badge bg-light text-dark"><i class="bi bi-phone"></i>
                                                @if (empty($item['websiteMobilePlugin']))
                                                    @lang('fresns.enginesTableThemePcNull')
                                                @else
                                                    {{ $item['websiteMobilePlugin'] }}
                                                @endif
                                                {{-- {{ empty($item['websiteMobilePlugin']) ? "@lang('fresns.enginesTableThemePcNull')" : $item['websiteMobilePlugin'] }} --}}
                                            </span>                                   
                                        </td>
                                        <td class="text-end">
                                            @if ($item['is_enable'] == 1)
                                                <button type="button" class="btn btn-outline-success btn-sm btn_enable1" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('fresns.deactivateInfo')" data_id = "{{$item['id']}}">@lang('fresns.deactivate')</button> 
                                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#themeSetting" id = "linkSubject" unikey = "{{$item['unikey']}}" subectUnikeyPc = "{{$item['websitePc']}}" subectUnikeyMobile = "{{$item['websiteMobile']}}">@lang('fresns.enginesTableOptionsTheme')</button>
                                                @if ($item['setting_path'] == '')
                                                    <a href="#" class="btn btn-primary btn-sm disabled">@lang('fresns.setting')</a>
                                                @else
                                                    <a href="/fresns/iframe?url={{$item['setting_path']}}" class="btn btn-primary btn-sm"  title="@lang('fresns.settingInfo')" data-bs-toggle="tooltip" data-bs-placement="top">@lang('fresns.setting')</a>
                                                @endif
                                            @else
                                                <button type="button" class="btn btn-outline-secondary btn-sm btn_enable2" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('fresns.activateInfo')" data_id = "{{$item['id']}}">@lang('fresns.activate')</button>
                                                <button type="button" class="btn btn-outline-danger btn-sm  uninstallUnikey" data-name="{{$item['name']}}" unikey = "{{$item['unikey']}}">@lang('fresns.uninstall')</button>
                                            @endif
                                            
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <h3 class="mt-5">@lang('fresns.themesTitle')</h3>
                <p class="text-secondary">@lang('fresns.themesIntro')</p>
                <!--主题 开始-->
                <div class="row">
                    <!--主题列表 开始-->
                    @if(empty($subjectPluginArr))
                        <div class="p-5 text-center">
                            <i class="bi bi-view-list"></i> @lang('fresns.themesNull')
                        </div>
                    @else
                        @foreach ($subjectPluginArr as $item)
                        <div class="col-sm-6 col-xl-3 mb-4">
                            <div class="card">
                                <div class="position-relative">
                                    <img src="/themes/{{$item['unikey']}}/fresns.png" class="card-img-top" alt="{{$item['name']}}">
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
                                            <a href="/fresns/iframe?url=/themes/{{$item['unikey']}}/functions.php" class="btn btn-primary btn-sm"  title="@lang('fresns.settingInfo')" data-bs-toggle="tooltip" data-bs-placement="top">@lang('fresns.setting')</a>
                                        @endif
                                    @else
                                        <button type="button" class="btn btn-outline-secondary btn-sm btn_enable2" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('fresns.activateInfo')" data_id="{{$item['id']}}">@lang('fresns.activate')</button>
                                        <button type="button" class="btn btn-outline-danger btn-sm uninstallUnikey" data-name="{{$item['name']}}" unikey="{{$item['unikey']}}" data-bs-toggle="tooltip" data-bs-placement="top" title="@lang('fresns.uninstallInfo')">@lang('fresns.uninstall')</button>
                                    @endif
                                    </div>
                                </div>
                                <div class="card-footer fs-8">@lang('fresns.author'): <a href="{{$item['author_link']}}" target="_blank" class="link-info fresns-link">{{$item['author']}}</a></div>
                            </div>
                        </div>
                        @endforeach
                    @endif
                    <!--主题列表 结束-->
                </div>
                <!--主题结束-->
            </div>
        </div>
    </main>

     <!-- Modal -->
     <div class="modal fade" id="themeSetting" tabindex="-1" aria-labelledby="themeSetting" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('fresns.engineThemeTitle')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <input type="hidden" id = 'updateWebsite' value="">
                </div>
                <div class="modal-body">
                    <!--网站设置 开始-->
                    <form>
                        <div class="form-floating mb-3">
                            <select class="form-select subectUnikeyPc" id="PCTheme" aria-label="Floating label select example">
                                <option value="">@lang('fresns.engineThemeNoOption')</option>
                                @foreach ($subjectPluginArr as $item)
                                    <option value="{{$item['unikey']}}">{{$item['name']}}</option>
                                @endforeach
                            </select>
                            <label for="PCtheme"><i class="bi bi-laptop"></i> @lang('fresns.engineThemePc')</label>
                        </div>
                        <div class="form-floating mb-4">
                            <select class="form-select subectUnikeyMobile" id="mobileTheme" aria-label="Floating label select example">
                                <option value="">@lang('fresns.engineThemeNoOption')</option>
                                @foreach ($subjectPluginArr as $item)
                                    <option value="{{$item['unikey']}}">{{$item['name']}}</option>
                                @endforeach
                            </select>
                            <label for="mobileTheme"><i class="bi bi-phone"></i> @lang('fresns.engineThemeMobile')</label>
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn btn-primary updateSubject">@lang('fresns.consoleSettingBtn')</button>
                        </div>
                    </form>
                    <!--网站设置 结束-->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <!-- <div class="modal fade" id="confirmDele" tabindex="-1" aria-labelledby="confirmDele" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('fresns.confirmUninstall') ？</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div style="text-align:right">
                            <button type="submit" class="btn btn-primary delete-btn">@lang('fresns.uninstall')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> -->

    <div class="modal fade" id="confirmDele" tabindex="-1" aria-labelledby="confirmDele" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">名称</h5>
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

<script src="/assets/js/console.js"></script>
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
        var name = $(this).attr('data-name');
        $('#confirmDele .modal-title').text(name);
        $('#confirmDele').addClass('show');
        $('#confirmDele').css({
            'display': 'block'
        })
        
        var unikey = $(this).attr('unikey');
        $(".delete-btn").attr('unikey', unikey);
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
    $("#linkSubject").click(function(){
        var unikey = $(this).attr('unikey');
        var subectUnikeyPc = $(this).attr('subectUnikeyPc');
        var subectUnikeyMobile = $(this).attr('subectUnikeyMobile');
        $("#updateWebsite").val(unikey);
        $(".subectUnikeyPc").val(subectUnikeyPc);
        $(".subectUnikeyMobile").val(subectUnikeyMobile);
    })

</script>

</body>
</html>