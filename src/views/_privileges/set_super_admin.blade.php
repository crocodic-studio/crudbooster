<label>{{ cbTrans('set_as_superadmin')}}</label><br/>
<div id='set_as_superadmin' class='radio inline'>
    <label>
        <input required {{ ($privilege->is_superadmin==1)?'checked':'' }} type='radio' name='is_superadmin' value='1'/>
        {{ cbTrans('confirmation_yes')}}
    </label>
    &nbsp;&nbsp;
    <label>
        <input {{ ($privilege->is_superadmin==0)?'checked':'' }} type='radio' name='is_superadmin' value='0'/>
        {{ cbTrans('confirmation_no')}}
    </label>
</div>
<div class="text-danger">{{ $errors->first('is_superadmin') }}</div>