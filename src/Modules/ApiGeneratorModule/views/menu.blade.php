<li class='treeview'>
    <a href='#'>{!! cbIcon('fire') !!}
        <span>{{ cbTrans('API_Generator') }}</span> {!! cbIcon('angle-right pull-right') !!}</a>
    <ul class='treeview-menu'>
        <li class="{{ (Request::is(cbAdminPath().'/api-generator/generator*')) ? 'active' : '' }}">
            <a href='{{route("AdminApiGeneratorControllerGetGenerator")}}'>{!! cbIcon('plus') !!} {{ cbTrans('Add_New_API') }}</a>
        </li>
        <li class="{{ (Request::is(cbAdminPath().'/api-generator')) ? 'active' : '' }}">
            <a href='{{route("AdminApiGeneratorControllerGetIndex")}}'>{!! cbIcon('bars') !!} {{ cbTrans('list_API') }}</a>
        </li>
        <li class="{{ (Request::is(cbAdminPath().'/api-generator/secret-key*')) ? 'active' : '' }}">
            <a href='{{ route("AdminApiKeyControllerGetIndex") }}'>{!! cbIcon('bars') !!} {{ cbTrans('Generate_Secret_Key') }}</a>
        </li>
    </ul>
</li>