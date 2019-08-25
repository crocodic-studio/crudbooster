@extends(getThemePath('layout.layout'))
@section('content')

        @if(verifyReferalUrl())
            <p>
                <a href="{{ getReferalUrl("url") }}"><i class="fa fa-arrow-left"></i> Back To {{ getReferalUrl("name")?:cbLang("data") }} List</a>
            </p>
            @else
            <p>
                <a href="{{ module()->url() }}"><i class="fa fa-arrow-left"></i> &nbsp; {{cbLang('back_to_list')}}</a>
            </p>
        @endif


        <div class="box box-default">
            <div class="box-header with-border">
                <h1 class="box-title"><i class="fa fa-file"></i> {{ (cb()->getCurrentMethod()=="getAdd")?cbLang("add")." ".cbLang("data"):cbLang("edit")." ".cbLang("data") }}</h1>
            </div>

            <form method='post' id="form" enctype="multipart/form-data" action='{{ $action_url }}'>
                {!! csrf_field() !!}
                <input type="hidden" name="ref" value="{{ verifyReferalUrl()?request("ref"):null }}">

                <div class="box-body" id="parent-form-area">
                    @if(cb()->getCurrentMethod() == "getEdit")

                        @if(isset($prepend_edit_form) && is_callable($prepend_edit_form))
                            {!! call_user_func($prepend_edit_form, $row) !!}
                        @endif

                        @include('crudbooster::module.form.form_body')

                        @if(isset($append_edit_form) && is_callable($append_edit_form))
                            {!! call_user_func($append_edit_form, $row) !!}
                        @endif

                    @elseif(cb()->getCurrentMethod() == "getAdd")

                        @if(isset($prepend_add_form) && is_callable($prepend_add_form))
                            {!! call_user_func($prepend_add_form) !!}
                        @endif

                        @include('crudbooster::module.form.form_body')

                        @if(isset($append_add_form) && is_callable($append_add_form))
                            {!! call_user_func($append_add_form) !!}
                        @endif

                    @endif
                </div><!-- /.box-body -->

                <div class="box-footer">

                    <div style="text-align: right">
                        @if(module()->canCreate() && module()->getData("button_add_more") && cb()->getCurrentMethod()=="getAdd")
                            <input type="submit" name="submit" value='{{ cbLang("save")." & ".cbLang("add")." ".cbLang("more") }}' class='btn btn-default'>
                        @endif

                        @if(cb()->getCurrentMethod()=="getEdit")
                        <button type="button" class="btn btn-default" onclick="location.href='{{ verifyReferalUrl()?getReferalUrl("url"):module()->url() }}'">{{ cbLang("cancel") }}</button>
                        @endif

                        @if(cb()->getCurrentMethod()=="getAdd")
                            @if(module()->canCreate() && module()->getData("button_save"))
                                <input type="submit" name="submit" value='{{ cbLang("add")." ".cbLang("data") }}' class='btn btn-success'>
                            @endif
                        @endif

                        @if(cb()->getCurrentMethod()=="getEdit")
                            @if(module()->canUpdate() && module()->getData("button_save"))
                                <input type="submit" name="submit" value='{{ cbLang("update")." ".cbLang("data") }}' class='btn btn-success'>
                            @endif
                        @endif
                    </div>


                </div><!-- /.box-footer-->

            </form>
        </div>

@endsection