<li class='treeview'>
    <a href='#'>{!! CB::icon('th') !!}
        <span>{{ cbTrans('Module_Generator') }}</span> {!! CB::icon('angle-right pull-right') !!}
    </a>
    <ul class='treeview-menu'>
        <li class="{{ (Request::is(cbAdminPath().'/modules/step1')) ? 'active' : '' }}">
            <a href='{{Route("AdminModulesControllerGetStep1")}}'>
                {!! CB::icon('plus') !!} {{ cbTrans('Add_New_Module') }}
            </a>
        </li>
        <li class="{{ (Request::is(cbAdminPath().'/modules')) ? 'active' : '' }}">
            <a href='{{Route("AdminModulesControllerGetIndex")}}'>
                {!! CB::icon('bars') !!} {{ cbTrans('List_Module') }}
            </a>
        </li>
    </ul>
</li>