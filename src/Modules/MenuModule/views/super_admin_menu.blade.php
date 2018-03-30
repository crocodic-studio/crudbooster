<li class="{{ (Request::is(cbAdminPath().'/menus*')) ? 'active' : '' }}">
    <a href='{{Route("AdminMenusControllerGetIndex")}}'>
        {!! cbIcon('bars') !!} {{ cbTrans('Menu_Management') }}
    </a>
</li>