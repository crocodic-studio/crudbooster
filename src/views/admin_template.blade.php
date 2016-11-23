<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ ($page_title)?CRUDBooster::getSetting('appname').': '.strip_tags($page_title):"Admin Area" }}</title>
	<meta name="csrf-token" content="{{ csrf_token() }}" />
	<meta name='generator' content='CRUDBooster 5.3'/>
    <meta name='robots' content='noindex,nofollow'/>
    <link rel="shortcut icon" href="{{ CRUDBooster::getSetting('favicon')?asset(CRUDBooster::getSetting('favicon')):asset('vendor/crudbooster/assets/logo_crudbooster.png') }}">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    
    @include('crudbooster::admin_template_plugins')
    
    <!-- Theme style -->
    <link href="{{ asset("vendor/crudbooster/assets/adminlte/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />    
    <link href="{{ asset("vendor/crudbooster/assets/adminlte/dist/css/skins/_all-skins.min.css")}}" rel="stylesheet" type="text/css" />      

    <!-- load js -->
    <script type="text/javascript">
      var site_url = "{{url('/')}}" ;
      @if($script_js)
        {!! $script_js !!}
      @endif 
    </script>
    @if($load_js)
      @foreach($load_js as $js)
        <script src="{{$js}}"></script>
      @endforeach
    @endif
    <style type="text/css">
        .dropdown-menu-action {left:-130%;}
        .btn-group-action .btn-action {cursor: default}
        #box-header-module {box-shadow:10px 10px 10px #dddddd;}
        .sub-module-tab li {background: #F9F9F9;cursor:pointer;}
        .sub-module-tab li.active {background: #ffffff;box-shadow: 0px -5px 10px #cccccc}
        .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover {border:none;}
        .nav-tabs>li>a {border:none;}                
        .breadcrumb {
            margin:0 0 0 0;
            padding:0 0 0 0;
        }
        .form-group > label:first-child {display: block}
    </style>
</head>
<body class="<?php echo (Session::get('theme_color'))?:'skin-blue'?>">
<div id='app' class="wrapper">    

    <!-- Header -->
    @include('crudbooster::header')

    <!-- Sidebar -->
    @include('crudbooster::sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">		
		

        <!-- Main content -->
        <section id='content_section' class="content">

        	@if(@$alerts)
        		@foreach(@$alerts as $alert)
        			<div class='callout callout-{{$alert[type]}}'>        				
        					{!! $alert['message'] !!}
        			</div>
        		@endforeach
        	@endif


			@if (Session::get('message')!='')
			<div class='alert alert-{{ Session::get("message_type") }}'>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<h4><i class="icon fa fa-info"></i> {{ trans("crudbooster.alert_".Session::get("message_type")) }}</h4>
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

<!-- Optionally, you can add Slimscroll and FastClick plugins.
      Both of these plugins are recommended to enhance the
      user experience -->
</body>
</html>