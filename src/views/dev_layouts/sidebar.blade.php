<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-{{ trans('crudbooster.left') }} image">
                <img src="{{ asset(dummyPhoto()) }}" class="img-circle" />
            </div>
            <div class="pull-{{ trans('crudbooster.left') }} info">
                <p>Developer</p>
                <!-- Status -->
                <a href="#"><i class="fa fa-circle text-success"></i> {{ trans('crudbooster.online') }}</a>
            </div>
        </div>


        <div class='main-menu'>

            <!-- Sidebar Menu -->
            <ul class="sidebar-menu">
                <li class="header">{{trans("crudbooster.menu_navigation")}}</li>
                <!-- Optionally, you can add icons to the links -->

                <li>
                    <a href='{{ cb()->getDeveloperUrl("modules") }}'><i class='fa fa-cubes'></i>
                        <span>Module Manager</span>
                    </a>
                </li>

                <li>
                    <a href='{{ cb()->getDeveloperUrl("roles") }}'><i class='fa fa-key'></i>
                        <span>Role Manager</span>
                    </a>
                </li>

                <li>
                    <a href='{{ cb()->getDeveloperUrl("menus") }}'><i class='fa fa-bars'></i>
                        <span>Menu Manager</span>
                    </a>
                </li>

            </ul><!-- /.sidebar-menu -->

        </div>

    </section>
    <!-- /.sidebar -->
</aside>
