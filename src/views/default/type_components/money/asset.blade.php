@push('bottom')
<script>
    $(function () {
        $('.inputMoney').priceFormat({
            prefix: '',
            @if($form['options']['thousands_separator'])
            thousandsSeparator: '{!! $form['options']['thousands_separator']?: '' !!}',
            @endif
                    @if($form['options']['cents_limit'])
            centsLimit: {!! $form['cents_limit'] !!},
            @else
            centsLimit: 0,
            @endif
            clearOnEmpty: true,
        });
    });
</script>
@endpush
