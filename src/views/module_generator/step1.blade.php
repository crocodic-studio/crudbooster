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
    <script>
        $(function () {
            $('.select2').select2();

        })
        $(function () {
            $('select[name=table]').change(function () {
                var v = $(this).val().replace(".", "_");
                $.get("{{CRUDBooster::mainpath('check-slug')}}/" + v, function (resp) {
                    if (resp.total == 0) {
                        $('input[name=path]').val(v);
                    } else {
                        v = v + resp.lastid;
                        $('input[name=path]').val(v);
                    }
                })

            })
        })
    </script>
    @endpush

    <ul class="nav nav-tabs">
        @if($id)

            @include('crudbooster::module_generator.partials.nav_tabs', ['step' => ['active','','',''], 'id' => $id ])

        @else
            <li role="presentation" class="active"><a href="#"><i class='fa fa-info'></i> Step 1 - Module
                    Information</a></li>
            <li role="presentation"><a href="#"><i class='fa fa-table'></i> Step 2 - Table Display</a></li>
            <li role="presentation"><a href="#"><i class='fa fa-plus-square-o'></i> Step 3 - Form Display</a></li>
            <li role="presentation"><a href="#"><i class='fa fa-wrench'></i> Step 4 - Configuration</a></li>
        @endif
    </ul>

    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Module Information</h3>
        </div>
        @include('crudbooster::module_generator.step1.step1form')
    </div>


@endsection
