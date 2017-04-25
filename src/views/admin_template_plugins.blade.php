@section('admin_template_plugins_css')
	
	<!-- Bootstrap 3.3.2 -->
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/crudbooster/assets/adminlte/bootstrap/css/bootstrap.min.css') }}">
	
	<!-- Font Awesome Icons -->
    <link rel="stylesheet" type="text/css" href="{{asset('vendor/crudbooster/assets/adminlte/font-awesome/css')}}/font-awesome.min.css" >
	
    <!-- Ionicons -->
    <link rel="stylesheet" type="text/css" href="https://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css">
	
	<!--BOOTSTRAP DATEPICKER-->	
	<link rel="stylesheet" type="text/css" href="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/datepicker/datepicker3.css') }}">

	<!--BOOTSTRAP DATERANGEPICKER-->
	<link rel="stylesheet" type="text/css" href="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/daterangepicker/daterangepicker-bs3.css') }}">

	<!-- Bootstrap time Picker -->
	<link rel="stylesheet" type="text/css" href="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/timepicker/bootstrap-timepicker.min.css') }}">

	<!-- Lightbox -->
	<link rel="stylesheet" type="text/css" href="{{ asset('vendor/crudbooster/assets/lightbox/dist/css/lightbox.css') }}">

	<!--SWEET ALERT-->
	<link rel="stylesheet" type="text/css" href="{{asset('vendor/crudbooster/assets/sweetalert/dist/sweetalert.css')}}">
	
	<!--DATATABLE-->
	<link rel="stylesheet" type="text/css" href="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/datatables/dataTables.bootstrap.css')}}">
	
	<!--CrudBooster CSS -->
	<link rel="stylesheet" type="text/css" href="{{asset('vendor/crudbooster/assets/css/main.css')}}">
	
	<!-- Theme style -->
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/crudbooster/assets/adminlte/dist/css/AdminLTE.min.css')}}">    
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/crudbooster/assets/adminlte/dist/css/skins/_all-skins.min.css')}}">
	
	<!-- support rtl-->
    @if (App::getLocale() == 'ar')
        <link rel="stylesheet" href="//cdn.rawgit.com/morteza/bootstrap-rtl/v3.3.4/dist/css/bootstrap-rtl.min.css">
        <link href="{{ asset("vendor/crudbooster/assets/rtl.css")}}" rel="stylesheet" type="text/css" />
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
	
	
@endsection
	
    
@section('admin_template_plugins_js') 
	
	<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script> 
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
	
	<!-- REQUIRED JS SCRIPTS -->

	<!-- jQuery 2.1.3 -->
	<script src="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/jQuery/jQuery-2.1.4.min.js') }}"></script>
	
	<!-- Bootstrap 3.3.2 JS -->
	<script src="{{ asset ('vendor/crudbooster/assets/adminlte/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
	<!-- AdminLTE App -->
	<script src="{{ asset ('vendor/crudbooster/assets/adminlte/dist/js/app.js') }}" type="text/javascript"></script>
	
	<!--BOOTSTRAP DATEPICKER-->	
	<script src="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
	
	<!--BOOTSTRAP DATERANGEPICKER-->
	<script src="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/daterangepicker/moment.min.js') }}"></script>
	<script src="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/daterangepicker/daterangepicker.js') }}"></script>

	<!-- Bootstrap time Picker -->
	<script src="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>

	<!-- Lightbox -->
	<script src="{{ asset('vendor/crudbooster/assets/lightbox/dist/js/lightbox.min.js') }}"></script>

	<!--SWEET ALERT-->
	<script src="{{asset('vendor/crudbooster/assets/sweetalert/dist/sweetalert.min.js')}}"></script>

	<!--MONEY FORMAT-->
	<script src="{{asset('vendor/crudbooster/jquery.price_format.2.0.min.js')}}"></script>

	<!--DATATABLE-->
	<script src="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/datatables/jquery.dataTables.min.js')}}"></script>
	<script src="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

	<script>			
		var ASSET_URL           = "{{asset('/')}}";
		var APP_NAME            = "{{CRUDBooster::getSetting('appname')}}";		
		var ADMIN_PATH          = '{{url(config("crudbooster.ADMIN_PATH")) }}';
		var NOTIFICATION_JSON   = "{{route('NotificationsControllerGetLatestJson')}}";
		var NOTIFICATION_INDEX  = "{{route('NotificationsControllerGetIndex')}}";

		var NOTIFICATION_YOU_HAVE      = "{{trans('crudbooster.notification_you_have')}}";
		var NOTIFICATION_NOTIFICATIONS = "{{trans('crudbooster.notification_notification')}}";
		var NOTIFICATION_NEW           = "{{trans('crudbooster.notification_new')}}";

		$(function() {
			$('.datatables-simple').DataTable();
		})
	</script>

	<script src="{{asset('vendor/crudbooster/assets/js/main.js')}}"></script>

@endsection
    