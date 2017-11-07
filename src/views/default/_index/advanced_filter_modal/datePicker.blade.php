<div class='col-sm-6'>
    <div class='input-group {{ ($type_data == "time")?"bootstrap-timepicker":"" }}'>
        <span class="input-group-addon">{{cbTrans("filter_$dir")}}:</span>
        <input
                {{ (CRUDBooster::getTypeFilter($field_with) != 'between')?"disabled":"" }}
                type='text'
                class='filter-value-between form-control {{ (in_array($type_data,["date","datetime","timestamp"]))?"datepicker":"timepicker" }}'
                readonly
                placeholder='{{$label}} {{cbTrans("filter_$dir")}}'
                name='filter_column[{{$field_with}}][value][]'
                value='<?php echo (CRUDBooster::getTypeFilter($field_with) == 'between') ? $value : "";
                ?>'>
    </div>
</div>
