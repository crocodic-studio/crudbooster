<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ ($page_title)?CRUDBooster::getSetting('appname').': '.strip_tags($page_title):"Admin Area" }}</title>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="generator" content="CRUDBooster 5.3">
    <meta name="robots" content="noindex,nofollow">
    <link rel="shortcut icon" href="{{ CRUDBooster::getSetting('favicon')?asset(CRUDBooster::getSetting('favicon')):asset('vendor/crudbooster/assets/logo_crudbooster.png') }}">

    <!-- Include Admin plugins files -->
	@include('crudbooster::admin_template_plugins')

	<!-- Include Admin CSS files -->
	@yield('admin_template_plugins_css')

    <!-- load css -->
    <style type="text/css">
        @if($style_css)
            {!! $style_css !!}
        @endif
    </style>

    @if($load_css)
        @foreach($load_css as $css)
            <link href="{{$css}}" rel="stylesheet" type="text/css" />
        @endforeach
    @endif

	<!-- Include custom CSS files or rules from blade-->
	@stack('css')

</head>
<body class="{{ (Session::get('theme_color')) ?:'skin-blue'}} {{ config('crudbooster.ADMIN_LAYOUT') }}">

<div id="app" class="wrapper">    

    <!-- Header -->
    @include('crudbooster::partials.header')

    <!-- Sidebar -->
    @include('crudbooster::partials.sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

        <section class="content-header">
          <?php 
            $module = CRUDBooster::getCurrentModule();
          ?>
          @if($module)
          <h1>
            <i class="{{$module->icon}}"></i>  {{($page_title)?:$module->name}} &nbsp;&nbsp;

            <!--START BUTTON -->         

            @if(CRUDBooster::getCurrentMethod() == 'getIndex')
            @if($button_show)
            <a href="{{ CRUDBooster::mainpath().'?'.http_build_query(Request::all()) }}" id='btn_show_data' class="btn btn-sm btn-primary" title="{{trans('crudbooster.action_show_data')}}">
              <i class="fa fa-table"></i> {{trans('crudbooster.action_show_data')}}
            </a>
            @endif

            @if($button_add && CRUDBooster::isCreate())                          
            <a href="{{ CRUDBooster::mainpath('add').'?return_url='.urlencode(Request::fullUrl()).'&parent_id='.g('parent_id').'&parent_field='.$parent_field }}" id='btn_add_new_data' class="btn btn-sm btn-success" title="{{trans('crudbooster.action_add_data')}}">
              <i class="fa fa-plus-circle"></i> {{trans('crudbooster.action_add_data')}}
            </a>
            @endif                          
            @endif

              
            @if($button_export)
            <a href="javascript:void(0)" id='btn_export_data' data-url-parameter='{{$build_query}}' title='Export Data' class="btn btn-sm btn-primary btn-export-data">
              <i class="fa fa-upload"></i> {{trans("crudbooster.button_export")}}
            </a>
            @endif

            @if($button_import)
            <a href="{{ CRUDBooster::mainpath('import-data') }}" id='btn_import_data' data-url-parameter='{{$build_query}}' title='Import Data' class="btn btn-sm btn-primary btn-import-data">
              <i class="fa fa-download"></i> {{trans("crudbooster.button_import")}}
            </a>
            @endif

            <!--ADD ACTIon-->
             @if(count($index_button))                          
               
                    @foreach($index_button as $ib)
                     <a href='{{$ib["url"]}}' id='{{str_slug($ib["label"])}}' class='btn {{($ib['color'])?'btn-'.$ib['color']:'btn-primary'}} btn-sm' 
                      @if($ib['onClick']) onClick='return {{$ib["onClick"]}}' @endif
                      @if($ib['onMouseOever']) onMouseOever='return {{$ib["onMouseOever"]}}' @endif
                      @if($ib['onMoueseOut']) onMoueseOut='return {{$ib["onMoueseOut"]}}' @endif
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
            <li><a href="{{CRUDBooster::adminPath()}}"><i class="fa fa-dashboard"></i> {{ trans('crudbooster.home') }}</a></li>
            <li class="active">{{$module->name}}</li>
          </ol>
          @else
          <h1>{{CRUDBooster::getSetting('appname')}} <small>Information</small></h1>
          @endif
        </section>	
		

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
				<div class="alert alert-{{ Session::get('message_type') }}">
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
    @include('crudbooster::partials.footer')

</div><!-- ./wrapper -->
	
	<!-- Include Admin JS files -->
	@yield('admin_template_plugins_js')
	
	<!-- Include custom JS files or functions from blade -->
	@stack('javascript')
	
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
	
	
	
<!-- Optionally, you can add Slimscroll and FastClick plugins.
      Both of these plugins are recommended to enhance the
      user experience -->
</body>
</html>
