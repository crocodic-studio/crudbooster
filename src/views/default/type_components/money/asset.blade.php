@push('bottom')
    <script>
        $(function () {
            @foreach($forms as $form)
            @if($form['type'] == $type)
            $('.inputMoney#{{ $form['name'] }}').priceFormat({!! json_encode(array_merge(array(
				            'prefix' 				=> '',
				            'thousandsSeparator'    => $form['dec_point'] ? : ',',
				            'centsLimit'          	=> $form['decimals'] ? : '0',
				            'clearOnEmpty'         	=> false,
				        ), (array)$form['priceformat_parameters'] 
					)) !!});
            @endif
            @endforeach
        });
    </script>
@endpush
