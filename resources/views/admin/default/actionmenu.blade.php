<?php 
$parameters = Request::all();
$build_query = urldecode(http_build_query($parameters));
$build_query = (Request::all())?"?".$build_query:"";
$build_query_add = str_replace(array("&detail=1","detail=1"),"",$build_query);
?>
<a href="{{ url($dashboard).$build_query }}" id='btn_show_data' class="btn btn-app">
	<i class="fa fa-bars"></i> Show Data
</a>
<a href="{{ Request::url().$build_query }}" id='btn_reload_data' title='Reload Data' class="btn btn-app btn-reload-table ajax-button">
	<i class="fa fa-refresh"></i> Reload Data
</a>
@if($priv->is_create)
<a href="{{ url($mainpath.'/add').$build_query_add }}" id='btn_add_new_data' class="btn btn-app">
	<i class="fa fa-plus"></i> Add New Data
</a>
@endif

@if($priv->is_delete)
	<a href="javascript:void(0)" id='btn_delete_selected' class="disabled btn btn-app btn-delete-selected"><i class="fa fa-trash"></i> Delete Selected</a>
@endif

@if($columns)
<div class='pull-right'>
<a href="javascript:void(0)" id='btn_sort_data' data-url-parameter='{{$build_query}}' title='Sort Data' class="btn btn-app btn-sort-data">
	@if(Request::get('sort_column'))<span class="badge bg-yellow"><em>Sorted</em></span>@endif
	<i class="fa fa-sort"></i> Sort Data
</a>

<a href="javascript:void(0)" id='btn_filter_data' data-url-parameter='{{$build_query}}' title='Filter By Existing Data' class="btn btn-app btn-filter-data">
	@if(Request::get('filter_data_column'))<span class="badge bg-yellow"><em>Filtered</em></span>@endif
	<i class="fa fa-filter"></i> Filter Data
</a>

<a href="javascript:void(0)" id='btn_export_data' data-url-parameter='{{$build_query}}' title='Export Data' class="btn btn-app btn-export-data">
	<i class="fa fa-download"></i> Export Data
</a>
</div>

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
			$.post("{{action($current_controller.'@postDeleteSelected')}}",{id:checks},function(resp) {				
				show_alert_floating('Delete selected successfully !');
				hide_alert_floating();
				$(".btn-reload-table").click();
			})
		}else{
			alert("Please checking any checkbox first !");
		}
	})


	$('.btn-sort-data').click(function() {
		$('#sort-data').modal('show');
	})

	$("#sort-data .btn-reset").click(function() {
		$(this).parents("form").find("input[type=text],select").val('');
		$(this).parents('form').submit();
	})
})
</script>


<!-- MODAL FOR SORTING DATA-->
<div class="modal fade" tabindex="-1" role="dialog" id='sort-data'>
	<div class="modal-dialog">
		<div class="modal-content" >
			<div class="modal-header">
				<button class="close" aria-label="Close" type="button" data-dismiss="modal">
				<span aria-hidden="true">×</span></button>
				<h4 class="modal-title"><i class='fa fa-sort'></i> Sort Data</h4>
			</div>
			<form method='get' action=''>
				<div class="modal-body">
					
					@if(@$_GET)
					@foreach(@$_GET as $a=>$b)
					<?php
					if(is_array($b)) {
					$a = $a.'['.key($b).']';
					$b = $b[key($b)];
					}
					if($a=='sort_column' || $a=='sort_type') continue;
					echo "<input type='hidden' name='$a' value='$b'/>";
					?>
					@endforeach
					@endif
					<div class='form-group'>
						<label>Select By Column</label>
						<select name='sort_column' required class="form-control">
							<option value=''>** Select A Column</option>
							<?php
								$join_i = 0;
								foreach($columns as $key=>$col) {									
									if(isset($col['join'])) {
										$join_table = substr($col['join'], 0, strpos($col['join'], ','));
										$field = $join_table.$key.'.'.$col['field_raw'];
										$join_i++;
									}else{
										$field = $col['field'];
									}																									
									$select = (Request::get('sort_column')==$field)?'selected':'';
									echo "<option $select value='$field'>$col[label]</option>";
							}
							?>
						</select>
					</div>
					<div class="form-group">
						<label>Sort Data Type</label>
						<select name='sort_type' class='form-control'>
							<option <?=(Request::get('sort_type')=='desc')?'selected':''?> value='desc'>DESCENDING</option>
							<option <?=(Request::get('sort_type')=='asc')?'selected':''?> value='asc'>ASCENDING</option>
						</select>
					</div>
					
				</div>
				<div class="modal-footer">
					<button class="btn btn-default pull-left" type="button" data-dismiss="modal">Close</button>
					<button class="btn btn-default pull-left btn-reset" type="reset" >Reset</button>
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

	$("#filter-data .btn-reset").click(function() {
		$(this).parents("form").find("input[type=text],select").val('');
		$(this).parents('form').submit();
	})

	$("#filter-data select[name=filter_data_column]").change(function() {
		console.log('filter_data_column change detected');
		var col = $(this).val();
		var opt = ''; 
		if(col=='') return false;

		$('#filter-data select[name=filter_data_by]').html('<option value="">Please wait loading data '+col+'...</option>');

		$.get("{{url($mainpath.'/find-group-data')}}?column="+col,function(resp) {
			var select = '';
			$.each(resp,function(i,obj) {
				select = (obj == '{{Request::get("filter_data_by")}}')?'selected':'';
				opt += '<option '+select+' value="'+obj+'">'+obj+'</option>';
			})
			$('#filter-data select[name=filter_data_by]').html('<option value="">** Select A Data</option><option value="FILTER_TEXT_MANUAL">* FILTER BY MANUAL TEXT *</option>'+opt);
		})	

		var is_date = parseInt($(this).find('option:selected').attr('data-is-date'));
		if(is_date) {
			$(".form-group-filter-data-text,.form-group-filter-exists-data").hide();
			$(".form-group-filter-date-range").slideDown();
		}else{
			$(".form-group-filter-date-range").slideUp();
			$(".form-group-filter-exists-data").show();
		}	

	})
	$('#filter-data select[name=filter_data_by]').change(function() {
			var v = $(this).val();
			

			if(v == 'FILTER_TEXT_MANUAL') {
				$(".form-group-filter-data-text").slideDown();				
			}else{
				$(".form-group-filter-data-text").slideUp();				
			}

			
	})
})
</script>

