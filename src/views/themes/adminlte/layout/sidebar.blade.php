<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ cb()->session()->photo() }}" class="img-circle" style="width: 50px; height: 40px"/>
            </div>
            <div class="pull-left info">
                <p>{{ cb()->session()->name() }}</p>
                <!-- Status -->
                <a href="javascript:;">{{ cb()->session()->roleName() }}</a>
            </div>
        </div>


        <div class='main-menu'>

            <!-- Sidebar Menu -->
            <ul class="sidebar-menu" data-widget="tree">
                <li class="header">{{ cbLang("menu_navigation")}}</li>
                <!-- Optionally, you can add icons to the links -->

                <li class="{{ (request()->is(cb()->getAdminPath())) ? 'active' : '' }}">
                    <a href='{{ cb()->getAdminUrl() }}'><i class='fa fa-home'></i>
                        <span>{{ cbLang("dashboard")}}</span>
                    </a>
                </li>

                <!-- Sidebar Menu Has Been Cached -->
                @foreach(cb()->sidebar()->all() as $menu)
                    <?php /** @var \crocodicstudio\crudbooster\models\SidebarModel $menu */?>
                    <li class='{{($menu->getSub())?"treeview":""}} {{ ($menu->getPermalink() && request()->is(cb()->getAdminPath()."/".$menu->getPermalink()."*"))?"active":"inactive"}}'>
                        <a href='{{ $menu->getUrl() }}'>
                            <i class='{{$menu->getIcon()}}'></i> <span>{{$menu->getName()}}</span>
                            @if($menu->getSub())<i class="fa fa-angle-right pull-right"></i>@endif
                        </a>
                        @if($menu->getSub())
                            <ul class="treeview-menu">
                                @foreach($menu->getSub() as $sub)
                                    <li class='{{($sub->getSub())?"treeview":""}} {{ ($sub->getPermalink() && request()->is(cb()->getAdminPath()."/".$sub->getPermalink()."*"))?"active":"inactive"}}'>
                                        <a href='{{ $sub->getUrl() }}'>
                                            <i class='{{$sub->getIcon()}}'></i> <span>{{$sub->getName()}}</span>
                                            @if($sub->getSub())<i class="fa fa-angle-right pull-right"></i>@endif
                                        </a>
                                        @if($sub->getSub())
                                            <ul class="treeview-menu">
                                                @foreach($sub->getSub() as $sub2)
                                                    <li class='{{($sub2->getSub())?"treeview":""}}  {{ ($sub2->getPermalink() && request()->is(cb()->getAdminPath()."/".$sub2->getPermalink()."*"))?"active":"inactive"}}'>
                                                        <a href='{{ $sub2->getUrl() }}'>
                                                            <i class='{{$sub2->getIcon()}}'></i> <span>{{$sub2->getName()}}</span>
                                                            @if($sub2->getSub())<i class="fa fa-angle-right pull-right"></i>@endif
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
                @push("bottom")
                    <script>
                        $(function() {
                            // To handle parent sidebar menu active
                            $(".sidebar-menu ul li.active").each(function() {
                                if( $(this).parent().parent("li").length ) {
                                    $(this).parent().parent("li").addClass("active")
                                }
                                if( $(this).parent().parent().parent().parent("li").length ) {
                                    $(this).parent().parent().parent().parent("li").addClass("active")
                                }
                            })
                        })
                    </script>
                @endpush


            </ul><!-- /.sidebar-menu -->

        </div>

    </section>
    <!-- /.sidebar -->
</aside>
