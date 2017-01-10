<script type="text/javascript">
	$(function() {
		$('.datepicker{{$name}}').datepicker({
			format: 'yyyy-mm-dd',
		});
	});
	$('.form-datepicker i').click(function() {
		console.log('i datepicker');
		$(this).parent().parent().find('input').click();
	})
</script>
<style type="text/css">
	i.fa-calendar {cursor:pointer;}
</style>