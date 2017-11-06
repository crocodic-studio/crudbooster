<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}'
     style="{{@$formInput['style']}}">
    <label class='control-label col-sm-2'>{{$label}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>

    <div class="{{$col_width?:'col-sm-10'}}">

        <div id="{{$name}}"></div>
        <textarea name="{{$name}}" style="display:none"></textarea>

        @include('crudbooster::default._form_body.underField', ['help' => $formInput['help'], 'error' => $errors->first($name)])

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        console.log(document.getElementById('{{$name}}'));
        // Set an option globally
        JSONEditor.defaults.options.theme = 'bootstrap2';
        JSONEditor.plugins.select2.enable = false;
        JSONEditor.plugins.selectize.enable = true;//to avoid select2

        // Set an option during instantiation
        var editor = new JSONEditor(document.getElementById('{{$name}}'), {
            theme: 'bootstrap2',
            startval: <?=json_encode(json_decode($value, false))?>,
            schema: <?=json_encode(json_decode($formInput["options"]["schema"], false))?>
        });

        $('[name="{{$name}}"]').parents('form').on('submit', function () {
            $('[name="{{$name}}"]').val(JSON.stringify(editor.getValue()));
            return true;
        })
    });

</script>