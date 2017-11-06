@push('head')
    <style type="text/css">
        #table-detail tr td:first-child {
            font-weight: bold;
            width: 25%;
        }
    </style>
@endpush

<div class='table-responsive'>
    <table id='table-detail' class='table table-striped'>

        @foreach($forms as $index => $formInput)
        <?php
        $formInput = array_merge(['showInDetail' => true, 'value' => '', 'join' => ''], $formInput);
        $name = $formInput['name'];
        $value = (isset($row->{$name})) ? $row->{$name} : $value;
        $showInDetail = $formInput['showInDetail'];

        if ($showInDetail == FALSE) {
            continue;
        }

        if (isset($formInput['callback_php'])) {
            @eval("\$value = ".$formInput['callback_php'].";");
        }

        if (isset($formInput['callback'])) {
            $value = call_user_func($formInput['callback'], $row);
        }

        if (isset($formInput['default_value'])) {
            $value = $formInput['default_value'];
        }

        if ($formInput['join'] && @$row) {
            $join_arr = explode(',', $formInput['join']);
            array_walk($join_arr, 'trim');
            $join_table = $join_arr[0];
            $join_title = $join_arr[1];
            $join_table_pk = CB::pk($join_table);
            $join_fk = CB::getForeignKey($table, $join_table);
            $join_query_{$join_table} = DB::table($join_table)->select($join_title)->where($join_table_pk, $row->{$join_fk})->first();
            $value = @$join_query_{$join_table}->{$join_title};
        }

        $required = $formInput['required'] ? "required" : "";
        $readonly = $formInput['readonly'] ? "readonly" : "";
        $disabled = $formInput['disabled'] ? "disabled" : "";
        $jquery = @$formInput['jquery'];
        $placeholder = "placeholder='{$formInput['placeholder']}'";



        $file_location = base_path('vendor/crocodicstudio/crudbooster/src/views/default/type_components/'.$type.'/component_detail.blade.php');
        $user_location = resource_path('views/vendor/crudbooster/type_components/'.$type.'/component_detail.blade.php');

        ?>

        @if(file_exists($file_location))
            <?php $containTR = (substr(trim(file_get_contents($file_location)), 0, 4) == '<tr>') ? TRUE : FALSE;?>
            @if($containTR)
                @include('crudbooster::default.type_components.'.$type.'.component_detail')
            @else
                <tr>
                    <td>{{$label}}</td>
                    <td>@include('crudbooster::default.type_components.'.$type.'.component_detail')</td>
                </tr>
            @endif
        @elseif(file_exists($user_location))
            <?php $containTR = (substr(trim(file_get_contents($user_location)), 0, 4) == '<tr>') ? TRUE : FALSE;?>
            @if($containTR)
                @include('vendor.crudbooster.type_components.'.$type.'.component_detail')
            @else
                <tr>
                    <td>{{$label}}</td>
                    <td>@include('vendor.crudbooster.type_components.'.$type.'.component_detail')</td>
                </tr>
            @endif
        @else
        <!-- <tr><td colspan='2'>NO COMPONENT {{$type}}</td></tr> -->
        @endif


        @endforeach

    </table>
</div>