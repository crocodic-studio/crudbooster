@extends('crudbooster::admin_template')
@section('content')

    <div>
        <?php
        $action = (isset($row)) ? cb()->mainpath("edit-save/$row->id") : cb()->mainpath("add-save");
        $return_url = ($return_url) ?: g('return_url');
        ?>
        <form class='form-horizontal' method='post' id="form" enctype="multipart/form-data" action='{{$action}}'>
        {!! csrf_field() !!}
        <input type='hidden' name='return_url' value='{{ @$return_url }}'/>
        <input type='hidden' name='ref_mainpath' value='{{ cb()->mainpath() }}'/>
        <input type='hidden' name='ref_parameter' value='{{urldecode(http_build_query(@$_GET))}}'/>
        @if($hide_form)
            <input type="hidden" name="hide_form" value='{!! serialize($hide_form) !!}'>
        @endif

        <div class="row" style="margin-bottom: 10px">
            <div class="col-sm-6">
                <div style="padding-top: 5px">
                @if($button_cancel)
                    @if(g('return_url'))
                        <p><a title='Return' href='{{g("return_url")}}'><i class='fa fa-chevron-circle-left '></i>
                                &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>cb()->getCurrentModule()->name])}}</a></p>
                    @else
                        <p><a title='Main Module' href='{{cb()->mainpath()}}'><i class='fa fa-chevron-circle-left '></i>
                                &nbsp; {{trans("crudbooster.form_back_to_list",['module'=>cb()->getCurrentModule()->name])}}</a></p>
                    @endif
                @endif
                </div>
            </div>
            <div class="col-sm-6">
                <div align="right">

                </div>
            </div>
        </div>

        <div class="panel panel-default" style="margin-bottom: 10px">
            <div class="panel-heading">
                <strong><i class='{{cb()->getCurrentModule()->icon}}'></i> {!! $page_title !!}</strong>
            </div>
            <div class="panel-body" style="padding:20px 0px 0px 0px">
                <div class="box-body" id="parent-form-area">
                    @if($command == 'detail')
                        @include("crudbooster::default.form_detail")
                    @else
                        @include("crudbooster::default.form_body")
                    @endif
                </div><!-- /.box-body -->
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">

            </div>
            <div class="col-sm-6">
                <div align="right">
                    @if(cb()->isCreate() || cb()->isUpdate())
                        @if(cb()->isCreate() && $button_addmore==TRUE && $command == 'add')
                            <input type="submit" name="submit" value='{{trans("crudbooster.button_save_more")}}' class='btn btn-primary'>
                        @endif
                        @if($button_save && $command != 'detail')
                            <input type="submit" name="submit" value='{{trans("crudbooster.button_save")}}' class='btn btn-success'>
                        @endif

                    @endif
                </div>
            </div>
        </div>

        </form>
    </div><!--END AUTO MARGIN-->

@endsection
