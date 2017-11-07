@extends('crudbooster::admin_template')
@section('content')

    @push('head')
        @include('CbMenu::_menus_management.css')
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
        @include('CbMenu::_menus_management.js')
    @endpush

    <div class='row'>

        <div class="col-sm-5">

            <div class="panel panel-success">
                @widget('\crocodicstudio\crudbooster\Modules\MenuModule\widgets\ActiveMenus')
            </div>


            <div class="panel panel-danger">
                @widget('\crocodicstudio\crudbooster\Modules\MenuModule\widgets\InActiveMenus')
            </div>

        </div>


        <div class="col-sm-7">
            <div class="panel panel-primary">

                <div class="panel-heading"> Add Menu</div>

                <div class="panel-body">
                    <form class='form-horizontal' method='post' id="form" enctype="multipart/form-data" action='{{CRUDBooster::mainpath("add-save")}}'>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type='hidden' name='return_url' value='{{Request::fullUrl()}}'/>
                        @include("crudbooster::default.form_body")
                        <p align="right"><input type='submit' class='btn btn-primary' value='Add Menu'/></p>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection