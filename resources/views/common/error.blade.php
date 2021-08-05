<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="Fresns" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no,viewport-fit=cover">
    <title>Fresns {{$status}}</title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/console.css">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="/">
                    <img src="/assets/images/fresns-logo.png" alt="Fresns" height="30" class="d-inline-block align-text-top">
                </a>
            </div>
        </nav>
    </header>
    <main class="container">
        <div class="card mx-auto my-5">
            <div class="card-body p-5">
                <h3 class="card-title">Fresns {{$status}}</h3>
                <p>{{$msg}}</p>

                @if(env('APP_DEBUG') && true)
                <div class="fs-9 mt-4 overflow-auto">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">File</th>
                                <th scope="col">Line</th>
                                <th scope="col">Function</th>
                                <th scope="col">Class</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($traceMsgArr as $idx => $item)
                            <tr>
                                <th scope="row">{{$idx + 1}}</th>
                                <td>{{$item['file']}}</td>
                                <td>{{$item['line']}}</td>
                                <td>{{$item['function']}}</td>
                                <td>{{$item['class']}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

            </div>
        </div>
    </main>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>