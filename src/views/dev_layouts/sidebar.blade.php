<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">
    <section class="sidebar">
        <div class='main-menu mt-10'>
            <!-- Sidebar Menu -->
            <ul class="sidebar-menu" data-widget="tree">
                <li>
                    <a href='{{ cb()->getDeveloperUrl("menus") }}'><i class='fa fa-bars'></i>
                        <span>Manage Menus</span>
                    </a>
                </li>
                <li>
                    <a href='{{ cb()->getDeveloperUrl("modules") }}'><i class='fa fa-cubes'></i>
                        <span>Manage Modules</span>
                    </a>
                </li>
                <li>
                    <a href='{{ cb()->getDeveloperUrl("roles") }}'><i class='fa fa-key'></i>
                        <span>Manage Roles</span>
                    </a>
                </li>

                <li>
                    <a href='{{ cb()->getDeveloperUrl("users") }}'><i class='fa fa-users'></i>
                        <span>Manage Users</span>
                    </a>
                </li>

                <li>
                    <a href='{{ cb()->getDeveloperUrl("plugin-store") }}'><i class='fa fa-star'></i>
                        <span>Plugin Store</span>
                    </a>
                </li>
            </ul><!-- /.sidebar-menu -->
        </div>
    </section>
    <!-- /.sidebar -->
</aside>
