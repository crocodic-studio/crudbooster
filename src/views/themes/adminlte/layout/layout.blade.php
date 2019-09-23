<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ themeTitle($page_title) }}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link href="{{cbAsset('js/bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <!-- Font Awesome Icons -->
    <link href="{{cbAsset("js/font-awesome/css/font-awesome.min.css")}}" rel="stylesheet" type="text/css"/>

    <!-- Theme style -->
    <link href="{{ cbAsset("adminlte/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css"/>
    <link href="{{ cbAsset("adminlte/dist/css/skins/".getSetting("adminlte_theme_skin","skin-green").".css")}}" rel="stylesheet" type="text/css"/>

    @include(themeLayoutHead())

    @if($additional_css = getSetting("adminlte_additional_css"))
        <style>
            {!! $additional_css !!}
        </style>
    @endif
</head>
<body class="hold-transition {{getSetting("adminlte_theme_skin","skin-green")}} sidebar-mini">
<div id='app' class="wrapper">

    <!-- Header -->
    @include('crudbooster::themes.adminlte.layout.header')

    <!-- Sidebar -->
    @include('crudbooster::themes.adminlte.layout.sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

        <section class="content-header">

            <h1>@include(themeLayoutPageTitleAndButton())</h1>

            <ol class="breadcrumb">
                <li><a href="{{ cb()->getAdminUrl() }}"><i class="fa fa-dashboard"></i> {{ cbLang("home") }}</a></li>
                <li class="active">{{ isset($page_title)?$page_title:null  }}</li>
            </ol>
        </section>


        <!-- Main content -->
        <section id='content_section' class="content">

            @include(themeAlertMessage())

            @include(themeFlashMessage())

            @yield('content')
        </section><!-- /.content -->
    </div><!-- /.content-wrapper -->

    <!-- Footer -->
    @include('crudbooster::themes.adminlte.layout.footer')

</div><!-- ./wrapper -->

@include('crudbooster::layouts.javascripts')

<!-- AdminLTE App -->
<script src="{{ cbAsset('adminlte/dist/js/adminlte.min.js') }}" type="text/javascript"></script>

@include('crudbooster::layouts.bottom')
</body>
</html>
