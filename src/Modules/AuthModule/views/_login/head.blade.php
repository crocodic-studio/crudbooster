<meta charset="UTF-8">
<title>{{cbTrans("page_title_login")}} : {{cbGetsetting('appname')}}</title>
<meta name='generator' content='CRUDBooster'/>
<meta name='robots' content='noindex,nofollow'/>
<link rel="shortcut icon"
      href="{{ cbGetsetting('favicon')?asset(cbGetsetting('favicon')):cbAsset('logo_crudbooster.png') }}">

<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
<!-- Bootstrap 3.3.2 -->
{!! cbStyleSheet('adminlte/bootstrap/css/bootstrap.min.css') !!}

<!-- Font Awesome Icons -->
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet"
      type="text/css"/>
<!-- Theme style -->
{!! cbStyleSheet('adminlte/dist/css/AdminLTE.min.css') !!}

<!-- support rtl-->
@if(in_array(App::getLocale(), ['ar', 'fa']))
    <link rel="stylesheet" href="//cdn.rawgit.com/morteza/bootstrap-rtl/v3.3.4/dist/css/bootstrap-rtl.min.css">
    {!! cbStyleSheet('rtl.css') !!}
@endif

@include('crudbooster::_IE9')

{!! cbStyleSheet('css/main.css') !!}
<style type="text/css">
    .login-page, .register-page {
        background: {{ cbGetsetting("login_background_color")?:'#dddddd'}} url('{{ cbGetsetting("login_background_image")?asset(cbGetsetting("login_background_image")):cbAsset('bg_blur3.jpg') }}');
        color: {{ cbGetsetting("login_font_color")?:'#ffffff' }}   !important;
        background-repeat: no-repeat;
        background-position: center;
        background-size: cover;
    }

    .login-box, .register-box {
        margin: 2% auto;
    }

    .login-box-body {
        box-shadow: 0px 0px 50px rgba(0, 0, 0, 0.8);
        background: rgba(255, 255, 255, 0.9);
        color: {{ cbGetsetting("login_font_color")?:'#666666' }}   !important;
    }

    html, body {
        overflow: hidden;
    }
</style>