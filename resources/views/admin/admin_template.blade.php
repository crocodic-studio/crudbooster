<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ ($page_title)?$appname.': '.strip_tags($page_title):"Admin Area" }}</title>
	<meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link href="{{ asset("/assets/adminlte/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="{{asset("/assets/adminlte/font-awesome/css")}}/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons -->
    <link href="http://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="{{ asset("/assets/adminlte/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />    
    <link href="{{ asset("/assets/adminlte/dist/css/skins/_all-skins.min.css")}}" rel="stylesheet" type="text/css" />    
	
    <link href="{{ asset("/assets/datepicker/css/datepicker.css") }}" rel="stylesheet" type="text/css" />
	
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
	
	<!-- REQUIRED JS SCRIPTS -->

	<!-- jQuery 2.1.3 -->
	<script src="{{ asset ('/assets/adminlte/plugins/jQuery/jQuery-2.1.4.min.js') }}"></script>
	
	<!-- Bootstrap 3.3.2 JS -->
	<script src="{{ asset ('/assets/adminlte/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
	<!-- AdminLTE App -->
	<script src="{{ asset ('/assets/adminlte/dist/js/app.min.js') }}" type="text/javascript"></script>
	
	<link href="{{ asset("/assets/adminlte/plugins/iCheck/all.css")}}" rel="stylesheet" type="text/css" />
	<!-- iCheck 1.0.1 -->
    <script src="{{ asset("/assets/adminlte/plugins/iCheck/icheck.min.js")}}" type="text/javascript"></script>
    <script src="{{ asset("/assets/js/dateformat.js")}}" type="text/javascript"></script>
	<!-- ChartJS 1.0.1 -->
    <script src="{{ asset('/assets/adminlte/plugins/chartjs/Chart.min.js') }}"></script>
	
	<script src="{{ asset ('/assets/datepicker/js/bootstrap-datepicker.js') }}"></script>

	<link rel='stylesheet' href='{{ asset("/assets/fancy//source/jquery.fancybox.css") }}'/>
	<script src="{{ asset('/assets/fancy/source/jquery.fancybox.pack.js') }}"></script> 	

	<!--SWEET ALERT-->
	<script src="{{asset('assets/sweetalert/dist/sweetalert.min.js')}}"></script> 
	<link rel="stylesheet" type="text/css" href="{{asset('assets/sweetalert/dist/sweetalert.css')}}">
	
	<script>	
		Number.prototype.number_format = function(n, x) {
			var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
			return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
		};

		function date_time(id)
		{
				date = new Date;
				year = date.getFullYear();
				month = date.getMonth();
				months = new Array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
				d = date.getDate();
				day = date.getDay();
				days = new Array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
				h = date.getHours();
				if(h<10)
				{
						h = "0"+h;
				}
				m = date.getMinutes();
				if(m<10)
				{
						m = "0"+m;
				}
				s = date.getSeconds();
				if(s<10)
				{
						s = "0"+s;
				}
				result = ''+days[day]+', '+d+' '+months[month]+' '+year+' '+h+':'+m+':'+s;
				document.getElementById(id).innerHTML = result;
				setTimeout('date_time("'+id+'");','1000');
				return true;
		}
		function pad(pad, str, padLeft) {
		  if (typeof str === 'undefined') 
			return pad;
		  if (padLeft) {
			return (pad + str).slice(-pad.length);
		  } else {
			return (str + pad).substring(0, pad.length);
		  }
		}
		 function secondsToString(seconds)
		{
			var numyears = Math.floor(seconds / 31536000);
			var numdays = Math.floor((seconds % 31536000) / 86400); 
			var numhours = Math.floor(((seconds % 31536000) % 86400) / 3600);
			var numminutes = Math.floor((((seconds % 31536000) % 86400) % 3600) / 60);
			var numseconds = (((seconds % 31536000) % 86400) % 3600) % 60;
			numseconds = parseFloat(numseconds).toFixed(0);
			
			
			numseconds = pad('00',numseconds,true);
			numminutes = pad('00',numminutes,true);
			numhours = pad('00',numhours,true);
			
			return numhours + " jam " + numminutes + " menit " + numseconds + " detik";
		}
		
		function get_interval_minutes(date1,date2,type) {
			var today = date1;
			var nextdate = date2;
			var diffMs = Math.abs(nextdate - today); 
			var diffDays = Math.round(diffMs / 86400000); 
			var diffHrs = Math.round((diffMs % 86400000) / 3600000); 
			var diffMins = Math.round(((diffMs % 86400000) % 3600000) / 60000); 
			switch(type) {
				case "minutes":
					return diffMins;
				break;
				case "days":
					return diffDays;
				break;
				case "hours":
					return diffHrs;
				break;
			}
		}
		function show_alert_floating(message) {
			$(".alert_floating .message").text(message);
			$(".alert_floating").slideDown();						
		}
		function hide_alert_floating() {			
			$(".alert_floating").slideUp();
		}
		$(function() {			

			jQuery.fn.outerHTML = function(s) {
			    return s
			        ? this.before(s).remove()
			        : jQuery("<p>").append(this.eq(0).clone()).html();
			};

			$(".fancybox").fancybox();

			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			var cururl = window.location.href;						
			$(".sidebar-menu a[href='"+cururl+"']").parents(".treeview").addClass("active");			
			$(".sidebar-menu a[href='"+cururl+"']").parent("li").addClass("active");	

			<?php if(Session::get('current_mainpath')):?>
				$(".sidebar-menu a[href*='<?php echo Session::get('current_mainpath');?>']").parents(".treeview").addClass("active");
			<?php endif;?>		
			
			//iCheck for checkbox and radio inputs
			$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
			  checkboxClass: 'icheckbox_minimal-blue',
			  radioClass: 'iradio_minimal-blue'
			});
			
			$('input[type=text]').first().not(".notfocus").focus();
			
			if($("#tanggal").length > 0) {
				date_time("tanggal");
			}
			
			if($(".datepicker").length > 0) {
				$('.datepicker').datepicker({
					format: 'yyyy-mm-dd'			
				})
			}

			$(document).on('click',".ajax-button",function() {
				$("body").css("cursor", "progress");
				var title = $(this).attr('title');
				show_alert_floating('Please wait while loading '+title+'...');
				var u = $(this).attr('href');
				$(this).addClass('disabled');
				$.get(u,function(resp) {
					$("body").css("cursor", "default");
					var htm = $(resp).find('#content_section').html();
					$("#content_section").html(htm);		
					hide_alert_floating();			
				});
				return false;
			})
		});
		
		
		
	</script>

	<style>

	::-webkit-scrollbar {
	    width: 7px;
	}

	::-webkit-scrollbar-track {
	    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); 
	    border-radius: 10px;
	}
	::-webkit-scrollbar-thumb {
	    border-radius: 10px;
	    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.5); 
	}	

	.datepicker{z-index:1151 !important;}

	.sidebar-menu .treeview-menu>li>a {
		padding:5px 5px 5px 25px;
	}
	.form-tab li a:hover {
		background: #eeeeee !important;
	}
	.form-tab li a.selected {
		background: #3c8dbc !important;
		color:#ffffff !important;
	}
	.alert_floating {
		bottom:10px;
		right:10px;
		position: fixed;
		z-index:1001;
		background: rgba(255, 140, 127, 0.7);
		color:#222222;
		font-weight: bold;
		padding:20px 50px 20px 50px;
		border-radius: 10px;
		box-shadow: 0px 0px 5px #aaaaaa;
	}

	#table_dashboard {
		font-size: 12px;
	}
	</style>
	
