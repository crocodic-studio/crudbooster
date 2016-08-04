<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ (Session::get('admin_photo'))?:asset("/assets/adminlte/dist/img/user2-160x160.jpg") }}" style='height:50px;width:50px' class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
                <p>{{ Session::get('admin_name') }}</p>
                <!-- Status -->
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a> • <a target="_blank" href="{{url('/')}}">Visit FrontEnd</a>
            </div>
        </div>


        <?php if(Session::get('dashboard_config_mode')==1):?>

        <script type="text/javascript">
            function add_statistic_datatable() {
                
                $("#modal_widget_statistic .modal-title").text("Datatable Setting");
                $("#modal_widget_statistic .modal-body").load("{{action('AdminController@getSettingDatatable')}}",function() {
                    $("#modal_widget_statistic").modal("show");   
                });
            }

            function edit_statistic_datatable(id) {
                
                $("#modal_widget_statistic .modal-title").text("Datatable Setting");
                $("#modal_widget_statistic .modal-body").load("{{action('AdminController@getSettingDatatable')}}/"+id,function() {
                    $("#modal_widget_statistic").modal("show");                                            
                });                
            }

            function add_statistic_donut() {
                
                $("#modal_widget_statistic .modal-title").text("Chart Donut Setting");
                $("#modal_widget_statistic .modal-body").load("{{action('AdminController@getSettingChartDonut')}}",function() {
                    $("#modal_widget_statistic").modal("show");   
                });
            }

            function edit_statistic_donut(id) {
                
                $("#modal_widget_statistic .modal-title").text("Chart Donut Setting");
                $("#modal_widget_statistic .modal-body").load("{{action('AdminController@getSettingChartDonut')}}/"+id,function() {
                    $("#modal_widget_statistic").modal("show");   
                });                
            }

             function add_statistic_bar() {
                
                $("#modal_widget_statistic .modal-title").text("Chart Bar Setting");
                $("#modal_widget_statistic .modal-body").load("{{action('AdminController@getSettingChartBar')}}",function() {
                    $("#modal_widget_statistic").modal("show");   

                    $("#modal_widget_statistic select[name=table_name]").change(function() {
                        var table_name = $(this).val();
                        $.get("{{action('AdminController@getSelectColumnTable')}}/"+table_name,function(resp) {
                            $("#modal_widget_statistic select[name=column]").html(resp);
                        });
                    });
                });
            }

            function edit_statistic_bar(id) {
                
                $("#modal_widget_statistic .modal-title").text("Chart Bar Setting");
                $("#modal_widget_statistic .modal-body").load("{{action('AdminController@getSettingChartBar')}}/"+id,function() {
                    $("#modal_widget_statistic").modal("show");   

                    $("#modal_widget_statistic select[name=table_name]").change(function() {
                        var table_name = $(this).val();
                        var current = $("#modal_widget_statistic select[name=column]").attr("data-current");
                        $.get("{{action('AdminController@getSelectColumnTable')}}/"+table_name+"/"+current,function(resp) {
                            $("#modal_widget_statistic select[name=column]").html(resp);
                        })
                    })                      

                    $("#modal_widget_statistic select[name=table_name]").trigger('change');
                });                
            }



            function add_statistic_line() {
                
                $("#modal_widget_statistic .modal-title").text("Chart Line Setting");
                $("#modal_widget_statistic .modal-body").load("{{action('AdminController@getSettingChartLine')}}",function() {
                    $("#modal_widget_statistic").modal("show");   

                    $("#modal_widget_statistic select[name=table_name]").change(function() {
                        var table_name = $(this).val();
                        $.get("{{action('AdminController@getSelectColumnTable')}}/"+table_name,function(resp) {
                            $("#modal_widget_statistic select[name=column]").html(resp);
                        })
                    })                      
                });                
            }

            function edit_statistic_line(id) {
                
                $("#modal_widget_statistic .modal-title").text("Chart Line Setting");
                $("#modal_widget_statistic .modal-body").load("{{action('AdminController@getSettingChartLine')}}/"+id,function() {
                    $("#modal_widget_statistic").modal("show");   

                    $("#modal_widget_statistic select[name=table_name]").change(function() {
                        var table_name = $(this).val();
                        var current = $("#modal_widget_statistic select[name=column]").attr("data-current");
                        $.get("{{action('AdminController@getSelectColumnTable')}}/"+table_name+"/"+current,function(resp) {
                            $("#modal_widget_statistic select[name=column]").html(resp);
                        })
                    })                      

                    $("#modal_widget_statistic select[name=table_name]").trigger('change');
                });                
            }

            function add_statistic_number() {
                
                $("#modal_widget_statistic .modal-title").text("Statistic Number Setting");
                $("#modal_widget_statistic .modal-body").load("{{action('AdminController@getSettingStatNumber')}}",function() {
                    $("#modal_widget_statistic").modal("show");    

                    $("#modal_widget_statistic select[name=table_name]").change(function() {
                        var table_name = $(this).val();
                        $.get("{{action('AdminController@getSelectColumnTable')}}/"+table_name,function(resp) {
                            $("#modal_widget_statistic select[name=column]").html(resp);
                        })
                    })                    
                });                
            }            

            function edit_statistic_number(id) {
                
                $("#modal_widget_statistic .modal-title").text("Statistic Number Setting");
                $("#modal_widget_statistic .modal-body").load("{{action('AdminController@getSettingStatNumber')}}/"+id,function() {
                    $("#modal_widget_statistic").modal("show");    

                    $("#modal_widget_statistic select[name=table_name]").change(function() {
                        var table_name = $(this).val();
                        var current = $("#modal_widget_statistic select[name=column]").attr("data-current");
                        $.get("{{action('AdminController@getSelectColumnTable')}}/"+table_name+"/"+current,function(resp) {
                            $("#modal_widget_statistic select[name=column]").html(resp);
                        })
                    })   

                    $("#modal_widget_statistic select[name=table_name]").trigger('change');                 
                });                
            }
            $(function() {
                $("#dashboard").on("click",".btn-edit-stat",function() {
                    var id = $(this).attr('data-id');
                    edit_statistic_number(id);
                });
                $("#dashboard").on("click",".btn-edit-chart-line",function() {
                    var id = $(this).attr('data-id');
                    edit_statistic_line(id);
                });
                $("#dashboard").on("click",".btn-edit-chart-bar",function() {
                    var id = $(this).attr('data-id');
                    edit_statistic_bar(id);
                });
                $("#dashboard").on("click",".btn-edit-chart-donut",function() {
                    var id = $(this).attr('data-id');
                    edit_statistic_donut(id);
                });
                $("#dashboard").on("click",".btn-edit-datatable",function() {
                    var id = $(this).attr('data-id');
                    edit_statistic_datatable(id);
                });

                $("#modal_widget_statistic").on("submit","form",function() {
                    $.ajax({
                        data:$(this).serialize(),
                        type:'post',
                        url:$(this).attr('action'),
                        success:function(id) {
                            $.get("{{action('AdminController@getStatisticDashboard')}}/"+id,function(html_widget) {
                                console.log('l '+$("#dashboard_"+id).length);
                                if($("#dashboard_"+id).length>0) {
                                    $("#dashboard_"+id).replaceWith(html_widget);
                                }else{
                                    $("#dashboard").append(html_widget);    
                                }
                                
                            })
                            $("#modal_widget_statistic").modal("hide");
                        },
                        error: function(xhr, textStatus, errorThrown){
                           alert('Request Failed');
                        }
                    })
                    return false;
                })

                $("#dashboard").on("click",".btn-delete-stat",function() {
                    var h = $(this).parents('.dashboard_widget');
                    var id = $(this).attr('data-id');
                    if(confirm("Are you sure want to delete ?")) {                        
                        $.get("{{action('AdminController@getRemoveCmsDashboard')}}/"+id,function(resp) {
                            h.remove();
                        });
                    }
                })

                $("#modal_widget_statistic .btn-add").click(function() {
                    $("#modal_widget_statistic form").submit();
                })

            })
        </script>

            <div class='dashboard-config-menu'>
                <ul class='sidebar-menu'>
                    <li class='header'>Config Menu</li>
                    <li><a href='#' onclick="add_statistic_number()"><span>Statistic Number</span><i class='fa fa-plus pull-right'></i></a></li>
                    <li class="treeview">
                        <a href='#'><span>Statistic Graphic</span> <i class='fa fa-angle-left pull-right'></i></a>
                        <ul class="treeview-menu">
                            <li><a href='#' onclick="add_statistic_line()"><span>Line Chart</span><i class='fa fa-plus pull-right'></i></a></li>
                            <li><a href='#' onclick="add_statistic_bar()"><span>Bar Chart</span><i class='fa fa-plus pull-right'></i></a></li>
                            <li><a href='#' onclick="add_statistic_donut()"><span>Donut Chart</span><i class='fa fa-plus pull-right'></i></a></li>
                        </ul>
                    </li>                
                    <li><a href='#' onclick="add_statistic_datatable()"><span>Data Table</span><i class='fa fa-plus pull-right'></i></a></li>
                    <li><a href='{{action("AdminController@getUnsetDashboardConfigMode")}}'><i class='fa fa-power-off'></i> <span>Back To Admin</span></a></li>
                </ul>
            </div>
        <?php else:?>
            <div class='main-menu'> 

            <!-- Sidebar Menu -->
            <ul class="sidebar-menu">
                <li class="header">Menu Navigasi</li>
                <!-- Optionally, you can add icons to the links -->
            
                <li ><a href="{{ url('/admin') }}"><i class='fa fa-dashboard'></i><span>Dashboard</span></a></li>
                
                <?php        
                    $id_privileges = Session::get('admin_privileges');

                    $groups = DB::table("cms_moduls_group")->orderby("sorting_group","asc")->get();
                    foreach($groups as $g):
                        $current_path = "admin/".Request::segment(2);

                        $moduls = DB::table("cms_moduls")
                            ->where("is_active",1)     
                            ->where("id_cms_moduls_group",$g->id)               
                            ->whereraw("cms_moduls.id in (select b.id_cms_moduls from cms_privileges_roles b where b.id_cms_privileges = '$id_privileges' and is_visible = 1)")
                            ->orderby("sorting","asc")->get();
                ?>
                    @if($g->is_group==1)
                        <li class="treeview <?=(count($moduls)==0)?'hide':''?>">
                            <a href="#"><i class='{{$g->icon_group}}'></i> <span>{{$g->nama_group}}</span> <i class="fa fa-angle-left pull-right"></i></a>
                            <ul class="treeview-menu">
                    @endif


                    <?php  
                        
                        foreach($moduls as $modul):
                    ?>            
                        <li class="<?=($modul->path==$current_path)?'active':''?>"><a href="{{ url($modul->path) }}"><i class='<?=$modul->icon?>'></i><span><?=$modul->name?></span></a></li>
                    <?php endforeach;?>


                    @if($g->is_group==1)
                            </ul>
                        </li>
                    @endif

                <?php
                    endforeach;
                ?>



            </ul><!-- /.sidebar-menu -->

            </div>
        <?php endif;?>
    </section>
    <!-- /.sidebar -->
</aside>