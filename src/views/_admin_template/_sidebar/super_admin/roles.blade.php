<li class='treeview'>
    <a href='#'>{!! CB::icon('key') !!}
        <span>{{ cbTrans('Privileges_Roles') }}</span> {!! CB::icon('angle-right pull-right') !!}
    </a>
    <ul class='treeview-menu'>
        <li class="{{ (Request::is(cbAdminPath().'/privileges/add*')) ? 'active' : '' }}">
            <a href='{{Route("AdminPrivilegesControllerGetAdd")}}'>
                {{ $current_path }}{!! CB::icon('plus') !!} {{ cbTrans('Add_New_Privilege') }}
            </a>
        </li>
        <li class="{{ (Request::is(cbAdminPath().'/privileges')) ? 'active' : '' }}">
            <a href='{{Route("AdminPrivilegesControllerGetIndex")}}'>
                {!! CB::icon('bars') !!} {{ cbTrans('List_Privilege') }}
            </a>
        </li>
    </ul>
</li>