@if($form['options']['multiple']==false)
    <option value=''>{{cbTrans('text_prefix_option')}} {{$form['label']}}</option>
@endif
<?php

$result = DB::table($select_table)->select($select_value, $select_label);
$result->addSelect(DB::raw("CONCAT(".$select_label.") as select2_text"));
if ($select_where) {
    $result->whereraw($select_where);
}
if ($form['options']['sql_orderby']) {
    $result->orderByRaw($form['options']['sql_orderby']);
} else {
    $result->orderBy('select2_text', 'asc');
}

if (Schema::hasColumn($form['options']['table'], 'deleted_at')) {
    $result->whereNull('deleted_at');
}

$result = $result->get();


$options = [];
foreach ($result as $i => $row) {
    $options[$i]['label'] = $row->select2_text;
    $options[$i]['value'] = $row->{$form['options']['field_value']};

    if ($form['options']['format']) {
        $options[$i]['label'] = $form['options']['format'];
        foreach ($row as $k => $v) {
            $options[$i]['label'] = str_replace("[$k]", $v, $options[$i]['label']);
        }
    }
}
?>
@foreach($options as $option)
    <option {!! findSelected($value, $form, $option['value']) !!} value='{!! $option['value'] !!}'>{!! $option['label'] !!}</option>
@endforeach