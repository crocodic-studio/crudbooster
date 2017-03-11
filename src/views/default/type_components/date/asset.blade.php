<script type="text/javascript">
	$(function() {
		$('.datepicker{{$name}}').datepicker({
			format: 'yyyy-mm-dd',
		});
	});
	$('.form-datepicker i').click(function() {
		console.log('i datepicker');
		
	})
	function showDatepicker{{$name}}() {
		$('.datepicker{{$name}}').datepicker('show');
	}
</script>