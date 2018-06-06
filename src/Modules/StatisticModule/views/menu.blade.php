<li class='treeview'>
    <a href='#'>{!! cbIcon('dashboard') !!}
        <span>{{ cbTrans('Statistic_Builder') }}</span> {!! cbIcon('angle-right pull-right') !!}
    </a>
    <ul class='treeview-menu'>
        <li class="{{ (Request::is(cbAdminPath().'/statistic-builder/add')) ? 'active' : '' }}">
            <a href='{{route("AdminStatisticBuilderControllerGetAdd")}}'>{!! cbIcon('plus') !!} {{ cbTrans('Add_New_Statistic') }}</a>
        </li>

        <li class="{{ (Request::is(cbAdminPath().'/statistic-builder')) ? 'active' : '' }}">
            <a href='{{ route("AdminStatisticBuilderControllerGetIndex")}}'>{!! cbIcon('bars') !!} {{ cbTrans('List_Statistic') }}</a>
        </li>
    </ul>
</li>