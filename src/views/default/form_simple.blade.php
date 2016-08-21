<div id='form_simple_{{str_slug($table_name)}}' class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">{{$table_name}} Form's: {{(@$row)?"Edit ".$row->{$title_field}:"Add New Data"}}</h3>
                </div>


				<form method='post' id="form" enctype="multipart/form-data" action='@if (@!$row->id) {{route($controller_name.'PostAddSave')}} @else {{route($controller_name.'PostEditSave',['id'=>$row->id]) }}@endif'>
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="referal" value="{{$referal}}"/>
				<input type='hidden' name='ref_parameter' value='{{urldecode(http_build_query(@$_GET))}}'/>
				<input type='hidden' name='{{$parent_field}}' value='{{$parent_id}}'/>
                <div class="box-body">
                	@include("crudbooster::default.form_body")							
                </div><!-- /.box-body -->
				
                <div class="box-footer">	
                	<div class='pull-right'>														
					<a href='{{Request::url()}}' class='btn btn-default'>Cancel</a>
					@if( ($priv->is_create || $priv->is_edit) && count($forms)!=0 && $button_save)
					<button type='submit' class='btn btn-success btn-form-save' title='Save Data'><i class='fa fa-save'></i> Save</button> 					
					@endif
					</div>
                </div><!-- /.box-footer-->

                </form>
				
            </div><!-- /.box -->