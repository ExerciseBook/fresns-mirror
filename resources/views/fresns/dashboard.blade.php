@include('fresns.header')

    <main>
        <div class="container-lg">
            <!--Welcome-->
            <div class="card mt-4 mb-4 p-2 p-lg-4">
                <div class="card-body">
                    <h1 class="fs-3 fw-normal">@lang('fresns.welcome')</h1>
                    <p class="text-secondary pb-4">
                        @lang('fresns.currentVersion')
                        <span data-bs-toggle="tooltip" data-bs-placement="top" title="Database: v{{$version}}">v{{ $appVersion['currentVersion'] }}</span>
                        @if($appVersion['canUpgrade'] == false)
                        <button type="button" data-bs-toggle="modal" id="app_upgrade" class="btn btn-outline-primary btn-sm ms-3">升级 v{{ $appVersion['upgradeVersion'] }}</button>
                        @endif
                    </p>
                    <div class="row">
                        <div class="col-md mb-4 pe-lg-5">
                            <h3 class="h6">@lang('fresns.overview')</h3>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><i class="bi bi-person-fill"></i> @lang('fresns.userCounts') <span class="badge bg-success">{{ $total['member_count'] }}</span></li>
                                <li class="list-group-item"><i class="bi bi-person"></i> @lang('fresns.memberCounts') <span class="badge bg-success">{{ $total['user_count'] }}</span></li>
                                <li class="list-group-item"><i class="bi bi-collection"></i> @lang('fresns.groupCounts') <span class="badge bg-success">{{ $total['group_count'] }}</span></li>
                                <li class="list-group-item"><i class="bi bi-hash"></i> @lang('fresns.hashtagCounts') <span class="badge bg-success">{{ $total['hashtag_count'] }}</span></li>
                                <li class="list-group-item"><i class="bi bi-file-post-fill"></i> @lang('fresns.postCounts') <span class="badge bg-success">{{ $total['post_count'] }}</span></li>
                                <li class="list-group-item"><i class="bi bi-chat-right-dots"></i> @lang('fresns.commentCounts') <span class="badge bg-success">{{ $total['comment_count'] }}</span></li>
                            </ul>
                        </div>
                        <div class="col-md mb-4 pe-lg-5">
                            <h3 class="h6">@lang('fresns.extensions')</h3>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><i class="bi bi-key"></i> @lang('fresns.keys') <span class="badge bg-info">{{ $total['keys_count'] }}</span></li>
                                <li class="list-group-item"><i class="bi bi-sliders"></i> @lang('fresns.controlPanel') <span class="badge bg-info">{{ $total['plugin_4'] }}</span></li>
                                <li class="list-group-item"><i class="bi bi-laptop"></i> @lang('fresns.engines') <span class="badge bg-info">{{ $total['plugin_1'] }}</span></li>
                                <li class="list-group-item"><i class="bi bi-brush"></i> @lang('fresns.themes') <span class="badge bg-info">{{ $total['plugin_5'] }}</span></li>
                                <li class="list-group-item"><i class="bi bi-phone"></i> @lang('fresns.apps') <span class="badge bg-info">{{ $total['plugin_3'] }}</span></li>
                                <li class="list-group-item"><i class="bi bi-journal-code"></i> @lang('fresns.plugins') <span class="badge bg-info">{{ $total['plugin_2'] }}</span></li>
                            </ul>
                        </div>
                        <div class="col-md mb-4 pe-lg-5">
                            <h3 class="h6">@lang('fresns.support')</h3>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><a href="https://fresns.cn/" target="_blank" class="fresns-link">@lang('fresns.fresnsSite')</a></li>
                                <li class="list-group-item"><a href="https://fresns.cn/community/team.html" target="_blank" class="fresns-link">@lang('fresns.fresnsTeam')</a></li>
                                <li class="list-group-item"><a href="https://fresns.cn/community/partners.html" target="_blank" class="fresns-link">@lang('fresns.fresnsPartners')</a></li>
                                <li class="list-group-item"><a href="https://fresns.cn/community/join.html" target="_blank" class="fresns-link">@lang('fresns.fresnsJoin')</a></li>
                                <li class="list-group-item"><a href="https://discuss.fresns.cn/" target="_blank" class="fresns-link">@lang('fresns.fresnsCommunity')</a></li>
                                <li class="list-group-item"><a href="https://apps.fresns.cn/" target="_blank" class="fresns-link">@lang('fresns.fresnsAppStore')</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!--Two-Columns-->
            <div class="row">
                <!--News-->
                <div class="col-md mb-4">
                    <div class="card">
                        <div class="card-header">@lang('fresns.news')</div>
                        <div class="card-body">
                            @if (!empty($notice_arr))
                                @foreach($notice_arr as $v)
                                    {!! $v !!}
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <!--Manage-->
                <div class="col-md">
                    <!--Installs-->
                    <div class="card mb-4">
                        <div class="card-header">@lang('fresns.installs')</div>
                        <div class="card-body">
                            <p class="card-text">@lang('fresns.installIntro')</p>
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#localInstall">@lang('fresns.localInstall')</button>
                            <button type="button" class="btn btn-success btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#codeInstall">@lang('fresns.codeInstall')</button>
                        </div>
                    </div>
                    <!--Updates-->
                    <div class="card">
                        <div class="card-header">@lang('fresns.updates')</div>
                        @if(empty($newVisionPlugin))
                            <div class="p-5 text-center">
                                <i class="bi bi-view-list"></i> @lang('fresns.updatesNull')
                            </div>
                        @else
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                @foreach ($newVisionPlugin as $item)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div><i class="bi bi-sliders"></i> {{$item['name']}} <span class="badge bg-secondary">{{$item['version']}}</span> to <span class="badge bg-danger">{{$item['newVision']}}</span></div>
                                        <div><button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateActionModal" id="updateExtensions" data_unikey="{{$item['unikey']}}" data_local_vision="{{$item['version_int']}}" data_new_vision_int="{{$item['newVisionInt']}}" data_new_vision="{{$item['newVision']}}">@lang('fresns.updateBtn')</button></div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!--localInstall Modal-->
    <div class="modal fade" id="localInstall" tabindex="-1" aria-labelledby="localInstall" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('fresns.localInstall')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text">@lang('fresns.folderName')</span>
                            <input type="text" class="form-control installDirName" placeholder="@lang('fresns.folderName')">
                            <button type="button" class="btn btn-outline-secondary installLocal"  data-bs-dismiss="modal">@lang('fresns.localInstallBtn')</button>
                        </div>
                        <div class="form-text"><i class="bi bi-info-circle"></i> @lang('fresns.localInstallInfo')</div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--codeInstall Modal-->
    <div class="modal fade" id="codeInstall" tabindex="-1" aria-labelledby="codeInstall" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('fresns.codeInstall')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text">@lang('fresns.fresnsCode')</span>
                            <input type="text" class="form-control" placeholder="@lang('fresns.fresnsCode')">
                            <button class="btn btn-outline-secondary" type="submit" id="codeInstall-button">@lang('fresns.codeInstallBtn')</button>
                        </div>
                        <div class="form-text"><i class="bi bi-info-circle"></i> @lang('fresns.codeInstallInfo')</div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--upgrade Modal-->
    <div class="modal fade" id="upgrade" tabindex="-1" aria-labelledby="upgrade" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-laptop"></i> Fresns <span class="badge bg-secondary">{{ $appVersion['currentVersion'] }}</span> to <span class="badge bg-danger">{{ $appVersion['upgradeVersion'] }}</span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body ps-5">
                    <p><i class="spinner-border spinner-border-sm me-2 step1"></i>@lang('fresns.codeInstallStep1')</p>
                    <p><i class="bi bi-hourglass text-secondary me-2 step2"></i>@lang('fresns.codeInstallStep2')</p>
                    <p><i class="bi bi-hourglass text-secondary me-2 step3"></i>@lang('fresns.codeInstallStep3')</p>
                    <p><i class="bi bi-hourglass text-secondary me-2 step4"></i>@lang('fresns.codeInstallStep4')</p>
                    <p><i class="bi bi-hourglass text-secondary me-2 step5"></i>@lang('fresns.codeInstallStep5')</p>
                    <p><i class="bi bi-hourglass text-secondary me-2 step6"></i>@lang('fresns.codeInstallStep6')</p>
                </div>
            </div>
        </div>
    </div>


