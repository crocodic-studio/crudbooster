<!DOCTYPE html>
<html>
<head>
    @include('CbAuth::_login.head')
</head>

<body class="login-page">

<div class="login-box">

    <div class="login-logo">
        <a href="{{url('/')}}">
            <img title='{!!(cbGetsetting('appname') == 'CRUDBooster')?"<b>CRUD</b>Booster":cbGetsetting('appname')!!}'
                 src='{{ cbGetsetting("logo")?asset(cbGetsetting('logo')):cbAsset('logo_crudbooster.png') }}'
                 style='max-width: 100%;max-height:170px'/>
        </a>
    </div>

    <div class="login-box-body">

        @if ( session('message') != '' )
            <div class='alert alert-warning'>
                {{ session('message') }}
            </div>
        @endif

        <p class='login-box-msg'>{{cbTrans("login_message")}}</p>
        @include('CbAuth::_login.form')
        <br/>
    </div>

</div>


<!-- jQuery 2.1.3 -->
{!! cbScript('adminlte/plugins/jQuery/jQuery-2.1.4.min.js') !!}
<!-- Bootstrap 3.3.2 JS -->
{!! cbScript('adminlte/bootstrap/js/bootstrap.min.js') !!}
</body>
</html>