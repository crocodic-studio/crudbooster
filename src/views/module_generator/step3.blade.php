@extends("crudbooster::admin_template")
@section("content")
    @push('head')
    <link rel='stylesheet' href='{!! asset("vendor/crudbooster/assets/select2/dist/css/select2.min.css") !!}'/>
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
    <script src='{!!  asset("vendor/crudbooster/assets/select2/dist/js/select2.full.min.js") !!}'></script>
    <script>
        $(function () {
            $('.select2').select2();
        })
    </script>
    @endpush


    @include('crudbooster::module_generator.partials.nav_tabs', ['step' => ['','','active',''], 'id' => $id ])


    @push('head')
    @include('crudbooster::module_generator.step3.styles')
    @endpush


    @push('bottom')
    @include('crudbooster::module_generator.step3.script')
    @endpush


    @include('crudbooster::module_generator.step3.optionsModal')

    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Form Display</h3>
        </div>
        <div class="box-body">
            <form method="post" autocomplete="off" action="{{Route('AdminModulesControllerPostStep4')}}">
                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <input type="hidden" name="id" value="{{$id}}">

                <table class='table-form table table-striped'>
                    <thead>
                    <tr>
                        <th width="180px">Action</th>
                        <th>Label</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Validation</th>
                        <th width="90px">Width</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($forms as $index => $form)
                        <tr>
                            @include('crudbooster::module_generator.step3.tableRow')
                        </tr>
                    @endforeach

                    <tr id='tr-sample' style="display: none">
                        @include('crudbooster::module_generator.step3.lastRow')
                    </tr>


                    </tbody>
                </table>

                @include('crudbooster::module_generator.step3.footer')
            </form>
        </div>
    </div>


@endsection