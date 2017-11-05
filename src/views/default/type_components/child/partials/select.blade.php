<?php
if ($col['datatable']) {
    $tableJoin = explode(',', $col['datatable'])[0];
    $titleField = explode(',', $col['datatable'])[1];
    if (! $col['datatable_where']) {
        $data = CRUDBooster::get($tableJoin, NULL, "$titleField ASC");
    } else {
        $data = CRUDBooster::get($tableJoin, $col['datatable_where'], "$titleField ASC");
    }
    foreach ($data as $i => $d) {
        $options[$i]['value'] = $d->id;
        $options[$i]['label'] = $d->$titleField;
    }
} else {
    $data = $col['dataenum'];
    $data = (is_array($data))?$data:explode(";",$data);
    $options = [];
    foreach ($data as $i => $d) {
        $enum = explode('|', $d);
        if (count($enum) == 1) {
            $enum[1] = $enum[0];
        }
        $options[$i]['value'] = $enum[0];
        $options[$i]['label'] = $enum[1];
    }
}
?>
<select id='{{$name_column}}'
        name='child-{{$col["name"]}}'
        class='form-control select2 {{$col['required']?'required':''}}'
        {{ ($col['readonly']===true)?"readonly":"" }}
>
    <option value=''>{{cbTrans('text_prefix_option')}} {{$col['label']}}</option>
    @foreach ($options as $i => $option)
        <option value='{!! $option['value'] !!}'>{!! $option['label'] !!}</option>
    @endforeach
</select>