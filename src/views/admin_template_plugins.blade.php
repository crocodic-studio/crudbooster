<!-- Bootstrap 3.3.2 -->
    <link href="{{ asset("vendor/crudbooster/assets/adminlte/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="{{asset("vendor/crudbooster/assets/adminlte/font-awesome/css")}}/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons -->
    <link href="http://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css" rel="stylesheet" type="text/css" />
      	    
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
	<script src="{{ asset ('vendor/crudbooster/assets/adminlte/dist/js/app.min.js') }}" type="text/javascript"></script>
	
	<link href="{{ asset("vendor/crudbooster/assets/adminlte/plugins/iCheck/all.css")}}" rel="stylesheet" type="text/css" />
	<!-- iCheck 1.0.1 -->
    <script src="{{ asset("vendor/crudbooster/assets/adminlte/plugins/iCheck/icheck.min.js")}}" type="text/javascript"></script>
    <script src="{{ asset("vendor/crudbooster/assets/js/dateformat.js")}}" type="text/javascript"></script>
	<!-- ChartJS 1.0.1 -->
    <script src="{{ asset('vendor/crudbooster/assets/adminlte/plugins/chartjs/Chart.min.js') }}"></script>		

	<!--BOOTSTRAP DATEPICKER-->	
	<script src="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
	<link rel="stylesheet" href="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/datepicker/datepicker3.css') }}">

	<!--BOOTSTRAP DATERANGEPICKER-->
	<script src="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/daterangepicker/moment.min.js') }}"></script>
	<script src="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/daterangepicker/daterangepicker.js') }}"></script>
	<link rel="stylesheet" href="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/daterangepicker/daterangepicker-bs3.css') }}">

	<!-- Bootstrap time Picker -->
  	<link rel="stylesheet" href="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/timepicker/bootstrap-timepicker.min.css') }}">  	  	
	<script src="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>

	<link rel='stylesheet' href='{{ asset("vendor/crudbooster/assets/fancy//source/jquery.fancybox.css") }}'/>
	<script src="{{ asset('vendor/crudbooster/assets/fancy/source/jquery.fancybox.pack.js') }}"></script> 	

	<!--SWEET ALERT-->
	<script src="{{asset('vendor/crudbooster/assets/sweetalert/dist/sweetalert.min.js')}}"></script> 
	<link rel="stylesheet" type="text/css" href="{{asset('vendor/crudbooster/assets/sweetalert/dist/sweetalert.css')}}">

	<!--MONEY FORMAT-->
	<script src="{{asset('vendor/crudbooster/jquery.price_format.2.0.min.js')}}"></script>

	<script>			
		var ASSET_URL = "{{asset('/')}}";
		var APP_NAME = "{{$appname}}";
		var CURRENT_MODULE_PATH = "{{ Session::get('current_mainpath') }}";
		var SUB_MODULE = "{{ Request::get('submodul') }}";
		var ADMIN_PATH = '{{ url(config("crudbooster.ADMIN_PATH")) }}';
		var NOTIFICATION_JSON = "{{route('NotificationsControllerGetLatestJson')}}";
		var NOTIFICATION_INDEX = "{{route('NotificationsControllerGetIndex')}}";
		var LOCK_SCREEN_TIME = <?php echo $setting->app_lockscreen_timeout?:30?>;
		var LOCK_SCREEN_URL = "{{route('getLockScreen')}}";
	</script>
	<script src="{{asset('vendor/crudbooster/assets/js/main.js')}}"></script>
	<link rel='stylesheet' href='{{asset("vendor/crudbooster/assets/css/main.css")}}'/>

    <!-- Pace style -->
    <link rel="stylesheet" href="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/pace/pace.min.css') }}">    
    <script src="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/pace/pace.min.js') }}"></script>