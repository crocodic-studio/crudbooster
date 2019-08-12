<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->
    <a href="{{ cb()->getAdminUrl() }}" title='{{ cb()->getAppName()  }}' class="logo">
        <span class="logo-mini">{{ substr(cb()->getAppName(),0,2) }}</span>
        <span class="logo-lg">{{ cb()->getAppName() }}</span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

                <li>
                    <a target="_blank" href="{{ cb()->getAdminUrl() }}">{{ __("cb::cb.open_admin_area") }} <i class="fa fa-sign-in"></i></a>
                </li>
                <!-- User Account Menu -->
                <li class="dropdown user user-menu">
                    <!-- Menu Toggle Button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <!-- The user image in the navbar-->
                        <img src="{{ asset(dummyPhoto()) }}" class="user-image" alt="User Image"/>
                        <!-- hidden-xs hides the username on small devices so only the image appears. -->
                        <span class="hidden-xs">{{ __("cb::cb.developer") }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- The user image in the menu -->
                        <li class="user-header">
                            <img src="{{ asset(dummyPhoto())  }}" class="img-circle" alt="User Image"/>
                            <p>
                                {{ __("cb::cb.developer") }}
                                <small><em><?php echo date('d F Y')?></em></small>
                            </p>
                        </li>

                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-right">
                                <a href="{{ cb()->getDeveloperUrl("logout") }}" title="{{ __("cb::cb.click_here_to_logout") }}" class="btn btn-danger btn-flat"><i class='fa fa-power-off'></i></a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>
