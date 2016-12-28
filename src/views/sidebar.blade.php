<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ CRUDBooster::myPhoto() }}" style='height:50px;width:50px' class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
                <p>{{ CRUDBooster::myName() }}</p>
                <!-- Status -->
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>


     
            <div class='main-menu'> 

            <!-- Sidebar Menu -->
            <ul class="sidebar-menu">
                <li class="header">{{trans("crudbooster.menu_navigation")}}</li>
                <!-- Optionally, you can add icons to the links -->

                <?php $dashboard = CRUDBooster::sidebarDashboard();?>
                @if($dashboard)
                    <li data-id='{{$dashboard->id}}' class='{{(CRUDBooster::getCurrentDashboardId()==$dashboard->id)?"active":""}}'><a href='{{CRUDBooster::adminPath()}}?m=0&d={{$dashboard->id}}' class='{{($dashboard->color)?"text-".$dashboard->color:""}}' ><i class='fa fa-dashboard'></i> <span>{{trans("crudbooster.text_dashboard")}}</span> </a></li>
                @endif
                
                @foreach(CRUDBooster::sidebarMenu() as $menu)                         
                    <li data-id='{{$menu->id}}' class='{{(count($menu->children))?"treeview":""}} {{(CRUDBooster::getCurrentMenuId()==$menu->id && CRUDBooster::getCurrentDashboardId()!=$menu->id )?"active":""}}'><a href='{{ ($menu->is_broken)?"javascript:alert('Controller / Route Not Found')":$menu->url."?m=".$menu->id }}' class='{{($menu->color)?"text-".$menu->color:""}}'><i class='{{$menu->icon}} {{($menu->color)?"text-".$menu->color:""}}'></i> <span>{{$menu->name}}</span> 
                    @if(count($menu->children))<i class="fa fa-angle-left pull-right"></i>@endif
                    </a>
                        @if(count($menu->children))
                            <ul class="treeview-menu"> 
                                @foreach($menu->children as $child)
                                    <li data-id='{{$child->id}}' class='{{(CRUDBooster::getCurrentMenuId()==$child->id && CRUDBooster::getCurrentDashboardId()!=$child->id)?"active":""}}'><a href='{{ ($child->is_broken)?"javascript:alert('Controller / Route Not Found')":$child->url."?m=".$child->id }}'><i class='{{$child->icon}}'></i> <span>{{$child->name}}</span></a></li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach



                @if(CRUDBooster::isSuperadmin())
                    <li class="header">SUPERADMIN</li>
                    <li class='treeview'>
                        <a href='#'><i class='fa fa-key'></i> <span>Privileges & Roles</span>  <i class="fa fa-angle-left pull-right"></i></a>
                        <ul class='treeview-menu'>
                            <li><a href='{{Route("PrivilegesControllerGetAdd")}}?m=0'><i class='fa fa-plus'></i> Add New Privilege</a></li>
                            <li><a href='{{Route("PrivilegesControllerGetIndex")}}?m=0'><i class='fa fa-bars'></i> List Privilege</a></li>
                        </ul>
                    </li>

                    <li class='treeview'>
                        <a href='#'><i class='fa fa-users'></i> <span>Users Management</span>  <i class="fa fa-angle-left pull-right"></i></a>
                        <ul class='treeview-menu'>
                            <li><a href='{{Route("AdminCmsUsersControllerGetAdd")}}?m=0'><i class='fa fa-plus'></i> Add New User</a></li>
                            <li><a href='{{Route("AdminCmsUsersControllerGetIndex")}}?m=0'><i class='fa fa-bars'></i> List User</a></li>
                        </ul>
                    </li>
                                                        
                    <li><a href='{{Route("MenusControllerGetIndex")}}?m=0'><i class='fa fa-bars'></i> Menu Management</a></li>                    
                    <li class="treeview">
                    <a href="#"><i class='fa fa-wrench'></i> <span>Settings</span> <i class="fa fa-angle-left pull-right"></i></a>
                        <ul class="treeview-menu"> 
                            <li class="<?=('settings'==$current_path && !Request::get('group'))?'active':''?>"><a href='{{route("SettingsControllerGetAdd")}}?m=0'><i class='fa fa-plus'></i> Add New Setting</a></li>
                            <?php 
                                $groupSetting = DB::table('cms_settings')->groupby('group_setting')->pluck('group_setting');
                                foreach($groupSetting as $gs):
                            ?>                                
                            <li class="<?=($gs == Request::get('group'))?'active':''?>"><a href='{{route("SettingsControllerGetShow")}}?group={{urlencode($gs)}}&m=0'><i class='fa fa-wrench'></i> {{$gs}}</a></li>
                            <?php endforeach;?>        
                        </ul>
                    </li>
                    <li class='treeview'>
                        <a href='#'><i class='fa fa-th'></i> <span>Module Generator</span>  <i class="fa fa-angle-left pull-right"></i></a>
                        <ul class='treeview-menu'>
                            <li><a href='{{Route("ModulsControllerGetStep1")}}?m=0'><i class='fa fa-plus'></i> Add New Module</a></li>
                            <li><a href='{{Route("ModulsControllerGetIndex")}}?m=0'><i class='fa fa-bars'></i> List Module</a></li>
                        </ul>
                    </li>

                    <li class='treeview'>
                        <a href='#'><i class='fa fa-dashboard'></i> <span>Statistic Builder</span>  <i class="fa fa-angle-left pull-right"></i></a>
                        <ul class='treeview-menu'>
                            <li><a href='{{Route("StatisticBuilderControllerGetAdd")}}?m=0'><i class='fa fa-plus'></i> Add New Statistic</a></li>
                            <li><a href='{{Route("StatisticBuilderControllerGetIndex")}}?m=0'><i class='fa fa-bars'></i> List Statistic</a></li>
                        </ul>
                    </li>

                    <li class='treeview'>
                        <a href='#'><i class='fa fa-fire'></i> <span>API Generator</span>  <i class="fa fa-angle-left pull-right"></i></a>
                        <ul class='treeview-menu'>
                            <li><a href='{{Route("ApiCustomControllerGetGenerator")}}?m=0'><i class='fa fa-plus'></i> Add New API</a></li>
                            <li><a href='{{Route("ApiCustomControllerGetIndex")}}?m=0'><i class='fa fa-bars'></i> List API (Documentation)</a></li>
                            <li><a href='{{Route("ApiCustomControllerGetScreetKey")}}?m=0'><i class='fa fa-bars'></i> Generate Screet Key</a></li>
                        </ul>
                    </li>

                    <li class='treeview'>
                        <a href='#'><i class='fa fa-envelope-o'></i> <span>Email Templates</span>  <i class="fa fa-angle-left pull-right"></i></a>
                        <ul class='treeview-menu'>
                            <li><a href='{{Route("EmailTemplatesControllerGetAdd")}}?m=0'><i class='fa fa-plus'></i> Add New Email</a></li>
                            <li><a href='{{Route("EmailTemplatesControllerGetIndex")}}?m=0'><i class='fa fa-bars'></i> List Email Template</a></li>        
                        </ul>
                    </li>
                    
                    <li><a href='{{Route("LogsControllerGetIndex")}}?m=0'><i class='fa fa-flag'></i> Log User Access</a></li>
                @endif

            </ul><!-- /.sidebar-menu -->

            </div>
       
    </section>
    <!-- /.sidebar -->
</aside>
