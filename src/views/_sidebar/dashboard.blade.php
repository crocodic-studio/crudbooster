<li data-id='{{$dashboard->id}}'
    class="{{ (Request::is(cbConfig('ADMIN_PATH'))) ? 'active' : '' }}">
    <a href='{{CRUDBooster::adminPath()}}'
       class='{{($dashboard->color)?"text-".$dashboard->color:""}}'>
        {!! CB::icon('dashboard') !!}
        <span>{{cbTrans("text_dashboard")}}</span>
    </a>
</li>
