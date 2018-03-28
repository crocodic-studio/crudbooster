<li class='treeview'>
    <a href='#'>{!! CB::icon('envelope-o') !!}
        <span>{{ cbTrans('Email_Templates') }}</span> {!! CB::icon('angle-right pull-right') !!}</a>
    <ul class='treeview-menu'>
        <li class="{{ (Request::is(cbAdminPath().'/email-templates/add*')) ? 'active' : '' }}">
            <a href='{{Route("AdminEmailTemplatesControllerGetAdd")}}'>{!! CB::icon('plus') !!} {{ cbTrans('Add_New_Email') }}</a>
        </li>
        <li class="{{ (Request::is(cbAdminPath().'/email-templates')) ? 'active' : '' }}">
            <a href='{{Route("AdminEmailTemplatesControllerGetIndex")}}'>{!! CB::icon('bars') !!} {{ cbTrans('List_Email_Template') }}</a>
        </li>
    </ul>
</li>