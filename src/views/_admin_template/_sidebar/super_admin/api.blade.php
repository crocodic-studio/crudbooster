<li class='treeview'>
    <a href='#'>{!! cbIcon('fire') !!}
        <span>{{ cbTrans('API_Generator') }}</span> {!! cbIcon('angle-right pull-right') !!}</a>
    <ul class='treeview-menu'>
        <li class="{{ (Request::is(cbAdminPath().'/api-generator/generator*')) ? 'active' : '' }}">
            <a href='{{Route("AdminApiGeneratorControllerGetGenerator")}}'>{!! cbIcon('plus') !!} {{ cbTrans('Add_New_API') }}</a>
        </li>
        <li class="{{ (Request::is(cbAdminPath().'/api-generator')) ? 'active' : '' }}">
            <a href='{{Route("AdminApiGeneratorControllerGetIndex")}}'>{!! cbIcon('bars') !!} {{ cbTrans('list_API') }}</a>
        </li>
        <li class="{{ (Request::is(cbAdminPath().'/api-generator/screet-key*')) ? 'active' : '' }}">
            <a href='{{Route("AdminApiGeneratorControllerGetScreetKey")}}'>{!! cbIcon('bars') !!} {{ cbTrans('Generate_Screet_Key') }}</a>
        </li>
    </ul>
</li>