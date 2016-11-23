<style type="text/css">
					.form-divider {
						padding:10px 0px 10px 0px;
						margin-bottom: 10px;
						border-bottom:1px solid #dddddd;
					}
					.header-title {
						cursor: pointer;
					}
</style>
<script type="text/javascript">
	$(function() {
		if (typeof event_header_click === 'undefined') {	
			event_header_click = true;						    						
			$(document).on("click",".header-title",function() {
				console.log("header title click");
				var index = $(this).attr('id').replace("header","");
				var handel = $(this);
				var parent = $(this).parent('.box-body');
				var first_group = parent.find(".header-group-"+index+":first").is(":hidden");
				if(first_group) {
					parent.find(".header-group-"+index).slideDown(function() {
						handel.find(".icon i").attr('class','fa fa-minus-square-o');
						handel.attr("title","Click here to slide up");
					});										
				}else{
					parent.find(".header-group-"+index).slideUp(function() {
						handel.find(".icon i").attr('class','fa fa-plus-square-o');
						handel.attr("title","Click here to expand");
					});										
				}								
			})
			$(".header-title").each(function() {
				var data_collapsed = $(this).attr('data-collapsed');
				console.log("header title "+data_collapsed);
				if(data_collapsed == 'false') {
					console.log("collapsed false");
					$(this).click();
				}
			})
		}

		
	})						
</script>