@if(CRUDBooster::getCurrentMethod() != 'getProfile' && $button_cancel)
    @if(request('return_url'))
        <p><a title='Return' href='{{request("return_url")}}'>
                <i class='fa fa-chevron-circle-left '></i>
                &nbsp; {{ cbTrans("form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}
            </a>
        </p>
    @else
        <p><a title='Main Module' href='{{CRUDBooster::mainpath()}}'><i class='fa fa-chevron-circle-left '></i>
                &nbsp; {{ cbTrans("form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a>
        </p>
    @endif
@endif