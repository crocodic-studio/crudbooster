@extends('admin/admin_template')

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
				<form method='post' action='{{ (@$row->id)?action("PrivilegesController@postEditSave")."/$row->id":action("PrivilegesController@postAddSave") }}'>
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="box-body">
					<div class='form-group'>
						<label>Nama</label>
						<input type='text' class='form-control' name='name' value='{{ @$row->name }}'/>
						<div class="text-danger">{{ $errors->first('name') }}</div>
					</div>

					<div class='form-group'>
						<label>Is Superadmin</label>
						<select name='is_superadmin' required class='form-control'>
							<option value=''>** Select Type</option>
							<option {{ (@$row->is_superadmin==1)?"selected":"" }} value='1'>YES</option>
							<option {{ (@$row->is_superadmin==0)?"selected":"" }} value='0'>NO</option>
						</select>
						<div class="text-danger">{{ $errors->first('is_superadmin') }}</div>
					</div>

					<div class='form-group'>
						<label>Filter Field</label>
						<input type='text' class='form-control' name='filter_field' value='{{ @$row->filter_field }}'/>
						<div class="text-danger">{{ $errors->first('filter_field') }}</div>
						<div class='help-block'>Isi dengan nama kolom user yang ingin di filter untuk tampil data</div>
					</div>


					<div class='form-group'>
						<label>Theme Color</label>
						<select name='theme_color' class='form-control'>
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
						<label>Privileges</label>
						<script>
							$(function() {
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
						<table class='table table-striped'>
							<thead>
								<tr class='active'>
									<th width='3%'>No.</th><th width='60%'>Nama Modul</th><th>&nbsp;</th><th>Create</th><th>Read</th><th>Update</th><th>Delete</th>
								</tr>
								<tr class='active'>
									<th>&nbsp;</th>
									<th>&nbsp;</th>
									<th>&nbsp;</th>
									<th><input title='Check all vertical' type='checkbox' id='is_create'/></th>
									<th><input title='Check all vertical' type='checkbox' id='is_read'/></th>
									<th><input title='Check all vertical' type='checkbox' id='is_edit'/></th>
									<th><input title='Check all vertical' type='checkbox' id='is_delete'/></th>
								</tr>
							</thead>
							<tbody>
								<?php $no = 1;?>
								@foreach($moduls as $modul)
								<tr>
									<td><?php echo $no++;?></td>
									<td>{{$modul->name}}</td>
									<td><input type='checkbox' title='Check All Horizontal' <?=($modul->is_create && $modul->is_read && $modul->is_edit && $modul->is_delete)?"checked":""?> class='select_horizontal'/></td>
									<td><input type='checkbox' class='is_create' name='privileges[<?=$modul->id?>][is_create]' <?=@$modul->is_create?"checked":""?> value='1'/></td>
									<td><input type='checkbox' class='is_read' name='privileges[<?=$modul->id?>][is_read]' <?=@$modul->is_read?"checked":""?> value='1'/></td>									
									<td><input type='checkbox' class='is_edit' name='privileges[<?=$modul->id?>][is_edit]' <?=@$modul->is_edit?"checked":""?> value='1'/></td>
									<td><input type='checkbox' class='is_delete' name='privileges[<?=$modul->id?>][is_delete]' <?=@$modul->is_delete?"checked":""?> value='1'/></td>
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