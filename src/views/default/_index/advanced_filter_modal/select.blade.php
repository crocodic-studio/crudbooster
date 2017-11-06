<select name='filter_column[{{ $field_with }}][type]' data-type='{{ $type_data }}' class="filter-combo form-control">

    <option value=''>** {{cbTrans("filter_select_operator_type")}}</option>

    @if(in_array($type_data, ['string','varchar','text','char']))
        <option {{ (CRUDBooster::getTypeFilter( $field_with ) == 'like')?"selected":"" }} value='like'>{{cbTrans("filter_like")}}</option>
    @endif

    @if(in_array($type_data, ['string','varchar','text','char']))
        <option {{ (CRUDBooster::getTypeFilter( $field_with ) == 'not like')?"selected":"" }} value='not like'>{{cbTrans("filter_not_like")}}</option>
    @endif

    <option typeallow='all' {{ (CRUDBooster::getTypeFilter( $field_with ) == '=')?"selected":"" }} value='='>{{cbTrans("filter_equal_to")}}</option>

    @if(in_array($type_data, ['int','integer','double','float','decimal','time']))
        <option {{ (CRUDBooster::getTypeFilter( $field_with ) == '>=')?"selected":"" }} value='>='>{{cbTrans("filter_greater_than_or_equal")}}</option>
    @endif

    @if(in_array($type_data, ['int','integer','double','float','decimal','time']))
        <option {{ (CRUDBooster::getTypeFilter( $field_with ) == '<=')?"selected":"" }} value='<='>{{cbTrans("filter_less_than_or_equal")}}</option>
    @endif

    @if(in_array($type_data, ['int','integer','double','float','decimal','time']))
        <option {{ (CRUDBooster::getTypeFilter( $field_with ) == '<')?"selected":"" }} value='<'>{{cbTrans("filter_less_than")}}</option>
    @endif

    @if(in_array($type_data, ['int','integer','double','float','decimal','time']))
        <option {{ (CRUDBooster::getTypeFilter( $field_with ) == '>')?"selected":"" }} value='>'>{{cbTrans("filter_greater_than")}}</option>
    @endif

    <option typeallow='all' {{ (CRUDBooster::getTypeFilter( $field_with ) == '!=')?"selected":"" }} value='!='>{{cbTrans("filter_not_equal_to")}}</option>
    <option typeallow='all' {{ (CRUDBooster::getTypeFilter( $field_with ) == 'in')?"selected":"" }} value='in'>{{cbTrans("filter_in")}}</option>
    <option typeallow='all' {{ (CRUDBooster::getTypeFilter( $field_with ) == 'not in')?"selected":"" }} value='not in'>{{cbTrans("filter_not_in")}}</option>

    @if(in_array($type_data, ['date','time','datetime','int','integer','double','float','decimal','timestamp']))
        <option {{ (CRUDBooster::getTypeFilter( $field_with ) == 'between')?"selected":"" }} value='between'>{{cbTrans("filter_between")}}</option>
    @endif

    <option {{ (CRUDBooster::getTypeFilter( $field_with ) == 'empty')?"selected":"" }} value='empty'>
        Empty ( or Null)
    </option>
</select>