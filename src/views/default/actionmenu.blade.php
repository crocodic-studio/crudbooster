<?php 
	$parameters = Request::all();
	unset($parameters['submodul']);
	$build_query = urldecode(http_build_query($parameters))	;
	$build_query = (Request::all())?"?".$build_query:"";
?>


@if($button_show_data)
<a href="{{ url($dashboard) }}" id='btn_show_data' class="btn btn-app">
	<i class="fa fa-bars"></i> Show Data
</a>
@endif

@if($button_reload_data)
<a href="{{ Request::url().$build_query }}" id='btn_reload_data' title='Reload Data' class="btn btn-app btn-reload-table ajax-button">
	<i class="fa fa-refresh"></i> Reload Data
</a>
@endif

@if($button_new_data && $priv->is_create)
<a href="{{ url($mainpath.'/add') }}" id='btn_add_new_data' class="btn btn-app">
	<i class="fa fa-plus"></i> Add New Data
</a>
@endif

@if($button_delete_data && $priv->is_delete)
	<a href="javascript:void(0)" id='btn_delete_selected' class="disabled btn btn-app btn-delete-selected"><i class="fa fa-trash"></i> Delete Selected</a>
@endif

<!--YOUR OWN HEADER BUTTON-->
@if(count($index_button))
	@foreach($index_button as $ib)
	<a href='{{$ib["url"]}}' id='{{str_slug($ib["label"])}}' class='btn btn-app'>
		<i class='{{$ib["icon"]}}'></i> {{$ib["label"]}}
	</a>
	@endforeach
@endif
<!--END OWN HEADER BUTTON-->

@if($columns)
<div class='pull-right'>

	@if($button_sort_filter)
	<a href="javascript:void(0)" id='btn_advanced_filter' data-url-parameter='{{$build_query}}' title='Advanced Filter Data' class="btn btn-app">		
		@if(Request::get('filter_column'))<span class="badge bg-yellow"><em>Filtered</em></span>@endif
		<i class="fa fa-filter"></i> Sort & Filter
	</a>
	@endif	

	@if($button_export_data)
	<a href="javascript:void(0)" id='btn_export_data' data-url-parameter='{{$build_query}}' title='Export Data' class="btn btn-app btn-export-data">
		<i class="fa fa-download"></i> Export Data
	</a>
	@endif

</div><!--END PULL RIGHT-->

