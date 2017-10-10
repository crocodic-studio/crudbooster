<!DOCTYPE html>
<html>
<head>
    <title>{{ ($page_title)?CRUDBooster::getSetting('appname').': '.strip_tags($page_title):"Admin Area" }}</title>
    @include('crudbooster::_admin_template.meta')
    @include('crudbooster::_admin_template.css')

    @stack('head')
</head>
<body class="{!! Session::get('theme_color','skin-blue') !!} {!! cbConfig('ADMIN_LAYOUT') !!}">
<div id='app' class="wrapper">

    <!-- Header -->
@include('crudbooster::_admin_template.header')

<!-- Sidebar -->
@include('crudbooster::_admin_template.sidebar')

<!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

        <section class="content-header">
            <?php
            $module = CRUDBooster::getCurrentModule();
            ?>
            @if($module)
                <h1>
                    <i class='{{$module->icon}}'></i>
                    {{($page_title)?:$module->name}} &nbsp;&nbsp;

                    <!--START BUTTON -->

                    @if(CRUDBooster::getCurrentMethod() == 'getIndex')
                        @if($button_show)
                            <a href="{{ CRUDBooster::mainpath().'?'.http_build_query(Request::all()) }}"
                               id='btn_show_data' class="btn btn-sm btn-primary"
                               title="{{cbTrans('action_show_data')}}">

                                {!!  CB::icon('table') !!}
                                {{cbTrans('action_show_data')}}
                            </a>
                        @endif

                        @include('crudbooster::_admin_template.addButton')
                    @endif


                @include('crudbooster::_admin_template.export_import_buttons', ['exportBtn' => $button_export, 'importBtn' => $button_import, 'query' => $build_query])

                <!--ADD ACTIon-->
                @include('crudbooster::_admin_template.action_buttons', ['index_button' => $index_button])
                <!-- END BUTTON -->
                </h1>


                <ol class="breadcrumb">
                    <li>
                        <a href="{{CRUDBooster::adminPath()}}">
                            {!!  CB::icon('dashboard') !!}
                            {{ cbTrans('home') }}
                        </a>
                    </li>
                    <li class="active">{{$module->name}}</li>
                </ol>
            @else
                <h1>{{CRUDBooster::getSetting('appname')}}
                    <small>Information</small>
                </h1>
            @endif
        </section>


        <!-- Main content -->
        <section id='content_section' class="content">

            @include('crudbooster::_admin_template.alert')

            @yield('content')
        </section>
    </div>


    @include('crudbooster::_admin_template.footer')

</div><!-- ./wrapper -->


@include('crudbooster::_admin_template.admin_template_plugins')

<!-- load js -->
@include('crudbooster::_admin_template.js')

@stack('bottom')

<!-- Optionally, you can add Slimscroll and FastClick plugins.
      Both of these plugins are recommended to enhance the
      user experience -->
</body>
</html>
