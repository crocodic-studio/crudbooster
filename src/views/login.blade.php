<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Login Panel : {{$appname}}</title>
    <meta name='generator' content='CRUDBooster.com'/>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link href="{{asset('vendor/crudbooster/assets/adminlte/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="{{asset('vendor/crudbooster/assets/adminlte/dist/css/AdminLTE.min.css')}}" rel="stylesheet" type="text/css" />

    
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <style>
    .login-box-body {
      box-shadow: 0px 0px 25px #999999;
    }
    </style>
  </head>
  <body class="login-page">
    <div class="login-box">
      <div class="login-logo">
        <a href="{{url('/')}}">{!!($appname == 'CRUDBooster')?"<b>CRUD</b>Booster":$appname!!}</a>
      </div><!-- /.login-logo -->
      <div class="login-box-body">
	  
    		@if ( Session::get('message') != '' )
        		<div class='alert alert-warning'>
        			{{ Session::get('message') }}
        		</div>	
    		@endif 
		
        <p class="login-box-msg">Please login to start your session</p>
        <form autocomplete='off' action="{{ route('postLogin') }}" method="post">
		  <input type="hidden" name="_token" value="{{ csrf_token() }}" />
          <div class="form-group has-feedback">
            <input autocomplete='off'  type="text" class="form-control" name='email' required placeholder="Email"/>
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input autocomplete='off'  type="password" class="form-control" name='password' required placeholder="Password"/>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
          <div style="margin-bottom:10px" class='row'>
            <div class='col-xs-12'>
                <button type="submit" class="btn btn-success btn-block btn-flat"><i class='fa fa-lock'></i> Sign In</button>                
            </div>
          </div>       

          @if(count($privileges_register))
          <div class="row">
            <div class="col-xs-12">                  
              <a class='btn btn-primary btn-block btn-flat' href='{{route("getRegister")}}'><i class='fa fa-user'></i> Register</a>          
            </div><!-- /.col -->
          </div>
          @endif
          
          <div class='row'>
            <div class='col-xs-12' align="center"><p style="padding:10px 0px 10px 0px">Forgot the password ? <a href='{{route("getForgot")}}'>Click here</a>   </p></div>
          </div>
        </form>
        

		<br/>
        <!--a href="#">I forgot my password</a-->

      </div><!-- /.login-box-body -->

    </div><!-- /.login-box -->



    <!-- jQuery 2.1.3 -->
    <script src="{{asset('vendor/crudbooster/assets/adminlte/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{asset('vendor/crudbooster/assets/adminlte/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>
  </body>
</html>
