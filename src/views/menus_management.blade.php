@extends('crudbooster::admin_template')
@section('content')

    @push('head')
    @include('crudbooster::_menus_management.css')
    @endpush

    @push('bottom')
    <script type="text/javascript">
        $(function () {
            function format(icon) {
                var originalOption = icon.element;
                var label = $(originalOption).text();
                var val = $(originalOption).val();
                if (!val) return label;
                var $resp = $('<span><i style="margin-top:5px" class="pull-right ' + $(originalOption).val() + '"></i> ' + $(originalOption).data('label') + '</span>');
                return $resp;
            }

            $('#list-icon').select2({
                width: "100%",
                templateResult: format,
                templateSelection: format
            });
        })
    </script>
    @endpush
    @push('bottom')
    <script src='{{asset("vendor/crudbooster/assets/jquery-sortable-min.js")}}'></script>
    @include('crudbooster::_menus_management.js')
    @endpush

    <div class='row'>

        <div class="col-sm-5">

            <div class="panel panel-success">
                @widget('\crocodicstudio\crudbooster\widgets\ActiveMenus')
            </div>


            <div class="panel panel-danger">
                @widget('\crocodicstudio\crudbooster\widgets\InActiveMenus')
            </div>

        </div>


        <div class="col-sm-7">
            <div class="panel panel-primary">

                <div class="panel-heading"> Add Menu</div>

                <div class="panel-body">
                    @include("crudbooster::default.form_body")
                </div>
            </div>
        </div>
    </div>


@endsection