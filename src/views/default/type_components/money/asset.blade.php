<script>
	$('.inputMoney').priceFormat({
		prefix: '',
		thousandsSeparator: '{!! $form['dec_point']?: '' !!}',
		centsLimit: {!! $form['decimals']?: "''" !!},
		clearOnEmpty:true,
	});
</script>