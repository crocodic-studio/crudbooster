@if($button_cancel && CRUDBooster::getCurrentMethod() != 'getDetail')
    <a href='
@if(request('return_url'))
    {{request("return_url")}}
    @else
    {{CRUDBooster::mainpath("?".http_build_query(@$_GET)) }}
    @endif
            ' class='btn btn-default'>
        <i class='fa fa-chevron-circle-left'></i> {{ cbTrans("button_back")}}</a>
@endif


@if(CRUDBooster::canCreate() || CRUDBooster::canUpdate())
    @if(CRUDBooster::canCreate() && $button_addmore==true && $command == 'add')
        <input type="submit" name="submit" value='{{ cbTrans("button_save_more")}}' class='btn btn-success'>
    @endif

    @if(($button_save && $command != 'detail') || CB::getCurrentMethod() == 'getProfile')
        <input type="submit" name="submit" value='{{ cbTrans("button_save")}}' class='btn btn-success'>
    @endif
@endif