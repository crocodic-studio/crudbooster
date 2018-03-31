<li class='treeview'>
    <a href='#'>{!! cbIcon('key') !!}
        <span>{{ cbTrans('Privileges_Roles') }}</span> {!! cbIcon('angle-right pull-right') !!}
    </a>
    <ul class='treeview-menu'>
        <li class="{{ (Request::is(cbAdminPath().'/privileges/add*')) ? 'active' : '' }}">
            <a href='{{route("AdminPrivilegesControllerGetAdd")}}'>
                {{ $current_path }}{!! cbIcon('plus') !!} {{ cbTrans('Add_New_Privilege') }}
            </a>
        </li>
        <li class="{{ (Request::is(cbAdminPath().'/privileges')) ? 'active' : '' }}">
            <a href='{{route("AdminPrivilegesControllerGetIndex")}}'>
                {!! cbIcon('bars') !!} {{ cbTrans('List_Privilege') }}
            </a>
        </li>
    </ul>
</li>