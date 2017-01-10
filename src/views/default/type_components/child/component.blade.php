<div class='form-group {{$header_group_class}} {{ ($errors->first($form['name']))?"has-error":"" }}' id='form-group-{{$form['name']}}'>								
	
	<label class='control-label col-sm-2'>{{$form['label']}}</label>							
	<div class="col-sm-10">

	<div class="box box-default" style="box-shadow:0px 0px 5px #ccc">
		<div class="box-header">
		  <h1 class="box-title">{{$form['label']}}</h1>		  
		  <div class="box-tools">
		  	<a class='btn btn-primary btn-sm btn-add' href='javascript:void(0)'><i class='fa fa-plus'></i> {{trans('crudbooster.button_add')}}</a>
		  </div>
		</div>
		<div class="box-body">
			<?php 
				$classname         = 'App\Http\Controllers\\'.$form['controller'];
				$sub               = new $classname();				
				$subtable          = $sub->table;
				$columns           = $sub->columns_table;			
				$fk                = CRUDBooster::getForeignKey($table,$subtable);	
				$fk_id 			   = ($row)?$row->id:0;	
				if($row) {
				$subquery          = DB::table($subtable)->where($fk,$fk_id)->get();	
				}else{
				$subquery          = CRUDBooster::getTemporary($subtable,[$fk=>$fk_id]);									
				}
				
			?>

			<input type='hidden' name='subtable[{{$name}}][fk]' value='{{$fk}}'/>
			<input type='hidden' name='subtable[{{$name}}][table]' value='{{$subtable}}'/>

			<table id='table-{{$form["name"]}}' class='table table-striped'>
			<thead>
				<tr>
					@foreach($columns as $col)
						<?php if($col['name'] == $fk) continue;?>
						<th>{{$col['label']}}</th>
					@endforeach		
						<th width="90px">{{trans('crudbooster.action_label')}}</th>
				</tr>
			</thead>
			<tbody>
				@foreach($subquery as $s)
					<tr>
						@foreach($columns as $col)
							<?php if($col['name'] == $fk) continue;?>

							@if($col['image'])
								@if($s->$col['name'])
									<td><a rel='img-{{$name}}' class='fancybox' href='{{ asset($s->$col['name']) }}'><img class='thumbnail' src="{{ asset($s->$col['name']) }}" width='40px' height='40px'/></a></td>
								@else
									<td>-</td>
								@endif
							@elseif($col['download'])
								@if($s->$col['name'])
									<td><a target="_blank" href='{{ asset($s->$col['name']) }}'>Download File</a></td>
								@else
									<td>
										-
									</td>
								@endif
							@else
								<td>{{ trim(strip_tags($s->$col['name'])) }}</td>
							@endif
						@endforeach
						<td>
							<a href="javascript:void(0)" data-id="{{$s->id}}" class='btn btn-sm btn-success btn-edit'><i class='fa fa-pencil'></i></a>										
							<a href="javascript:void(0)" data-id="{{$s->id}}" class='btn btn-sm btn-warning btn-delete'><i class='fa fa-trash'></i></a>										
						</td>
					</tr>
				@endforeach
			</tbody>
			</table>
		</div>
		<!-- /.box-body -->
	</div> 
	

	<div id='modal_add_{{$form["name"]}}' class="modal" tabindex="-1" role="dialog">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title">{{trans("crudbooster.button_add")}}</h4>
	      </div>
	      <div class="modal-body">	      	
	      		<input type="hidden" name="_token" value="{{csrf_token()}}">	      		
		        <div class="form-content"></div>        
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        <button type="button" class="btn btn-primary btn-save">Save changes</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<div id='modal_edit_{{$form["name"]}}' class="modal" tabindex="-1" role="dialog">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title">{{trans("crudbooster.button_edit")}}</h4>
	      </div>
	      <div class="modal-body">	      	
	      		<input type="hidden" name="_token" value="{{csrf_token()}}">	      		
		        <div class='form-content'>

		        </div>       
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        <button type="button" data-url='' class="btn btn-primary btn-save">Save changes</button>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->


	<script type="text/javascript">
		$(function() {
			var parent_field = "{{$fk}}";
			var parent_id = "{{$fk_id}}";

			$("#table-{{$name}}").DataTable();

			$('#form-group-{{$form["name"]}} .modal').on('hidden.bs.modal', function () {
			    $(this).find('.form-content').empty();
			})

			$(document).on('click','#form-group-{{$form["name"]}} .btn-add',function() {		
				@if($row)
				var url = '{{route($form["controller"]."PostAddSave")}}';				
				@else		
				var url = '{{route($form["controller"]."PostAddSave")}}?temporary=1';				
				@endif
				$('#modal_add_{{$form["name"]}} .btn-save').attr('data-url',url);
				$('#modal_add_{{$form["name"]}}').modal("show");
				$('#modal_add_{{$form["name"]}} .form-content').html("<p class='loading'><i class='fa fa-spin fa-spinner'></i> {{trans('crudbooster.text_loading')}}</p>");
				$.get('{{route($form["controller"]."GetIndex")}}/add-raw?parent_field='+parent_field+'&parent_id='+parent_id,function(response) {
					$('#modal_add_{{$form["name"]}} .form-content').html(response);
				})				
			})

			$(document).on('click','#table-{{$form["name"]}} .btn-edit',function() {
				var id = $(this).data('id');
				@if($row)
				var url = '{{route($form["controller"]."GetIndex")}}/edit-save/'+id;
				@else
				var url = '{{route($form["controller"]."GetIndex")}}/edit-save/'+id+'?temporary=1';
				@endif
				$('#modal_edit_{{$form["name"]}} .btn-save').attr('data-url',url);
				$('#modal_edit_{{$form["name"]}}').modal("show");
				$('#modal_edit_{{$form["name"]}} .form-content').html("<p class='loading'><i class='fa fa-spin fa-spinner'></i> {{trans('crudbooster.text_loading')}}</p>");
				var is_temporary = '{{ ($row)?0:1 }}';
				$.get('{{route($form["controller"]."GetIndex")}}/edit-raw/'+id+'?parent_field='+parent_field+'&parent_id='+parent_id+'&temporary='+is_temporary,function(response) {
					$('#modal_edit_{{$form["name"]}} .form-content').html(response);
				})				
			})

			$(document).on('click','#table-{{$form["name"]}} .btn-delete',function() {
				var id = $(this).data('id');

				swal({
				  title: "{{trans('crudbooster.delete_title_confirm')}}",
				  text: "{{trans('crudbooster.delete_description_confirm')}}",
				  type: "warning",
				  showCancelButton: true,
				  confirmButtonColor: "#DD6B55",
				  confirmButtonText: "Yes, delete it!",
				  closeOnConfirm: false
				},
				function(){
					@if($row)
					var url = '{{route($form["controller"]."GetIndex")}}/delete/'+id;
					@else
					var url = '{{route($form["controller"]."GetIndex")}}/delete/'+id+'?temporary=1';
					@endif
				  	$.get(url,function(response) {
						sweetAlert("Oops..",response.message,response.message_type);
						$("#table-{{$name}}").DataTable().destroy();
						$.get("{{Request::fullUrl()}}",function(html) {
							var tableHtml = $(html).find("#table-{{$name}}").html();
							$('#table-{{$name}}').html(tableHtml);
							$("#table-{{$name}}").DataTable();
						})						
					})
				});

				
			});

			$('#form-group-{{$form["name"]}} .btn-save').click(function() {
				var data = new FormData();
				var url = $(this).data('url');
				var modal_handel = $(this).parent().parent().parent().parent();

				var required_error = false;
				modal_handel.find("input,select,textarea,radio").each(function() {
					var v = $(this).val();
					var key = $(this).attr('name');
					var type = $(this).attr('type');

					if($(this).prop('required')) {
						if(v == '') {
							$(this).parent().parent().addClass('has-error');
							required_error = true;
							return true;
						}else{
							$(this).parent().parent().removeClass('has-error');
						}
					}					

					if(type == 'file') {
						data.append(key,$(this)[0].files[0]);
					}else{
						data.append(key,v);	
					}								
				})

				if(required_error==false) {
					modal_handel.find("input,select,textarea,radio").not("[type=hidden]").val("");
					$.ajax({
						url: url,
				        data: data,				        
				        async: false,
				        cache: false,
				        contentType: false,
				        processData: false,
				        type: 'POST',
				        success: function ( data ) {
				        	if(data.message_type == 'success') {
				        		sweetAlert("{{$form['label']}}", data.message, data.message_type);
					            	
					            $("#table-{{$name}}").DataTable().destroy();

					            $.get("{{Request::fullUrl()}}",function(html) {
									var tableHtml = $(html).find("#table-{{$name}}").html();
									$('#table-{{$name}}').html(tableHtml);
									$("#table-{{$name}}").DataTable();
								})
								modal_handel.modal('hide');
				        	}else{
				        		sweetAlert("{{$form['label']}}", data.message, data.message_type);
				        	}
				            
				        },
				        error:function() {
				        	sweetAlert("{{$form['label']}}",'{{trans("crudbooster.alert_error_ajax")}}','error');
				        }
					})
				}else{
					sweetAlert("Oops...", "{{trans('crudbooster.alert_required')}}", "error");
				}
				
			})
		})
	</script>

	</div>
</div>