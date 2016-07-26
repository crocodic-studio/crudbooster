<div id='form_simple_{{str_slug($table_name)}}' class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">{{$table_name}} Form's: {{(@$row)?"Edit ".$row->{$titlefield}:"Add New Data"}}</h3>
                </div>

                <iframe id="iframe_upload_fake" name="iframe_upload_fake" style="display:none"></iframe>
				<form id="form_upload_fake" action="{{ url($mainpath.'/upload') }}" target="iframe_upload_fake" method="post" enctype="multipart/form-data" style="width:0px;height:0;overflow:hidden">
				    <input type="hidden" name="_token" value="{{ csrf_token() }}">
				    <input name="userfile" type="file" onchange="$('#form_upload_fake').submit();this.value='';">
				</form>


				<form method='post' id="form" enctype="multipart/form-data" action='@if (@!$row->id) {{action($controller_name.'@postAddSave')}} @else {{action($controller_name.'@postEditSave')."/$row->id" }}@endif'>
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="referal" value="{{$referal}}"/>
				<input type='hidden' name='ref_parameter' value='{{urldecode(http_build_query(@$_GET))}}'/>
				<input type='hidden' name='{{$parent_field}}' value='{{$parent_id}}'/>
                <div class="box-body">
                	@include("admin.default.form_body")							
                </div><!-- /.box-body -->
				
                <div class="box-footer">	
                	<div class='pull-right'>														
					<a href='{{Request::url()}}' class='btn btn-default'>Cancel</a>
					@if( ($priv->is_create || $priv->is_edit) && count($forms)!=0)
					<button type='submit' class='btn btn-success btn-form-save' title='Save Data'><i class='fa fa-save'></i> Save</button> 					
					@endif
					</div>
                </div><!-- /.box-footer-->

                </form>
				
            </div><!-- /.box -->