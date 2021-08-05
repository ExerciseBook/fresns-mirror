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
    <main class="form-signin text-center">
        <form class="p-3" action="/loginAcc" method="POST">
            <img class="my-5" src="/assets/images/fresns-icon.png" alt="Fresns" width="72" height="72">
            <h1 class="h3 mb-3 fw-normal">@lang('fresns.language')</h1>
            <select class="form-select mb-5" id="form-select">
                <option value="en">English</option>
                <option value="es">Español - Spanish</option>
                <option value="fr">Français - French</option>
                <option value="de">Deutsch - German</option>
                <option value="ja">日本語 - Japanese</option>
                <option value="ko">한국어 - Korean</option>
                <option value="ru">Русский - Russian</option>
                <option value="pt">Português - Portuguese</option>
                <option value="id">Bahasa Indonesia - Indonesian</option>
                <option value="hi">हिन्दी - Hindi</option>
                <option value="zh-Hans" selected>简体中文 - Chinese (Simplified)</option>
                <option value="zh-Hant">繁體中文 - Chinese (Traditional)</option>
            </select>
            <h1 class="h3 mb-3 fw-normal">@lang('fresns.login')</h1>
            <div class="form-floating">
                <input type="text" class="form-control rounded-bottom-0" id="account" name="account" placeholder="@lang('fresns.account')">
                <label for="account">@lang('fresns.account')</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control rounded-top-0 border-top-0" id="password" name="password" placeholder="@lang('fresns.password')">
                <label for="password">@lang('fresns.password')</label>
            </div>
            <input type="text" style="display:none;" id="lang" name="lang">
            <button class="w-100 btn btn-lg btn-primary mt-4" onclick="return checkData()" >@lang('fresns.enter')</button>
            <p class="mt-5 mb-5 text-muted">&copy; 2021 Fresns</p>
        </form>
    </main>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/jquery-3.6.0.min.js"></script>
    <script src="/assets/js/console.js"></script>
    <script src="/assets/js/base64.js"></script>

    <script>
        $(document).ready(function(){
            var val = getQueryVariable("lang"); 
            $('.form-select option[value="'+val+'"]').prop("selected","selected");
            if(val){
                $('#lang').val(val)
            }
            $("#form-select").change(function(){
                var url = $('.form-select option:selected').val();
                location.search = '?lang='+url
            })
            function getQueryVariable(variable)
                {
                    var query = window.location.search.substring(1);
                    var vars = query.split("&");
                    for (var i=0;i<vars.length;i++) {
                            var pair = vars[i].split("=");
                            if(pair[0] == variable){return pair[1];}
                    }
                    return(false);
                }
        });
        // 提交创建
        var isLogin = false;

        function checkData(){
            var result = true;
            if(isLogin){return}
            var account = $('#account').val();
            var password = $('#password').val();
            var lang = $('.form-select option:selected').val();

            isLogin = false;
            password = Base64.encode(password);
            $('#password').val(password)
            console.log(password)
            $.ajax({
                async: false,    //设置为同步
                type: "post",
                url: "/checkLogin",
                data: {'account':account,'password':password,'lang':lang},
                beforeSend: function (request) {
                        return request.setRequestHeader('X-CSRF-Token', "{{ csrf_token() }}");
                    },
                success: function (data) {
                    console.log(data)
                    isLogin = false;
                    if(data.code != 0){
                        result = false;
                        alert(data.message)
                    }else{
                    
                    }
                }
            });

            return result
        }
    </script>
</body>

</html>
