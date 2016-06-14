<!DOCTYPE HTML>
<html>
	<head>
		<title>{{$title_meta}}</title>	
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
    	<meta name="viewport" content="width=device-width, initial-scale=1">	
		<meta name="description" content="{{$description_meta}}">

		<script type="text/javascript" src="{{asset('assets/js/jquery-1.10.2.min.js')}}"></script>		
		<link rel="stylesheet" href="{{asset('assets/bootstrap/css/bootstrap.min.css')}}">
		<script type="text/javascript" src="{{asset('assets/bootstrap/js/bootstrap.min.js')}}"></script>
		<link rel='stylesheet' href='{{asset("assets/siaptrans/style.css")}}'/>
		<link rel="icon" type="image/png" sizes="192x192"  href="{{asset("assets/siaptrans/fav-icon.png")}}">

		<script type="text/javascript" src="{{asset("assets/datepicker/js/bootstrap-datepicker.js")}}"></script>
		<link rel='stylesheet' href='{{asset("assets/datepicker/css/datepicker.css")}}'/>

		<script type="text/javascript">
			$(function() {
				$.ajaxSetup({
					headers: {
						'X-CSRF-TOKEN': '{{ csrf_token() }}'
					}
				});
				var nowTemp = new Date();
				var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
				$(".datepicker").datepicker({
					format: 'yyyy-mm-dd',
					onRender: function(date) {
					    return date.valueOf() < now.valueOf() ? 'disabled' : '';
					  }
				})
				$(".datepicker").on("changeDate",function() {
						$(this).datepicker("hide");
				});
				$(".button_datepicker").click(function() {
					$(this).parents(".input-group").find(".datepicker").datepicker("show");
				})
			})
		</script>
	</head>
	<body>
		<div class='container-fluid'>

			<!--div id='header-wrapper' class='container'>
				<div class='row'>
					<div class='col-sm-6'><a href='{{url("/")}}'><img height="80px" src='#'/></a></div>
					<div class='col-sm-6'>
						
						<nav class="navbar navbar-default">
						  <div class="container-fluid">
						    
						    <div class="navbar-header">
						      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
						        <span class="sr-only">Toggle navigation</span>
						        <span class="icon-bar"></span>
						        <span class="icon-bar"></span>
						        <span class="icon-bar"></span>
						      </button>
						      
						    </div>
						    
						    <div class="navbar-collapse collapse" id="bs-example-navbar-collapse-1" style="height: 1px;">
							      <ul class="nav navbar-nav">
									<li><a href='{{url("/")}}'>HOME</a></li>
									<li><a href='{{url("page/profile")}}'>PROFILE</a></li>
									<li><a href='{{url("page/contact")}}'>CONTACT</a></li>							
								 </ul>						     						      
						    </div>

						  </div>
						</nav>
					</div>
				</div>
			</div><!--END ROW-->