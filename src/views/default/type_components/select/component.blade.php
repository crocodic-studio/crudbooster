	@if($form['parent_select'])
	<script type="text/javascript">
		$(function() {			
			$('#{{$form['parent_select']}}').change(function() {
				var $current = $("#{{$form['name']}}");
				var parent_id = $(this).val();
				var fk_name = "{{$form['parent_select']}}";
				var fk_value = $(this).val();
				var datatable = "{{$form['datatable']}}".split(',');
				var table = datatable[0].trim('');
				var label = datatable[1].trim('');
				var value = "{{$value}}";				

				if(fk_value!='') {					
					$current.html("<option value=''>{{trans('crudbooster.text_loading')}} {{$form['label']}}");
					$.get("{{CRUDBooster::mainpath('data-table')}}?table="+table+"&label="+label+"&fk_name="+fk_name+"&fk_value="+fk_value,function(response) {
						if(response) {
							$current.html("<option value=''>{{trans('crudbooster.text_prefix_option')}} {{$form['label']}}");
							$.each(response,function(i,obj) {
								var selected = (value && value == obj.select_value)?"selected":"";
								$("<option "+selected+" value='"+obj.select_value+"'>"+obj.select_label+"</option>").appendTo("#{{$form['name']}}");
							})							
							$current.trigger('change');
						}						
					});
				}else{
					$current.html("<option value=''>{{trans('crudbooster.text_prefix_option')}} {{$form['label']}}");
				}								
			})

			@if($value)
				$('#{{$form['parent_select']}}').trigger('change');
				$("#{{$form['name']}}").trigger('change');
			@endif
		})
	</script>
	@endif
	<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
		<label class='control-label col-sm-2'>{{$form['label']}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>			

		<div class="{{$col_width?:'col-sm-10'}}">									
		<select class='form-control' id="{{$name}}" data-value='{{$value}}' {{$required}} {!!$placeholder!!} {{$readonly}} {{$disabled}} name="{{$name}}">
			<option value=''>{{trans('crudbooster.text_prefix_option')}} {{$form['label']}}</option>
			<?php 	
				if(!$form['parent_select']) {
					if(@$form['dataquery']):

						$query = DB::select(DB::raw($form['dataquery']));
						if($query) {
							foreach($query as $q) {
								$selected = ($value == $q->value)?"selected":"";
								echo "<option $selected value='$q->value'>$q->label</option>";
							}
						}

					endif;

					if(@$form['dataenum']):
						$dataenum = $form['dataenum'];
						$dataenum = (is_array($dataenum))?$dataenum:explode(";",$dataenum);

						foreach($dataenum as $d) {

							$val = $lab = '';
							if(strpos($d,'|')!==FALSE) {
								$draw = explode("|",$d);
								$val = $draw[0];
								$lab = $draw[1];
							}else{
								$val = $lab = $d;
							}

							$select = ($value == $val)?"selected":"";				

							echo "<option $select value='$val'>$lab</option>";
						}
					endif;

					if(@$form['datatable']):
						$raw = explode(",",$form['datatable']);
						$format = $form['datatable_format'];
						$table1 = $raw[0];
						$column1 = $raw[1];
						
						@$table2 = $raw[2];
						@$column2 = $raw[3];

						@$table3 = $raw[4];
						@$column3 = $raw[5];
						
						$selects_data = DB::table($table1)->select($table1.".id");	

						if(\Schema::hasColumn($table1,'deleted_at')) {
							$selects_data->where($table1.'.deleted_at',NULL);
						}

						if(@$form['datatable_where']) {
							$selects_data->whereraw($form['datatable_where']);
						}	

						if($table1 && $column1) {
							$orderby_table  = $table1;
							$orderby_column = $column1;
						}

						if($table2 && $column2) {
							$selects_data->join($table2,$table2.'.id','=',$table1.'.'.$column1);											
							$orderby_table  = $table2;
							$orderby_column = $column2;										
						}													

						if($table3 && $column3) {
							$selects_data->join($table3,$table3.'.id','=',$table2.'.'.$column2);											
							$orderby_table  = $table3;
							$orderby_column = $column3;
						}

						if($format) {				
							$format = str_replace('&#039;', "'", $format);						
							$selects_data->addselect(DB::raw("CONCAT($format) as label"));	
							$selects_data = $selects_data->orderby(DB::raw("CONCAT($format)"),"asc")->get();
						}else{
							$selects_data->addselect($orderby_table.'.'.$orderby_column.' as label');
							$selects_data = $selects_data->orderby($orderby_table.'.'.$orderby_column,"asc")->get();
						}										

						
						foreach($selects_data as $d) {											

							$val    = $d->id;
							$select = ($value == $val)?"selected":"";							

							echo "<option $select value='$val'>".$d->label."</option>";
						}
					endif;
				} //end if not parent select
			?>
		</select>
		<div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
		<p class='help-block'>{{ @$form['help'] }}</p>
		</div>
	</div>