@if($form['datatable'])
							<?php 							
								$datatable = @$form['datatable'];
								$where     = @$form['datatable_where'];
								$format    = @$form['datatable_format'];													

								$raw       = explode(',',$datatable);
								$url       = CRUDBooster::mainpath("find-data");

								$table1    = $raw[0];
								$column1   = $raw[1];
								
								@$table2   = $raw[2];
								@$column2  = $raw[3];
								
								@$table3   = $raw[4];
								@$column3  = $raw[5];
							?>
							<script>				
								$(function() {
									$('#<?php echo $name?>').select2({								  							  
									  placeholder: {
										    id: '-1', 
										    text: '{{trans('crudbooster.text_prefix_option')}} {{$form['label']}}'
										},
									  allowClear: true,
									  ajax: {								  	
									    url: '{!! $url !!}',								    
									    delay: 250,								   								    
									    data: function (params) {
									      var query = {
											q: params.term,
											format: "{{$format}}",
											table1: "{{$table1}}",
											column1: "{{$column1}}",
											table2: "{{$table2}}",
											column2: "{{$column2}}",
											table3: "{{$table3}}",
											column3: "{{$column3}}",
											where: "{!! addslashes($where) !!}"
									      }
									      return query;
									    },
									    processResults: function (data) {
									      return {
									        results: data.items
									      };
									    }								    								    
									  },
									  escapeMarkup: function (markup) { return markup; }, 							        							    
									  minimumInputLength: 1,
								      @if($value)
									  initSelection: function(element, callback) {
								            var id = $(element).val()?$(element).val():"{{$value}}";
								            if(id!=='') {
								                $.ajax('{{$url}}', {
								                    data: {
								                    	id: id, 
								                    	format: "{{$format}}",
								                    	table1: "{{$table1}}",
														column1: "{{$column1}}",
														table2: "{{$table2}}",
														column2: "{{$column2}}",
														table3: "{{$table3}}",
														column3: "{{$column3}}"
													},
								                    dataType: "json"
								                }).done(function(data) {							                	
								                    callback(data.items[0]);	
								                    $('#<?php echo $name?>').html("<option value='"+data.items[0].id+"' selected >"+data.items[0].text+"</option>");			                	
								                });
								            }
								      }
							
								      @endif							      
									});

								})
							</script>

						@else
							<script type="text/javascript">
								$(function() {
									$('#{{$name}}').select2();
								})
							</script>

						@endif

						<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
							<label class='control-label col-sm-2'>{{$form['label']}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>

							<div class="{{$col_width?:'col-sm-10'}}">								
							<select style='width:100%' class='form-control' id="{{$name}}" {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} name="{{$name}}">	
								@if($form['dataenum'])
									<option value=''>{{trans('crudbooster.text_prefix_option')}} {{$form['label']}}</option>
									<?php 
										$dataenum = $form['dataenum'];
										$dataenum = (is_array($dataenum))?$dataenum:explode(";",$dataenum);
									?>
									@foreach($form['dataenum'] as $enum)										
										<?php 
											$val = $lab = '';
											if(strpos($enum,'|')!==FALSE) {
												$draw = explode("|",$enum);
												$val = $draw[0];
												$lab = $draw[1];
											}else{
												$val = $lab = $enum;
											}

											$select = ($value == $val)?"selected":"";
										?>	
										<option {{$select}} value='{{$val}}'>{{$lab}}</option>
									@endforeach
								@endif
							</select>
							<div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
							<p class='help-block'>{{ @$form['help'] }}</p>

							</div>
						</div>