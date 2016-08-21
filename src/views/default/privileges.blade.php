@extends('crudbooster::admin_template')

@section('content')
	
    <div class='row'>
        <div class='col-md-12'> 			
			
		
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ $page_title }}</h3>
                    <div class="box-tools">
			
                    </div>
                </div>
				<form method='post' action='{{ (@$row->id)?route("PrivilegesControllerPostEditSave")."/$row->id":route("PrivilegesControllerPostAddSave") }}'>
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="box-body">
					<div class='form-group'>
						<label>Privilege Name</label>
						<input type='text' class='form-control' name='name' required value='{{ @$row->name }}'/>
						<div class="text-danger">{{ $errors->first('name') }}</div>
					</div>

					<div class='form-group'>
						<label>Set as Superadmin</label><br/>
						<div class='radio inline'>
							<label><input {{ (@$row->is_superadmin==1)?'checked':'' }} type='radio' name='is_superadmin' value='1'/> Yes</label> &nbsp;&nbsp;
							<label><input {{ (@$row->is_superadmin==0)?'checked':'checked' }} type='radio' name='is_superadmin' value='0'/> No</label>
						</div>
						<div class="text-danger">{{ $errors->first('is_superadmin') }}</div>
					</div>

					<div class='form-group'>
						<label>Available To Register</label><br/>
						<div class='radio inline'>
							<label><input {{ (@$row->is_register==1)?'checked':'' }} type='radio' name='is_register' value='1'/> Yes, Show the Registration Form</label> &nbsp;&nbsp;
							<label><input {{ (@$row->is_register==0)?'checked':'checked' }} type='radio' name='is_register' value='0'/> No</label>
						</div>
						<div class="text-danger">{{ $errors->first('is_register') }}</div>
					</div>

					<div class='form-group'>
						<label>Theme Color</label>
						<select name='theme_color' class='form-control' required>
							<option value=''>** Choose Backend Theme Color</option>
							<?php 
								$skins = array('skin-blue','skin-blue-light','skin-yellow','skin-yellow-light','skin-green','skin-green-light','skin-purple','skin-purple-light','skin-red','skin-red-light','skin-black','skin-black-light');
								foreach($skins as $skin):
							?>
							<option <?=(@$row->theme_color==$skin)?"selected":""?> value='<?=$skin?>'><?=ucwords(str_replace('-',' ',$skin))?></option>
							<?php endforeach;?>
						</select>
						<div class="text-danger">{{ $errors->first('theme_color') }}</div>			
						<script type="text/javascript">
							$(function() {
								$("select[name=theme_color]").change(function() {
									var n = $(this).val();
									$("body").attr("class",n);
								})
							})
						</script>								
					</div>
	
					<div class='form-group'>
						<label>Privileges Configuration</label>
						<script>
							$(function() {
								$("#is_visible").click(function() {
									var is_ch = $(this).prop('checked');
									console.log('is checked create '+is_ch);
									$(".is_visible").prop("checked",is_ch);
									console.log('Create all');
								})
								$("#is_create").click(function() {
									var is_ch = $(this).prop('checked');
									console.log('is checked create '+is_ch);
									$(".is_create").prop("checked",is_ch);
									console.log('Create all');
								})
								$("#is_read").click(function() {
									var is_ch = $(this).is(':checked');
									$(".is_read").prop("checked",is_ch);
								})
								$("#is_edit").click(function() {
									var is_ch = $(this).is(':checked');
									$(".is_edit").prop("checked",is_ch);
								})
								$("#is_delete").click(function() {
									var is_ch = $(this).is(':checked');
									$(".is_delete").prop("checked",is_ch);
								})
								$(".select_horizontal").click(function() {
									var p = $(this).parents('tr');
									var is_ch = $(this).is(':checked');
									p.find("input[type=checkbox]").prop("checked",is_ch);
								})
							})
						</script>
						<table class='table table-striped table-hover'>
							<thead>
								<tr class='active'>
									<th width='3%'>No.</th><th width='60%'>Module's Name</th><th>&nbsp;</th><th>Visible</th><th>Create</th><th>Read</th><th>Update</th><th>Delete</th>
								</tr>
								<tr class='active'>
									<th>&nbsp;</th>
									<th>&nbsp;</th>
									<th>&nbsp;</th>
									<td align="center"><input title='Check all vertical' type='checkbox' id='is_visible'/></td>
									<td align="center"><input title='Check all vertical' type='checkbox' id='is_create'/></td>
									<td align="center"><input title='Check all vertical' type='checkbox' id='is_read'/></td>
									<td align="center"><input title='Check all vertical' type='checkbox' id='is_edit'/></td>
									<td align="center"><input title='Check all vertical' type='checkbox' id='is_delete'/></td>
								</tr>
							</thead>
							<tbody>
								<?php $no = 1;?>
								@foreach($moduls as $modul)
								<tr>
									<td><?php echo $no++;?></td>
									<td>{{$modul->name}}</td>
									<td align="center"><input type='checkbox' title='Check All Horizontal' <?=($modul->is_create && $modul->is_read && $modul->is_edit && $modul->is_delete)?"checked":""?> class='select_horizontal'/></td>
									<td align="center"><input type='checkbox' class='is_visible' name='privileges[<?=$modul->id?>][is_visible]' <?=@$modul->is_visible?"checked":""?> value='1'/></td>
									<td align="center"><input type='checkbox' class='is_create' name='privileges[<?=$modul->id?>][is_create]' <?=@$modul->is_create?"checked":""?> value='1'/></td>
									<td align="center"><input type='checkbox' class='is_read' name='privileges[<?=$modul->id?>][is_read]' <?=@$modul->is_read?"checked":""?> value='1'/></td>									
									<td align="center"><input type='checkbox' class='is_edit' name='privileges[<?=$modul->id?>][is_edit]' <?=@$modul->is_edit?"checked":""?> value='1'/></td>
									<td align="center"><input type='checkbox' class='is_delete' name='privileges[<?=$modul->id?>][is_delete]' <?=@$modul->is_delete?"checked":""?> value='1'/></td>
								</tr>
								@endforeach
							</tbody>
						</table>

					</div>

                </div><!-- /.box-body -->
                <div class="box-footer">					
					<button type='submit' class='btn btn-primary'><i class='fa fa-save'></i> Simpan</button>
                </div><!-- /.box-footer-->
            </div><!-- /.box -->
        </div><!-- /.col -->


    </div><!-- /.row -->
@endsection