<li class="{{ (Request::is(cbAdminPath().'/file-manager*')) ? 'active' : '' }}">
    <a href='{{Route("AdminFileManagerControllerGetIndex")}}'>
        {!! cbIcon('bars') !!} {{ cbTrans('menu_filemanager') }}
    </a>
</li>