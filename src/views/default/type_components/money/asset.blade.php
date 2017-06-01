@push('bottom')
<script>
	$(function() {
		$('.inputMoney').priceFormat({
			prefix: '',
			@if($form['dec_point'])
			thousandsSeparator: '{!! $form['dec_point']?: '' !!}',
			@endif
			@if($form['decimals'])
			centsLimit: {!! $form['decimals'] !!},
			@else
			centsLimit: 0,		
			@endif
			clearOnEmpty:true,
		});
	});
</script>
@endpush
