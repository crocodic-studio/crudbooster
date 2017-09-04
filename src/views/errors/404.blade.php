<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>404 PAGE NOT FOUND</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.2 -->
    <link href="{{cbAsset('adminlte/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet"
          type="text/css"/>
    <!-- Font Awesome Icons -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet"
          type="text/css"/>
    <!-- Theme style -->
    <link href="{{cbAsset('adminlte/dist/css/AdminLTE.min.css')}}" rel="stylesheet"
          type="text/css"/>


    @include('crudbooster::_IE9')
    <style>
        .login-box-body {
            box-shadow: 0px 0px 25px #999999;
        }

        body {
            background: #ededed;
        }
    </style>
</head>
<body>

<!-- Main content -->
<section class="content">

    <div class="error-page">
        <h2 class="headline text-yellow"> 404</h2>
        <div class="error-content">
            <h3><i class="fa fa-warning text-yellow"></i> {{cbTrans('page_not_found')}}</h3>
            <p>
                {{cbTrans('page_not_found_text')}}
            </p>
            <p>Tips : <br/>
                {{cbTrans('page_not_found_tips')}}
            </p>
        </div><!-- /.error-content -->
    </div><!-- /.error-page -->
</section><!-- /.content -->


<!-- jQuery 2.1.3 -->
<script src="{{cbAsset('adminlte/plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
<!-- Bootstrap 3.3.2 JS -->
<script src="{{cbAsset('adminlte/bootstrap/js/bootstrap.min.js')}}"
        type="text/javascript"></script>
</body>
</html>