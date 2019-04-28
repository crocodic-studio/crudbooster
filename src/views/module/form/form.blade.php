@extends('crudbooster::layouts.layout')
@section('content')

        <div class="panel panel-default">
            <div class="panel-heading">
                <strong><i class='{{ module()->getIcon() }}'></i> {!! module()->getPageTitle() !!}</strong>
            </div>

            <div class="panel-body" style="padding:20px 0px 0px 0px">
                <form class='form-horizontal' method='post' id="form" enctype="multipart/form-data" action='{{ $action_url }}'>
                    {!! csrf_field() !!}
                    <div class="box-body" id="parent-form-area">
                        @if(cb()->getCurrentMethod() == "getEdit")
                            @include('crudbooster::module.form.form_body')
                        @elseif(cb()->getCurrentMethod() == "getAdd")
                            @include('crudbooster::module.form.form_body')
                        @elseif(cb()->getCurrentMethod() == "getDetail")
                            @include('crudbooster::module.form.form_detail')
                        @endif
                    </div><!-- /.box-body -->

                    <div class="box-footer" style="background: #F5F5F5">

                        <div class="form-group">
                            <label class="control-label col-sm-2"></label>
                            <div class="col-sm-10">

                                @if(module()->canCreate() && $button_add_more)
                                    <input type="submit" name="submit" value='{{trans("crudbooster.button_save_more")}}' class='btn btn-success'>
                                @endif

                                @if(module()->canCreate() && $button_save)
                                        <input type="submit" name="submit" value='{{trans("crudbooster.button_save")}}' class='btn btn-success'>
                                @endif
                            </div>
                        </div>


                    </div><!-- /.box-footer-->

                </form>

            </div>
        </div>

@endsection