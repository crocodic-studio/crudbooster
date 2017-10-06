@extends('crudbooster::admin_template')
@section('content')


    @if($button_show_data || $button_reload_data || $button_new_data || $button_delete_data || $index_button || $columns)
        <div id='box-actionmenu' class='box'>
            <div class='box-body'>
                @include("crudbooster::default.actionmenu")
            </div>
        </div>
    @endif


    @if(request('file') && request('import'))
        <ul class='nav nav-tabs'>
            @include('crudbooster::_import.tabs')
        </ul>

        <div id='box_main' class="box box-primary">
            @include('crudbooster::_import.progress_box')
        </div>
    @endif

    @if(request('file') && !request('import'))

        @include('crudbooster::_import.nav')

        <!-- Box -->
        <div id='box_main' class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Adjustment</h3>
                <div class="box-tools">

                </div>
            </div>

            <?php
            if ($data_sub_module) {
                $action_path = Route($data_sub_module->controller."GetIndex");
            } else {
                $action_path = CRUDBooster::mainpath();
            }

            $action = $action_path."/done-import?file=".request('file').'&import=1';
            ?>

            @include('crudbooster::_import.form')


        </div>
    @endif


    @if(!request('file'))
        <ul class='nav nav-tabs'>
            <li style="background:#ffffff" class='active'>

                <a style="color:#111" onclick="if(confirm('Are you sure want to leave ?')) location.href='{{ CRUDBooster::mainpath("import-data") }}'" href='javascript:;'>
                    {!! CB::icon('download') !!} Upload aFile &raquo;</a>
            </li>
            <li style="background:#eeeeee"><a style="color:#111" href='#'>{!! CB::icon('cogs')  !!}Adjustment &raquo;</a>
            </li>
            <li style="background:#eeeeee"><a style="color:#111" href='#'>{!! CB::icon('cloud-download') !!} Importing
                    &raquo;</a></li>
        </ul>

        <!-- Box -->
        <div id='box_main' class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Upload a File</h3>
                <div class="box-tools">

                </div>
            </div>

            <?php
            if ($data_sub_module) {
                $action_path = Route($data_sub_module->controller."GetIndex");
            } else {
                $action_path = CRUDBooster::mainpath();
            }

            $action = $action_path."/do-upload-import-data";
            ?>

            <form method='post' id="form" enctype="multipart/form-data" action='{{$action}}'>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="box-body">

                    <div class='callout callout-success'>
                        <h4>Welcome to Data Importer Tool</h4>
                        Before doing upload a file, its better to read this bellow instructions : <br/>
                        * File format should be : xls or xlsx or csv<br/>
                        * If you have a big file data, we can't guarantee. So, please split those files into some parts
                        of file (at least max 5 MB).<br/>
                        * This tool is generate data automatically so, be carefull about your table xls structure.
                        Please make sure correctly the table structure.<br/>
                        * Table structure : Line 1 is heading column , and next is the data. (For example, you can
                        export any module you wish to XLS format)
                    </div>

                    <div class='form-group'>
                        <label>File XLS / CSV</label>
                        <input type='file' name='userfile' class='form-control' required/>
                        <div class='help-block'>File type supported only : XLS, XLSX, CSV</div>
                    </div>
                </div><!-- /.box-body -->

                <div class="box-footer">
                    <div class='pull-right'>
                        <a href='{{ CRUDBooster::mainpath() }}' class='btn btn-default'>Cancel</a>
                        <input type='submit' class='btn btn-primary' name='submit' value='Upload'/>
                    </div>
                </div><!-- /.box-footer-->
            </form>
        </div><!-- /.box -->


        @endif
        </div><!-- /.col -->


        </div><!-- /.row -->

@endsection