@include('fresns.footer')


<script>
    //upgrade program
    $("#app_upgrade").click(function () {
        $.ajax({
            async: false,
            type: "post",
            url: "/fresns/upgrade",
            beforeSend: function (request) {
                return request.setRequestHeader('X-CSRF-Token', "{{ csrf_token() }}");
            },
            success: function (data) {
                console.log(data);
                if (data.code == 0) {
                    $('#upgrade').addClass('show');
                    $('#upgrade').css({
                        'display': 'block',
                    });
                    setTimeout(function () {
                        $('.step1').removeClass("spinner-border spinner-border-sm")
                        $('.step1').addClass("bi bi-check-lg text-success")
                        $('.step2').removeClass("bi bi-hourglass text-secondary")
                        $('.step2').addClass("spinner-border spinner-border-sm")
                    }, 300)
                    setTimeout(function () {
                        $('.step2').removeClass("spinner-border spinner-border-sm")
                        $('.step2').addClass("bi bi-check-lg text-success")
                        $('.step3').removeClass("bi bi-hourglass text-secondary")
                        $('.step3').addClass("spinner-border spinner-border-sm")
                    }, 600)
                    setTimeout(function () {
                        $('.step3').removeClass("spinner-border spinner-border-sm")
                        $('.step3').addClass("bi bi-check-lg text-success")
                        $('.step4').removeClass("bi bi-hourglass text-secondary")
                        $('.step4').addClass("spinner-border spinner-border-sm")
                    }, 900)
                    setTimeout(function () {
                        $('.step4').removeClass("spinner-border spinner-border-sm")
                        $('.step4').addClass("bi bi-check-lg text-success")
                        $('.step5').removeClass("bi bi-hourglass text-secondary")
                        $('.step5').addClass("spinner-border spinner-border-sm")
                    }, 1200)
                    setTimeout(function () {
                        $('.step5').removeClass("spinner-border spinner-border-sm")
                        $('.step5').addClass("bi bi-check-lg text-success")
                        $('.step6').removeClass("bi bi-hourglass text-secondary")
                        $('.step6').addClass("spinner-border spinner-border-sm")
                    }, 1500)
                    setTimeout(function () {
                        window.location.reload();
                    }, 1800)
                } else {
                    alert(data.message);
                }
            }
        })
    });
</script>


</body>
</html>
