<div id="{{$name}}"></div>

<script type="text/javascript">
    $(document).ready(function () {
        console.log(document.getElementById('{{$name}}'));
        // Set an option globally
        JSONEditor.defaults.options.theme = 'bootstrap2';

        // Set an option during instantiation
        var editor = new JSONEditor(document.getElementById('{{$name}}'), {
            theme: 'bootstrap2',
            disable_array_add: true,
            disable_array_delete: true,
            disable_array_reorder: true,
            disable_collapse: true,
            disable_edit_json: true,
            disable_properties: true,
            startval: <?=json_encode(json_decode($value, false))?>,
            schema: <?=json_encode(json_decode($form["schema"], false))?>
        });
        editor.disable();
    });

</script>