<script>
$(function(){
	$("#table_dashboard .checkbox").click(function() {
		var is_any_checked = $("#table_dashboard .checkbox:checked").length;
		if(is_any_checked) {
			$(".btn-delete-selected").removeClass("disabled");
		}else{
			$(".btn-delete-selected").addClass("disabled");
		}
	})

	$("#table_dashboard #checkall").click(function() {
		var is_checked = $(this).is(":checked");
		$("#table_dashboard .checkbox").prop("checked",!is_checked).trigger("click");
	})

	$(".btn-delete-selected").click(function() {
		var is_any_checked = $("#table_dashboard .checkbox:checked").length;
		if(is_any_checked) {

			if(!confirm("Are you sure want to delete all selected data ?")) return false;

			var checks = [];
			$("#table_dashboard .checkbox:checked").each(function() {
				var id = $(this).val();
				checks.push(id);
			})

			show_alert_floating('Please wait whilte delete selected...');
			$.post("{{ mainpath('deleted-selected') }}",{id:checks},function(resp) {				
				show_alert_floating('Delete selected successfully !');
				hide_alert_floating();
				$(".btn-reload-table").click();
			})
		}else{
			alert("Please checking any checkbox first !");
		}
	})


	$('#btn_advanced_filter').click(function() {
		$('#advanced_filter_modal').modal('show');
	})

	$(".filter-combo").change(function() {
		console.log('Filter combo detected');

		var n = $(this).val();
		var p = $(this).parents('.row-filter-combo');
		var type_data = $(this).attr('data-type');

		var filter_value = p.find('.filter-value');

		// $(this).parents('.row').find('.filter-value').prop('disabled',true).removeAttr('placeholder');

		switch(n) {
			default:
				filter_value.removeAttr('placeholder').val('').prop('disabled',true);
				
				filter_value.val('').show().focus();
				p.find('.between-group').hide();

				p.find('.filter-value-between').val('').prop('disabled',true);
			break;
			case 'like':
			case 'not like':
				filter_value.val('').show().focus();	
				p.find('.between-group').hide();
				
				filter_value.attr('placeholder','e.g : Lorem Ipsum').prop('disabled',false);
			break;
			case 'asc':
				filter_value.val('').show().focus();
				p.find('.between-group').hide();

				filter_value.prop('disabled',true).attr('placeholder','Sort ascending');
			break;
			case 'desc':
				filter_value.val('').show().focus();
				p.find('.between-group').hide();

				filter_value.prop('disabled',true).attr('placeholder','Sort descending');
			break;
			case '=':
				filter_value.val('').show().focus();
				p.find('.between-group').hide();

				filter_value.prop('disabled',false).attr('placeholder','e.g : Lorem Ipsum');
			break;
			case '>=':				
				filter_value.val('').show().focus();
				p.find('.between-group').hide();

				filter_value.prop('disabled',false).attr('placeholder','e.g : 1000');
			break;
			case '<=':				
				filter_value.val('').show().focus();
				p.find('.between-group').hide();

				filter_value.prop('disabled',false).attr('placeholder','e.g : 1000');
			break;
			case '>':				
				filter_value.val('').show().focus();
				p.find('.between-group').hide();

				filter_value.prop('disabled',false).attr('placeholder','e.g : 1000');
			break;
			case '<':				
				filter_value.val('').show().focus();
				p.find('.between-group').hide();

				filter_value.prop('disabled',false).attr('placeholder','e.g : 1000');	
			break; 
			case '!=':
				filter_value.val('').show().focus();
				p.find('.between-group').hide();

				filter_value.prop('disabled',false).attr('placeholder','e.g : Lorem Ipsum');
			break;
			case 'in':
				filter_value.val('').show().focus();
				p.find('.between-group').hide();

				filter_value.prop('disabled',false).attr('placeholder','e.g : Lorem,Ipsum,Dolor Sit');
			break;
			case 'not in':
				filter_value.val('').show().focus();
				p.find('.between-group').hide();

				filter_value.prop('disabled',false).attr('placeholder','e.g : Lorem,Ipsum,Dolor Sit');
			break;
			case 'between':				
				filter_value.val('').hide();
				p.find('.between-group').show().focus();
				p.find('.filter-value-between').prop('disabled',false);
				
			break;
		}
	})

	/* Remove disabled when reload page and input value is filled */
	$(".filter-value").each(function() {
		var v = $(this).val();
		if(v != '') $(this).prop('disabled',false);
	})
	$(".filter-value-between").each(function() {
		var v = $(this).val();
		if(v != '') {
			// $(this).parents('.row-filter-combo').find('.filter-value').hide();
			$(this).prop('disabled',false);
		}
	})
})
</script>
<!-- MODAL FOR SORTING DATA-->
<div class="modal fade" tabindex="-1" role="dialog" id='advanced_filter_modal'>
	<div class="modal-dialog">
		<div class="modal-content" >
			<div class="modal-header">
				<button class="close" aria-label="Close" type="button" data-dismiss="modal">
				<span aria-hidden="true">×</span></button>
				<h4 class="modal-title"><i class='fa fa-filter'></i> Advanced Sort & Filter Data</h4>
			</div>
			<form method='get' action=''>
				<div class="modal-body">
					
					@foreach($columns as $key => $col)					
					<div class='form-group'>
						<label>{{ $col['label'] }}</label>
						<div class='row-filter-combo row'>

							<div class='col-sm-6'>
								<select name='filter_column[{{$col["field_with"]}}][type]' data-type='{{$col["type_data"]}}' class="filter-combo form-control">
									<option value=''>** Select Operator Type</option>							
									@if(in_array($col['type_data'],['string','varcar','text']))<option {{ (get_type_filter($col["field_with"]) == 'like')?"selected":"" }} value='like'>LIKE</option> @endif
									@if(in_array($col['type_data'],['string','varcar','text']))<option {{ (get_type_filter($col["field_with"]) == 'not like')?"selected":"" }} value='not like'>NOT LIKE</option>@endif
									<option typeallow='all' {{ (get_type_filter($col["field_with"]) == 'asc')?"selected":"" }} value='asc'>ASCENDING</option>
									<option typeallow='all' {{ (get_type_filter($col["field_with"]) == 'desc')?"selected":"" }} value='desc'>DESCENDING</option>
									<option typeallow='all' {{ (get_type_filter($col["field_with"]) == '=')?"selected":"" }} value='='>= (Equal To)</option>
									@if(in_array($col['type_data'],['int','integer','double','float','decimal']))<option {{ (get_type_filter($col["field_with"]) == '>=')?"selected":"" }} value='>='>>= (Greater Than or Equal)</option>@endif
									@if(in_array($col['type_data'],['int','integer','double','float','decimal']))<option {{ (get_type_filter($col["field_with"]) == '<=')?"selected":"" }} value='<='><= (Less Than or Equal)</option>@endif
									@if(in_array($col['type_data'],['int','integer','double','float','decimal']))<option {{ (get_type_filter($col["field_with"]) == '<')?"selected":"" }} value='<'>< (Less Than)</option>@endif
									@if(in_array($col['type_data'],['int','integer','double','float','decimal']))<option {{ (get_type_filter($col["field_with"]) == '>')?"selected":"" }} value='>'>> (Greater Than)</option>@endif
									<option typeallow='all' {{ (get_type_filter($col["field_with"]) == '!=')?"selected":"" }} value='!='>!= (Not Equal To)</option>
									<option typeallow='all' {{ (get_type_filter($col["field_with"]) == 'in')?"selected":"" }} value='in'>IN</option>
									<option typeallow='all' {{ (get_type_filter($col["field_with"]) == 'not in')?"selected":"" }} value='not in'>NOT IN</option>
									@if(in_array($col['type_data'],['date','time','datetime','int','integer','double','float','decimal']))<option {{ (get_type_filter($col["field_with"]) == 'between')?"selected":"" }} value='between'>BETWEEN</option>@endif													
								</select>
							</div>
							<div class='col-sm-6'>
								<input type='text' class='filter-value form-control' {{ (get_type_filter($col["field_with"]) == 'between')?"style=\"display:none\"":""}} disabled name='filter_column[{{$col["field_with"]}}][value]' value='{{ (!is_array(get_value_filter($col["field_with"])))?get_value_filter($col["field_with"]):"" }}'>

								<div class='row between-group' style="{{ (get_type_filter($col["field_with"]) == 'between')?"display:block":"display:none" }}">
									<div class='col-sm-6'>
										<input type='text' class='filter-value-between form-control {{ (in_array($col["type_data"],["date","time","datetime"]))?"datepicker":"" }}' disabled placeholder='{{$col["label"]}} from' name='filter_column[{{$col["field_with"]}}][value][]' value='<?php 
										$value = get_value_filter($col["field_with"]); 
										echo (get_type_filter($col["field_with"])=='between')?$value[0]:"";
										?>'>
									</div>
									<div class='col-sm-6'>
										<input type='text' class='filter-value-between form-control {{ (in_array($col["type_data"],["date","time","datetime"]))?"datepicker":"" }}' disabled placeholder='{{$col["label"]}} to' name='filter_column[{{$col["field_with"]}}][value][]' value='<?php 
										$value = get_value_filter($col["field_with"]); 
										echo (get_type_filter($col["field_with"])=='between')?$value[1]:"";
										?>'>
									</div>
								</div>

							</div>
						</div>

					</div>
					@endforeach					
					
				</div>
				<div class="modal-footer">
					<button class="btn btn-default pull-left" type="button" data-dismiss="modal">Close</button>
					<button class="btn btn-default pull-left btn-reset" type="reset" onclick='location.href="{{$dashboard}}"' >Reset</button>
					<button class="btn btn-primary btn-submit" type="submit">Submit</button>
				</div>
			</form>
		</div>
		<!-- /.modal-content -->
	</div>
