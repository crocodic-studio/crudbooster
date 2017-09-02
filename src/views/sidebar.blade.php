<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-{{ cbTrans('left') }} image">
                <img src="{{ CRUDBooster::myPhoto() }}" style="width:45px;height:45px;" class="img-circle"
                     alt="{{ cbTrans('user_image') }}"/>
            </div>
            <div class="pull-{{ cbTrans('left') }} info">
                <p>{{ CRUDBooster::myName() }}</p>
                <!-- Status -->
                <a href="#">{!! CB::icon('circle text-success') !!} {{ cbTrans('online') }}</a>
            </div>
        </div>


        <div class='main-menu'>

            <!-- Sidebar Menu -->
            <ul class="sidebar-menu">
                <li class="header">{{cbTrans("menu_navigation")}}</li>
                <!-- Optionally, you can add icons to the links -->

                <?php $dashboard = CRUDBooster::sidebarDashboard();?>
                @if($dashboard)
                    <li data-id='{{$dashboard->id}}'
                        class="{{ (Request::is(config('crudbooster.ADMIN_PATH'))) ? 'active' : '' }}"><a
                                href='{{CRUDBooster::adminPath()}}'
                                class='{{($dashboard->color)?"text-".$dashboard->color:""}}'>{!! CB::icon('dashboard') !!}
                            <span>{{cbTrans("text_dashboard")}}</span> </a></li>
                @endif

                @foreach(CRUDBooster::sidebarMenu() as $menu)
                    <li data-id='{{$menu->id}}'
                        class='{{(count($menu->children))?"treeview":""}} {{ (Request::is($menu->url_path."*"))?"active":""}}'>
                        <a href='{{ ($menu->is_broken)?"javascript:alert('".cbTrans('controller_route_404')."')":$menu->url }}'
                           class='{{($menu->color)?"text-".$menu->color:""}}'>
                            <i class='{{$menu->icon}} {{($menu->color)?"text-".$menu->color:""}}'></i>
                            <span>{{$menu->name}}</span>
                            @if(count($menu->children)){!! CB::icon('angle-right pull-right') !!}@endif
                        </a>
                        @if(count($menu->children))
                            <ul class="treeview-menu">
                                @foreach($menu->children as $child)
                                    <li data-id='{{$child->id}}'
                                        class='{{(Request::is($child->url_path."*"))?"active":""}}'>
                                        <a href='{{ ($child->is_broken)?"javascript:alert('".cbTrans('controller_route_404')."')":$child->url}}'
                                           class='{{($child->color)?"text-".$child->color:""}}'>
                                            <i class='{{$child->icon}}'></i> <span>{{$child->name}}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach


                @if(CRUDBooster::isSuperadmin())
                    <li class="header">{{ cbTrans('SUPERADMIN') }}</li>
                    <li class='treeview'>
                        <a href='#'>{!! CB::icon('key') !!}
                            <span>{{ cbTrans('Privileges_Roles') }}</span> {!! CB::icon('angle-right pull-right') !!}
                        </a>
                        <ul class='treeview-menu'>
                            <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/privileges/add*')) ? 'active' : '' }}">
                                <a href='{{Route("AdminPrivilegesControllerGetAdd")}}'>{{ $current_path }}{!! CB::icon('plus') !!} {{ cbTrans('Add_New_Privilege') }}</a>
                            </li>
                            <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/privileges')) ? 'active' : '' }}">
                                <a href='{{Route("AdminPrivilegesControllerGetIndex")}}'>{!! CB::icon('bars') !!} {{ cbTrans('List_Privilege') }}</a>
                            </li>
                        </ul>
                    </li>

                    <li class='treeview'>
                        <a href='#'>{!! CB::icon('users') !!}
                            <span>{{ cbTrans('Users_Management') }}</span> {!! CB::icon('angle-right pull-right') !!}
                        </a>
                        <ul class='treeview-menu'>
                            <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/users/add*')) ? 'active' : '' }}">
                                <a href='{{Route("AdminUsersControllerGetAdd")}}'>{!! CB::icon('plus') !!} {{ cbTrans('add_user') }}</a>
                            </li>
                            <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/users')) ? 'active' : '' }}">
                                <a href='{{Route("AdminUsersControllerGetIndex")}}'>{!! CB::icon('bars') !!} {{ cbTrans('List_users') }}</a>
                            </li>
                        </ul>
                    </li>

                    <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/menus*')) ? 'active' : '' }}"><a
                                href='{{Route("AdminMenusControllerGetIndex")}}'>{!! CB::icon('bars') !!} {{ cbTrans('Menu_Management') }}</a>
                    </li>
                    <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/file-manager*')) ? 'active' : '' }}">
                        <a href='{{Route("AdminFileManagerControllerGetIndex")}}'>{!! CB::icon('bars') !!} {{ cbTrans('menu_filemanager') }}</a>
                    </li>
                    <li class="treeview">
                        <a href="#">{!! CB::icon('wrench') !!}
                            <span>{{ cbTrans('settings') }}</span> {!! CB::icon('angle-right pull-right') !!}</a>
                        <ul class="treeview-menu">
                            <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/settings/add*')) ? 'active' : '' }}">
                                <a href='{{route("AdminSettingsControllerGetAdd")}}'>{!! CB::icon('plus') !!} {{ cbTrans('Add_New_Setting') }}</a>
                            </li>
                            <?php
                            $groupSetting = DB::table('cms_settings')->groupby('group_setting')->pluck('group_setting');
                            foreach($groupSetting as $gs):
                            ?>
                            <li class="<?=($gs == Request::get('group')) ? 'active' : ''?>"><a
                                        href='{{route("AdminSettingsControllerGetShow")}}?group={{urlencode($gs)}}&m=0'>{!! CB::icon('wrench') !!} {{$gs}}</a>
                            </li>
                            <?php endforeach;?>
                        </ul>
                    </li>
                    <li class='treeview'>
                        <a href='#'>{!! CB::icon('th') !!}
                            <span>{{ cbTrans('Module_Generator') }}</span> {!! CB::icon('angle-right pull-right') !!}
                        </a>
                        <ul class='treeview-menu'>
                            <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/modules/step1')) ? 'active' : '' }}">
                                <a href='{{Route("AdminModulesControllerGetStep1")}}'>{!! CB::icon('plus') !!} {{ cbTrans('Add_New_Module') }}</a>
                            </li>
                            <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/modules')) ? 'active' : '' }}">
                                <a href='{{Route("AdminModulesControllerGetIndex")}}'>{!! CB::icon('bars') !!} {{ cbTrans('List_Module') }}</a>
                            </li>
                        </ul>
                    </li>

                    <li class='treeview'>
                        <a href='#'>{!! CB::icon('dashboard') !!}
                            <span>{{ cbTrans('Statistic_Builder') }}</span> {!! CB::icon('angle-right pull-right') !!}
                        </a>
                        <ul class='treeview-menu'>
                            <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/statistic-builder/add')) ? 'active' : '' }}">
                                <a href='{{Route("AdminStatisticBuilderControllerGetAdd")}}'>{!! CB::icon('plus') !!} {{ cbTrans('Add_New_Statistic') }}</a>
                            </li>
                            <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/statistic-builder')) ? 'active' : '' }}">
                                <a href='{{Route("AdminStatisticBuilderControllerGetIndex")}}'>{!! CB::icon('bars') !!} {{ cbTrans('List_Statistic') }}</a>
                            </li>
                        </ul>
                    </li>

                    <li class='treeview'>
                        <a href='#'>{!! CB::icon('fire') !!}
                            <span>{{ cbTrans('API_Generator') }}</span> {!! CB::icon('angle-right pull-right') !!}</a>
                        <ul class='treeview-menu'>
                            <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/api-generator/generator*')) ? 'active' : '' }}">
                                <a href='{{Route("AdminApiGeneratorControllerGetGenerator")}}'>{!! CB::icon('plus') !!} {{ cbTrans('Add_New_API') }}</a>
                            </li>
                            <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/api-generator')) ? 'active' : '' }}">
                                <a href='{{Route("AdminApiGeneratorControllerGetIndex")}}'>{!! CB::icon('bars') !!} {{ cbTrans('list_API') }}</a>
                            </li>
                            <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/api-generator/screet-key*')) ? 'active' : '' }}">
                                <a href='{{Route("AdminApiGeneratorControllerGetScreetKey")}}'>{!! CB::icon('bars') !!} {{ cbTrans('Generate_Screet_Key') }}</a>
                            </li>
                        </ul>
                    </li>

                    <li class='treeview'>
                        <a href='#'>{!! CB::icon('envelope-o') !!}
                            <span>{{ cbTrans('Email_Templates') }}</span> {!! CB::icon('angle-right pull-right') !!}</a>
                        <ul class='treeview-menu'>
                            <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/email-templates/add*')) ? 'active' : '' }}">
                                <a href='{{Route("AdminEmailTemplatesControllerGetAdd")}}'>{!! CB::icon('plus') !!} {{ cbTrans('Add_New_Email') }}</a>
                            </li>
                            <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/email-templates')) ? 'active' : '' }}">
                                <a href='{{Route("AdminEmailTemplatesControllerGetIndex")}}'>{!! CB::icon('bars') !!} {{ cbTrans('List_Email_Template') }}</a>
                            </li>
                        </ul>
                    </li>

                    <li class="{{ (Request::is(config('crudbooster.ADMIN_PATH').'/logs*')) ? 'active' : '' }}"><a
                                href='{{Route("AdminLogsControllerGetIndex")}}'>{!! CB::icon('flag') !!} {{ cbTrans('Log_User_Access') }}</a>
                    </li>
                @endif

            </ul><!-- /.sidebar-menu -->

        </div>

    </section>
    <!-- /.sidebar -->
</aside>
