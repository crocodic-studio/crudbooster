<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Login Panel : {{$appname}}</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link href="{{asset('/assets/adminlte/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="{{asset('/assets/adminlte/dist/css/AdminLTE.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body class="login-page">
    <div class="login-box">
      <div class="login-logo">
        <a href="#"><b>{{ $page_title or "Forgot Password" }}</b></a>
      </div><!-- /.login-logo -->
      <div class="login-box-body">
	  
  		@if ( Session::get('message') != '' )
      		<div class='alert alert-warning'>
      			{{ Session::get('message') }}
      		</div>	
  		@endif 
		
        <p class="login-box-msg">Please enter your email address</p>
        <form action="{{ action('AdminController@postForgot') }}" method="post">
		  <input type="hidden" name="_token" value="{{ csrf_token() }}" />
          <div class="form-group has-feedback">
            <input type="text" class="form-control" name='email' required placeholder="Email Address"/>
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
          </div>
          <div class="row">
            <div class="col-xs-8">    
              Try login again ? <a href='{{url("admin/login")}}'>Click here</a>                          
            </div><!-- /.col -->
            <div class="col-xs-4">
              <button type="submit" class="btn btn-primary btn-block btn-flat">Submit</button>
            </div><!-- /.col -->
          </div>
        </form>

		<br/>
        <!--a href="#">I forgot my password</a-->

      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->

    <!-- jQuery 2.1.3 -->
    <script src="{{asset('adminlte/plugins/jQuery/jQuery-2.1.3.min.js')}}"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{asset('adminlte/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>

  </body>
</html>