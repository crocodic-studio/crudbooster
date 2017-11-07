<form method="post" action="{{Route('AdminModulesControllerPostStep2')}}">

    <div class="box-body">

        <input type="hidden" name="_token" value="{{csrf_token()}}">
        <input type="hidden" name="id" value="{{$row->id}}">

        <div class="row">
            <div class="col-md-3 form-group">
                <label for="">Table</label>
                <select name="table" id="table" required class="select2 form-control" value="{{$row->table_name}}">
                    <option value="">{{cbTrans('text_prefix_option')}} Table</option>

                    @foreach($tables_list as $table)
                        <option {{($table == $row->table_name)?"selected":""}} value="{{$table}}">{{$table}}</option>
                    @endforeach

                </select>
                <div class="help-block">
                    Do not use cms_* as prefix on your tables name
                </div>
            </div>
        </div>
        <div class="row">


            <div class="col-md-3 form-group">
                <label for="">Module Name</label>
                <input type="text" class="form-control" required name="name" value="{{$row->name}}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-3  form-group">
                <label for="">Icon</label>
                <select name="icon" id="icon" required class="select2 form-control">
                    @php
                        $fontAwesome = \crocodicstudio\crudbooster\controllers\Helpers\FontAwesome::cssClass();
                    @endphp
                    @foreach($fontAwesome as $font)
                        <option {{($row->icon == 'fa fa-'.$font)?"selected":""}} value="fa fa-{{$font}}">{{$font}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 form-group">
                <label for="">Module Slug</label>
                <input type="text" class="form-control" required name="path" value="{{$row->path}}">
                <div class="help-block">
                    Please alpha numeric only, without space instead _ and or special character
                </div>
            </div>
        </div>

    </div>
    <div class="box-footer">

        <input checked type='checkbox' name='create_menu' value='1'/> Also create menu for this module
        <a href='#' title='If you check this, we will create the menu for this module'>(?)</a>

        <div class='pull-right'>
            <a class='btn btn-default' href='{{Route("AdminModulesControllerGetIndex")}}'>
                {{cbTrans('button_back')}}
            </a>
            <input type="submit" class="btn btn-primary" value="Step 2 &raquo;">
        </div>
    </div>
</form>