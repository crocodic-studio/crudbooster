@if (App::getLocale() != 'en')
    <script src="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/datepicker/locales/bootstrap-datepicker.'.App::getLocale().'.js') }}" charset="UTF-8"></script>
@endif
<script type="text/javascript">
    var lang = '{{App::getLocale()}}';
    $(function() {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            @if (App::getLocale() == 'ar')
            rtl: true,
            @endif
            language: lang
        });
    });

    function showDatepicker() {
        $('.datepicker').datepicker('show');
    }
</script>