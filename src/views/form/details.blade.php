@extends('crudbooster::admin_template')
@section('content')

    <div>
        @if(request('return_url'))
            <p><a title='Return' href='{{request("return_url")}}'><i class='fa fa-chevron-circle-left '></i>
                    &nbsp; {{ cbTrans("form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a>
            </p>
        @else
            <p><a title='Main Module' href='{{CRUDBooster::mainpath()}}'><i class='fa fa-chevron-circle-left '></i>
                nbsp; {{ cbTrans("form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a>
            </p>
        @endif


        <div class="panel panel-default">
            <div class="panel-heading">
                <strong><i class='{{CRUDBooster::getCurrentModule()->icon}}'></i> {!! $page_title or "Page Title" !!}
                </strong>
            </div>

            <div class="panel-body" style="padding:20px 0px 0px 0px">
                <?php
                $action = (@$row) ? CRUDBooster::mainpath("edit-save/$id") : CRUDBooster::mainpath("add-save");
                $return_url = ($return_url) ?: request('return_url');
                ?>
                <form class='form-horizontal' method='post' id="form" enctype="multipart/form-data" action='{{$action}}'>
                    <input type='hidden' name='return_url' value='{{ @$return_url }}'/>
                    <input type='hidden' name='ref_mainpath' value='{{ CRUDBooster::mainpath() }}'/>
                    <input type='hidden' name='ref_parameter' value='{{urldecode(http_build_query(@$_GET))}}'/>
                    <div class="box-body" id="parent-form-area">
                        @include("crudbooster::form.form_detail", ['forms' => $forms])
                    </div><!-- /.box-body -->

                    <br><br><br>
                    <div class="box-footer" style="background: #F5F5F5">
                        <br>
                    </div><!-- /.box-footer-->
                </form>

            </div>
        </div>
    </div><!--END AUTO MARGIN-->

@endsection