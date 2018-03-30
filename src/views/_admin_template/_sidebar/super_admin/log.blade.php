<li class="{{ (Request::is(cbAdminPath().'/logs*')) ? 'active' : '' }}">
    <a href='{{route("AdminLogsControllerGetIndex")}}'>
        {!! cbIcon('flag') !!} {{ cbTrans('Log_User_Access') }}
    </a>
</li>