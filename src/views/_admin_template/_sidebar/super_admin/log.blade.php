<li class="{{ (Request::is(cbAdminPath().'/logs*')) ? 'active' : '' }}">
    <a href='{{Route("AdminLogsControllerGetIndex")}}'>
        {!! CB::icon('flag') !!} {{ cbTrans('Log_User_Access') }}
    </a>
</li>