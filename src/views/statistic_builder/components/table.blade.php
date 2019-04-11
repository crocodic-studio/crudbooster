@if($command=='layout')
    <div id='{{$componentID}}' class='border-box'>

        <div class="panel panel-default">
            <div class="panel-heading">
                [name]
            </div>
            <div class="panel-body table-responsive no-padding">
                [sql]
            </div>
        </div>

        <div class='action pull-right'>
            <a href='javascript:void(0)' data-componentid='{{$componentID}}' data-name='Small Box' class='btn-edit-component'><i class='fa fa-pencil'></i></a>
            &nbsp;
            <a href='javascript:void(0)' data-componentid='{{$componentID}}' class='btn-delete-component'><i class='fa fa-trash'></i></a>
        </div>
    </div>
@elseif($command=='configuration')
    <form method='post'>
        <input type='hidden' name='_token' value='{{csrf_token()}}'/>
        <input type='hidden' name='componentid' value='{{$componentID}}'/>
        <div class="form-group">
            <label>Name</label>
            <input class="form-control" required name='config[name]' type='text' value='{{@$config->name}}'/>
        </div>

        <div class="form-group">
            <label>SQL Query</label>
            <textarea name='config[sql]' rows="5" placeholder="E.g : select column_id,column_name from view_table_name"
                      class='form-control'>{{@$config->sql}}</textarea>
            <div class='help-block'>
                Make sure the sql query are correct unless the widget will be broken. Mak sure give the alias name each column. You may use alias [SESSION_NAME]
                to get the session. We strongly recommend that you use a <a href='http://www.w3schools.com/sql/sql_view.asp' target='_blank'>view table</a>
            </div>
        </div>

        <div class="form-group">
            <label>Column number to sort</label>
            <input class="form-control" name='config[sort]' type='text' value='{{@$config->sort}}'/>
        </div>
        <div class="form-group">
            <label>Sort Direction</label>
            <select class='form-control' name='config[sort_dir]'>
                <option value='asc' {{(@$config->sort_dir == 'asc')?"selected":""}}>ASC</option>
                <option value='desc' {{(@$config->sort_dir == 'desc')?"selected":""}}>DESC</option>
            </select>
        </div>

    </form>
@elseif($command=='showFunction')
    <?php
    if($key == 'sql') {
    try {
        $sessions = Session::all();
        foreach ($sessions as $key => $val) {
            $value = str_replace("[".$key."]", $val, $value);
        }
        $sql = DB::select(DB::raw($value));
    } catch (\Exception $e) {
        die('ERROR');
    }
    ?>

    @if($sql)
        <table class='table table-striped'>
            <thead>
            <tr>
                @foreach($sql[0] as $key=>$val)
                    <th>{{$key}}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($sql as $row)
                <tr>
                    @foreach($row as $key=>$val)
                        <td>{{$val}}</td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
        <script type="text/javascript">
            $('table.table').DataTable({
                @if($config->sort!="")
                    order: [[{{@$config->sort}}, "{{@$config->sort_dir}}" ]],
                @endif
                dom: "<'row'<'col-sm-6'l><'col-sm-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>",
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]]
            });
        </script>
    @endif
    <?php
    }else {
        echo $value;
    }
    ?>
@endif  

