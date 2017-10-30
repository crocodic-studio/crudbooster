<div class="row">

    <div class="col-sm-12">
        <div class="form-group">
            <label>Global Privilege</label>
            <label class='radio-inline'>
                <input type='radio' name='global_privilege'
                       {{($config['global_privilege'])?"checked":""}} value='true'/> TRUE
            </label>
            <label class='radio-inline'>
                <input {{(!$config['global_privilege'])?"checked":""}} type='radio'
                       name='global_privilege' value='false'/> FALSE
            </label>
        </div>
    </div>

    <div class="col-sm-12">
        <div class="form-group">
            <label>Show Button Table Action</label>
            <label class='radio-inline'>
                <input {{($config['button_table_action'])?"checked":""}} type='radio'
                       name='button_table_action' value='true'/> TRUE
            </label>
            <label class='radio-inline'>
                <input {{(!$config['button_table_action'])?"checked":""}} type='radio'
                       name='button_table_action' value='false'/> FALSE
            </label>
        </div>
    </div>

    <div class="col-sm-12">
        <div class="form-group">
            <label>Show Bulk Action Button</label>

            <label class='radio-inline'>
                <input {{($config['button_bulk_action'])?"checked":""}} type='radio'
                       name='button_bulk_action' value='true'/> TRUE
            </label>

            <label class='radio-inline'>
                <input {{(!$config['button_bulk_action'])?"checked":""}} type='radio'
                       name='button_bulk_action' value='false'/> FALSE
            </label>

        </div>
    </div>

    <div class="col-sm-12">
        <div class="form-group">
            <label>Button Action Style</label>

            <label class='radio-inline'>
                <input {{($config['button_action_style']=='button_icon')?"checked":""}} type='radio'
                       name='button_action_style' value='button_icon'/> Icon
            </label>

            <label class='radio-inline'>
                <input {{($config['button_action_style']=='button_icon_text')?"checked":""}} type='radio'
                       name='button_action_style' value='button_icon_text'/> Icon & Text
            </label>

            <label class='radio-inline'>
                <input {{($config['button_action_style']=='button_text')?"checked":""}} type='radio'
                       name='button_action_style' value='button_text'/> Button Text
            </label>

            <label class='radio-inline'>
                <input {{($config['button_action_style']=='button_dropdown')?"checked":""}} type='radio'
                       name='button_action_style' value='button_dropdown'/> Dropdown
            </label>

        </div>
    </div>


</div>
