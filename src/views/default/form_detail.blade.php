					<style type="text/css">
					.form-divider {
						padding:10px 0px 10px 0px;
						margin-bottom: 10px;
						border-bottom:1px solid #dddddd;
					}
					.header-title {
						cursor: pointer;
					}
					i.fa-calendar {cursor:pointer;}

					#table-detail tr td:first-child {
						font-weight: bold;
						width: 25%;
					}
					</style>
					<div class='table-responsive'>
					<table id='table-detail' class='table table-striped'>
					
                	<?php 
                		$header_group_class = "";
                		foreach($forms as $index=>$form):
                			
                			$name 		= $form['name'];
                			@$join 		= $form['join'];
                			@$value		= (isset($form['value']))?$form['value']:'';
                			@$value		= (isset($row->{$name}))?$row->{$name}:$value;
                			


                			if(isset($form['onlyfor'])) {
								if(is_array($form['onlyfor'])) {
									if(!in_array(Session::get('admin_privileges_name'), $form['onlyfor'])) {
										continue;
									}
								}else{
									if(Session::get('admin_privileges_name') != $form['onlyfor']) {
										continue;
									}
								}
							}

                			if(isset($form['callback_php'])) {
                				@eval("\$value = ".$form['callback_php'].";");
                			}

                			if(isset($form['default_value'])) {
                				@$value = $form['default_value'];
                			}

                			if($join && @$row) {
                				$join_arr = explode(',', $join);
                				array_walk($join_arr, 'trim');
                				$join_table = $join_arr[0];
                				$join_title = $join_arr[1];
                				$join_query_{$join_table} = DB::table($join_table)->select($join_title)->where("id",$row->{'id_'.$join_table})->first();
	                			$value = @$join_query_{$join_table}->{$join_title};	                				                				
                			}
                			$type 		= @$form['type'];
                			$required 	= (@$form['required'])?"required":"";
                			$readonly 	= (@$form['readonly'])?"readonly":"";
                			$disabled 	= (@$form['disabled'])?"disabled":"";
                			$jquery 	= @$form['jquery'];
                			$placeholder = (@$form['placeholder'])?"placeholder='".$form['placeholder']."'":"";

                			if($type=='header') {
                				$header_group_class = "header-group-$index";
                			}else{
                				$header_group_class = ($header_group_class)?:"header-group-$index";	
                			}
                			
                	?>          

                		@if($type=='header' || $type=='heading')                			
            				<tr><td>{{$form['label']}}</td><td>&nbsp;</td></tr>
                		@endif	

                		@if(@$type=='qrcode')
                		<script type="text/javascript">
                			$(function() {
                				@if($form['text'])
                					var text = $("#{{$form['text']}}").val();
                				@else 
                					var text = "{{@$row->id}}";
                				@endif

                				if(text) {
                					$("#qrcode_{{$name}}").qrcode({
	                					"size":{{$form['size']}},
	                					"color":"{{$form['color']}}",
	                					"text":text
	                				})	
                				}                				
                			})
                		</script>
               		
                		<tr><td>{{$form['label']}}</td><td><div id='qrcode_{{$name}}'></div></td></tr>
                		@endif

                		@if(@$type=='checkbox')
                		<tr><td>{{$form['label']}}</td><td>
                		<?php 
								$value = explode(";",$value);
								array_walk($value, 'trim');
							?>
							{{ implode(', ',$value) }}
							</td></tr>											
						@endif


                		@if(@$type=='text' || @!$type)						
						<tr><td>{{$form['label']}}</td><td>{{$value}}</td></tr>
						@endif

						@if(@$type=='number')
						<tr><td>{{$form['label']}}</td><td>{{$value}}</td></tr>
						@endif

						@if(@$type=='email')
						<tr><td>{{$form['label']}}</td><td>{{$value}}</td></tr>
						@endif

						@if(@$type=='money')
						<tr><td>{{$form['label']}}</td><td>{{number_format($value)}}</td></tr>						
						@endif

						@if(@$type=='date' || @$type=='datepicker')						
						<tr><td>{{$form['label']}}</td><td>{{date("d F Y",strtotime($value))}}</td></tr>
						@endif

						@if(@$type=='datetime' || @$type=='datetimepicker')
						<tr><td>{{$form['label']}}</td><td>{{date("d F Y H:i:s",strtotime($value))}}</td></tr>
						@endif

						@if(@$type=='time' || @$type=='timepicker')
						<tr><td>{{$form['label']}}</td><td>{{$value}}</td></tr>
						@endif
	
 
						@if(@$type=='textarea')
						<tr><td>{{$form['label']}}</td><td>{{$value}}</td></tr>
						@endif


						@if(@$type=='wysiwyg')
						<tr><td>{{$form['label']}}</td><td>{!!$value!!}</td></tr>
						@endif

						@if(@$type=='select')

						<?php 
							if(isset($form['sub_select'])):
								$tab = str_replace("id_","",$form['sub_select']);
								$sub_label = '';
								foreach($forms as $f) {
									if($f['name'] == $form['sub_select']) {
										$sub_label = $f['label'];
										break;
									}
								}
						?>
								<script>
								var val;
								$(function() { 
									val = $('#{{$form['sub_select']}}').attr('data-value');
									
									$('#{{$name}}').change(function() {
										console.log('{{$name}} changed');
										$('#{{$form['sub_select']}}').html('<option value="">Please wait loading data...</option>');
										var id = $(this).val();
										$.get('{{mainpath("find-data")}}?table1={{$tab}}&fk={{$name}}&fk_value='+id+'&limit=1000',function(resp) {								
											$('#{{$form['sub_select']}}').empty().html("<option value=''>** Please Select a {{$sub_label}}</option>");
											$.each(resp.items,function(i,obj) {
												var selected = (val && val == obj.id)?'selected':'';
												if(val && val == obj.id) {
													$('#txt_select_{{$name}}').text(obj.text);
												}
											})
										})
									});	

									if(val) {
										console.log('Value Detect :'+val);
										$('#{{$name}}').trigger('change');
									}								
								});
								</script>
						<?php 
							endif;
						?>

						<tr><td>{{$form['label']}}</td><td id='{{$name}}'><span id='txt_select_{{$name}}'></span></td></tr>

								<?php 
									if(@$form['dataenum']):
										foreach($form['dataenum'] as $d) {

											$val = $lab = '';
											if(strpos($d,'|')!==FALSE) {
												$draw = explode("|",$d);
												$val = $draw[0];
												$lab = $draw[1];
											}else{
												$val = $lab = $d;
											}

											$select = ($value == $val)?"selected":"";

											if(Request::get("parent_field")==$name && Request::get("parent_id")==@$val) {
												$select = "selected";
											}

											if($select == 'selected') {
												echo "<script>
													$('#txt_select_$name').text(\"$lab\");
												</script>";
											}
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

											if(@$form['datatable_exception'] == $val || @$form['datatable_exception'] == $d->label) continue;

											
											if($select == 'selected') {
												echo "<script>
													$('#txt_select_$name').text(\"$d->label\");
												</script>";
											}
										}
									endif
								?>
		
						@endif


						@if(@$type=='select2')

						<tr><td>{{$form['label']}}</td><td><span id='txt_select2_{{$name}}'></span></td></tr>

						<?php 							
							$datatable = @$form['datatable'];
							$where     = @$form['datatable_where'];
							$format    = @$form['datatable_format'];													

							$raw       = explode(',',$datatable);
							$url       = mainpath("find-data");

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
									    text: '** Please Select a {{$form['label']}}'
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
										where: "{{$where}}"
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
							                    	
							                    $('#txt_select2_{{$name}}').text(data.items[0].text);		                	
							                });
							            }
							      }
						
							      @endif							      
								});

							})
						</script>
						
						@endif


						@if(@$type=='radio')
							<?php 
							foreach($form['dataenum'] as $k=>$d):
								$val = $lab = '';
								if(strpos($d,'|')!==FALSE) {
									$draw = explode("|",$d);
									$val = $draw[0];
									$lab = $draw[1];
								}else{
									$val = $lab = $d;
								}
								$select = ($value == $val)?"checked":"";
								if($value == $val) {
									echo "<tr><td>$form[label]</td><td>$lab</td></tr>";
								}
							endforeach;
							?>	
						@endif

						@if(@$type=='upload')							
							<tr><td>{{$form['label']}}</td><td>

							    @if(@$value)						        
						          	<img id='holder-{{$name}}' {{ ($value)?'src='.asset($value):'' }} style="margin-top:15px;max-height:100px;">
						        @endif
							    </td></tr>							
						@endif

						@if(@$type=='upload_standard')
						<tr><td>{{$form['label']}}</td><td>
							@if($value)
								<?php 
									$file = str_replace('uploads/','',$row->{$name});
									if(Storage::exists($file)) {										
										$url         = asset($row->{$name});
										$ext 	     = explode('.',$url);
										$ext 		 = end($ext);
										$ext 		 = strtolower($ext);
										$images_type = array('jpg','png','gif','jpeg','bmp','tiff');									
										$filesize    = Storage::size($file);																						
									}
									
									if($filesize):
										if(in_array($ext, $images_type)):
										?>
											<p><img style='max-width:100%' title="Image For {{$form['label']}}, File Type : {{$ext}}" src='{{$url}}'/></p>
										<?php else:?>
											<p><a href='{{$url}}'>Download File ({{$ext}})</a></p>
										<?php endif;									
									endif; 
								?>
							
							@endif	
						</td></tr>
						@endif

					<?php endforeach;?>

					</table>
					</div>