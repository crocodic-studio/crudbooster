<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ ($page_title)?Session::get('appname').': '.strip_tags($page_title):"Admin Area" }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta name='generator' content='CRUDBooster 5.5.0'/>
    <meta name='robots' content='noindex,nofollow'/>

    <!-- maxcdn -->
    <link rel="dns-prefetch" href="https://oss.maxcdn.com/">
    <link rel="preconnect" href="https://oss.maxcdn.com/" crossorigin>

    <!-- rawgit -->
    <link rel="dns-prefetch" href="https://cdn.rawgit.com/">
    <link rel="preconnect" href="https://cdn.rawgit.com/" crossorigin>

    <link rel="shortcut icon"
          href="{{ cb()->getSetting('favicon')?asset(cb()->getSetting('favicon')):asset('vendor/crudbooster/assets/logo_crudbooster.png') }}">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.4.1 -->
    <link href="{{ asset("vendor/crudbooster/assets/adminlte/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css"/>
    <!-- Font Awesome Icons -->
    <link href="{{asset("vendor/crudbooster/assets/adminlte/font-awesome/css")}}/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <!-- Ionicons -->
    <link href="{{asset("vendor/crudbooster/ionic/css/ionicons.min.css")}}" rel="stylesheet" type="text/css"/>
    <!-- Theme style -->
    <link href="{{ asset("vendor/crudbooster/assets/adminlte/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset("vendor/crudbooster/assets/adminlte/dist/css/skins/_all-skins.min.css")}}" rel="stylesheet" type="text/css"/>

    <!-- support rtl-->
    @if (in_array(App::getLocale(), ['ar', 'fa']))
        <link rel="stylesheet" href="//cdn.rawgit.com/morteza/bootstrap-rtl/v3.3.4/dist/css/bootstrap-rtl.min.css">
        <link href="{{ asset("vendor/crudbooster/assets/rtl.css")}}" rel="stylesheet" type="text/css"/>
    @endif

    <link rel='stylesheet' href='{{asset("vendor/crudbooster/assets/css/main.css").'?v=1.0.3'}}'/>

    <!-- load css -->
    <style type="text/css">
        @if(isset($style_css))
            {!! $style_css !!}
        @endif
    </style>
    @if(isset($load_css))
        @foreach($load_css as $css)
            <link href="{{$css}}" rel="stylesheet" type="text/css"/>
        @endforeach
    @endif

    @stack('head')
