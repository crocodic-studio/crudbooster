
			@push('bottom')
			<script>				
				$(function() {
					$('#{{$name}}').select2({	
					  @if($form['options']['multiple']==true)
					  multiple: true,
					  @endif			
					  placeholder: "{{ ($form['placeholder'])?:trans('crudbooster.text_prefix_option')." ".$form['label'] }}",				  							  					  
					  allowClear: {{$form['options']['allow_clear']?'true':'false'}},
					  escapeMarkup: function (markup) { return markup; }, 							        							    					  
					  
					  @if($form['options']['ajax_mode']==true)
					  minimumInputLength: 1,
					  ajax: {								 
					  	type: 'POST', 	
					    url: '{{ CRUDBooster::mainpath("find-dataquery") }}',								    
					    delay: 250,								   								    
					    data: function (params) {
					      var query = {
							q: params.term,
							_token: '{{csrf_token()}}',
							data: "<?php echo base64_encode(json_encode($form['options'])) ?>",							
					      }
					      return query;
					    },
					    processResults: function (data) {
					      return {
					        results: data.items
					      };
					    }								    								    
					  }
			
				      @endif							      
					});

				})
			</script>
			@endpush
			

		<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
			<label class='control-label col-sm-2'>{{$form['label']}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>

			<div class="{{$col_width?:'col-sm-10'}}">								
			<select style='width:100%' class='form-control' id="{{$name}}" {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} name="{{$name}}{{($form['options']['multiple']==true)?'[]':''}}" {{ ($form['options']['multiple'])?'multiple="multiple"':'' }} >					
				
						@php
							$query = $form['options']['query'];
							$select_label = $form['options']['field_label'];
							$select_value = $form['options']['field_value'];								
						@endphp

						@if($form['options']['ajax_mode'] == false)
							@if($form['options']['multiple']==false)
							<option value=''>{{trans('crudbooster.text_prefix_option')}} {{$form['label']}}</option>
							@endif
							<?php 
																							
								$result = DB::select(DB::raw($query));																																
								$rawvalue = $value;																

								foreach($result as $r) {
									$option_label = $r->$select_label;
									$option_value = $r->$select_value;
									if($rawvalue) {
										if($form['options']['multiple']==true) {
											switch ($form['options']['multiple_result_format']) {
												case 'JSON':
													$value = json_decode($rawvalue,true);
													$selected = (in_array($option_value, $value))?"selected":"";
													break;
												default:
												case 'COMMA_SEPARATOR':
													$value = explode(', ',$rawvalue);
													$selected = (in_array($option_value,$value))?"selected":"";
													break;
												case 'SEMICOLON_SEPARATOR':
													$value = explode('; ', $rawvalue);
													$selected = (in_array($option_value, $value))?"selected":"";
													break;												
											}										
										}else{										

											$selected = ($option_value == $value)?"selected":"";
										}
									}else{
										$selected = '';
									}
									
									echo "<option $selected value='$option_value'>$option_label</option>";
								}
							?>
						<!--end-datatable-ajax-->
						@else
							@if($value)
								@php
									$rawvalue = $value;
									$result = DB::select(DB::raw($query));								
								@endphp	
								@foreach($result as $r)
									@php 
										$option_value = $r->$select_value;
										if($form['options']['multiple']==true) {
											switch ($form['options']['multiple_result_format']) {
												case 'JSON':
													$value = json_decode($rawvalue,true)?:[];
													$selected = (in_array($option_value, $value))?"selected":"";
													break;
												default:
												case 'COMMA_SEPARATOR':
													$value = explode(', ',$rawvalue);
													$selected = (in_array($option_value,$value))?"selected":"";
													break;
												case 'SEMICOLON_SEPARATOR':
													$value = explode('; ', $rawvalue);
													$selected = (in_array($option_value, $value))?"selected":"";
													break;												
											}										
										}else{										
											$selected = ($option_value == $value)?"selected":"";
										}
									@endphp
									<option value="{{$option_value}}" {{$selected}} >{{$r->$select_label}}</option>
								@endforeach
							@endif
						@endif 									
			</select>
			<div class="text-danger">
				{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}
			</div><!--end-text-danger-->
			<p class='help-block'>{{ @$form['help'] }}</p>

			</div>
		</div>