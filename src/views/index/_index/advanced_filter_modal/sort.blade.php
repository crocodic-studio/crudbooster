<div class='col-sm-2'>
    <select class='form-control' name='filter_column[{{$field_with}}][sorting]'>
        <option value=''>** Sorting</option>
        <option {{ (CRUDBooster::getSortingFilter($field_with) == 'asc')?"selected":"" }} value='asc'>{{cbTrans("filter_ascending")}}</option>
        <option {{ (CRUDBooster::getSortingFilter($field_with) == 'desc')?"selected":"" }} value='desc'>{{cbTrans("filter_descending")}}</option>
    </select>
</div><!--END_COL_SM_2-->