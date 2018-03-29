@push('bottom')
    {!! cbScript('select2/dist/js/select2.full.min.js') !!}
@endpush
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