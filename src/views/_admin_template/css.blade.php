{!! cbStyleSheet('adminlte/bootstrap/css/bootstrap.min.css') !!}
{!! cbStyleSheet('adminlte/font-awesome/css/font-awesome.min.css') !!}
<!-- Ionicons -->
<link href="{{asset("vendor/crudbooster/ionic/css/ionicons.min.css")}}" rel="stylesheet" type="text/css"/>
<!-- Theme style -->
{!! cbStyleSheet('adminlte/dist/css/AdminLTE.min.css') !!}
{!! cbStyleSheet('adminlte/dist/css/skins/_all-skins.min.css') !!}

<!-- support rtl-->
@if (App::getLocale() == 'ar')
    <link rel="stylesheet" href="//cdn.rawgit.com/morteza/bootstrap-rtl/v3.3.4/dist/css/bootstrap-rtl.min.css">
    <link href="{{ cbAsset("rtl.css")}}" rel="stylesheet" type="text/css"/>
@endif

<link rel='stylesheet' href='{{cbAsset("css/main.css").'?r='.time()}}'/>

<!-- load css -->
<style type="text/css">
    @if($style_css)
        {!! $style_css !!}
    @endif
</style>
@if($load_css)
    @foreach($load_css as $css)
        <link href="{{$css}}" rel="stylesheet" type="text/css"/>
    @endforeach
@endif

<style type="text/css">
    .dropdown-menu-action {
        left: -130%;
    }

    .btn-group-action .btn-action {
        cursor: default
    }

    #box-header-module {
        box-shadow: 10px 10px 10px #dddddd;
    }

    .sub-module-tab li {
        background: #F9F9F9;
        cursor: pointer;
    }

    .sub-module-tab li.active {
        background: #ffffff;
        box-shadow: 0px -5px 10px #cccccc
    }

    .nav-tabs > li.active > a, .nav-tabs > li.active > a:focus, .nav-tabs > li.active > a:hover {
        border: none;
    }

    .nav-tabs > li > a {
        border: none;
    }

    .breadcrumb {
        margin: 0 0 0 0;
        padding: 0 0 0 0;
    }

    .form-group > label:first-child {
        display: block
    }
</style>