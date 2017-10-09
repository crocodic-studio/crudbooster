@if($button_add && CRUDBooster::canCreate())
    <a href="{{ CRUDBooster::mainpath('add').'?return_url='.urlencode(Request::fullUrl()).'&parent_id='.g('parent_id').'&parent_field='.$parent_field }}"
       id='btn_add_new_data' class="btn btn-sm btn-success"
       title="{{cbTrans('action_add_data')}}">

        {!!  CB::icon('plus-circle') !!}
        {{cbTrans('action_add_data')}}
    </a>
@endif