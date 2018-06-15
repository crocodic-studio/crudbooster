@extends('crudbooster::admin_template')
@section('content')

    <div>
        @if(request('return_url'))
            <p><a title='Return' href='{{request("return_url")}}'><i class='fa fa-chevron-circle-left'></i>
                    &nbsp; {{ cbTrans("form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a>
            </p>
        @else
            <p><a title='Main Module' href='{{CRUDBooster::mainpath()}}'><i class='fa fa-chevron-circle-left'></i>
                nbsp; {{ cbTrans("form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a>
            </p>
        @endif


        <div class="panel panel-default">
            <div class="panel-heading">
                <strong><i class='{{CRUDBooster::getCurrentModule()->icon}}'></i> {!! isset($page_title) ? $page_title : "Crudbooster Admin Area" !!}
                </strong>
            </div>

            <div class="panel-body" style="padding:20px 0px 0px 0px">
                <?php
                $action = isset($row) ? CRUDBooster::mainpath("edit-save/$id") : CRUDBooster::mainpath("add-save");
                $return_url = ($return_url) ?: request('return_url');
                ?>
                <div class="box-body" id="parent-form-area">
                    @include("crudbooster::form.form_detail", ['forms' => $forms])
                </div>

                <br><br><br>
                <div class="box-footer" style="background: #F5F5F5">
                    <br>
                </div>


            </div>
        </div>
    </div><!--END AUTO MARGIN-->

@endsection