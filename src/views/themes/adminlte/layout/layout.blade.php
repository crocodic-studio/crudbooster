<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ (isset($page_title))?cb()->getAppName().': '.strip_tags($page_title):"Admin Area" }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta name='generator' content='CRUDBooster'/>
    <meta name='robots' content='noindex,nofollow'/>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link href="{{cbAsset('adminlte/bower_components/bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <!-- Font Awesome Icons -->
    <link href="{{cbAsset("adminlte/bower_components/font-awesome/css/font-awesome.min.css")}}" rel="stylesheet" type="text/css"/>

    <!-- Theme style -->
    <link href="{{ cbAsset("adminlte/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css"/>
    <link href="{{ cbAsset("adminlte/dist/css/skins/".getSetting("cms_theme_color","skin-blue").".css")}}" rel="stylesheet" type="text/css"/>

    <link rel='stylesheet' href='{{cbAsset("css/main.css")}}?v=1.2'/>

    @if(isset($head_script))
        {!! $head_script !!}
    @endif

    @stack('head')

    @include("crudbooster::layouts.layout_appearance_custom")

    @if($style = module()->getData("style"))
        <style>
            {!! call_user_func($style) !!}
        </style>
    @endif
</head>
<body class="hold-transition {{getSetting("cms_theme_color","skin-blue")}} sidebar-mini">
<div id='app' class="wrapper">

    <!-- Header -->
    @include('crudbooster::layouts.header')

    <!-- Sidebar -->
    @include('crudbooster::layouts.sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

        <section class="content-header">

            <h1>
                @if(request()->is(cb()->getAdminPath()))
                    <i class="fa fa-dashboard"></i> {{ cbLang("dashboard") }}
                @else
                    @if(module()->getPageIcon())
                        <i class="{{ module()->getPageIcon() }}"></i>
                    @elseif(!module()->getPageIcon() && isset($pageIcon))
                        <i class="{{ $pageIcon }}"></i>
                    @endif

                    @if(module()->getPageTitle())
                        {{ module()->getPageTitle() }}
                    @elseif(!module()->getPageTitle() && isset($page_title))
                        {{ $page_title }}
                    @endif
                @endif

                @include('crudbooster::module.index.index_head_buttons')
            </h1>


            <ol class="breadcrumb">
                <li><a href="{{ cb()->getAdminUrl() }}"><i class="fa fa-dashboard"></i> {{ cbLang("home") }}</a></li>
                <li class="active">{{ (module()->getPageTitle()?:@$page_title)?:null }}</li>
            </ol>
        </section>


        <!-- Main content -->
        <section id='content_section' class="content">

                @if(isset($alert_message))
                    <div class='callout callout-{{$alert_message_type}}'>
                        {!! $alert_message !!}
                    </div>
                @endif


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
    @include('crudbooster::layouts.footer')

</div><!-- ./wrapper -->

@include('crudbooster::layouts.javascripts')

@if(isset($bottom_view))
    @include($bottom_view)
@endif

@stack('bottom')
</body>
</html>
