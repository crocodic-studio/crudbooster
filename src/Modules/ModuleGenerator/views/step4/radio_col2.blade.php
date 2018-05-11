<div class="row">

    <div class="col-sm-12">
        <div class="form-group">
            <label>Add Button</label>
            <label class='radio-inline'>
                <input {{($config['button_add'])?"checked":""}} type='radio' name='button_add'
                       value='true'/> True
            </label>
            <label class='radio-inline'>
                <input {{(!$config['button_add'])?"checked":""}} type='radio' name='button_add'
                       value='false'/> False
            </label>
        </div>
    </div>

    <div class="col-sm-12">
        <div class="form-group">
            <label>Edit Button</label>
            <label class='radio-inline'>
                <input {{($config['button_edit'])?"checked":""}} type='radio' name='button_edit'
                       value='true'/> True
            </label>
            <label class='radio-inline'>
                <input {{(!$config['button_edit'])?"checked":""}} type='radio'
                       name='button_edit' value='false'/> False
            </label>
        </div>
    </div>

    <div class="col-sm-12">
        <div class="form-group">
            <label>Delete Button</label>
            <label class='radio-inline'>
                <input {{($config['button_delete'])?"checked":""}} type='radio'
                       name='button_delete' value='true'/> True
            </label>
            <label class='radio-inline'>
                <input {{(!$config['button_delete'])?"checked":""}} type='radio'
                       name='button_delete' value='false'/> False
            </label>
        </div>
    </div>


    <div class="col-sm-12">
        <div class="form-group">
            <label>Detail Button</label>
            <label class='radio-inline'>
                <input {{($config['button_detail'])?"checked":""}} type='radio'
                       name='button_detail' value='true'/> True
            </label>
            <label class='radio-inline'>
                <input {{(!$config['button_detail'])?"checked":""}} type='radio'
                       name='button_detail' value='false'/> False
            </label>
        </div>
    </div>


</div>


