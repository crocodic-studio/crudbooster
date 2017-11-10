<li data-id='{{$dashboard->id}}'
    class="{{ (Request::is(cbAdminPath())) ? 'active' : '' }}">
    <a href='{!! $dashboard->url !!}'
       class='{{($dashboard->color)?"text-".$dashboard->color:""}}'>
        {!! CB::icon('dashboard') !!}
        <span>{{cbTrans("text_dashboard")}}</span>
    </a>
</li>
