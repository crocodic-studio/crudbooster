<div class="row">

    <div class="col-sm-12">
        <div class="form-group">
            <label>Show Button Show Data</label>
            <label class='radio-inline'>
                <input {{($config['button_show'])?"checked":""}} type='radio' name='button_show'
                       value='true'/> TRUE
            </label>
            <label class='radio-inline'>
                <input {{(!$config['button_show'])?"checked":""}} type='radio'
                       name='button_show' value='false'/> FALSE
            </label>
        </div>
    </div>

    <div class="col-sm-12">
        <div class="form-group">
            <label>Show Button Filter & Sorting</label>
            <label class='radio-inline'>
                <input {{($config['button_filter'])?"checked":""}} type='radio'
                       name='button_filter' value='true'/> TRUE
            </label>
            <label class='radio-inline'>
                <input {{(!$config['button_filter'])?"checked":""}} type='radio'
                       name='button_filter' value='false'/> FALSE
            </label>
        </div>
    </div>

    <div class="col-sm-12">
        <div class="form-group">
            <label>Show Button Import</label>
            <label class='radio-inline'>
                <input {{($config['button_import'])?"checked":""}} type='radio'
                       name='button_import' value='true'/> TRUE
            </label>
            <label class='radio-inline'>
                <input {{(!$config['button_import'])?"checked":""}} type='radio'
                       name='button_import' value='false'/> FALSE
            </label>
        </div>
    </div>

    <div class="col-sm-12">
        <div class="form-group">
            <label>Show Button Export</label>
            <label class='radio-inline'>
                <input {{($config['button_export'])?"checked":""}} type='radio'
                       name='button_export' value='true'/> TRUE
            </label>
            <label class='radio-inline'>
                <input {{(!$config['button_export'])?"checked":""}} type='radio'
                       name='button_export' value='false'/> FALSE
            </label>
        </div>
    </div>
</div>
                    