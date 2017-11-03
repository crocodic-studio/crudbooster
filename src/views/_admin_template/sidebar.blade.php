<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        @include('crudbooster::_admin_template._sidebar.userpanel')

        <div class='main-menu'>
            <ul class="sidebar-menu">
                <li class="header">{{cbTrans("menu_navigation")}}</li>
                <!-- Optionally, you can add icons to the links -->
                @includeWhen(CRUDBooster::sidebarDashboard(), 'crudbooster::_admin_template._sidebar.dashboard')
                @include('crudbooster::_admin_template._sidebar.dynamic_menus')
                @if(CRUDBooster::isSuperadmin())
                    <li class="header">{{ cbTrans('SUPERADMIN') }}</li>
                    @include('crudbooster::_admin_template._sidebar.super_admin.roles')
                    @include('crudbooster::_admin_template._sidebar.super_admin.users')
                    @include('crudbooster::_admin_template._sidebar.super_admin.menus')
                    @include('crudbooster::_admin_template._sidebar.super_admin.settings')
                    @include('crudbooster::_admin_template._sidebar.super_admin.module')
                    @include('crudbooster::_admin_template._sidebar.super_admin.static')
                    @include('crudbooster::_admin_template._sidebar.super_admin.api')
                    @include('crudbooster::_admin_template._sidebar.super_admin.email')
                    @include('crudbooster::_admin_template._sidebar.super_admin.log')
                @endif
            </ul>
        </div>


    </section>

</aside>


