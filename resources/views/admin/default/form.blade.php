@extends('admin/admin_template')
@section('content')

	<style type="text/css">
	@if(Request::get('detail'))
		input[readonly],textarea[readonly],select[readonly],input[disabled],textarea[disabled],select[disabled] {
			background: #ffffff !important;
			border-top: 0px !important;
			border-left: 0px !important;
			border-right: 0px !important;
			cursor: default !important;
		}

		.btn-form-save, .btn-delete, .btn-browse {
			display: none;
		}
	@endif 
	</style>

	<!-- DataTables -->
	<script src="{{ asset("/assets/adminlte/plugins/datatables/jquery.dataTables.min.js")}}"></script>
	<script src="{{ asset("/assets/adminlte/plugins/datatables/dataTables.bootstrap.min.js")}}"></script>

	<!-- JQUERY QRCODE-->
	<script src="{{asset("assets/jquery-qrcode/jquery.qrcode-0.12.0.min.js")}}"></script>	

	<!--LOAD SELECT2-->	
	<link rel='stylesheet' href='<?php echo asset("assets/select2/dist/css/select2.min.css")?>'/>
	<script src='<?php echo asset("assets/select2/dist/js/select2.full.min.js")?>'></script>
	<style>.select2-container .select2-selection--single {height: 35px}</style>
    <div class='row'>
        <div class='col-md-12'> 			
			 @if($form_tab && (Request::get('is_sub') || @$row))            
		      <ul class="nav nav-tabs form-tab">         
		        @foreach($form_tab as $ft)
		        <?php  
		          $url_tab = $ft['route'];
		           @$id = $row->id;         
		          
		          if($ft['filter_field'] && @$_GET['where']) {
		            $where = $_GET['where'];
		            $field = $ft['filter_field'];
		            $id = $where[$field]?:$id;		            
		          }

		          if(!$id) continue;

		          $url_tab = str_replace("%id%",$id,$url_tab); 
		          $active = '';
	          		if(strpos($url_tab, Session::get('current_mainpath').'/') !==FALSE || strpos($url_tab, Session::get('current_mainpath').'?')!==FALSE) {
		              $active = 'selected';
		            }
		        ?>
		            <li role="presentation" class="active"><a class='{{$active}}' style='cursor:pointer' href="{{$url_tab}}"><i class='<?=($ft["icon"])?:"fa fa-bars"?>'></i> {{$ft[label]}}</a></li>
		          @endforeach
		      </ul>
		      @endif


		      <script type="text/javascript">
            $(function() {
              $(".form-tab a").each(function() {
                var hrf = $(this).attr('href');
                if(hrf.indexOf("/edit/")==-1 && hrf.indexOf("/add")==-1) {
                  var hr = hrf+'&format=total';
                  var hdl = $(this);
                  hdl.append('&nbsp;<em><i class="fa fa-spinner fa-spin"></i></em>');
                  $.get(hr,function(total) {
                    hdl.find('em').text('('+total+')');
                  }).fail(function() {
                  	hdl.find('em').text('(0)');
                  })
                }               
                
              })
            })
          </script>

			<div class='box'>
				<div class='box-body'>
					 @include("admin/default/actionmenu")
				</div>
			  </div>

            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ (Request::get('detail'))?str_replace(array("Add Data","Edit Data"),"Detail Data",$page_title):$page_title }}</h3>
                    <div class="box-tools">
                    	<button title='Cancel' class='btn btn-warning btn-sm' onclick='if(confirm("Are you sure want to cancel before save ?")) location.href="{{url($mainpath)."?".urldecode(http_build_query(@$_GET)) }}"' type='button'><i class='fa fa-arrow-left'></i> Cancel</button>
                    	<button title='Save Data' class='btn btn-success btn-sm btn-form-save' onclick="$('#form').submit()" type='button'><i class='fa fa-save'></i> Save</button>
                    </div>
                </div>

                <iframe id="iframe_upload_fake" name="iframe_upload_fake" style="display:none"></iframe>
				<form id="form_upload_fake" action="{{ url($mainpath.'/upload') }}" target="iframe_upload_fake" method="post" enctype="multipart/form-data" style="width:0px;height:0;overflow:hidden">
				    <input type="hidden" name="_token" value="{{ csrf_token() }}">
				    <input name="userfile" type="file" onchange="$('#form_upload_fake').submit();this.value='';">
				</form>

				<form method='post' id="form" enctype="multipart/form-data" action='@if (@!$row->id) {{url($mainpath."/add-save")}} @else {{url($mainpath."/edit-save")."/$row->id" }} @endif'>
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type='hidden' name='ref_parameter' value='{{urldecode(http_build_query(@$_GET))}}'/>
                <div class="box-body">
                	<?php if(count($forms)==0):?>
                			<div class='callout callout-danger'>
                					<h4>Oops Sorry !</h4>
                					<p>Sorry this modul there is no feature for add new data</p>
                			</div>

                	<?php endif;?>
                	<?php 

                		foreach($forms as $form):
                			$name 		= $form['name'];
                			$join 		= @$form['join'];
                			$value		= (@isset($form['value']))?$form['value']:'';
                			$value		= (@$row->{$name})?:$value;
                			if($join && @$row) {
                				$join_query_{$join} = DB::table($join)->select($name)->where("id",$row->{'id_'.$join})->first();
	                			$value = @$join_query_{$join}->{$name};	                				                				
                			}
                			$type 		= @$form['type'];
                			$required 	= (@$form['required'])?"required":"";
                			$readonly 	= (@$form['readonly'])?"readonly":"";
                			$disabled 	= (@$form['disabled'])?"disabled":"";
                			$jquery 	= @$form['jquery'];

                			if(Request::get('detail')) {
                				$disabled = 'disabled';
                			}
                	?>                		
                		@if($jquery)
                		<script>
                		$(function() {
                			<?php echo $jquery;?>
                		});
                		</script>
                		@endif

                		@if(@$type=='html')
                		<div class='form-group'>
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
                		<div class='form-group'>
                			<label>{{$form['label']}}</label>
                			<div id='qrcode_{{$name}}'></div>
                		</div>                		
                		@endif


                		@if(@$type=='text' || @!$type)
						<div class='form-group'>
							<label>{{$form['label']}}</label>
							<input type='text' title="{{$form['label']}}" {{$required}} {{$readonly}} {{$disabled}} class='form-control' name="{{$name}}" id="{{$name}}" value='{{$value}}'/>
							<div class="text-danger">{{ $errors->first($name) }}</div>
							<p class='help-block'>{{ @$form['help'] }}</p>
						</div>
						@endif

						@if(@$type=='browse')
						<div class='form-group'>
							<label>{{$form['label']}}</label>
							<div class="input-group">
						      <input type="text" class="form-control" id="{{$name.'_label'}}" {{$required}} readonly placeholder="Please browse data...">
						      <span class="input-group-btn">
						      	<button class="btn btn-danger btn-delete" id="btn_clear_{{$name}}" type="button" title='Clear' onclick='if(!confirm("Are you sure want to clear ?")) return false'><i class='fa fa-times'></i></button>
						        <button class="btn btn-primary btn-browse" type="button" data-toggle="modal" title='Browse data' data-target="#modal_{{$name}}"><i class='fa fa-search'></i> Browse</button>						        
						      </span>
						    </div><!-- /input-group -->
						    <input type='hidden' name="{{$name}}" id="{{$name}}" value="{{$value}}"/>
							<div class="text-danger">{{ $errors->first($name) }}</div>
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
							        "ajax": "{{action($form['browse_source'].'@getDataTables')}}?browse_where={{$form['browse_where']}}",
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
							    $.get("{{action($form['browse_source'].'@getCurrentDataTables')}}/{{$value}}",function(resp) {
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
						<div class='form-group'>
							<label>{{$form['label']}}</label>
							<div class="input-group">
  								<span class="input-group-addon"><i class='fa fa-calendar'></i></span>
								<input type='text' title="{{$form['label']}}" {{$required}} {{$readonly}} {{$disabled}} class='form-control notfocus datepicker' name="{{$name}}" id="{{$name}}" value='{{$value}}'/>
							</div>
							<div class="text-danger">{{ $errors->first($name) }}</div>
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
						<div class='form-group'>
							<label>{{$form['label']}}</label>
							<div class='row'>
							<div class='col-sm-3'>
								<div class="input-group">
  									<span class="input-group-addon"><i class='fa fa-calendar'></i></span>
									<input type='text' title="{{$form['label']}}" {{$required}} {{$readonly}} {{$disabled}} class='form-control notfocus datepicker date_{{$name}}' id="{{$name}}" value='{{($value)?date("Y-m-d",strtotime($value)):''}}'/>
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
							<div class="text-danger">{{ $errors->first($name) }}</div>
							<p class='help-block'>{{ @$form['help'] }}</p>
						</div>
						@endif

						@if(@$type=='hide' || @$type=='hidden')
						<input type='hidden' name="{{$name}}" value='{{$value}}'/>
						@endif
 
						@if(@$type=='textarea')
						<div class='form-group'>
							<label>{{$form['label']}}</label>							
							<textarea name="{{$form['name']}}" id="{{$name}}" {{$required}} {{$readonly}} {{$disabled}} class='form-control' rows='5'>{{ $value}}</textarea>
							<div class="text-danger">{{ $errors->first($name) }}</div>
							<p class='help-block'>{{ @$form['help'] }}</p>
						</div>
						@endif


						@if(@$type=='wysiwyg')
						<div class='form-group'>
							<label>{{$form['label']}}</label>	
							<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
	  						<script>
	  							tinymce.init({
								  selector: '#textarea_{{$name}}',
								  convert_urls: false,
								  height: 500,
								  plugins: [
								    'advlist autolink lists link image charmap print preview anchor',
								    'searchreplace visualblocks code fullscreen',
								    'insertdatetime media table contextmenu paste code'
								  ],
								  file_browser_callback: function(field_name, url, type, win) {
							            if(type=='image') $('#form_upload_fake input').click();
							        },
								  toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
								});
	  						</script>						
							<textarea id='textarea_{{$name}}' id="{{$name}}" {{$required}} {{$readonly}} {{$disabled}} name="{{$form['name']}}" class='form-control' rows='5'>{{ $value }}</textarea>
							<div class="text-danger">{{ $errors->first($name) }}</div>
							<p class='help-block'>{{ @$form['help'] }}</p>
						</div>
						@endif

						@if(@$type=='password')
						<div class='form-group'>
							<label>{{$form['label']}}</label>
							<input type='password' title="{{$form['label']}}" id="{{$name}}" {{$required}} {{$readonly}} {{$disabled}} class='form-control' name="{{$name}}"/>							
							<div class="text-danger">{{ $errors->first($name) }}</div>
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
										$('#{{$form['sub_select']}}').html('<option value="">Tunggu sebentar...</option>');
										var parid = $(this).val();
										$.get('{{url("admin/$tab/find-data")}}?parid='+parid+'&parfield={{$name}}&limit=500',function(resp) {
											$('#{{$form['sub_select']}}').empty();
											$('#{{$form['sub_select']}}').html("<option value=''>** Pilih Salah Satu</option>");
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
						<div class='form-group'>
							<label>{{$form['label']}}</label>												
							<select class='form-control' id="{{$name}}" data-value='{{$value}}' {{$required}} {{$readonly}} {{$disabled}} name="{{$name}}">
								<option value=''>** Silahkan Pilih {{$form['label']}}</option>
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

										if(Session::get('filter_field')) {
											$columns = \Schema::getColumnListing($datatable_tab);	
											foreach(Session::get('filter_field') as $k=>$v) {
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
							<div class="text-danger">{{ $errors->first($name) }}</div>
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
								    url: '{{action($select2_source."@getFindData")}}',
								    delay: 250,								   
								    placeholder: {
									    id: '-1', 
									    text: '** Silahkan Pilih {{$form['label']}}'
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
							                $.ajax('{{action($select2_source."@getFindData")}}', {
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
						<div class='form-group'>
							<label>{{$form['label']}}</label>												
							<select class='form-control' id="{{$name}}" {{$required}} {{$readonly}} {{$disabled}} name="{{$name}}">	
								
							</select>
							<div class="text-danger">{{ $errors->first($name) }}</div>
							<p class='help-block'>{{ @$form['help'] }}</p>
						</div>
						@endif


						@if(@$type=='radio')
						<div class='form-group'>
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
							<input type='radio' {{$select}} name='{{$name}}' {{$readonly}} {{$disabled}} value='{{$val}}'/> {!!$lab!!} &nbsp;							
							<?php endforeach;?>																
							<div class="text-danger">{{ $errors->first($name) }}</div>
							<p class='help-block'>{{ @$form['help'] }}</p>
						</div>
						@endif


						@if(@$type=='upload')
						<div class='form-group'>
							<label>{{$form['label']}}</label>
							@if($value)
								<?php 
									$url = asset($row->{$name});
									$info = new SplFileInfo($url);
									$ext  = strtolower($info->getExtension());
									$images_type = array('jpg','png','gif','jpeg','bmp','tiff');
									$filesize = get_size($url);								
									
									$humansize = human_filesize($filesize);
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
							<div class="text-danger">{{ $errors->first($name) }}</div>
							
						</div>
						@endif

					<?php endforeach;?>

					
                </div><!-- /.box-body -->
				
                <div class="box-footer">										
					<button type='button' onclick='if(confirm("Are you sure want to cancel before save ?")) location.href="{{url($dashboard)."?".urldecode(http_build_query(@$_GET)) }}"' class='btn btn-warning btn-lg'><i class='fa fa-arrow-left'></i> Cancel</button> 
					@if( ($priv->is_create || $priv->is_edit) && count($forms)!=0)
					<div class='pull-right'><button type='submit' class='btn btn-success btn-lg btn-form-save' title='Save Data'><i class='fa fa-save'></i> Save</button> </div>
					@endif
                </div><!-- /.box-footer-->
				
            </div><!-- /.box -->
        </div><!-- /.col -->


    </div><!-- /.row -->
@endsection