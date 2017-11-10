<li class="{{ (Request::is(cbAdminPath().'/menus*')) ? 'active' : '' }}">
    <a href='{{Route("AdminMenusControllerGetIndex")}}'>
        {!! CB::icon('bars') !!} {{ cbTrans('Menu_Management') }}
    </a>
</li>