<li class="{{ (Request::is(cbAdminPath().'/file-manager*')) ? 'active' : '' }}">
    <a href='{{route("AdminFileManagerControllerGetIndex")}}'>
        {!! cbIcon('bars') !!} {{ cbTrans('menu_filemanager') }}
    </a>
</li>