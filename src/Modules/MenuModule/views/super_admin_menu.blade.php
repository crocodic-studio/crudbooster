<li class="{{ (Request::is(cbAdminPath().'/menus*')) ? 'active' : '' }}">
    <a href='{{route("AdminMenusControllerGetIndex")}}'>
        {!! cbIcon('bars') !!} {{ cbTrans('Menu_Management') }}
    </a>
</li>