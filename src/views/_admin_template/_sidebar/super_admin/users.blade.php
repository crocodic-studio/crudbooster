<li class='treeview'>
    <a href='#'>{!! CB::icon('users') !!}
        <span>{{ cbTrans('Users_Management') }}</span> {!! CB::icon('angle-right pull-right') !!}
    </a>
    <ul class='treeview-menu'>
        <li class="{{ (Request::is(cbAdminPath().'/users/add*')) ? 'active' : '' }}">
            <a href='{{Route("AdminUsersControllerGetAdd")}}'>
                {!! CB::icon('plus') !!} {{ cbTrans('add_user') }}
            </a>
        </li>
        <li class="{{ (Request::is(cbAdminPath().'/users')) ? 'active' : '' }}">
            <a href='{{Route("AdminUsersControllerGetIndex")}}'>
                {!! CB::icon('bars') !!} {{ cbTrans('List_users') }}
            </a>
        </li>
    </ul>
</li>
