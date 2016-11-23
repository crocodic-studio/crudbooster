<!-- DataTables -->
<script src="{{asset('vendor/crudbooster/assets/adminlte/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('vendor/crudbooster/assets/adminlte/plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<div id='form-body-modal' class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Modal title</h4>
      </div>
      <div class="modal-body">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">{{trans('crudbooster.button_close')}}</button>
        <button type="button" class="btn btn-primary btn-save">{{trans('crudbooster.button_save')}}</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script type="text/javascript">
	$(function() {

		$(document).on('submit','#form-body-modal form',function() {
			$(this).find('.has-error').removeClass('has-error');
			$(this).find('.empty').removeClass('empty');

			$(this).find('input[required],select[required],textarea[required]').each(function() {
				var v = $(this).val();
				if(v == '') {
					$(this).addClass('empty');
				}
			})
			if($(this).find('.empty').length > 0) {
				$(this).find('.empty').each(function() {
					$(this).parent('.form-group').addClass('has-error');
				})
				return false;
			}

			$(this).parent('.modal-body').prepend('<div class="alert alert-warning">'+
				'<i class="fa fa-refresh fa-spin"></i> {{trans("crudbooster.text_loading")}}</div>');
			$(this).parent('.modal-body').find('form').hide();

			var formData = new FormData();

		    $(this).find('input[type="file"]').each(function() {							    	
		    	var name = $(this).attr('name');
		    	var file = $(this)[0].files[0];
		        formData.append(name, file);
		    });
		    
			var other_data = $(this).serializeArray();
		    $.each(other_data,function(key,input){
		        formData.append(input.name,input.value);
		    });

			$.ajax({
				data:formData,
				type:'POST',
				url:$(this).attr('action'),
				processData: false,
					contentType: false,
				success:function(data) {
					$('#form-body-modal .alert').remove();
					$('#form-body-modal').modal('hide');
					var table = $('#form-body-modal input[name=table]').val();
					var foreign_key = $('#form-body-modal input[name=foreign_key]').val();
					load_table_data(table,foreign_key);
				},
				error:function() {
					alert("{{trans('crudbooster.alert_danger')}}");
				}
			})

			return false;
		})

		$('#form-body-modal .btn-save').click(function() {
			console.log('Save Called');
			$('#form-body-modal form').submit();
		})

		$(document).on('click','.btn-delete-child',function() {
			var table = $(this).data('table');
			var id = $(this).data('id');

			if(!confirm("{{trans('crudbooster.delete_title_confirm')}}")) return false;

			$.post("{{CRUDBooster::mainpath('delete-session-child-data')}}",{id:id,table:table},function(response) {
				console.log(response);
			})

			eval('datatable_'+table)
		        .row( $(this).parents('tr') )
		        .remove()
		        .draw();
		})
	})

	function load_table_data(table,foreign_key) {
		var row_id = "{{$row->id}}";							
		
		if(row_id) {
			var url = "{{CRUDBooster::mainpath('session-child-data')}}/"+table+"?fk="+foreign_key+"&fk_value="+row_id;
		}else{
			var url = "{{CRUDBooster::mainpath('session-child-data')}}/"+table;	
		}
		
		$.get(url,function(response) {
			eval('datatable_'+table).clear();
			$.each(response,function(i,obj) {
				var rowNode = eval('datatable_'+table)
				    .row.add( obj )
				    .draw()
				    .node();									 						
			})
			
		});
	}
</script>