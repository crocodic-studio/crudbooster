@extends('admin/admin_template')

@section('content')

<script language="javascript" type="text/javascript" src="{{asset('assets/edit_area/edit_area_full.js')}}"></script>
<script language="javascript" type="text/javascript">
editAreaLoader.init({
	id : "content_php"		// textarea id
	,syntax: "php"			// syntax to be uses for highgliting
	,start_highlight: true		// to display with highlight mode on start-up
});
$(function() {
	$("#form-edit-php").submit(function() {
		var content_php = editAreaLoader.getValue("content_php");
		var action = $(this).attr('action');
		show_alert_floating('Please wait saving...');
		$.post(action,{content_php:content_php},function(resp) {
			if(resp==1) {
				hide_alert_floating();	
			}else{
				alert("Oops something went wrong !");
			}
		});
		return false;
	})
})
</script>
	
    <div class='row'>
        <div class='col-md-12'> 			
					
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ $page_title }}</h3>
                    <div class="box-tools">
			
                    </div>
                </div>
				<form method='post' id='form-edit-php' action='{{Request::url()}}'>
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="box-body">
					<div class='form-group'>												
						<textarea name='content_php' rows='40' id='content_php' class='form-control'>{{$content_php}}</textarea>					
					</div>
                </div><!-- /.box-body -->
                <div class="box-footer">					
					<button type='submit' class='btn btn-primary'><i class='fa fa-save'></i> Save</button> 
					<button type='button' onclick='if(confirm("Are you sure want to cancel before save ?")) location.href="{{url($dashboard)."?".urldecode(http_build_query(@$_GET)) }}"' class='btn btn-warning'><i class='fa fa-arrow-left'></i> Cancel</button> 
                </div><!-- /.box-footer-->
            </div><!-- /.box -->
        </div><!-- /.col -->


    </div><!-- /.row -->
@endsection