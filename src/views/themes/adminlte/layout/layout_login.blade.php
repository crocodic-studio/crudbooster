<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ cbLang("login_page")}} : {{ cb()->getAppName() }}</title>
    <meta name='generator' content='CRUDBooster'/>
    <meta name='robots' content='noindex,nofollow'/>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link href="{{cbAsset('js/bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="{{ cbAsset("css/login.css") }}?v=1.0.1">

    @include(themeLoginBackground())
</head>
<body>

<div class="login-wrapper">

    @if($loginLogo = getSetting("login_logo"))
        <div align="center" style="margin-bottom: 30px">
            <img src="{{ asset($loginLogo) }}" style="max-width: 400px; max-height: 190px" alt="Login Logo">
        </div>
    @endif

    <div class="login-box">
        <div class="login-body">

            @yield('content')

        </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->
</div>

{{--<!-- jQuery 2.1.3 -->--}}
<script src="{{cbAsset('js/jquery/dist/jquery.min.js')}}"></script>
{{--<!-- Bootstrap 3.3.2 JS -->--}}
{{--<script src="{{cbAsset('adminlte/bower_components/bootstrap/dist/js/bootstrap.min.js')}}" type="text/javascript"></script>--}}
@stack("bottom")
</body>
</html>