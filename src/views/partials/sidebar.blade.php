<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-{{ trans('crudbooster.left') }} image">
                <img src="{{ CRUDBooster::myPhoto() }}" style='height:50px;width:50px' class="img-circle" alt="{{ trans('crudbooster.user_image') }}" />
            </div>
            <div class="pull-{{ trans('crudbooster.left') }} info">
                <p>{{ CRUDBooster::myName() }}</p>
                <!-- Status -->
                <a href="#"><i class="fa fa-circle text-success"></i> {{ trans('crudbooster.online') }}</a>
            </div>
        </div>


        <div class='main-menu'>

            <!-- Sidebar Menu -->
            <ul class="sidebar-menu">
                <li class="header">{{trans("crudbooster.menu_navigation")}}</li>
                <!-- Optionally, you can add icons to the links -->

                <?php $dashboard = CRUDBooster::sidebarDashboard();?>
                @if($dashboard)
                    <li data-id='{{$dashboard->id}}' class="{{ (Request::is(config('crudbooster.ADMIN_PATH'))) ? 'active' : '' }}"><a href='{{CRUDBooster::adminPath()}}' class='{{($dashboard->color)?"text-".$dashboard->color:""}}' ><i class='fa fa-dashboard'></i> <span>{{trans("crudbooster.text_dashboard")}}</span> </a></li>
                @endif

                @foreach(CRUDBooster::sidebarMenu() as $menu)
                    <li data-id='{{$menu->id}}' class='{{(count($menu->children))?"treeview":""}} {{(CRUDBooster::getCurrentMenuId()==$menu->id && CRUDBooster::getCurrentDashboardId()!=$menu->id )?"active":""}}'><a href='{{ ($menu->is_broken)?"javascript:alert('".trans('crudbooster.controller_route_404')."')":$menu->url."?m=".$menu->id }}' class='{{($menu->color)?"text-".$menu->color:""}}'><i class='{{$menu->icon}} {{($menu->color)?"text-".$menu->color:""}}'></i> <span>{{$menu->name}}</span>
                            @if(count($menu->children))<i class="fa fa-angle-{{ trans("crudbooster.right") }} pull-{{ trans("crudbooster.right") }}"></i>@endif
                        </a>
                        @if(count($menu->children))
                            <ul class="treeview-menu">
                                @foreach($menu->children as $child)
                                    <li data-id='{{$child->id}}' class='{{(CRUDBooster::getCurrentMenuId()==$child->id && CRUDBooster::getCurrentDashboardId()!=$child->id)?"active":""}}'><a href='{{ ($child->is_broken)?"javascript:alert('".trans('crudbooster.controller_route_404')."')":$child->url."?m=".$child->id }}'><i class='{{$child->icon}}'></i> <span>{{$child->name}}</span></a></li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach



                @if(CRUDBooster::isSuperadmin())
                    <li class="header">{{ trans('crudbooster.SUPERADMIN') }}</li>
                    <li class='treeview'>
                        <a href='#'><i class='fa fa-key'></i> <span>{{ trans('crudbooster.Privileges_Roles') }}</span>  <i class="fa fa-angle-{{ trans("crudbooster.right") }} pull-{{ trans("crudbooster.right") }}"></i></a>
                        <ul class='treeview-menu'>
                            <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/privileges/add*')) ? 'active' : '' }}"><a href='{{Route("PrivilegesControllerGetAdd")}}?m=0'>{{ $current_path }}<i class='fa fa-plus'></i> {{ trans('crudbooster.Add_New_Privilege') }}</a></li>
                            <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/privileges')) ? 'active' : '' }}"><a href='{{Route("PrivilegesControllerGetIndex")}}?m=0'><i class='fa fa-bars'></i> {{ trans('crudbooster.List_Privilege') }}</a></li>
                        </ul>
                    </li>

                    <li class='treeview'>
                        <a href='#'><i class='fa fa-users'></i> <span>{{ trans('crudbooster.Users_Management') }}</span>  <i class="fa fa-angle-{{ trans("crudbooster.right") }} pull-{{ trans("crudbooster.right") }}"></i></a>
                        <ul class='treeview-menu'>
                            <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/users/add*')) ? 'active' : '' }}"><a href='{{Route("AdminCmsUsersControllerGetAdd")}}?m=0'><i class='fa fa-plus'></i> {{ trans('crudbooster.add_user') }}</a></li>
                            <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/users')) ? 'active' : '' }}"><a href='{{Route("AdminCmsUsersControllerGetIndex")}}?m=0'><i class='fa fa-bars'></i> {{ trans('crudbooster.List_users') }}</a></li>
                        </ul>
                    </li>

                    <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/menu_management*')) ? 'active' : '' }}"><a href='{{Route("MenusControllerGetIndex")}}?m=0'><i class='fa fa-bars'></i> {{ trans('crudbooster.Menu_Management') }}</a></li>
                    <li class="treeview">
                        <a href="#"><i class='fa fa-wrench'></i> <span>{{ trans('crudbooster.settings') }}</span> <i class="fa fa-angle-{{ trans("crudbooster.right") }} pull-{{ trans("crudbooster.right") }}"></i></a>
                        <ul class="treeview-menu">
                            <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/settings/add*')) ? 'active' : '' }}"><a href='{{route("SettingsControllerGetAdd")}}?m=0'><i class='fa fa-plus'></i> {{ trans('crudbooster.Add_New_Setting') }}</a></li>
                            <?php
                            $groupSetting = DB::table('cms_settings')->groupby('group_setting')->pluck('group_setting');
                            foreach($groupSetting as $gs):
                            ?>
                            <li class="<?=($gs == Request::get('group'))?'active':''?>"><a href='{{route("SettingsControllerGetShow")}}?group={{urlencode($gs)}}&m=0'><i class='fa fa-wrench'></i> {{$gs}}</a></li>
                            <?php endforeach;?>
                        </ul>
                    </li>
                    <li class='treeview'>
                        <a href='#'><i class='fa fa-th'></i> <span>{{ trans('crudbooster.Module_Generator') }}</span>  <i class="fa fa-angle-{{ trans("crudbooster.right") }} pull-{{ trans("crudbooster.right") }}"></i></a>
                        <ul class='treeview-menu'>
                            <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/module_generator/step1')) ? 'active' : '' }}"><a href='{{Route("ModulsControllerGetStep1")}}?m=0'><i class='fa fa-plus'></i> {{ trans('crudbooster.Add_New_Module') }}</a></li>
                            <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/module_generator')) ? 'active' : '' }}"><a href='{{Route("ModulsControllerGetIndex")}}?m=0'><i class='fa fa-bars'></i> {{ trans('crudbooster.List_Module') }}</a></li>
                        </ul>
                    </li>

                    <li class='treeview'>
                        <a href='#'><i class='fa fa-dashboard'></i> <span>{{ trans('crudbooster.Statistic_Builder') }}</span>  <i class="fa fa-angle-{{ trans("crudbooster.right") }} pull-{{ trans("crudbooster.right") }}"></i></a>
                        <ul class='treeview-menu'>
                            <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/statistic_builder/add')) ? 'active' : '' }}"><a href='{{Route("StatisticBuilderControllerGetAdd")}}?m=0'><i class='fa fa-plus'></i> {{ trans('crudbooster.Add_New_Statistic') }}</a></li>
                            <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/statistic_builder')) ? 'active' : '' }}"><a href='{{Route("StatisticBuilderControllerGetIndex")}}?m=0'><i class='fa fa-bars'></i> {{ trans('crudbooster.List_Statistic') }}</a></li>
                        </ul>
                    </li>

                    <li class='treeview'>
                        <a href='#'><i class='fa fa-fire'></i> <span>{{ trans('crudbooster.API_Generator') }}</span>  <i class="fa fa-angle-{{ trans("crudbooster.right") }} pull-{{ trans("crudbooster.right") }}"></i></a>
                        <ul class='treeview-menu'>
                            <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/api_generator/generator*')) ? 'active' : '' }}"><a href='{{Route("ApiCustomControllerGetGenerator")}}?m=0'><i class='fa fa-plus'></i> {{ trans('crudbooster.Add_New_API') }}</a></li>
                            <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/api_generator')) ? 'active' : '' }}"><a href='{{Route("ApiCustomControllerGetIndex")}}?m=0'><i class='fa fa-bars'></i> {{ trans('crudbooster.list_API') }}</a></li>
                            <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/api_generator/screet-key*')) ? 'active' : '' }}"><a href='{{Route("ApiCustomControllerGetScreetKey")}}?m=0'><i class='fa fa-bars'></i> {{ trans('crudbooster.Generate_Screet_Key') }}</a></li>
                        </ul>
                    </li>

                    <li class='treeview'>
                        <a href='#'><i class='fa fa-envelope-o'></i> <span>{{ trans('crudbooster.Email_Templates') }}</span>  <i class="fa fa-angle-{{ trans("crudbooster.right") }} pull-{{ trans("crudbooster.right") }}"></i></a>
                        <ul class='treeview-menu'>
                            <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/email_templates/add*')) ? 'active' : '' }}"><a href='{{Route("EmailTemplatesControllerGetAdd")}}?m=0'><i class='fa fa-plus'></i> {{ trans('crudbooster.Add_New_Email') }}</a></li>
                            <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/email_templates')) ? 'active' : '' }}"><a href='{{Route("EmailTemplatesControllerGetIndex")}}?m=0'><i class='fa fa-bars'></i> {{ trans('crudbooster.List_Email_Template') }}</a></li>
                        </ul>
                    </li>

                    <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/logs*')) ? 'active' : '' }}"><a href='{{Route("LogsControllerGetIndex")}}?m=0'><i class='fa fa-flag'></i> {{ trans('crudbooster.Log_User_Access') }}</a></li>
                @endif

            </ul><!-- /.sidebar-menu -->

        </div>

    </section>
    <!-- /.sidebar -->
</aside>
