@push('head')
    <style type="text/css">
        #table-detail tr td:first-child {
            font-weight: bold;
            width: 25%;
        }
    </style>
@endpush
@include('crudbooster::default._form_body.component_assets', ['forms' => $forms])
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
            ?>
        @include('crudbooster::default._form_body.component_details')

        @endforeach

    </table>
</div>