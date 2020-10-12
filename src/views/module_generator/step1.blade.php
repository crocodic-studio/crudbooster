@extends("crudbooster::admin_template")
@section("content")

    @push('head')
        <link rel='stylesheet' href='<?php echo asset("vendor/crudbooster/assets/select2/dist/css/select2.min.css")?>'/>
        <style>
            .select2-container--default .select2-selection--single {
                border-radius: 0px !important
            }

            .select2-container .select2-selection--single {
                height: 35px
            }
        </style>
    @endpush

    @push('bottom')
        <script src='<?php echo asset("vendor/crudbooster/assets/select2/dist/js/select2.full.min.js")?>'></script>
        <script>
            $(function () {
                $('.select2').select2();

            })
            $(function () {
                $('select[name=table]').change(function () {
                    var v = $(this).val().replace(".", "_");
                    $.get("{{CRUDBooster::mainpath('check-slug')}}/" + v, function (resp) {
                        if (resp.total == 0) {
                            $('input[name=path]').val(v);
                        } else {
                            v = v + resp.lastid;
                            $('input[name=path]').val(v);
                        }
                    })

                })
            })
        </script>
    @endpush

    <ul class="nav nav-tabs">
        @if($id)
            <li role="presentation" class="active"><a href="{{Route('ModulsControllerGetStep1')."/".$id}}"><i class='fa fa-info'></i> Step 1 - Module
                    Information</a></li>
            <li role="presentation"><a href="{{Route('ModulsControllerGetStep2')."/".$id}}"><i class='fa fa-table'></i> Step 2 - Table Display</a></li>
            <li role="presentation"><a href="{{Route('ModulsControllerGetStep3')."/".$id}}"><i class='fa fa-plus-square-o'></i> Step 3 - Form Display</a></li>
            <li role="presentation"><a href="{{Route('ModulsControllerGetStep4')."/".$id}}"><i class='fa fa-wrench'></i> Step 4 - Configuration</a></li>
        @else
            <li role="presentation" class="active"><a href="#"><i class='fa fa-info'></i> Step 1 - Module Information</a></li>
            <li role="presentation"><a href="#"><i class='fa fa-table'></i> Step 2 - Table Display</a></li>
            <li role="presentation"><a href="#"><i class='fa fa-plus-square-o'></i> Step 3 - Form Display</a></li>
            <li role="presentation"><a href="#"><i class='fa fa-wrench'></i> Step 4 - Configuration</a></li>
        @endif
    </ul>

    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Module Information</h3>
        </div>
        <div class="box-body">
            <form method="post" action="{{Route('ModulsControllerPostStep2')}}">
                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <input type="hidden" name="id" value="{{$row->id}}">
                <div class="form-group">
                    <label for="">Table</label>
                    <select name="table" id="table" required class="select2 form-control" value="{{$row->table_name}}">
                        <option value="">{{cbLang('text_prefix_option')}} Table</option>
                        @foreach($tables_list as $table)

                            <option {{($table == $row->table_name)?"selected":""}} value="{{$table}}">{{$table}}</option>

                        @endforeach
                    </select>
                    <div class="help-block">
                        Do not use cms_* as prefix on your tables name
                    </div>
                </div>
                <div class="form-group">
                    <label for="">Module Name</label>
                    <input type="text" class="form-control" required name="name" value="{{$row->name}}">
                </div>

                <div class="form-group">
                    <label for="">Icon</label>
                    <select name="icon" id="icon" required class="select2 form-control">
                        @foreach($fontawesome as $f)
                            <option {{($row->icon == 'fa fa-'.$f)?"selected":""}} value="fa fa-{{$f}}">{{$f}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="">Module Slug</label>
                    <input type="text" class="form-control" required name="path" value="{{$row->path}}">
                    <div class="help-block">Please alpha numeric only, without space instead _ and or special character</div>
                </div>
        </div>
        <div class="box-footer">

            <input checked type='checkbox' name='create_menu' value='1'/> Also create menu for this module <a href='#'
                                                                                                              title='If you check this, we will create the menu for this module'>(?)</a>

            <div class='pull-right'>
                <a class='btn btn-default' href='{{Route("ModulsControllerGetIndex")}}'> {{cbLang('button_back')}}</a>
                <input type="submit" class="btn btn-primary" value="Step 2 &raquo;">
            </div>
        </div>
        </form>
    </div>


@endsection
