<li class='treeview'>
    <a href='#'>{!! CB::icon('dashboard') !!}
        <span>{{ cbTrans('Statistic_Builder') }}</span> {!! CB::icon('angle-right pull-right') !!}
    </a>
    <ul class='treeview-menu'>
        <li class="{{ (Request::is(cbAdminPath().'/statistic-builder/add')) ? 'active' : '' }}">
            <a href='{{Route("AdminStatisticBuilderControllerGetAdd")}}'>{!! CB::icon('plus') !!} {{ cbTrans('Add_New_Statistic') }}</a>
        </li>

        <li class="{{ (Request::is(cbAdminPath().'/statistic-builder')) ? 'active' : '' }}">
            <a href='{{ Route("AdminStatisticBuilderControllerGetIndex")}}'>{!! CB::icon('bars') !!} {{ cbTrans('List_Statistic') }}</a>
        </li>
    </ul>
</li>