<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{trans("crudbooster.page_title_login")}} : {{ cb()->getAppName() }}</title>
    <meta name='generator' content='CRUDBooster'/>
    <meta name='robots' content='noindex,nofollow'/>
    <link rel="shortcut icon" href="{{ getSetting('favicon')?asset(getSetting('favicon')):cbAsset('logo.png') }}">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link href="{{cbAsset('adminlte/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <!-- Font Awesome Icons -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <!-- Theme style -->
    <link href="{{cbAsset('adminlte/dist/css/AdminLTE.min.css')}}" rel="stylesheet" type="text/css"/>
    <!-- support rtl-->
    @if (in_array(App::getLocale(), ['ar', 'fa']))
        <link rel="stylesheet" href="//cdn.rawgit.com/morteza/bootstrap-rtl/v3.3.4/dist/css/bootstrap-rtl.min.css">
        <link href="{{ cbAsset("rtl.css")}}" rel="stylesheet" type="text/css"/>
    @endif

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <link rel='stylesheet' href='{{ cbAsset("css/main.css")}}'/>
    <style type="text/css">
        body {
            background: #ffffff url("{{ cbAsset("images/bg_login.png") }}");
            color: #111111  !important;
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
        }

        .login-box {
            width: 850px;
            margin: 10% auto;
            border-radius: 15px;
            background: #ffffff url("{{ cbAsset("images/image_login.png") }}");
            background-repeat: no-repeat;
            background-position: left;
            box-shadow: 0px 0px 20px #bbbbbb;
        }

        @media(max-width: 720px) {
            .login-box {
                width: 100%;
                background-position: center;
                color: #ffffff;
                text-shadow: 0px 0px 3px #999999;
            }
        }

        .login-body {
            padding: 40px;
        }

        html, body {
            overflow: hidden;
        }

    </style>
</head>

<body>

<div class="login-box">
    <div class="row">
        <div class="col-sm-6">

        </div>
        <div class="col-sm-6">
            <div class="login-body">
                @if ( Session::get('message') != '' )
                    <div class='alert alert-warning'>
                        {{ Session::get('message') }}
                    </div>
                @endif

                @yield('content')
            </div><!-- /.login-box-body -->
        </div>
    </div>

</div><!-- /.login-box -->


<!-- jQuery 2.1.3 -->
<script src="{{cbAsset('adminlte/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
<!-- Bootstrap 3.3.2 JS -->
<script src="{{cbAsset('adminlte/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>
</body>
</html>