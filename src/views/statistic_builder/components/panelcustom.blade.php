@if($command=='layout')
    <div id='{{$componentID}}' class='border-box'>

        <div class="panel panel-default">
            <div class="panel-heading">
                [name]
            </div>
            <div class="panel-body">
                [value]
            </div>
        </div>

        <div class='action pull-right'>
            <a href='javascript:void(0)' data-componentid='{{$componentID}}' data-name='Panel Custom' class='btn-edit-component'><i
                        class='fa fa-pencil'></i></a> &nbsp;
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
            <label>Type</label>
            <select name='config[type]' class='form-control'>
                <option {{(@$config->type == 'controller')?"selected":""}} value='controller'>Controller & Method</option>
                <option {{(@$config->type == 'route')?"selected":""}} value='route'>Route Name</option>
            </select>
        </div>

        <div class="form-group">
            <label>Value</label>
            <input name='config[value]' type='text' class='form-control' value='{{@$config->value}}'/>
            <div class='help-block'>You must enter the valid value related with current TYPE unless, widget will not work</div>
        </div>

    </form>
@elseif($command=='showFunction')
    <?php
    if($key == 'value') {
    if ($config->type == 'controller') {
        $url = action($value);
    } elseif ($config->type == 'route') {
        $url = route($value);
    }
    echo "<div id='content-$componentID'></div>";
    ?>

    <script>
        $(function () {
            $('#content-{{$componentID}}').html("<i class='fa fa-spin fa-spinner'></i> Please wait loading...");
            $.get('{{$url}}', function (response) {
                $('#content-{{$componentID}}').html(response);
            });
        })
    </script>

    <?php
    }else {
        echo $value;
    }
    ?>
@endif	