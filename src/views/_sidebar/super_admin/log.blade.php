<li class="{{ (Request::is(cbConfig('ADMIN_PATH').'/logs*')) ? 'active' : '' }}">
    <a href='{{Route("AdminLogsControllerGetIndex")}}'>
        {!! CB::icon('flag') !!} {{ cbTrans('Log_User_Access') }}
    </a>
</li>