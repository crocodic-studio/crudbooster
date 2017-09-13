<div class="row">

    <div class="col-sm-12">
        <div class="form-group">
            <label>Show Button Add</label>
            <label class='radio-inline'>
                <input {{($config['button_add'])?"checked":""}} type='radio' name='button_add'
                       value='true'/> TRUE
            </label>
            <label class='radio-inline'>
                <input {{(!$config['button_add'])?"checked":""}} type='radio' name='button_add'
                       value='false'/> FALSE
            </label>
        </div>
    </div>

    <div class="col-sm-12">
        <div class="form-group">
            <label>Show Button Edit</label>
            <label class='radio-inline'>
                <input {{($config['button_edit'])?"checked":""}} type='radio' name='button_edit'
                       value='true'/> TRUE
            </label>
            <label class='radio-inline'>
                <input {{(!$config['button_edit'])?"checked":""}} type='radio'
                       name='button_edit' value='false'/> FALSE
            </label>
        </div>
    </div>

    <div class="col-sm-12">
        <div class="form-group">
            <label>Show Button Delete</label>
            <label class='radio-inline'>
                <input {{($config['button_delete'])?"checked":""}} type='radio'
                       name='button_delete' value='true'/> TRUE
            </label>
            <label class='radio-inline'>
                <input {{(!$config['button_delete'])?"checked":""}} type='radio'
                       name='button_delete' value='false'/> FALSE
            </label>
        </div>
    </div>


    <div class="col-sm-12">
        <div class="form-group">
            <label>Show Button Detail</label>
            <label class='radio-inline'>
                <input {{($config['button_detail'])?"checked":""}} type='radio'
                       name='button_detail' value='true'/> TRUE
            </label>
            <label class='radio-inline'>
                <input {{(!$config['button_detail'])?"checked":""}} type='radio'
                       name='button_detail' value='false'/> FALSE
            </label>
        </div>
    </div>


</div>


