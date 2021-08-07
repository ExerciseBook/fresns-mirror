//dashboard.blade
$(".installLocal").click(function(){
    var dirName = $(".installDirName").val();
    $('#upgrade').addClass('show');
    var isAdd = true;
    $.ajax({
        async: false,    //设置为同步
        type: "post",
        url: "/localInstall",
        data: {'dirName':dirName},
        beforeSend: function (request) {
            return request.setRequestHeader('X-CSRF-Token', "{{ csrf_token() }}");
        },
        success: function (data) {
            if(data.code == 0){
                setTimeout(function(){
                    $('.step1').removeClass("spinner-border spinner-border-sm")
                    $('.step1').addClass("bi bi-check-lg text-success")
                    $('.step2').removeClass("bi bi-hourglass text-secondary")
                    $('.step2').addClass("spinner-border spinner-border-sm")
                },300)
                setTimeout(function(){
                    $('.step2').removeClass("spinner-border spinner-border-sm")
                    $('.step2').addClass("bi bi-check-lg text-success")
                    $('.step3').removeClass("bi bi-hourglass text-secondary")
                    $('.step3').addClass("spinner-border spinner-border-sm")
                },600)
                setTimeout(function(){
                    $('.step3').removeClass("spinner-border spinner-border-sm")
                    $('.step3').addClass("bi bi-check-lg text-success")
                    $('.step4').removeClass("bi bi-hourglass text-secondary")
                    $('.step4').addClass("spinner-border spinner-border-sm")
                },900)
                setTimeout(function(){
                    $('.step4').removeClass("spinner-border spinner-border-sm")
                    $('.step4').addClass("bi bi-check-lg text-success")
                    $('.step5').removeClass("bi bi-hourglass text-secondary")
                    $('.step5').addClass("spinner-border spinner-border-sm")
                },1200)
                setTimeout(function(){
                    $('.step5').removeClass("spinner-border spinner-border-sm")
                    $('.step5').addClass("bi bi-check-lg text-success")
                },1500)
                setTimeout(function(){
                    window.location.reload();
                },1800)
            }else{
                $('#upgrade').removeClass('show');
                alert(data.message);
            }
        }
    })
});

//websites.blade
// 设置主题
$(".updateSubject").click(function(){
    var websiteUnikey = $("#updateWebsite").val();
    if(!websiteUnikey){
        alert("插件未知");
        return ;
    }
    var pluginPc = $(".subectUnikeyPc").find("option:selected").val();
    var pluginMobile = $(".subectUnikeyMobile").find("option:selected").val();
    $.ajax({
        async: false,    //设置为同步
        type: "post",
        url: "/websiteLinkSubject",
        data: {'websiteUnikey':websiteUnikey,'subjectUnikeyPc':pluginPc,'subjectUnikeyMobile':pluginMobile},
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
    // console.log(plugin);
    // console.log(websiteUnikey);
});

// 远程更新插件
$(".update_plugin").click(function(){
    var unikey = $(this).attr('unikey');
    var dirName = unikey;
    var downloadUrl = "https://cdn.fresns.cn/extensions/plugin_v1.0.0.zip";
    $.ajax({
        async: false,    //设置为同步
        type: "get",
        url: "/api/fresns/plugin/upgrade",
        data: {'unikey':unikey,'dirName':dirName,'downloadUrl':downloadUrl,"localVision":1,'remoteVisionInt':2,'remoteVision':'2.0.0'},
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