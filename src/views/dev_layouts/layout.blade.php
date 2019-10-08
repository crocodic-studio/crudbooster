<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    @if(isset($page_title))
        <title>{{ $page_title }} - {{ cb()->getAppName() }}</title>
    @else
        <title>{{ cb()->getAppName() }}</title>
    @endif
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta name='generator' content='CRUDBooster'/>
    <meta name='robots' content='noindex,nofollow'/>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link href="{{cbAsset('js/bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <!-- Font Awesome Icons -->
    <link href="{{cbAsset("js/font-awesome/css/font-awesome.min.css")}}" rel="stylesheet" type="text/css"/>

    <!-- Theme style -->
    <link href="{{ cbAsset("adminlte/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css"/>
    <link href="{{ cbAsset("adminlte/dist/css/skins/skin-green.css")}}" rel="stylesheet" type="text/css"/>

    <link rel='stylesheet' href='{{cbAsset("css/main.css")}}'/>

    @stack('head')
</head>
<body class="hold-transition {{ isset($sidebar_collapse)?"sidebar-collapse":"" }} skin-green sidebar-mini">
<div id='app' class="wrapper">

    <!-- Header -->
    @include('crudbooster::dev_layouts.header')

    <!-- Sidebar -->
    @include('crudbooster::dev_layouts.sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                {{ $page_title }}
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ cb()->getDeveloperUrl() }}"><i class="fa fa-dashboard"></i> {{ __('cb::cb.home') }}</a></li>
                <li class="active">{{ $page_title }}</li>
            </ol>
        </section>


        <!-- Main content -->
        <section id='content_section' class="content">

            @if (session()->has('message'))
                <div class='callout callout-{{ session('message_type') }}'>
                   <strong>{{ ucwords(session('message_type'))  }}!</strong> {!! session('message') !!}
                </div>
            @endif


            <!-- Your Page Content Here -->
            @yield('content')
        </section><!-- /.content -->
    </div><!-- /.content-wrapper -->

    <!-- Footer -->
    @include('crudbooster::dev_layouts.footer')

</div><!-- ./wrapper -->

@include('crudbooster::layouts.javascripts')

@stack('bottom')

<script>
    function showLoading(message) {
        $("#modal-loading").modal({
            show: true,
            backdrop: 'static',
            keyboard: false
        })
        if(message) {
            $("#modal-loading .modal-body p").html(message)
        } else {
            $("#modal-loading .modal-body p").html("Please wait while loading...")
        }
    }
    function hideLoading() {
        $("#modal-loading").modal("hide")
    }
</script>
<div class="modal" id="modal-loading">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div align="center">
                    <h1 align="center"><i class="fa fa-spin fa-spinner"></i></h1>
                    <p> </p>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

</body>
</html>
