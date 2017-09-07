@include('crudbooster::_IE9')

<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 2.1.3 -->
{!! cbScript('adminlte/plugins/jQuery/jQuery-2.1.4.min.js') !!}
<!-- Bootstrap 3.3.2 JS -->
{!! cbScript('adminlte/bootstrap/js/bootstrap.min.js') !!}
{!! cbScript('adminlte/dist/js/app.js') !!}
{!! cbScript('adminlte/plugins/datepicker/bootstrap-datepicker.js') !!}

{!! cbStyleSheet('adminlte/plugins/datepicker/datepicker3.css') !!}

<!--BOOTSTRAP DATERANGEPICKER-->
{!! cbScript('adminlte/plugins/daterangepicker/moment.min.js') !!}
{!! cbScript('adminlte/plugins/daterangepicker/daterangepicker.js') !!}
{!! cbStyleSheet('adminlte/plugins/daterangepicker/daterangepicker-bs3.css') !!}

<!-- Bootstrap time Picker -->

{!! cbStyleSheet('adminlte/plugins/timepicker/bootstrap-timepicker.min.css') !!}
{!! cbScript('adminlte/plugins/timepicker/bootstrap-timepicker.min.js') !!}

{!! cbStyleSheet('lightbox/dist/css/lightbox.css') !!}
{!! cbScript('lightbox/dist/js/lightbox.min.js') !!}

<!--SWEET ALERT-->
{!! cbScript('sweetalert/dist/sweetalert.min.js') !!}
{!! cbStyleSheet('sweetalert/dist/sweetalert.css') !!}

<!--MONEY FORMAT-->
<script src="{{asset('vendor/crudbooster/jquery.price_format.2.0.min.js')}}"></script>

<!--DATATABLE-->
{!! cbStyleSheet('adminlte/plugins/datatables/dataTables.bootstrap.css') !!}
{!! cbScript('adminlte/plugins/datatables/jquery.dataTables.min.js') !!}
{!! cbScript('adminlte/plugins/datatables/dataTables.bootstrap.min.js') !!}

<script>
    var ASSET_URL = "{{asset('/')}}";
    var APP_NAME = "{{CRUDBooster::getSetting('appname')}}";
    var ADMIN_PATH = '{{url(cbConfig("ADMIN_PATH")) }}';
    var NOTIFICATION_JSON = "{{route('AdminNotificationsControllerGetLatestJson')}}";
    var NOTIFICATION_INDEX = "{{route('AdminNotificationsControllerGetIndex')}}";

    var NOTIFICATION_YOU_HAVE = "{{cbTrans('notification_you_have')}}";
    var NOTIFICATION_NOTIFICATIONS = "{{cbTrans('notification_notification')}}";
    var NOTIFICATION_NEW = "{{cbTrans('notification_new')}}";

    $(function () {
        $('.datatables-simple').DataTable();
    })
</script>
{!! cbScript('js/main.js?r='.time()) !!}


	
