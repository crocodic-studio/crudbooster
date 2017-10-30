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

    <ul class="nav nav-tabs">
        @if($id)

            @include('CbModulesGen::partials.nav_tabs', ['step' => ['active','','',''], 'id' => $id ])

        @else
            <li role="presentation" class="active"><a href="#"> {!! CB::icon('info') !!} Step 1 - Module Information</a></li>
            <li role="presentation"><a href="#"> {!! CB::icon('table') !!} Step 2 - Table Display</a></li>
            <li role="presentation"><a href="#"> {!! CB::icon('plus-square-o') !!} Step 3 - Form Display</a></li>
            <li role="presentation"><a href="#"> {!! CB::icon('wrench') !!} Step 4 - Configuration</a></li>
        @endif
    </ul>

    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Module Information</h3>
        </div>
        @include('CbModulesGen::step1.step1form')
    </div>


@endsection
