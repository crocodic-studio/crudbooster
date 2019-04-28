<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-{{ trans('crudbooster.left') }} image">
                <img src="{{ cb()->session()->photo() }}" class="img-circle" alt="{{ trans('crudbooster.user_image') }}"/>
            </div>
            <div class="pull-{{ trans('crudbooster.left') }} info">
                <p>{{ cb()->session()->name() }}</p>
                <!-- Status -->
                <a href="#"><i class="fa fa-circle text-success"></i> {{ trans('crudbooster.online') }}</a>
            </div>
        </div>


        <div class='main-menu'>

            <!-- Sidebar Menu -->
            <ul class="sidebar-menu">
                <li class="header">{{trans("crudbooster.menu_navigation")}}</li>
                <!-- Optionally, you can add icons to the links -->

                <li class="{{ (request()->is(config('crudbooster.ADMIN_PATH'))) ? 'active' : '' }}">
                    <a href='{{ cb()->getAdminUrl() }}'><i class='fa fa-dashboard'></i>
                        <span>{{trans("crudbooster.text_dashboard")}}</span>
                    </a>
                </li>

                @foreach(cb()->sidebar()->all() as $menu)
                    <?php /** @var \crocodicstudio\crudbooster\models\SidebarModel $menu */?>
                    <li class='{{($menu->getSub())?"treeview":""}} {{ (request()->is($menu->getBasepath()."*"))?"active":""}}'>
                        <a href='{{ $menu->getUrl() }}'>
                            <i class='{{$menu->getIcon()}}'></i> <span>{{$menu->getName()}}</span>
                            @if($menu->getSub())<i class="fa fa-angle-{{ trans("crudbooster.right") }} pull-{{ trans("crudbooster.right") }}"></i>@endif
                        </a>
                        @if($menu->getSub())
                            <ul class="treeview-menu">
                                @foreach($menu->getSub() as $sub)
                                    <li class='{{($sub->getSub())?"treeview":""}} {{ (request()->is($sub->getBasepath()."*"))?"active":""}}'>
                                        <a href='{{ $sub->getUrl() }}'>
                                            <i class='{{$sub->getIcon()}}'></i> <span>{{$sub->getName()}}</span>
                                            @if($sub->getSub())<i class="fa fa-angle-{{ trans("crudbooster.right") }} pull-{{ trans("crudbooster.right") }}"></i>@endif
                                        </a>
                                        @if($sub->getSub())
                                            <ul class="treeview-menu">
                                                @foreach($sub->getSub() as $sub2)
                                                    <li class='{{($sub2->getSub())?"treeview":""}} {{ (request()->is($sub2->getBasepath()."*"))?"active":""}}'>
                                                        <a href='{{ $sub2->getUrl() }}'>
                                                            <i class='{{$sub2->getIcon()}}'></i> <span>{{$sub2->getName()}}</span>
                                                            @if($sub2->getSub())<i class="fa fa-angle-{{ trans("crudbooster.right") }} pull-{{ trans("crudbooster.right") }}"></i>@endif
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach


            </ul><!-- /.sidebar-menu -->

        </div>

    </section>
    <!-- /.sidebar -->
</aside>
