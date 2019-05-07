@extends('crudbooster::layouts.layout')
@section('content')

        <p>
            <a href="{{ module()->url() }}"><i class="fa fa-arrow-left"></i> &nbsp; Back to list</a>
        </p>

        <div class="box box-default">
            <div class="box-header">
                <h1 class="box-title">Form Data</h1>
            </div>

            <form method='post' id="form" enctype="multipart/form-data" action='{{ $action_url }}'>
                {!! csrf_field() !!}
                <div class="box-body" id="parent-form-area">
                    @if(cb()->getCurrentMethod() == "getEdit")
                        @include('crudbooster::module.form.form_body')
                    @elseif(cb()->getCurrentMethod() == "getAdd")
                        @include('crudbooster::module.form.form_body')
                    @endif
                </div><!-- /.box-body -->

                <div class="box-footer">

                    <div style="text-align: right">
                        @if(module()->canCreate() && module()->getData("button_add_more") && cb()->getCurrentMethod()=="getAdd")
                            <input type="submit" name="submit" value='{{trans("crudbooster.button_save_more")}}' class='btn btn-default'>
                        @endif

                        @if(cb()->getCurrentMethod()=="getEdit")
                        <button type="button" class="btn btn-default" onclick="location.href='{{ module()->url() }}'">Cancel</button>
                        @endif

                        @if(cb()->getCurrentMethod()=="getAdd")
                            @if(module()->canCreate() && module()->getData("button_save"))
                                <input type="submit" name="submit" value='{{trans("crudbooster.button_save")}}' class='btn btn-success'>
                            @endif
                        @endif

                        @if(cb()->getCurrentMethod()=="getEdit")
                            @if(module()->canUpdate() && module()->getData("button_save"))
                                <input type="submit" name="submit" value='{{trans("crudbooster.button_save")}}' class='btn btn-success'>
                            @endif
                        @endif
                    </div>


                </div><!-- /.box-footer-->

            </form>
        </div>

@endsection