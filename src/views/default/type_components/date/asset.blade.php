<script type="text/javascript">
	$('.form-datepicker i').click(function() {
		console.log('i datepicker');
		$(this).parent().parent().find('input').click();
	})
</script>
<style type="text/css">
	i.fa-calendar {cursor:pointer;}
</style>