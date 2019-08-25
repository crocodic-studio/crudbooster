{{--<!--[if lt IE 9]>--}}
{{--<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>--}}
{{--<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>--}}
{{--<![endif]-->--}}
<!-- REQUIRED JS SCRIPTS -->
<!-- jQuery 2.1.3 -->
<script src="{{ cbAsset('js/jquery/dist/jquery.min.js') }}"></script>
<!-- Bootstrap 3.3.2 JS -->
<script src="{{ cbAsset('js/bootstrap/dist/js/bootstrap.min.js') }}" type="text/javascript"></script>
<!-- jQuery DateTimePicker -->
<link rel="stylesheet" href="{{ cbAsset("js/jquery-datetimepicker/jquery.datetimepicker.min.css") }}">
<script src="{{ cbAsset("js/jquery-datetimepicker/jquery.datetimepicker.full.min.js") }}"></script>
<!--Lightbox-->
<link rel='stylesheet' href='{{ cbAsset("js/lightbox/dist/css/lightbox.min.css") }}'/>
<script src="{{ cbAsset('js/lightbox/dist/js/lightbox.min.js') }}"></script>
<!--SWEET ALERT-->
<script src="{{ cbAsset('js/sweetalert/dist/sweetalert.min.js')}}?v=2.1.3"></script>
<!--SELECT2-->
<script src="{{ cbAsset("js/select2/select2.min.js") }}"></script>
<link rel="stylesheet" href="{{ cbAsset("js/select2/select2.min.css") }}">
<!--DATATABLE-->
<link rel="stylesheet" href="{{ cbAsset('js/datatables/dataTables.bootstrap.css')}}">
<script src="{{ cbAsset('js/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{ cbAsset('js/datatables/dataTables.bootstrap.min.js')}}"></script>
<!--Main CB JS-->
<script src="{{ cbAsset('js/main.js').'?v=1.6'}}"></script>
<script>
    function deleteConfirmation(url)
    {
        showConfirmation("{{ __("cb::cb.are_you_sure") }}", "{!! __("cb::cb.delete_the_data_can_not_be_undone") !!}", () => {
            location.href = url
        })
    }

    function goToUrlWithConfirmation(url, message)
    {
        showConfirmation("{{ __("cb::cb.are_you_sure") }}", message, () => {
            location.href = url
        })
    }

    @if($javascript = module()->getData("javascript"))
        {!! call_user_func($javascript)  !!}
    @endif

</script>

