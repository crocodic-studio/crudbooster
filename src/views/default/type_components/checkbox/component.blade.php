<div class='form-group {{$col_width}} {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
							<label>{{$form['label']}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>							
							<?php 
								$value = explode(";",$value);
								array_walk($value, 'trim');
							?>
							@if(isset($form['dataenum']))
								@foreach($form['dataenum'] as $k=>$d)
									<?php 
										if(strpos($d, '|')) {
											$val = substr($d, 0, strpos($d, '|'));
											$label = substr($d, strpos($d, '|')+1);
										}else{
											$val = $label = $d;
										}
										$checked = (in_array($val, $value))?"checked":"";									
									?>
									<div class="checkbox {{$disabled}}">
									  <label>
									    <input type="checkbox" {{$disabled}} {{$checked}} name="{{$name}}[]" value="{{$val}}"> {{$label}}								    
									  </label>
									</div>
								@endforeach
							@endif

								<?php 
									if(@$form['datatable']):
										$datatable_array = explode(",",$form['datatable']);
										$datatable_tab = $datatable_array[0];
										$datatable_field = $datatable_array[1];

										$tables = explode('.',$datatable_tab);
										$selects_data = DB::table($tables[0])->select($tables[0].".id");	

										if(\Schema::hasColumn($tables[0],'deleted_at')) {
											$selects_data->where('deleted_at',NULL);
										}

										if(@$form['datatable_where']) {
											$selects_data->whereraw($form['datatable_where']);
										}

										if(count($tables)) {
											for($i=1;$i<=count($tables)-1;$i++) {
												$tab = $tables[$i];
												$selects_data->leftjoin($tab,$tab.'.id','=','id_'.$tab);
											}
										}																			

										$selects_data->addselect($datatable_field);				

										$selects_data = $selects_data->orderby($datatable_field,"asc")->get();
										foreach($selects_data as $d) {											

											$val = $d->{$datatable_field};
											$checked = (in_array($val, $value))?"checked":"";											

											if(@$form['datatable_exception'] == $val || @$form['datatable_exception'] == $d->{$datatable_field}) continue;

											echo "<div class='checkbox $disabled'>
											  <label>
											    <input type='checkbox' $disabled $checked name='".$name."[]' value='".$d->{$datatable_field}."'> ".$d->{$datatable_field}."								    
											  </label>
											</div>";
										}
									endif
								?>
							<div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
							<p class='help-block'>{{ @$form['help'] }}</p>
						</div>