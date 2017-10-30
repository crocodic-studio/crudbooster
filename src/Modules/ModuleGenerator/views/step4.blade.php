@extends("crudbooster::admin_template")
@section("content")

    @include('CbModulesGen::partials.nav_tabs', ['step' => ['','','','active'], 'id' => $id ])

    <div class="box box-default">
        <div class="box-header">
            <h1 class="box-title">Configuration</h1>
        </div>
        <form method='post' action='{{Route('AdminModulesControllerPostStepFinish')}}'>
            {{csrf_field()}}
            <input type="hidden" name="id" value='{{$id}}'>
            <div class="box-body">

                <div class="row">
                    @include('CbModulesGen::step4.text_field')
                </div>

                <div class="row">
                    <div class="col-sm-4">
                        @include('CbModulesGen::step4.radio_col1')
                    </div>

                    <div class="col-sm-3">
                        @include('CbModulesGen::step4.radio_col2')
                    </div>

                    <div class="col-sm-4">
                        @include('CbModulesGen::step4.radio_col3')
                    </div>

                </div>

            </div>
            @include('CbModulesGen::step4.footer')
        </form>
    </div>


@endsection