</div>


<script>
$(function(){
	$('.btn-filter-data').click(function() {
		$('#filter-data').modal('show');
	})
})
</script>


<script>
$(function(){
	$('.btn-export-data').click(function() {
		$('#export-data').modal('show');
	})

	var toggle_advanced_report_boolean = 1;
	$(".toggle_advanced_report").click(function() {
		
		if(toggle_advanced_report_boolean==1) {
			$("#advanced_export").slideDown();
			$(this).html("<i class='fa fa-minus-square-o'></i> Show Advanced Export");
			toggle_advanced_report_boolean = 0;
		}else{
			$("#advanced_export").slideUp();
			$(this).html("<i class='fa fa-plus-square-o'></i> Show Advanced Export");
			toggle_advanced_report_boolean = 1;
		}		
		
	})
})
</script>

<!-- MODAL FOR EXPORT DATA-->
<div class="modal fade" tabindex="-1" role="dialog" id='export-data'>
	<div class="modal-dialog">
		<div class="modal-content" >
			<div class="modal-header">
				<button class="close" aria-label="Close" type="button" data-dismiss="modal">
				<span aria-hidden="true">×</span></button>
				<h4 class="modal-title"><i class='fa fa-download'></i> Export Data</h4>
			</div>
			<form method='post' target="_blank" action='{{url($mainpath."/export-data?t=".time())}}'> 
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			{!! get_input_url_parameters() !!}
				<div class="modal-body">										
					<div class="form-group">
						<label>File Name</label>
						<input type='text' name='filename' class='form-control' required value='Report {{$modulname}} - {{date("d M Y")}}'/>
						<div class='help-block'>You can rename the filename according to your whises</div>
					</div>

					<p><a href='javascript:void(0)' class='toggle_advanced_report' title='Click here for more advanced configuration export data'><i class='fa fa-plus-square-o'></i> Show Advanced Export</a></p>

					<div id='advanced_export' style='display: none'>
					<div class="form-group">
						<label>Max Data</label>
						<input type='number' name='limit' class='form-control' required value='100' max="100000" min="1" />	
						<div class='help-block'>Minimum 1 and maximum 100,000 rows per export session</div>					
					</div>	

					<div class='form-group'>
						<label>Columns</label><br/>
						@foreach($columns as $col)
							<div class='checkbox inline'><label><input type='checkbox' checked name='columns[]' value='{{$col["name"]}}'>{{$col["label"]}}</label></div>
						@endforeach
					</div>

					<div class="form-group">
						<label>Format Export</label>
						<select name='fileformat' class='form-control'>
							<option value='pdf'>PDF</option>
							<option value='xls'>Microsoft Excel (xls)</option>							
							<option value='csv'>CSV</option>
						</select>
					</div>							

					<div class="form-group">
						<label>Page Size</label>
						<select class='form-control' name='page_size'>
							<option <?=($setting->default_paper_size=='Letter')?"selected":""?> value='Letter'>Letter</option>
							<option <?=($setting->default_paper_size=='Legal')?"selected":""?> value='Legal'>Legal</option>
							<option <?=($setting->default_paper_size=='Ledger')?"selected":""?> value='Ledger'>Ledger</option>
							<?php for($i=0;$i<=8;$i++):
								$select = ($setting->default_paper_size == 'A'.$i)?"selected":"";
							?>
							<option <?=$select?> value='A{{$i}}'>A{{$i}}</option>
							<?php endfor;?>

							<?php for($i=0;$i<=10;$i++):
								$select = ($setting->default_paper_size == 'B'.$i)?"selected":"";
							?>
							<option <?=$select?> value='B{{$i}}'>B{{$i}}</option>
							<?php endfor;?>
						</select>		
						<div class='help-block'><input type='checkbox' name='default_paper_size' value='1'/> Set As Default Paper Size</div>				
					</div>

					<div class="form-group">
						<label>Page Orientation</label>
						<select class='form-control' name='page_orientation'>
							<option value='potrait'>Potrait</option>
							<option value='landscape'>Landscape</option>
						</select>						
					</div>
					</div>

				</div>
				<div class="modal-footer">
					<button class="btn btn-default pull-left" type="button" data-dismiss="modal">Close</button>					
					<button class="btn btn-primary btn-submit" type="submit">Export</button>
				</div>
			</form>
		</div>
		<!-- /.modal-content -->
	</div>
</div>

@endif
