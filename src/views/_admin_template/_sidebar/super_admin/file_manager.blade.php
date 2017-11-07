<li class="{{ (Request::is(cbConfig('ADMIN_PATH').'/file-manager*')) ? 'active' : '' }}">
    <a href='{{Route("AdminFileManagerControllerGetIndex")}}'>
        {!! CB::icon('bars') !!} {{ cbTrans('menu_filemanager') }}
    </a>
</li>