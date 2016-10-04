<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->
    <a href="{{url(config('crudbooster.ADMIN_PATH'))}}" class="logo">{{get_setting('appname')}}</a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">      

            <li class="dropdown notifications-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" title='Notifications' aria-expanded="false">
                  <i id='icon_notification' class="fa fa-bell-o"></i>
                  <span id='notification_count' class="label label-danger" style="display:none">0</span>
                </a>
                <ul id='list_notifications' class="dropdown-menu">
                  <li class="header">You have 0 notifications</li>
                  <li>
                    <!-- inner menu: contains the actual data -->
                    <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 200px;">
                    <ul class="menu" style="overflow: hidden; width: 100%; height: 200px;">
                      <li>
                        <a href="#">
                          <em>No Notifications Is Found</em>
                        </a>
                      </li>

                    </ul>
                    <div class="slimScrollBar" style="width: 3px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 7px; z-index: 99; right: 1px; height: 195.122px; background: rgb(0, 0, 0);"></div><div class="slimScrollRail" style="width: 3px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; opacity: 0.2; z-index: 90; right: 1px; background: rgb(51, 51, 51);"></div></div>
                  </li>
                  <li class="footer"><a href="{{route('NotificationsControllerGetIndex')}}">View all</a></li>
                </ul>
              </li>

                <!-- User Account Menu -->
                <li class="dropdown user user-menu">
                    <!-- Menu Toggle Button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <!-- The user image in the navbar-->
                        <img src="{{ get_my_photo() }}" class="user-image" alt="User Image"/>
                        <!-- hidden-xs hides the username on small devices so only the image appears. -->
                        <span class="hidden-xs">{{ get_my_name() }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- The user image in the menu -->
                        <li class="user-header">
                            <img src="{{ get_my_photo() }}" class="img-circle" alt="User Image" />
                            <p>
                                {{ get_my_name() }}
                                <small>{{ get_my_privilege_name() }}</small>
                                <small><em><?php echo date('d F Y')?></em> </small>                                
                            </p>
                        </li>

                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="{{ route('UsersControllerGetProfile') }}" class="btn btn-default btn-flat"><i class='fa fa-user'></i> Profile</a>
                            </div>
                            <div class="pull-right">
                                <a title='Lock Screen' href="{{ route('getLockScreen') }}" class='btn btn-default btn-flat'><i class='fa fa-key'></i></a> 
                                <a href="javascript:void(0)" onclick="swal({   
                                    title: 'Do you want to logout ?',   
                                    text: 'You should login again in the future, or you may press Lock Screen only',   
                                    type:'info',   
                                    showCancelButton:true, 
                                    allowOutsideClick:true,  
                                    confirmButtonColor: '#DD6B55',   
                                    confirmButtonText: 'Logout',   
                                    cancelButtonText: 'Cancel',
                                    closeOnConfirm: false 
                                    }, function(){                                                                                 
                                        location.href = '{{ route("getLogout") }}';

                                    });" title="Sign Out ?" class="btn btn-danger btn-flat"><i class='fa fa-power-off'></i></a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>