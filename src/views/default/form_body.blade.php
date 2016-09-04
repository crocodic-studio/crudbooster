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
					<script src="{{asset('vendor/laravel-filemanager/js/lfm.js')}}"></script>
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

					<?php if(count($forms)==0):?>
                			<div class='callout callout-danger'>
                					<h4>Oops Sorry !</h4>
                					<p>Sorry this modul there is no feature for add new data</p>
                			</div>
                	<?php endif;?>

                	<?php 
                		$header_group_class = "";
                		foreach($forms as $index=>$form):
                			
                			$name 		= $form['name'];
                			@$join 		= $form['join'];
                			@$value		= (isset($form['value']))?$form['value']:'';
                			@$value		= (isset($row->{$name}))?$row->{$name}:$value;

                			$old 		= old($name);
                			$value 		= (!empty($old))?$old:$value;

                			if( ($parent_field && $name == $parent_field) && !isset($form['visible']) ) continue;

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

                			if(get_method() == 'getDetail') {
                				$disabled = 'disabled';
                			}

                			if(Request::segment(3)=='edit' && $priv->is_edit==0) {
                				$disabled = 'disabled';
                			}

                			if($type=='header') {
                				$header_group_class = "header-group-$index";
                			}else{
                				$header_group_class = ($header_group_class)?:"header-group-$index";	
                			}
                			
                	?>          

                		@if($type=='header' || $type=='heading')
                			<div id='header{{$index}}' data-collapsed="{{ ($form['collapsed']===false)?'false':'true' }}" class='header-title form-divider'>                				
	            					<h4>
	            						<strong><i class='{{$form['icon']?:"fa fa-check-square-o"}}'></i> {{$form['label']}}</strong>
	            						<span class='pull-right icon'><i class='fa fa-minus-square-o'></i></span>
	            					</h4>            					
            				</div>
                		@endif	


                		@if($form['googlemaps']==true)
                		<style>

					      #map {
					        height: 300px;
					      }
					      .controls {
					        margin-top: 10px;
					        border: 1px solid transparent;
					        border-radius: 2px 0 0 2px;
					        box-sizing: border-box;
					        -moz-box-sizing: border-box;
					        height: 32px;
					        outline: none;
					        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
					      }

					      #pac-input {
					        background-color: #fff;
					        font-family: Roboto;
					        font-size: 15px;
					        font-weight: 300;
					        margin-left: 12px;
					        padding: 0 11px 0 13px;
					        text-overflow: ellipsis;
					        width: 300px;
					      }

					      #pac-input:focus {
					        border-color: #4d90fe;
					      }

					      .pac-container {
					        font-family: Roboto;
					      }

					      #type-selector {
					        color: #fff;
					        background-color: #4d90fe;
					        padding: 5px 11px 0px 11px;
					      }

					      #type-selector label {
					        font-family: Roboto;
					        font-size: 13px;
					        font-weight: 300;
					      }
					    </style>
                		<div class='form-group peta {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}'>
							<input id="pac-input" class="controls" autofocus type="text"
						        placeholder="Enter a location">
						    <div id="type-selector" class="controls">
						      <input type="radio" name="type" id="changetype-all" checked="checked">
						      <label for="changetype-all">All</label>

						      <input type="radio" name="type" id="changetype-establishment">
						      <label for="changetype-establishment">Establishments</label>

						      <input type="radio" name="type" id="changetype-address">
						      <label for="changetype-address">Addresses</label>

						      <input type="radio" name="type" id="changetype-geocode">
						      <label for="changetype-geocode">Geocodes</label>
						    </div>
						    <div id="map"></div>
						</div>
                		<script type="text/javascript">
					      function initMap() {
					        var map = new google.maps.Map(document.getElementById('map'), {
					          @if($row->latitude && $row->longitude)
					          center: {lat: {{$row->latitude}}, lng: {{$row->longitude}} },
					          @else 
					          center: {lat: -7.0157404, lng: 110.4171283},
					          @endif
					          zoom: 12
					        });

					        @if($row->latitude && $row->longitude)
					        var marker = new google.maps.Marker({
					          position: {lat: {{$row->latitude}}, lng: {{$row->longitude}} },
					          map: map,
					          title: 'Location Here !'
					        });
					        @endif

					        var input = /** @type  {!HTMLInputElement} */(
					            document.getElementById('pac-input'));

					        var types = document.getElementById('type-selector');
					        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
					        map.controls[google.maps.ControlPosition.TOP_LEFT].push(types);

					        var autocomplete = new google.maps.places.Autocomplete(input);
					        autocomplete.bindTo('bounds', map);

					        var infowindow = new google.maps.InfoWindow();
					        var marker = new google.maps.Marker({
					          map: map,
					          anchorPoint: new google.maps.Point(0, -29)
					        });

					        autocomplete.addListener('place_changed', function() {
					          infowindow.close();
					          marker.setVisible(false);
					          var place = autocomplete.getPlace();
					          if (!place.geometry) {
					            window.alert("Autocomplete's returned place contains no geometry");
					            return;
					          }

					          // If the place has a geometry, then present it on a map.
					          if (place.geometry.viewport) {
					            map.fitBounds(place.geometry.viewport);
					          } else {
					            map.setCenter(place.geometry.location);
					            map.setZoom(17);  // Why 17? Because it looks good.
					          }
					          marker.setIcon(/** @type  {google.maps.Icon} */({
					            url: 'http://maps.google.com/mapfiles/ms/icons/red.png',
					            size: new google.maps.Size(71, 71),
					            origin: new google.maps.Point(0, 0),
					            anchor: new google.maps.Point(17, 34),
					            scaledSize: new google.maps.Size(35, 35)
					          }));
					          marker.setPosition(place.geometry.location);
					          marker.setVisible(true);

					          var address = '';
					          if (place.address_components) {
					            address = [
					              (place.address_components[0] && place.address_components[0].short_name || ''),
					              (place.address_components[1] && place.address_components[1].short_name || ''),
					              (place.address_components[2] && place.address_components[2].short_name || '')
					            ].join(' ');
					          }

					          var latitude = place.geometry.location.lat();
							  var longitude = place.geometry.location.lng(); 

							  @if($form['googlemaps_address'])
							  	$("input[name={{$form['googlemaps_address']}}]").val(address);
							  @endif
					          
					          $("input[name=latitude]").val(latitude);
					          $("input[name=longitude]").val(longitude);

					          infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
					          infowindow.open(map, marker);
					        });

					        function setupClickListener(id, types) {
					          var radioButton = document.getElementById(id);
					          radioButton.addEventListener('click', function() {
					            autocomplete.setTypes(types);
					          });
					        }

					        setupClickListener('changetype-all', []);
					        setupClickListener('changetype-address', ['address']);
					        setupClickListener('changetype-establishment', ['establishment']);
					        setupClickListener('changetype-geocode', ['geocode']);
					      }
					    </script>		        					    
					    <script src="https://maps.googleapis.com/maps/api/js?key={{$setting->google_api_key}}&libraries=places&callback=initMap"
			        async defer></script>
                		@endif

                		@if($jquery)
                		<script>
                		$(function() {
                			<?php echo $jquery;?>
                		});
                		</script>
                		@endif

                		@if(@$type=='html')
                		<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}'>
                			{!!$form['html']!!}
                		</div>
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
                		<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}'>
                			<label>{{$form['label']}}</label>
                			<div id='qrcode_{{$name}}'></div>
                		</div>                		
                		@endif

                		@if(@$type=='checkbox')
						<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
							<label>{{$form['label']}}</label>							
							<?php 
								$value = explode(";",$value);
								array_walk($value, 'trim');
							?>
							@if(isset($form['dataenum']))
								@foreach($form['dataenum'] as $d)
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

										if(Session::get('foreign_key')) {
											$columns = \Schema::getColumnListing($datatable_tab);	
											foreach(Session::get('foreign_key') as $k=>$v) {
												if(in_array($k, $columns)){
													$selects_data->where($datatable_tab.'.'.$k,$v);
												}
											}
										}

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
						@endif


                		@if(@$type=='text' || @!$type)
						<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
							<label>{{$form['label']}}</label>
							<input type='text' title="{{$form['label']}}" {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} class='form-control' name="{{$name}}" id="{{$name}}" value='{{$value}}'/>
							<div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
							<p class='help-block'>{{ @$form['help'] }}</p>
						</div>
						@endif

						@if(@$type=='number')
						<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
							<label>{{$form['label']}}</label>
							<input type='number' title="{{$form['label']}}" {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} class='form-control' name="{{$name}}" id="{{$name}}" value='{{$value}}'/>
							<div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
							<p class='help-block'>{{ @$form['help'] }}</p>
						</div>
						@endif

						@if(@$type=='email')
						<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
							<label>{{$form['label']}}</label>
							<div class="input-group">
			                	<span class="input-group-addon"><i class="fa fa-envelope"></i></span>
			                	<input type='email' title="{{$form['label']}}" {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} class='form-control' name="{{$name}}" id="{{$name}}" value='{{$value}}'/>
			              	</div>							
							<div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
							<p class='help-block'>{{ @$form['help'] }}</p>
						</div>
						@endif

						@if(@$type=='money')
						<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
							<label>{{$form['label']}}</label>
							<input type='text' title="{{$form['label']}}" {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} class='form-control inputMoney' name="{{$name}}" id="{{$name}}" value='{{$value}}'/>
							<div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
							<p class='help-block'>{{ @$form['help'] }}</p>
						</div>						
						@endif

						@if(@$type=='browse')
						<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
							<label>{{$form['label']}}</label>
							<div class="input-group">
						      <input type="text" class="form-control" id="{{$name.'_label'}}" {{$required}} readonly placeholder="Please browse data...">
						      <span class="input-group-btn">
						      	<button class="btn btn-danger btn-delete" id="btn_clear_{{$name}}" type="button" title='Clear' onclick='if(!confirm("Are you sure want to clear ?")) return false'><i class='fa fa-times'></i></button>
						        <button class="btn btn-primary btn-browse" type="button" data-toggle="modal" title='Browse data' data-target="#modal_{{$name}}"><i class='fa fa-search'></i> Browse</button>						        
						      </span>
						    </div><!-- /input-group -->
						    <input type='hidden' name="{{$name}}" id="{{$name}}" value="{{$value}}"/>
							<div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
							<p class='help-block'>{{ @$form['help'] }}</p>
						</div>

						<div id='modal_{{$name}}' class="modal fade" tabindex="-1" role="dialog">
						  <div class="modal-dialog modal-lg">
						    <div class="modal-content">
						      <div class="modal-header">
						        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						        <h4 class="modal-title"><i class='fa fa-search'></i> Browse Data : {{$form['label']}} <small>Select one of these rows</small></h4>
						      </div>
						      <div class="modal-body">
						        <table id="table_{{$name}}" class="table table-bordered table-striped">
						        	<thead>	
						        		<tr class='info'>	
						        			@if($form['browse_columns'])		
						        			<th width='3%'>ID</th>		        		
							        		@foreach($form['browse_columns'] as $bc)
							        			<th>{{$bc['label']}}</th>
							        		@endforeach
							        		@else
							        		<th width='3%'>ID</th>
							        		<th>Name</th>
							        		@endif
						        		</tr>
						        	</thead>
						        	<tbody>

						        	</tbody>
						        	<tfoot>
						        		<tr class='info'>	
						        			@if($form['browse_columns'])		
						        			<th width='3%'>ID</th>		        		
							        		@foreach($form['browse_columns'] as $bc)
							        			<th>{{$bc['label']}}</th>
							        		@endforeach
							        		@else
							        		<th width='3%'>ID</th>
							        		<th>Name</th>
							        		@endif
						        		</tr>
						        	</tfoot>
						        </table>
						      </div>
						    </div><!-- /.modal-content -->
						  </div><!-- /.modal-dialog -->
						</div><!-- /.modal -->

						
						<script type="text/javascript">
							$(function() {
								$('#table_{{$name}}').DataTable( {
							        "processing": true,
							        "serverSide": true,
							        "ajax": "{{route($form['browse_source'].'GetDataTables')}}?browse_where={{$form['browse_where']}}",
							        "createdRow": function ( row, data, index ) {
							             $(row).attr('style','cursor:pointer');
							             $(row).attr('data-id',data[0]);
							             $(row).attr('data-label',data[1]);
							             $(row).addClass('dt_row_browse');
							        }
							    });

							    $(document).on("click",".dt_row_browse",function() {
							    	var id = $(this).attr('data-id');
							    	var label = $(this).attr('data-label');
							    	$("#{{$name}}").val(id);
							    	$("#{{$name.'_label'}}").val(label);
							    	$("#modal_{{$name}}").modal("hide");
							    });

							    //current data
							    @if($value)
							    $("#{{$name.'_label'}}").val("Please wait load data...");
							    $.get("{{route($form['browse_source'].'GetCurrentDataTables')}}/{{$value}}",function(resp) {
							    	$("#{{$name.'_label'}}").val(resp.label);
							    })
							    @endif

							    $("#btn_clear_{{$name}}").click(function() {
							    	$("#{{$name.'_label'}}").val('');
							    	$("#{{$name}}").val('');
							    })
							})
						</script>
						@endif

						@if(@$type=='date' || @$type=='datepicker')
						<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
							<label>{{$form['label']}}</label>
							<div class="input-group">
  								<span class="input-group-addon"><i class='fa fa-calendar'></i></span>
								<input type='text' title="{{$form['label']}}" {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} class='form-control notfocus datepicker' name="{{$name}}" id="{{$name}}" value='{{$value}}'/>
							</div>
							<div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
							<p class='help-block'>{{ @$form['help'] }}</p>
						</div>
						@endif

						@if(@$type=='datetime' || @type=='datetimepicker')
						<script>
						$(function() {
							$('.date_{{$name}}').on('changeDate', function (ev) {
							    $('.date_{{$name}}').trigger("change");
							});
							$(".date_{{$name}}, .hour_{{$name}}, minute_{{$name}}").change(function() {
								var d = $(".date_{{$name}}").val();
								var h = $(".hour_{{$name}}").val();
								var m = $(".minute_{{$name}}").val();
								var s = "00";
								var datetime = d+' '+h+':'+m+':'+s;
								$("input[name={{$name}}]").val(datetime);
								console.log("DateTime : "+datetime);
							})

						})							
						</script>
						<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
							<label>{{$form['label']}}</label>
							<div class='row'>
							<div class='col-sm-3'>
								<div class="input-group">
  									<span class="input-group-addon"><i class='fa fa-calendar'></i></span>
									<input type='text' title="{{$form['label']}}" {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} class='form-control notfocus datepicker date_{{$name}}' id="{{$name}}" value='{{($value)?date("Y-m-d",strtotime($value)):''}}'/>
								</div>
							</div>
							<div class='col-sm-1'><select class='form-control hour_{{$name}}'><?php 
								for($i=0;$i<=24;$i++) {
									$select = (date("H",strtotime($value)) == str_pad($i,2,0,STR_PAD_LEFT))?"selected":"";
									echo "<option $select value='".str_pad($i,2,0,STR_PAD_LEFT)."'>".str_pad($i,2,0,STR_PAD_LEFT)."</option>";
								}
								?></select></div>
							<div class='col-sm-1'><select class='form-control minute_{{$name}}'><?php 
								for($i=0;$i<=60;$i = $i+5) {
									$select = (date("i",strtotime($value)) == str_pad($i,2,0,STR_PAD_LEFT))?"selected":"";
									echo "<option $select value='".str_pad($i,2,0,STR_PAD_LEFT)."'>".str_pad($i,2,0,STR_PAD_LEFT)."</option>";
								}
								?></select></div>
							</div>
							<input type='hidden' name='{{$name}}' value='{{$value}}'/>
							<div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
							<p class='help-block'>{{ @$form['help'] }}</p>
						</div>
						@endif

						@if(@$type=='hide' || @$type=='hidden')
						<input type='hidden' name="{{$name}}" value='{{$value}}'/>
						@endif
 
						@if(@$type=='textarea')
						<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
							<label>{{$form['label']}}</label>							
							<textarea name="{{$form['name']}}" id="{{$name}}" {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} class='form-control' rows='5'>{{ $value}}</textarea>
							<div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
							<p class='help-block'>{{ @$form['help'] }}</p>
						</div>
						@endif


						@if(@$type=='wysiwyg')
						<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
							<label>{{$form['label']}}</label>	
							<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
							<textarea id='textarea_{{$name}}' name="{{$name}}" class="form-control my-editor">{!! old($name, $value) !!}</textarea>
							<script>
							  var editor_config = {
							    path_absolute : "{{asset('/')}}", 
							    selector: "#textarea_{{$name}}",
							    height:250,
							    {{ ($disabled)?"readonly:1,":"" }}
							    plugins: [
							      "advlist autolink lists link image charmap print preview hr anchor pagebreak",
							      "searchreplace wordcount visualblocks visualchars code fullscreen",
							      "insertdatetime media nonbreaking save table contextmenu directionality",
							      "emoticons template paste textcolor colorpicker textpattern"
							    ],
							    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media",
							    relative_urls: false,
							    file_browser_callback : function(field_name, url, type, win) {
							      var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
							      var y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight;

							      var cmsURL = editor_config.path_absolute + 'laravel-filemanager?field_name=' + field_name;
							      if (type == 'image') {
							        cmsURL = cmsURL + "&type=Images";
							      } else {
							        cmsURL = cmsURL + "&type=Files";
							      }

							      tinyMCE.activeEditor.windowManager.open({
							        file : cmsURL,
							        title : 'Filemanager',
							        width : x * 0.8,
							        height : y * 0.8,
							        resizable : "yes",
							        close_previous : "no"
							      });
							    }
							  };

							  tinymce.init(editor_config);
							</script>
							
							<div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
							<p class='help-block'>{{ @$form['help'] }}</p>
						</div>
						@endif

						@if(@$type=='password')
						<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
							<label>{{$form['label']}}</label>
							<input type='password' title="{{$form['label']}}" id="{{$name}}" {{$required}} {!!$placeholder!!} {{$readonly}} {{$disabled}} class='form-control' name="{{$name}}"/>							
							<div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
							<p class='help-block'>{{ @$form['help'] }}</p>
						</div>
						@endif

						@if(@$type=='select')
						<?php 
							if(isset($form['sub_select'])):
								$tab = str_replace("id_","",$form['sub_select']);
						?>
								<script>
								var val;
								$(function() { 
									val = $('#{{$form['sub_select']}}').attr('data-value');
									
									$('#{{$name}}').change(function() {
										console.log('{{$name}} changed');
										$('#{{$form['sub_select']}}').html('<option value="">Please wait...</option>');
										var parid = $(this).val();
										$.get('{{url("admin/$tab/find-data")}}?parid='+parid+'&parfield={{$name}}&limit=500',function(resp) {
											$('#{{$form['sub_select']}}').empty();
											$('#{{$form['sub_select']}}').html("<option value=''>** Please Select Data</option>");
											$.each(resp.items,function(i,obj) {
												var selected = (val && val == obj.id)?'selected':'';
												$('#{{$form['sub_select']}}').append('<option '+selected+' value=\"'+obj.id+'\">'+obj.text+'</option>');
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
						<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
							<label>{{$form['label']}}</label>												
							<select class='form-control' id="{{$name}}" data-value='{{$value}}' {{$required}} {!!$placeholder!!} {{$readonly}} {{$disabled}} name="{{$name}}">
								<option value=''>** Please Select a {{$form['label']}}</option>
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

											echo "<option $select value='$val'>$lab</option>";
										}
									endif;

									if(@$form['datatable']):
										$datatable_array = explode(",",$form['datatable']);
										$datatable_tab = $datatable_array[0];
										$datatable_field = $datatable_array[1];
										$datatable_field2 = @$datatable_array[2];

										$tables = explode('.',$datatable_tab);
										$selects_data = DB::table($tables[0])->select($tables[0].".id");	

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

										if($datatable_field2) {
											$selects_data->addselect($datatable_field2);
										}

										if(Session::get('foreign_key')) {
											$columns = \Schema::getColumnListing($datatable_tab);	
											foreach(Session::get('foreign_key') as $k=>$v) {
												if(in_array($k, $columns)){
													$selects_data->where($datatable_tab.'.'.$k,$v);
												}
											}
										}

										$selects_data = $selects_data->orderby($datatable_field,"asc")->get();
										foreach($selects_data as $d) {											

											$val = ($datatable_field2)?$d->{$datatable_field2}:$d->id;
											$select = ($value == $val)?"selected":"";

											if(Request::get("parent_field")==$name && Request::get("parent_id")==@$val) {
												$select = "selected";
											}

											if(@$form['datatable_exception'] == $val || @$form['datatable_exception'] == $d->{$datatable_field}) continue;

											echo "<option $select value='$val'>".$d->{$datatable_field}."</option>";
										}
									endif
								?>
							</select>
							<div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
							<p class='help-block'>{{ @$form['help'] }}</p>
						</div>
						@endif


						@if(@$type=='select2')
						<?php 
							$select2_source = ($form["select2_source"])?:$form["select2_controller"];
						?>
						<script>
							$(function() {
								$('#<?php echo $name?>').select2({
								  ajax: {								  	
								    url: '{{route($select2_source."GetFindData")}}',
								    delay: 250,								   
								    placeholder: {
									    id: '-1', 
									    text: '** Please Select a {{$form['label']}}'
									},
								    data: function (params) {
								      var query = {
								        q: params.term,
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
							                $.ajax('{{route($select2_source."GetFindData")}}', {
							                    data: {id: id},
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
						<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
							<label>{{$form['label']}}</label>												
							<select class='form-control' id="{{$name}}" {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} name="{{$name}}">	
								
							</select>
							<div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
							<p class='help-block'>{{ @$form['help'] }}</p>
						</div>
						@endif


						@if(@$type=='radio')
						<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' style="{{@$form['style']}}">
							<label>{{$form['label']}}</label><br/>
							<?php foreach($form['dataenum'] as $d):
								$val = $lab = '';
								if(strpos($d,'|')!==FALSE) {
									$draw = explode("|",$d);
									$val = $draw[0];
									$lab = $draw[1];
								}else{
									$val = $lab = $d;
								}
								$select = ($value == $val)?"checked":"";
							?>	
							<label class='radio-inline'>
								<input type='radio' {{$select}} name='{{$name}}' {{$readonly}} {{$disabled}} value='{{$val}}'/> {!!$lab!!} 
							</label>						

							<?php endforeach;?>																
							<div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
							<p class='help-block'>{{ @$form['help'] }}</p>
						</div>
						@endif

						@if(@$type=='upload')							
							<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style='{{@$form["style"]}}'>
								<label>{{$form['label']}}</label>

								<div class="input-group">
							      <span class="input-group-btn">
							        <a id="lfm-{{$name}}" data-input="thumbnail" data-preview="holder-{{$name}}" class="btn btn-primary">
							          @if(@$form['upload_file'])
							          	<i class="fa fa-file-o"></i> Choose a file
							          @else
							          	<i class='fa fa-picture-o'></i> Choose an image
							          @endif
							        </a>
							      </span>
							      <input id="thumbnail" class="form-control" type="text" readonly value='{{$value}}' name="{{$name}}">
							    </div>

							    @if(@$form['upload_file'])
						          	@if($value) <div style='margin-top:15px'><a id='holder-{{$name}}' href='{{asset($value)}}' target='_blank' title='Download File {{ basename($value)}}'><i class='fa fa-download'></i> Download {{ basename($value)}}</a> 
						          	&nbsp;<a class='btn btn-danger btn-delete btn-xs' onclick='swal({   title: "Are you sure?",   text: "You will not be able to undo this action!",   type: "warning",   showCancelButton: true,   confirmButtonColor: "#DD6B55",   confirmButtonText: "Yes, delete !",   closeOnConfirm: false }, function(){  location.href="{{url($mainpath."/delete-filemanager?file=".$row->{$name}."&id=".$row->id."&column=".$name)}}" });' href='javascript:void(0)' title='Delete this file'><i class='fa fa-ban'></i></a>
						          	</div>@endif
						        @else
						          	<img id='holder-{{$name}}' {{ ($value)?'src='.asset($value):'' }} style="margin-top:15px;max-height:100px;">
						        @endif
							    

								<div class='help-block'>{{@$form['help']}}</div>
								<div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
							</div>
							@if(@$form['upload_file'])
							<script type="text/javascript">$('#lfm-{{$name}}').filemanager('file','{{url("/")}}');</script>
							@else
							<script type="text/javascript">$('#lfm-{{$name}}').filemanager('images','{{url("/")}}');</script>
							@endif
						@endif

						@if(@$type=='upload_standard')
						<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
							<label>{{$form['label']}}</label>
							@if($value)
								<?php 
									if(Storage::exists($row->{$name})) {
										
										$url = asset($row->{$name});
										$info = new SplFileInfo($url);
										$ext  = strtolower($info->getExtension());
										$images_type = array('jpg','png','gif','jpeg','bmp','tiff');									
										$filesize = Storage::size($row->{$name});																
										$humansize = human_filesize($filesize);										
									}
									
									if($filesize):
									if(in_array($ext, $images_type)):
								?>
									<p><img style='max-width:100%' title="Image For {{$form['label']}}, File Size : {{$humansize}}, File Type : {{$ext}}" src='{{$url}}'/></p>
								<?php else:?>
									<p><a href='{{$url}}' title='Download File (Size ({{$humansize}}), Type : {{$ext}})'>Download File</a></p>
								<?php endif;
									else:
										echo "<p class='text-danger'><i class='fa fa-exclamation-triangle'></i> Oops looks like File was Broken !. Click Delete and Re-Upload.</p>";
									endif; 
								?>
								<p><a class='btn btn-danger btn-delete btn-sm' onclick="if(!confirm('Are you sure want to delete ? after delete you can upload other file.')) return false" href='{{url($mainpath."/delete-image?image=".$row->{$name}."&id=".$row->id."&column=".$name)}}'><i class='fa fa-ban'></i> Delete </a></p>
							@endif	
							@if(!$value)
							<input type='file' id="{{$name}}" title="{{$form['label']}}" {{$required}} {{$readonly}} {{$disabled}} class='form-control' name="{{$name}}"/>							
							<p class='help-block'>{{ @$form['help'] }}</p>
							@else
							<p class='text-muted'><em>* If you want to upload other file, please first delete the file.</em></p>
							@endif
							<div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
							
						</div>
						@endif

					<?php endforeach;?>