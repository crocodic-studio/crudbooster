<li class='treeview'>
    <a href='#'>{!! cbIcon('th') !!}
        <span>{{ cbTrans('Module_Generator') }}</span> {!! cbIcon('angle-right pull-right') !!}
    </a>
    <ul class='treeview-menu'>
        <li class="{{ (Request::is(cbAdminPath().'/modules/step1')) ? 'active' : '' }}">
            <a href='{{route("AdminModulesControllerGetStep1")}}'>
                {!! cbIcon('plus') !!} {{ cbTrans('Add_New_Module') }}
            </a>
        </li>
        <li class="{{ (Request::is(cbAdminPath().'/modules')) ? 'active' : '' }}">
            <a href='{{route("AdminModulesControllerGetIndex")}}'>
                {!! cbIcon('bars') !!} {{ cbTrans('List_Module') }}
            </a>
        </li>
    </ul>
</li>