</head>
<body class="@php echo (Session::get('theme_color'))?:'skin-blue'; echo ' '; echo config('crudbooster.ADMIN_LAYOUT'); @endphp {{(isset($sidebar_mode))?$sidebar_mode:''}}">
<div id='app' class="wrapper">

    <!-- Header -->
    @include('crudbooster::header')

    <!-- Sidebar -->
    <!-- Left side column. contains the sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel">
                <div class="pull-{{ trans('crudbooster.left') }} image">
                    <img src="{{ cb()->myPhoto() }}" class="rounded-image" alt="{{ trans('crudbooster.user_image') }}"/>
                </div>
                <div class="pull-{{ trans('crudbooster.left') }} info">
                    <p>{{ cb()->myName() }}</p>
                    <!-- Status -->
                    <a href="#"><i class="fa fa-circle text-success"></i> {{ trans('crudbooster.online') }}</a>
                </div>
            </div>

            <div class='main-menu'>
                <!-- Sidebar Menu -->
                <ul class="sidebar-menu">
                    <li class="header">{{trans("crudbooster.menu_navigation")}}</li>
                    <!-- Optionally, you can add icons to the links -->
                    @include(config('crudbooster.SIDEBAR_VIEW','crudbooster::sidebar'))
                </ul><!-- /.sidebar-menu -->
            </div>
        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

        <section class="content-header">
            <h1>
                <!--Now you can define $page_icon alongside $page_tite for custom forms to follow CRUDBooster theme style -->
                <i class='{!! $page_icon !!}'></i> {!! $page_title !!} &nbsp;&nbsp;

                @if(cb()->getCurrentMethod() == 'getIndex')
                    @if($button_show)
                        <a href="{{ cb()->mainpath().'?'.http_build_query(Request::all()) }}" id='btn_show_data' class="btn btn-sm btn-primary"
                           title="{{trans('crudbooster.action_show_data')}}">
                            <i class="fa fa-table"></i> {{trans('crudbooster.action_show_data')}}
                        </a>
                    @endif

                    @if($button_add && cb()->isCreate())
                        <a href="{{ cb()->mainpath('add').'?return_url='.urlencode(Request::fullUrl()).'&parent_id='.g('parent_id').'&parent_field='.$parent_field }}"
                           id='btn_add_new_data' class="btn btn-sm btn-success" title="{{trans('crudbooster.action_add_data')}}">
                            <i class="fa fa-plus-circle"></i> {{trans('crudbooster.action_add_data')}}
                        </a>
                    @endif
                @endif


                @if($button_export && cb()->getCurrentMethod() == 'getIndex')
                    <a href="javascript:void(0)" id='btn_export_data' data-url-parameter='{{$build_query}}' title='Export Data'
                       class="btn btn-sm btn-default btn-export-data">
                        <i class="fa fa-upload"></i> {{trans("crudbooster.button_export")}}
                    </a>
                @endif

                @if($button_import && cb()->getCurrentMethod() == 'getIndex')
                    <a href="{{ cb()->mainpath('import-data') }}" id='btn_import_data' data-url-parameter='{{$build_query}}' title='Import Data'
                       class="btn btn-sm btn-default btn-import-data">
                        <i class="fa fa-download"></i> {{trans("crudbooster.button_import")}}
                    </a>
                @endif

                @if(!empty($index_button))

                    @foreach($index_button as $ib)
                        <a href='{{$ib["url"]}}' id='{{str_slug($ib["label"])}}' class='btn {{($ib['color'])?'btn-'.$ib['color']:'btn-primary'}} btn-sm'
                           @if($ib['onClick']) onClick='return {{$ib["onClick"]}}' @endif
                           @if($ib['onMouseOver']) onMouseOver='return {{$ib["onMouseOver"]}}' @endif
                           @if($ib['onMouseOut']) onMouseOut='return {{$ib["onMouseOut"]}}' @endif
                           @if($ib['onKeyDown']) onKeyDown='return {{$ib["onKeyDown"]}}' @endif
                           @if($ib['onLoad']) onLoad='return {{$ib["onLoad"]}}' @endif
                        >
                            <i class='{{$ib["icon"]}}'></i> {{$ib["label"]}}
                        </a>
                @endforeach
            @endif
            <!-- END BUTTON -->
            </h1>

            <ol class="breadcrumb">
                <li><a href="{{cb()->adminPath()}}"><i class="fa fa-dashboard"></i> {{ trans('crudbooster.home') }}</a></li>
                <li class="active">{{ isset($module)?$module->name:$page_title }}</li>
            </ol>
        </section>


        <!-- Main content -->
        <section id='content_section' class="content">

            @if(@$alerts)
                @foreach(@$alerts as $alert)
                    <div class='callout callout-{{$alert["type"]}}'>
                        {!! $alert['message'] !!}
                    </div>
                @endforeach
            @endif


            @if(Session::get('message')!='')
                <div class='alert alert-{{ Session::get("type") }}'>
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-info"></i> {{ trans("crudbooster.alert_".Session::get("type")) }}</h4>
                    {!!Session::get('message')!!}
                </div>
            @endif



        <!-- Your Page Content Here -->
            @yield('content')
        </section><!-- /.content -->
    </div><!-- /.content-wrapper -->

    <!-- Footer -->
    @include('crudbooster::footer')

</div><!-- ./wrapper -->


@include('crudbooster::admin_template_plugins')

<!-- load js -->
@if($load_js)
    @foreach($load_js as $js)
        <script src="{{$js}}"></script>
    @endforeach
@endif
<script type="text/javascript">
    var site_url = "{{url('/')}}";
    @if($script_js)
        {!! $script_js !!}
    @endif
</script>

@stack('bottom')

<!-- Optionally, you can add Slimscroll and FastClick plugins.
      Both of these plugins are recommended to enhance the
      user experience -->
</body>
</html>
