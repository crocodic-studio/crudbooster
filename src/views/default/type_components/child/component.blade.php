<div class='form-group {{$col_width}} {{$header_group_class}} {{ ($errors->first($form['table']))?"has-error":"" }}' id='form-group-{{$form['table']}}' style="{{@$form['style']}}">								
								
<div class="panel panel-default">
<div class="panel-heading">
  <i class='fa fa-bars'></i> {{$form['label']}}
  <div class="pull-right">						                
    <a class='btn btn-xs btn-primary btn-add-{{$form["table"]}}' href='javascript:void(0)'><i class="fa fa-plus"></i> {{trans('crudbooster.action_add_data')}}</a>
  </div>
  <!-- /.box-tools -->
</div>
<!-- /.box-header -->
<?php 
	$table = $form['table'];
	$id = 'id';
	$fk = $form['foreign_key'];
	$query = DB::table($table)->where($fk,$row->id);
	if(\Schema::hasColumn($table,'deleted_at')) {
		$query->where($table.'.deleted_at',NULL);
	}
	$query = $query->orderby('id','desc')->get();
?>

<div class="panel-body">
  <table id='table-{{$form['table']}}' class='table table-hover table-striped table-bordered'>
  	<thead>
  		<tr>
  			@foreach($form['columns'] as $column)
  			<th>{{$column['label']}}</th>
  			@endforeach
  			<th>{{trans('crudbooster.action_label')}}</th>
  		</tr>
  	</thead>
  	<tbody>
  								              		
  	</tbody>
  </table>

  <script type="text/javascript">						            						            						       
	var datatable_{{$form['table']}} = $("#table-{{$form['table']}}").DataTable({						            		
		"columns": [
			@for($i=1;$i<=count($form['columns']);$i++)
		   	null,
		    @endfor
		    { "width": "15%" }											   
		  ]
	});
	var fields = {!! json_encode($form['fields']) !!};
	var $modal = $('#form-body-modal');

	var id = null;
	var item = null;
	var table = null;
	var html = '';
	var images_ext = ['jpg','jpeg','png','gif','bmp'];

	load_table_data('{{$form['table']}}','{{$form["foreign_key"]}}');

	$(document).on('click','#table-{{$form["table"]}} tbody .btn-edit-child',function() {
		id = $(this).data('id');
		item = $(this).data('item');
		table = $(this).data('table');						            		
		showChildModal{{$form["table"]}}();
	});

	$(function() {
		$('.btn-add-{{$form["table"]}}').click(function() {
			id = item = table = null;
			showChildModal{{$form["table"]}}();
		})
		$(document).on('click','.btn-delete-input-file',function() {
			var name = $(this).data('name');
			$('#form-body-modal form input[name="'+name+'"]').val('');
			$(this).parent('.form-group').find('.view').remove();
			$('#form-body-modal form').submit();
		});
	})
	
	function showChildModal{{$form["table"]}}() {
								            								            		
		html = '';						            		
		
		$modal.find('.modal-body').empty();

		if(id != null) {
			$modal.find('.modal-title').text("{{trans('crudbooster.action_edit_data')}} {{$form['label']}}");
			html += '<input type="hidden" name="id" value="'+id+'"/>';
		}else{
			$modal.find('.modal-title').text("{{trans('crudbooster.action_add_data')}} {{$form['label']}}");	
		}
		
		var child_parent_id = "{{$row->id}}";
		if(child_parent_id) {
			html += "<input type='hidden' name='child_parent_id' value='"+child_parent_id+"'/>";
		}

		html += '<input type="hidden" name="foreign_key" value="{{$form['foreign_key']}}"/>';
		html += '<input type="hidden" name="table" value="{{$form['table']}}"/>';
		html += '<input type="hidden" name="columns" value="{{ json_encode($form["columns"]) }}"/>';
		html += '<input type="hidden" name="fields" value="{{ json_encode($form["fields"]) }}"/>';
		
		$.each(fields,function(i,obj) {
			var type       = obj.type;
			var input      = '';												
			var validation = obj.validation;
			var required   = (validation && ~validation.indexOf('required'))?"required":"";
			var help  	   = (obj.help)?"<div class='help-block'>"+obj.help+"</div>":"";
			var name 	   = obj.name;
			var value 	   = (item)?item[name]:'';
			var assetURL   = "{{asset('/')}}";

			switch(type) {
				case 'text':
					input = "<input type='text' class='form-control' value='"+value+"' name='"+name+"' "+required+" />";
					break;
				case 'textarea':
					input = "<textarea class='form-control' name='"+name+"' "+required+" rows='5'>"+value+"</textarea>";
					break;
				case 'upload':
					if(value) {
						var ext = value.split('.').pop().toLowerCase();
						var is_image = $.inArray(ext, images_ext);
						
						if(is_image !== false) {
							input += "<div class='view'><a class='fancybox' href='"+assetURL+'/'+value+"'><img src='"+assetURL+'/'+value+"' height='150px'/></a></div>";	
						}else{
							input += "<div class='view'><a href='"+assetURL+'/'+value+"?download=1' target='_blank'><i class='fa fa-file'></i> Download File</a></div>";
						}
						
						input += "<a class='btn btn-warning btn-sm btn-delete-input-file' data-name='"+name+"' href='javascript:void(0)'>Delete File</a><br/>";
						input += "<input type='hidden' name='"+name+"' value='"+value+"'/>";
					}else{
						input += "<input type='file' class='form-control' name='"+name+"'' "+required+" />";						            					
					}
					
					break;
				case 'radio':
					if(obj.dataenum) {						             						
						for(var i=0;i<=obj.dataenum.length-1;i++) {
							var v = obj.dataenum[i];
							var checked = (value && value==v)?"checked":"";
							input += "<label class='radio-inline'>"+
							"<input type='radio' "+checked+" name='"+name+"' value='"+v+"'/> "+v+
							"</label>";						            							
						}							            										            					
					}
					if(obj.dataquery) {
						$.ajax({
							type:'POST',
							url:"{{CRUDBooster::mainpath('data-query')}}",
							data:{query:obj.dataquery},
							success:function(resp) {
								if(resp) {
									$.each(resp,function(i,objq) {
										var checked = (value && value == objq.value)?"checked":"";
        								input += "<div class='radio'>"+
        								"<label><input type='radio' "+checked+" name='"+objq.name+"' value='"+objq.value+"'/> "+objq.label+"</label>"+
        								"</div>";
        							});
								}						            								
							},
							async:false
						});						            												            						
					}
					break;
				case 'select':
					input = "<select class='form-control' "+required+" name='"+name+"'>";
					input += "<option value=''>{{trans('crudbooster.text_prefix_option')}} "+obj.label+"</option>";
					if(obj.dataenum) {						             						
						for(var i=0;i<=obj.dataenum.length-1;i++) {
							var v = obj.dataenum[i];
							var selected = (value && value == v)?"selected":"";
							input += "<option "+selected+" value='"+v+"'>"+v+"</option>";						            							
						}							            										            					
					}
					if(obj.dataquery) {
						$.ajax({
							type:'POST',
							url:"{{CRUDBooster::mainpath('data-query')}}",
							data:{query:obj.dataquery},
							success:function(resp) {
								if(resp) {
									$.each(resp,function(i,objq) {		
										var selected = (value && value == objq.value)?"selected":"";						            								
        								input += "<option "+selected+" value='"+objq.value+"'>"+objq.label+"</option>";
        							});
								}						            								
							},
							async:false
						});						            												            						
					}

					input += "</select>";
					break;
				default:
					input = ''; 
					break;
			} 						            			

			html += 
			"<div class='form-group'>"+
			"<label>"+obj.label+"</label>"+
			input +
			help +
			"</div>"
			;	
		});

			$("<form method='post' action='{{CRUDBooster::mainpath("add-save-session")}}'>"+html+"</form>").appendTo("#form-body-modal .modal-body");
			
			$modal.modal('show');
		
	}

	
</script>
</div>
<!-- /.box-body -->
</div>                			
</div>