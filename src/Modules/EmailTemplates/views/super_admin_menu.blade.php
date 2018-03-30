<li class='treeview'>
    <a href='#'>{!! cbIcon('envelope-o') !!}
        <span>{{ cbTrans('Email_Templates') }}</span> {!! cbIcon('angle-right pull-right') !!}</a>
    <ul class='treeview-menu'>
        <li class="{{ (Request::is(cbAdminPath().'/email-templates/add*')) ? 'active' : '' }}">
            <a href='{{route("AdminEmailTemplatesControllerGetAdd")}}'>{!! cbIcon('plus') !!} {{ cbTrans('Add_New_Email') }}</a>
        </li>
        <li class="{{ (Request::is(cbAdminPath().'/email-templates')) ? 'active' : '' }}">
            <a href='{{route("AdminEmailTemplatesControllerGetIndex")}}'>{!! cbIcon('bars') !!} {{ cbTrans('List_Email_Template') }}</a>
        </li>
    </ul>
</li>