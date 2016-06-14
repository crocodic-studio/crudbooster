<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->
    <a href="#" class="logo"><b>Admin</b>{{Session::get('appname')}}</a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
            @if(Session::get('admin_is_superadmin'))<li><a href="{{url('admin/api_generator')}}"><i class='fa fa-book'></i> API Doc</a></li>@endif
                <!-- User Account Menu -->
                <li class="dropdown user user-menu">
                    <!-- Menu Toggle Button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <!-- The user image in the navbar-->
                        <img src="{{ (Session::get('admin_photo'))?:asset("/assets/adminlte/dist/img/user2-160x160.jpg") }}" class="user-image" alt="User Image"/>
                        <!-- hidden-xs hides the username on small devices so only the image appears. -->
                        <span class="hidden-xs">{{ Session::get('admin_name') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- The user image in the menu -->
                        <li class="user-header">
                            <img src="{{ (Session::get('admin_photo'))?:asset("/assets/adminlte/dist/img/user2-160x160.jpg") }}" class="img-circle" alt="User Image" />
                            <p>
                                {{ Session::get('admin_name') }}
                                <small>{{Session::get('admin_privileges_name')}}</small>
                                <small><em><?php echo date('d F Y')?></em> </small>
                            </p>
                        </li>

                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="{{ url('admin/users/edit/'. Session::get('admin_id')) }}" class="btn btn-default btn-flat"><i class='fa fa-user'></i> Profile</a>
                            </div>
                            <div class="pull-right">
                                <a href="{{ url('admin/logout') }}" class="btn btn-default btn-flat"><i class='fa fa-power-off'></i> Sign out</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>