</head>
<body class="<?php echo (Session::get('theme_color'))?:'skin-blue'?>">
<div class='alert_floating' style='display:none'>
	<i class='fa fa-exclamation-triangle'></i> <span class='message'>Please wait...</span>
</div>
<div class="wrapper">

    <!-- Header -->
    @include('admin/header')

    <!-- Sidebar -->
    @include('admin/sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
		

        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
            	<i class='<?php echo ($priv->icon)?:"fa fa-bars"?>'></i> 
            	<?php 
            		if(Request::get("parent_module")) {
            			$url = url("admin/".Request::get("parent_module"));
            			echo "<a href='$url'>".Request::get("parent_name")."</a> &raquo; ";
            		}

            		$page_title = (Request::get('detail'))?str_replace(array("Add Data","Edit Data"),"Detail Data",$page_title):$page_title;
            	?>
                {!! $page_title or "Page Title" !!}
                <small>{!! $page_description or null !!}</small>
            </h1>            
        </section>
		
		

        <!-- Main content -->
        <section id='content_section' class="content">
			@if (Session::get('message')!='')
			<div class='alert alert-{{ Session::get("message_type") }}'>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<h4><i class="icon fa fa-info"></i> {{ ucfirst(Session::get("message_type")) }}</h4>
				{{Session::get('message')}}
			</div>
			@endif
            <!-- Your Page Content Here -->
            @yield('content')
        </section><!-- /.content -->
    </div><!-- /.content-wrapper -->

    <!-- Footer -->
    @include('admin/footer')

</div><!-- ./wrapper -->



<!-- Optionally, you can add Slimscroll and FastClick plugins.
      Both of these plugins are recommended to enhance the
      user experience -->
</body>
</html>