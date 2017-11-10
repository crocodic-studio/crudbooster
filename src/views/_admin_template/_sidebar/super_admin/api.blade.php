<li class='treeview'>
    <a href='#'>{!! CB::icon('fire') !!}
        <span>{{ cbTrans('API_Generator') }}</span> {!! CB::icon('angle-right pull-right') !!}</a>
    <ul class='treeview-menu'>
        <li class="{{ (Request::is(cbAdminPath().'/api-generator/generator*')) ? 'active' : '' }}">
            <a href='{{Route("AdminApiGeneratorControllerGetGenerator")}}'>{!! CB::icon('plus') !!} {{ cbTrans('Add_New_API') }}</a>
        </li>
        <li class="{{ (Request::is(cbAdminPath().'/api-generator')) ? 'active' : '' }}">
            <a href='{{Route("AdminApiGeneratorControllerGetIndex")}}'>{!! CB::icon('bars') !!} {{ cbTrans('list_API') }}</a>
        </li>
        <li class="{{ (Request::is(cbAdminPath().'/api-generator/screet-key*')) ? 'active' : '' }}">
            <a href='{{Route("AdminApiGeneratorControllerGetScreetKey")}}'>{!! CB::icon('bars') !!} {{ cbTrans('Generate_Screet_Key') }}</a>
        </li>
    </ul>
</li>