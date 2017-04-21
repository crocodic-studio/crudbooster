@extends('crudbooster::admin_template')

@push('css')

	<link rel="stylesheet" href="{{asset('vendor/crudbooster/assets/select2/dist/css/select2.min.css')}}">
	
	<style>
		.select2-container--default .select2-selection--single {border-radius: 0 !important}
		.select2-container .select2-selection--single {height: 35px}
	</style>

@endpush


@section("content")

<ul class="nav nav-tabs">
  @if($id)
  <li role="presentation" class="active"><a href="{{Route('ModulsControllerGetStep1',['id'=>$id])}}"><i class='fa fa-info'></i> Step 1 - Module Information</a></li>
  <li role="presentation"><a href="{{Route('ModulsControllerGetStep2',['id'=>$id])}}"><i class='fa fa-table'></i> Step 2 - Table Display</a></li>
  <li role="presentation"><a href="{{Route('ModulsControllerGetStep3',['id'=>$id])}}"><i class='fa fa-plus-square-o'></i> Step 3 - Form Display</a></li>
  <li role="presentation"><a href="{{Route('ModulsControllerGetStep4',['id'=>$id])}}"><i class='fa fa-wrench'></i> Step 4 - Configuration</a></li>
  @else
  <li role="presentation" class="active"><a href="#"><i class='fa fa-info'></i> Step 1 - Module Information</a></li>
  <li role="presentation"><a href="#"><i class='fa fa-table'></i> Step 2 - Table Display</a></li>
  <li role="presentation"><a href="#"><i class='fa fa-plus-square-o'></i> Step 3 - Form Display</a></li>
  <li role="presentation"><a href="#"><i class='fa fa-wrench'></i> Step 4 - Configuration</a></li>
  @endif
</ul>

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title">Module Information</h3>
    </div>
    <div class="box-body">
    <form method="post" action="{{Route('ModulsControllerPostStep2')}}">
        <input type="hidden" name="_token" value="{{csrf_token()}}">
        <input type="hidden" name="id" value="{{$row->id}}" >
        <div class="form-group">
            <label for="">Table</label>                   
            <select name="table" id="table" required class="select2 form-control" value="{{$row->table_name}}">
                <option value="">{{trans('crudbooster.text_prefix_option')}} Table</option>
                @foreach($tables_list as $table)                    

                    <option {{($table == $row->table_name)?"selected":""}} value="{{$table}}">{{$table}}</option>
                    
                @endforeach
            </select>
            <div class="help-block">
                Do not use cms_* as prefix on your tables name
            </div>
        </div>
        <div class="form-group">
            <label for="">Module Name</label>
            <input type="text" class="form-control" required name="name" value="{{$row->name}}" >
        </div>
                
        <div class="form-group">
            <label for="">Icon</label>
            <select name="icon" id='list-icon' required class="select2 form-control">
                @foreach($fontawesome as $f)
					<option value='fa fa-{{$f}}' {{ ($row->icon == "fa fa-$f")?"selected":"" }} data-label='{{$f}}'>{{$f}}</option>
                    
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="">Module Slug</label>
            <input type="text" class="form-control" required name="path" value="{{$row->path}}" >
            <div class="help-block">Please alpha numeric only, without space instead _ and or special character</div>
        </div>
    </div>
    <div class="box-footer">
        
            <input checked type='checkbox' name='create_menu' value='1'/> Also create menu for this module <a href='#' title='If you check this, we will create the menu for this module'>(?)</a>
        
        <div class='pull-right'>
            <a class='btn btn-default' href='{{Route("ModulsControllerGetIndex")}}'> {{trans('crudbooster.button_back')}}</a> 
            <input type="submit" class="btn btn-primary" value="Step 2 &raquo;">
        </div>
    </div>
    </form>
</div>


@endsection

@push('javascript')

	<script src="{{asset('vendor/crudbooster/assets/select2/dist/js/select2.full.min.js')}}"></script>
	
	<script>

		$(function() {
			$('select[name=table]').change(function() {
				var v = $(this).val().replace(".", "_");
				$.get("{{CRUDBooster::mainpath('check-slug')}}/"+v,function(resp) {
					if(resp.total==0) {
						$('input[name=path]').val(v);
					}    else{
						v = v+resp.lastid;
						$('input[name=path]').val(v);
					}
				})
				
			})	
		})
		
		$(function() {

		  function format(icon) {          
			  var originalOption = icon.element;
			  var label = $(originalOption).text();
			  var val = $(originalOption).val();
			  if(!val) return label;
			  var $resp = $('<span><i style="margin-top:5px" class="pull-right ' + $(originalOption).val() + '"></i> ' + $(originalOption).data('label') + '</span>');
			  return $resp;
		  }

		  $('#list-icon').select2({
			  width: "100%",
			  templateResult: format,
			  templateSelection: format
		  });

	  })  
		
	</script>
	
@endpush