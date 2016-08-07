<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>404 PAGE NOT FOUND</title>
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
    <style>
    .login-box-body {
      box-shadow: 0px 0px 25px #999999;
    }
    body {
      background: #ededed;
    }
    </style>
  </head>
  <body >

     <!-- Main content -->
        <section class="content">

          <div class="error-page">
            <h2 class="headline text-yellow"> 404</h2>
            <div class="error-content">
              <h3><i class="fa fa-warning text-yellow"></i> Oops! Page Not Found</h3>
              <p>                
                We are unable search the page that you are looking for.<br/>
                But, you are stiil able to go back just <a href='{{url("/")}}'>click here</a> to go homepage.                
              </p>
              <p>Tips : <br/>                
                * Please make sure the URL is correct<br/>
                * Please make sure the font capitalizing is correct because case sensitive<br/>
                * Maybe the page that you are looking for has been deleted by Admin<br/>
                * You can go back by click Back Button at browser<br/>
              </p>
            </div><!-- /.error-content -->
          </div><!-- /.error-page -->
        </section><!-- /.content -->



    <!-- jQuery 2.1.3 -->
    <script src="{{asset('assets/adminlte/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{asset('assets/adminlte/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>
  </body>
</html>