@if (App::getLocale() != 'en')
    <script src="{{ asset ('vendor/crudbooster/assets/adminlte/plugins/datepicker/locales/bootstrap-datepicker.'.App::getLocale().'.js') }}" charset="UTF-8"></script>
@endif
<script type="text/javascript">
    var lang = '{{App::getLocale()}}';
    $(function() {
        $('.datepicker{{$name}}').datepicker({
            format: 'yyyy-mm-dd',
            @if (App::getLocale() == 'ar')
            rtl: true,
            @endif
            language: lang
        });
    });
    /*$('.form-datepicker i').click(function() {
     console.log('i datepicker');

     })*/
    function showDatepicker{{$name}}() {
        $('.datepicker{{$name}}').datepicker('show');
    }
</script>