<div class="modal fade" tabindex="-1" role="dialog" id='advanced_filter_modal'>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" aria-label="Close" type="button" data-dismiss="modal">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title"><i class='fa fa-filter'></i> {{cbTrans("filter_dialog_title")}}</h4>
            </div>
            <form method='get' action=''>
                <div class="modal-body">
                    @foreach($columns as $key => $col)
                    <?php if (isset($col['image']) || isset($col['download']) || $col['visible'] === FALSE) continue;?>

                    <div class='form-group'>

                        <div class='row-filter-combo row'>

                            <div class="col-sm-2">
                                <strong>{{$col['label']}}</strong>
                            </div>

                            <div class='col-sm-3'>
                                @include('crudbooster::default._index.advanced_filter_modal.select', ['field_with' => $col['field_with'], 'type_data' => $col['type_data'] ])
                            </div><!--END COL_SM_4-->


                            <div class='col-sm-5'>
                                <input type='text' class='filter-value form-control'
                                       style="{{ (CRUDBooster::getTypeFilter($col["field_with"]) == 'between')?"display:none":"display:block"}}"
                                       disabled name='filter_column[{{$col["field_with"]}}][value]'
                                       value='{{ (!is_array(CRUDBooster::getValueFilter($col["field_with"])))?CRUDBooster::getValueFilter($col["field_with"]):"" }}'>

                                <div class='row between-group'
                                     style="{{ (CRUDBooster::getTypeFilter($col["field_with"]) == 'between')?"display:block":"display:none" }}">
                                    <div class='col-sm-6'>
                                        <div class='input-group {{ ($col["type_data"] == "time")?"bootstrap-timepicker":"" }}'>
                                            <span class="input-group-addon">{{cbTrans("filter_from")}}:</span>
                                            <input
                                                    {{ (CRUDBooster::getTypeFilter($col["field_with"]) != 'between')?"disabled":"" }}
                                                    type='text'
                                                    class='filter-value-between form-control {{ (in_array($col["type_data"],["date","datetime","timestamp"]))?"datepicker":"timepicker" }}'
                                                    readonly
                                                    placeholder='{{$col["label"]}} {{cbTrans("filter_from")}}'
                                                    name='filter_column[{{$col["field_with"]}}][value][]'
                                                    value='<?php
                                                    $value = CRUDBooster::getValueFilter($col["field_with"]);
                                                    echo (CRUDBooster::getTypeFilter($col["field_with"]) == 'between') ? $value[0] : "";
                                                    ?>'>
                                        </div>
                                    </div>
                                    <div class='col-sm-6'>
                                        <div class='input-group {{ ($col["type_data"] == "time")?"bootstrap-timepicker":"" }}'>
                                            <span class="input-group-addon">{{cbTrans("filter_to")}}:</span>
                                            <input
                                                    {{ (CRUDBooster::getTypeFilter($col["field_with"]) != 'between')?"disabled":"" }}
                                                    type='text'
                                                    class='filter-value-between form-control {{ (in_array($col["type_data"],["date","datetime","timestamp"]))?"datepicker":"timepicker" }}'
                                                    readonly
                                                    placeholder='{{$col["label"]}} {{cbTrans("filter_to")}}'
                                                    name='filter_column[{{$col["field_with"]}}][value][]'
                                                    value='<?php
                                                    $value = CRUDBooster::getValueFilter($col["field_with"]);
                                                    echo (CRUDBooster::getTypeFilter($col["field_with"]) == 'between') ? $value[1] : "";
                                                    ?>'>
                                        </div>
                                    </div>
                                </div>
                            </div><!--END COL_SM_6-->


                            <div class='col-sm-2'>
                                <select class='form-control' name='filter_column[{{$col["field_with"]}}][sorting]'>
                                    <option value=''>** Sorting</option>
                                    <option {{ (CRUDBooster::getSortingFilter($col["field_with"]) == 'asc')?"selected":"" }} value='asc'>{{cbTrans("filter_ascending")}}</option>
                                    <option {{ (CRUDBooster::getSortingFilter($col["field_with"]) == 'desc')?"selected":"" }} value='desc'>{{cbTrans("filter_descending")}}</option>
                                </select>
                            </div><!--END_COL_SM_2-->

                        </div>

                    </div>
                    @endforeach

                </div>
                <div class="modal-footer" align="right">
                    <button class="btn btn-default" type="button"
                            data-dismiss="modal">{{cbTrans("button_close")}}</button>
                    <button class="btn btn-default btn-reset" type="reset"
                            onclick='location.href="{{Request::get("lasturl")}}"'>{{cbTrans("button_reset")}}</button>
                    <button class="btn btn-primary btn-submit" type="submit">{{cbTrans("button_submit")}}</button>
                </div>
                <input type="hidden" name="lasturl" value="{{Request::get('lasturl')?:Request::fullUrl()}}">
            </form>
        </div>

    </div>
</div>