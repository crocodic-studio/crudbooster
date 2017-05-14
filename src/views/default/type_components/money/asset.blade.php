@push('bottom')
<script>
	$(function() {
		$('.inputMoney').priceFormat({
			prefix: '',
			thousandsSeparator: '{!! $form['dec_point']?: '' !!}',
			centsLimit: {!! $form['decimals']?: 3 !!},
			clearOnEmpty:true,
		});
	});
</script>
@endpush
