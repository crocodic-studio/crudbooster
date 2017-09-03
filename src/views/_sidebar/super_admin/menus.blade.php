<li class="{{ (Request::is(cbConfig('ADMIN_PATH').'/menus*')) ? 'active' : '' }}">
    <a href='{{Route("AdminMenusControllerGetIndex")}}'>
        {!! CB::icon('bars') !!} {{ cbTrans('Menu_Management') }}
    </a>
</li>