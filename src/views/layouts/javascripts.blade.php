{{--<!--[if lt IE 9]>--}}
{{--<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>--}}
{{--<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>--}}
{{--<![endif]-->--}}
<!-- REQUIRED JS SCRIPTS -->
<!-- jQuery 2.1.3 -->
<script src="{{ cbAsset('adminlte/plugins/jQuery/jQuery-2.1.4.min.js') }}"></script>

<!-- Bootstrap 3.3.2 JS -->
<script src="{{ cbAsset('adminlte/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
<!-- AdminLTE App -->
<script src="{{ cbAsset('adminlte/dist/js/app.js') }}" type="text/javascript"></script>

<!--DateRangePicker http://www.daterangepicker.com/-->
<script src="{{ cbAsset('adminlte/plugins/daterangepicker/moment.min.js') }}"></script>
<script src="{{ cbAsset('adminlte/plugins/daterangepicker/daterangepicker.js') }}"></script>
<link rel="stylesheet" href="{{ cbAsset('adminlte/plugins/daterangepicker/daterangepicker-bs3.css') }}">

<!-- Bootstrap time Picker -->
<link rel="stylesheet" href="{{ cbAsset('adminlte/plugins/timepicker/bootstrap-timepicker.min.css') }}">
<script src="{{ cbAsset('adminlte/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>

<link rel='stylesheet' href='{{ cbAsset("adminlte/plugins/lightbox/dist/css/lightbox.min.css") }}'/>
<script src="{{ cbAsset('adminlte/plugins/lightbox/dist/js/lightbox.min.js') }}"></script>

<!--SWEET ALERT-->
<script src="{{ cbAsset('adminlte/plugins/sweetalert/dist/sweetalert.min.js')}}"></script>
<link rel="stylesheet" type="text/css" href="{{ cbAsset('adminlte/plugins/sweetalert/dist/sweetalert.css')}}">

<!--MONEY FORMAT-->
<script src="{{ cbAsset('js/jquery.price_format.2.0.min.js')}}"></script>

<!--DATATABLE-->
<link rel="stylesheet" href="{{ cbAsset('adminlte/plugins/datatables/dataTables.bootstrap.css')}}">
<script src="{{ cbAsset('adminlte/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{ cbAsset('adminlte/plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script>
    $(function () {
        $(".datatable").dataTable();
    })
</script>

<!-- Default CB Script -->
<script src="{{ cbAsset('js/main.js').'?r='.time()}}"></script>
