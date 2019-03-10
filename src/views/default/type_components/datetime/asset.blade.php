@push('bottom')
    @if (App::getLocale() != 'en')
        <script src="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/daterangepicker/locales.min.js') }}"
                charset="UTF-8"></script>
    @endif

    <script type="text/javascript">
        var lang = '{{App::getLocale()}}';
        moment.locale(lang);
        $(function() {
            $('.input_datetime').daterangepicker({
                singleDatePicker: true,
                timePicker: true,
                timePickerIncrement: 1,
                timePicker24Hour: true,
                autoApply: false,
                showDropdowns: true,
                locale: {
                    format: "YYYY-MM-DD HH:mm:ss",
                },

            });
        });
    </script>
@endpush