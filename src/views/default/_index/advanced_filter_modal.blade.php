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

                                    <?php
                                    $value = CRUDBooster::getValueFilter($col["field_with"]);
                                    $_arr = array_only($col, ['field_with', 'type_data', 'label'])
                                    ?>

                                    @include('crudbooster::default._index.advanced_filter_modal.datePicker', ['dir' => 'from', 'value' => $value[0]] + $_arr)

                                    @include('crudbooster::default._index.advanced_filter_modal.datePicker', ['dir' => 'to',   'value' => $value[1]] + $_arr)

                                </div>
                            </div><!--END COL_SM_6-->


                            @include('crudbooster::default._index.advanced_filter_modal.sort', ['field_with' => $col['field_with']])

                        </div>

                    </div>
                    @endforeach

                </div>


                @include('crudbooster::default._index.advanced_filter_modal.footer')


                <input type="hidden" name="lasturl" value="{{Request::get('lasturl')?:Request::fullUrl()}}">
            </form>
        </div>

    </div>
</div>