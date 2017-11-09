<li class="{{ (Request::is(cbAdminPath().'/file-manager*')) ? 'active' : '' }}">
    <a href='{{Route("AdminFileManagerControllerGetIndex")}}'>
        {!! CB::icon('bars') !!} {{ cbTrans('menu_filemanager') }}
    </a>
</li>