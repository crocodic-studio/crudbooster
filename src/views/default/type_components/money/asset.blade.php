@push('bottom')
    <script>
        $(function () {
            $('.inputMoney').priceFormat({
                prefix: '',
                @if($formInput['options']['thousands_separator'])
                thousandsSeparator: '{!! $formInput['options']['thousands_separator']?: '' !!}',
                @endif
                        @if($formInput['options']['cents_limit'])
                centsLimit: {!! $formInput['cents_limit'] !!},
                @else
                centsLimit: 0,
                @endif
                clearOnEmpty: true,
            });
        });
    </script>
@endpush