<!-- MODAL FOR FILTER DATA-->
<div class="modal fade" tabindex="-1" role="dialog" id='filter-data'>
	<div class="modal-dialog">
		<div class="modal-content" >
			<div class="modal-header">
				<button class="close" aria-label="Close" type="button" data-dismiss="modal">
				<span aria-hidden="true">×</span></button>
				<h4 class="modal-title"><i class='fa fa-filter'></i> Filter Data</h4>
			</div>
			<form method='get' action=''>
				<div class="modal-body">
					
					@if(@$_GET)
					@foreach(@$_GET as $a=>$b)
					<?php
					if(is_array($b)) {
					$a = $a.'['.key($b).']';
					$b = $b[key($b)];
					}
					if($a=='filter_data_column' || $a=='filter_data_by') continue;
					echo "<input type='hidden' name='$a' value='$b'/>";
					?>
					@endforeach
					@endif

					<div class='form-group'>
						<label>Select By Column</label>
						<select name='filter_data_column' required class="form-control">
							<option value=''>** Select A Column</option>
							<?php			
								$join_i = 0;					
								foreach($columns as $key => $col) {		
									//Skip if subquery
									if($col['is_subquery']) continue;
																				
									if(isset($col['join'])) {
										$join_table = substr($col['join'], 0, strpos($col['join'], ','));
										$field = $join_table.$key.'.'.$col['field_raw'];
										$join_i++;
									}else{
										$field = $col['field'];
									}
									$is_date = (in_array($col['field'], $date_candidate))?1:0;

									foreach($date_candidate as $date) {
										if(substr($col['field'], 0, strpos($col['field'], '_')) == $date) {
											$is_date = 1;
											break;
										}

										if(substr($col['field'], strpos($col['field'], '_')+1) == $date) {
											$is_date = 1;
											break;
										}
									}

									$select = ($field == Request::get('filter_data_column'))?'selected':'';				
									echo "<option $select data-is-date='$is_date' value='$field'>$col[label]</option>";
								}
							?>
						</select>
						<script>
						$(function() {
							if($("select[name=filter_data_column] option:selected").length>0) {
								$("select[name=filter_data_column]").trigger('change');	
							}							
						})
						</script>
					</div>
					<div class="form-group form-group-filter-exists-data">
						<label>Filter By</label>
						<select name='filter_data_by' requireds class='form-control'>
							<option value=''>** Please select column</option>
						</select>
					</div>

					<div class="form-group form-group-filter-data-text" style='display: none'>
						<label>Filter By Manual Text</label>
						<input type='text' class='form-control' value='{{Request::get("filter_data_text")}}' name='filter_data_text'/>
						<div class='help-block'>This filter text is exact filter or equal filter</div>
					</div>

					<div class="form-group form-group-filter-date-range" style='display: none'>
						<label>Filter Date Range</label>
						<div class='row'>
							<div class='col-sm-6'>
								<input type='text' class='form-control datepicker' placeholder="YYYY-mm-dd" name='filter_date_range1' value='{{Request::get("filter_date_range1")}}'/>
							</div>
							<div class='col-sm-6'>
								<input type='text' class='form-control datepicker' placeholder="YYYY-mm-dd" name='filter_date_range2' value='{{Request::get("filter_date_range2")}}'/>
							</div>
						</div>
					</div>
					
				</div>
				<div class="modal-footer">
					<button class="btn btn-default pull-left" type="button" data-dismiss="modal">Close</button>
					<button class="btn btn-default pull-left btn-reset" type="reset" >Reset</button>
					<button class="btn btn-primary btn-submit" type="submit">Submit</button>
				</div>
			</form>
		</div>
		<!-- /.modal-content -->
	</div>
</div>

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
				<div class="modal-body">
					
					@if(@$_GET)
					@foreach(@$_GET as $a=>$b)
					<?php
					if(is_array($b)) {
					$a = $a.'['.key($b).']';
					$b = $b[key($b)];
					}					
					echo "<input type='hidden' name='$a' value='$b'/>";
					?>
					@endforeach
					@endif

					<div class="form-group">
						<label>File Name</label>
						<input type='text' name='filename' class='form-control' required value='Report {{$modulname}} - {{date("d M Y")}}'/>
						<div class='help-block'>You can rename the filename according to your whises</div>
					</div>

					<p><a href='javascript:void(0)' class='toggle_advanced_report' title='Click here for more advanced configuration export data'><i class='fa fa-plus-square-o'></i> Show Advanced Export</a></p>

					<div id='advanced_export' style='display: none'>
					<div class="form-group">
						<label>Max Data</label>
						<input type='number' name='limit' class='form-control' required value='20' max="10000" min="1" />						
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
