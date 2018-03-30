<li class='treeview'>
    <a href='#'>{!! cbIcon('users') !!}
        <span>{{ cbTrans('Users_Management') }}</span> {!! cbIcon('angle-right pull-right') !!}
    </a>
    <ul class='treeview-menu'>
        <li class="{{ (Request::is(cbAdminPath().'/users/add*')) ? 'active' : '' }}">
            <a href='{{route("AdminUsersControllerGetAdd")}}'>
                {!! cbIcon('plus') !!} {{ cbTrans('add_user') }}
            </a>
        </li>
        <li class="{{ (Request::is(cbAdminPath().'/users')) ? 'active' : '' }}">
            <a href='{{route("AdminUsersControllerGetIndex")}}'>
                {!! cbIcon('bars') !!} {{ cbTrans('List_users') }}
            </a>
        </li>
    </ul>
</li>
