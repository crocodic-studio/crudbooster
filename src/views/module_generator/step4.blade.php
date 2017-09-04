@extends("crudbooster::admin_template")
@section("content")

    @include('crudbooster::module_generator.partials.nav_tabs', ['step' => ['','','','active'], 'id' => $id ])

    <div class="box box-default">
        <div class="box-header">
            <h1 class="box-title">Configuration</h1>
        </div>
        <form method='post' action='{{Route('AdminModulesControllerPostStepFinish')}}'>
            {{csrf_field()}}
            <input type="hidden" name="id" value='{{$id}}'>
            <div class="box-body">

                <div class="row">
                    @include('crudbooster::module_generator.step4.text_field')
                </div>

                <div class="row">
                    <div class="col-sm-4">
                        @include('crudbooster::module_generator.step4.radio_col1')
                    </div>

                    <div class="col-sm-3">
                        @include('crudbooster::module_generator.step4.radio_col2')
                    </div>

                    <div class="col-sm-4">
                        @include('crudbooster::module_generator.step4.radio_col3')
                    </div>

                </div>

            </div>
            <div class="box-footer">
                @include('crudbooster::module_generator.step4.footer')
            </div>
        </form>
    </div>


@endsection