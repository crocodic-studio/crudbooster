<li class="dropdown notifications-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" title='Notifications'
       aria-expanded="false">
        <i id='icon_notification' class="fa fa-bell-o"></i>
        <span id='notification_count' class="label label-danger" style="display:none">0</span>
    </a>
    <ul id='list_notifications' class="dropdown-menu">
        <li class="header">{{cbTrans("text_no_notification")}}</li>
        <li>
            <!-- inner menu: contains the actual data -->
            <div class="slimScrollDiv"
                 style="position: relative; overflow: hidden; width: auto; height: 200px;">
                <ul class="menu" style="overflow: hidden; width: 100%; height: 200px;">
                    <li>
                        <a href="#">
                            <em>{{cbTrans("text_no_notification")}}</em>
                        </a>
                    </li>

                </ul>
                <div class="slimScrollBar"
                     style="width: 3px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 7px; z-index: 99; right: 1px; height: 195.122px; background: rgb(0, 0, 0);"></div>
                <div class="slimScrollRail"
                     style="width: 3px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; opacity: 0.2; z-index: 90; right: 1px; background: rgb(51, 51, 51);"></div>
            </div>
        </li>
        <li class="footer">
            <a href="{{route('AdminNotificationsControllerGetIndex')}}">{{cbTrans("text_view_all_notification")}}</a>
        </li>
    </ul>
</li>