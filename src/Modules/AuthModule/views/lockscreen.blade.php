<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>::LOCKSCREEN::</title>
    <meta name='generator' content='CRUDBooster'/>
    <meta name='robots' content='noindex,nofollow'/>
    <link rel="shortcut icon"
          href="{{ CRUDBooster::getSetting('favicon')?asset(CRUDBooster::getSetting('favicon')):cbAsset('logo_crudbooster.png') }}">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
{!! cbStyleSheet('adminlte/bootstrap/css/bootstrap.min.css') !!}

<!-- Font Awesome Icons -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet"
          type="text/css"/>
    <!-- Theme style -->
    {!! cbStyleSheet('adminlte/dist/css/AdminLTE.min.css') !!}


    @include('crudbooster::_IE9')

    {!! cbStyleSheet('css/main.css') !!}
    <style type="text/css">
        .lockscreen {
            background: {{ CRUDBooster::getSetting("login_background_color")?:'#dddddd'}} url('{{ CRUDBooster::getSetting("login_background_image")?asset(CRUDBooster::getSetting("login_background_image")):cbAsset('bg_blur3.jpg') }}');
            color: {{ CRUDBooster::getSetting("login_font_color")?:'#ffffff' }}   !important;
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
        }
    </style>

</head>
<body class="lockscreen">
<!-- Automatic element centering -->
<div class="lockscreen-wrapper">
    <div class="lockscreen-logo">
        <a href="{{url('/')}}">
            <img title='{!!($appname == 'CRUDBooster')?"<b>CRUD</b>Booster":$appname!!}'
                 src='{{ CRUDBooster::getSetting("logo")?asset(CRUDBooster::getSetting('logo')):cbAsset('logo_crudbooster.png') }}'
                 style='max-width: 100%;max-height:170px'/>
        </a>
    </div>
    <!-- User name -->
    <div class="lockscreen-name">{{Session::get('admin_name')}}</div>

    <!-- START LOCK SCREEN ITEM -->
    <div class="lockscreen-item">
        <!-- lockscreen image -->
        <div class="lockscreen-image">
            <img src="{{ (Session::get('admin_photo'))?:asset("assets/adminlte/dist/img/user2-160x160.jpg") }}"
                 alt="user image"/>
        </div>
        <!-- /.lockscreen-image -->

        <!-- lockscreen credentials (contains the form) -->
        <form class="lockscreen-credentials" method='post'
              action="{{url(cbAdminPath().'/unlock-screen')}}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
            <div class="input-group">
                <input type="password" class="form-control" required name='password' placeholder="password"/>
                <div class="input-group-btn">
                    <button class="btn"><i class="fa fa-arrow-right text-muted"></i></button>
                </div>
            </div>
        </form><!-- /.lockscreen credentials -->

    </div><!-- /.lockscreen-item -->
    <div class="text-center">
        {{cbTrans("text_enter_the_password")}}
    </div>
    <div class='text-center'>
        <a href="{{route("getLogout")}}">{{cbTrans('text_or_sign_in')}}</a>
    </div>
    <div class='lockscreen-footer text-center'>
        Copyright &copy; {{date("Y")}}<br>
        All rights reserved
    </div>
</div><!-- /.center -->


{!! cbScript('adminlte/plugins/jQuery/jQuery-2.1.4.min.js') !!}
<!-- Bootstrap 3.3.2 JS -->
{!! cbScript('adminlte/bootstrap/js/bootstrap.min.js') !!}
</body>
</html>