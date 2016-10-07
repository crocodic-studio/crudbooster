<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Register Panel : {{$appname}}</title>
    <meta name='generator' content='CRUDBooster.com'/>
    <meta name='robots' content='noindex,nofollow'/>
    <link rel="shortcut icon" href="{{ get_setting('favicon')?asset(get_setting('favicon')):asset('vendor/crudbooster/assets/logo_crudbooster.png') }}">
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
    <link rel='stylesheet' href='{{asset("vendor/crudbooster/assets/css/main.css")}}'/>
    <style type="text/css">
      .login-page, .register-page {
          background: {{ get_setting("login_background_color")?:'#dddddd'}} url('{{ get_setting("login_background_image")?asset(get_setting("login_background_image")):asset('vendor/crudbooster/assets/bg_blur5.jpg') }}');
          color: {{ get_setting("login_font_color")?:'#ffffff' }} !important;
          background-repeat: no-repeat;
          background-position: center;
          background-size: cover;

      }
      .login-box-body {
        box-shadow: 0px 0px 100px rgba(0,0,0,0.8);              
        background: none;
        color: {{ get_setting("login_font_color")?:'#ffffff' }} !important;
      }
    </style>
  </head>
  <body class="login-page">
    <div class="login-box">
      <div class="login-logo">
        <a href="{{url('/')}}">
          <img title='{!!($appname == 'CRUDBooster')?"<b>CRUD</b>Booster":$appname!!}' src='{{ get_setting("logo")?asset(get_setting('logo')):asset('vendor/crudbooster/assets/logo_crudbooster.png') }}' style='max-width: 100%;max-height:170px'/>
        </a>
      </div><!-- /.login-logo -->
      <div class="login-box-body">
	  
    		@if ( Session::get('message') != '' )
        		<div class='alert alert-warning'>
        			{{ Session::get('message') }}
        		</div>	
    		@endif 
		
        <p class="login-box-msg">Please register {!! (count($privileges_register) == 1)?"<strong>".$privileges_register[0]->name."</strong>":'' !!} to access the system</p>
        <form autocomplete='off' action="{{ route('postRegister') }}" method="post">
		    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
          <div class="form-group has-feedback">
            <input autocomplete='off'  type="text" class="form-control" name='name' title='Enter your full name' required placeholder="Full Name"/>
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input autocomplete='off'  type="email" class="form-control" title='Enter a valid email address' name='email' required placeholder="Email"/>
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input autocomplete='off'  type="password" title='Enter your new password' class="form-control" name='password' required placeholder="Password"/>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>

          <div class="form-group has-feedback {{ (count($privileges_register) == 1)?'hide':'' }}">            
            <select name='id_cms_privileges' title='Register As' required class='form-control'>
                @foreach($privileges_register as $p)
                    <option value='{{$p->id}}'>{{$p->name}}</option>
                @endforeach
            </select>
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
          </div>

          <div class="form-group has-feedback">
            <div  class='checkbox'>
              <label><input type='checkbox' required /> I agree the Terms and Conditions</label>
            </div>
          </div>
          <div style="margin-bottom:10px" class='row'>
            <div class='col-xs-12'>
                <button type="submit" class="btn btn-primary btn-block btn-flat">Register</button>                
            </div>
          </div>                
          <div class='row'>
            <div class='col-xs-12' align="center"><p style="padding:10px 0px 10px 0px">Do you have already an account ? <a href='{{route("getLogin")}}'>Click here</a>   </p></div>
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
