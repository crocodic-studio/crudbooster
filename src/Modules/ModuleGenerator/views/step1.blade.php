@extends("crudbooster::admin_template")
@section("content")
    @push('head')
        {!! cbStyleSheet('select2/dist/css/select2.min.css') !!}
        <style>
            .select2-container--default .select2-selection--single {
                border-radius: 0px !important
            }

            .select2-container .select2-selection--single {
                height: 35px
            }
        </style>
    @endpush

    @push('bottom')
        {!! cbScript("select2/dist/js/select2.full.min.js") !!}
        @include('CbModulesGen::step1.script')
    @endpush

    @include('CbModulesGen::partials.nav_tabs', ['step' => ['active','','',''], 'id' => $id ])

    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Module Information</h3>
        </div>
        @include('CbModulesGen::step1.step1form')
    </div>


@endsection
