<?php

if ($form['datatable'] && $form['relationship_table']) {
    $datatable_array = explode(",", $form['datatable']);
    $datatable_tab = $datatable_array[0];
    $datatable_field = $datatable_array[1];
    $foreignKey = CRUDBooster::getForeignKey($table, $form['relationship_table']);
    $foreignKey2 = CRUDBooster::getForeignKey($datatable_tab, $form['relationship_table']);

    $ids = DB::table($form['relationship_table'])->where($form['relationship_table'].'.'.$foreignKey, $id)->pluck($foreignKey2)->toArray();
    $value = DB::table($datatable_tab)->select($datatable_field)->whereIn('id', $ids)->pluck($datatable_field)->toArray();
} elseif ($form['datatable']) {

    $datatable = explode(',', $form['datatable']);
    $table = $datatable[0];
    $field = $datatable[1];
    $r = CRUDBooster::first($table, ['id' => $value])->$field;
    if ($r) {
        $value = [$r];
    } else {
        $value = [];
    }
} elseif ($form['dataquery']) {
    $dataquery = $form['dataquery'];
    $query = DB::select(DB::raw($dataquery));
    if ($query) {
        foreach ($query as $q) {
            if ($q->value == $value) {
                $value = [$q->label];
                break;
            }
        }
        if (! $value) $value = [];
    }
} else {
    $value = explode(";", $value);
}

foreach ($value as $v) {
    echo "<span class='badge'>$v</span> ";
}
?>