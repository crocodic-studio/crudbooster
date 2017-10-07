<label>{{ cbTrans('set_as_superadmin')}}</label><br/>
<div id='set_as_superadmin' class='radio-inline'>
	<label class="radio-inline">
		<input required {{ ($role->is_superadmin==1)?'checked':'' }} type='radio' name='is_superadmin' value='1'> {{cbTrans('confirmation_yes')}}
	</label>
	<label class="radio-inline">
		<input {{ ($role->is_superadmin==0)?'checked':'' }} type='radio' name='is_superadmin' value='0'> {{cbTrans('confirmation_no')}}
	</label>
</div>
<div class="text-danger">{{ $errors->first('is_superadmin') }}</div>
