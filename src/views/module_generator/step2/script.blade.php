<script type="text/javascript">
    var currentInputCallback = null;
    var hookQueryIndex = null;
    var hookRowIndexEditor = null;
    var hookBeforeAdd = null;
    var hookAfterAdd = null;
    var hookBeforeEdit = null;
    var hookAfterEdit = null;
    var hookBeforeDelete = null;
    var hookAfterDelete = null;
    var textareaCallback = null;

    $(function () {

        hookQueryIndexEditor = CodeMirror.fromTextArea(document.getElementById('textarea-hookqueryindex'), {
            lineNumbers: true,
            matchBrackets: true,
            mode: "text/x-php",
            indentUnit: 4,
            indentWithTabs: true,
            theme: 'blackboard',
            keyMap: "sublime"
        });
        hookQueryIndexEditor.setSize(null, 250);

        hookRowIndexEditor = CodeMirror.fromTextArea(document.getElementById('textarea-hookrowindex'), {
            lineNumbers: true,
            matchBrackets: true,
            mode: "text/x-php",
            indentUnit: 4,
            indentWithTabs: true,
            theme: 'blackboard',
            keyMap: "sublime"
        });
        hookRowIndexEditor.setSize(null, 250);

        hookBeforeAdd = CodeMirror.fromTextArea(document.getElementById('textarea-hookBeforeAdd'), {
            lineNumbers: true,
            matchBrackets: true,
            mode: "text/x-php",
            indentUnit: 4,
            indentWithTabs: true,
            theme: 'blackboard',
            keyMap: "sublime"
        });
        hookBeforeAdd.setSize(null, 250);

        hookAfterAdd = CodeMirror.fromTextArea(document.getElementById('textarea-hookAfterAdd'), {
            lineNumbers: true,
            matchBrackets: true,
            mode: "text/x-php",
            indentUnit: 4,
            indentWithTabs: true,
            theme: 'blackboard',
            keyMap: "sublime"
        });
        hookAfterAdd.setSize(null, 250);

        hookBeforeEdit = CodeMirror.fromTextArea(document.getElementById('textarea-hookBeforeEdit'), {
            lineNumbers: true,
            matchBrackets: true,
            mode: "text/x-php",
            indentUnit: 4,
            indentWithTabs: true,
            theme: 'blackboard',
            keyMap: "sublime"
        });
        hookBeforeEdit.setSize(null, 250);

        hookAfterEdit = CodeMirror.fromTextArea(document.getElementById('textarea-hookAfterEdit'), {
            lineNumbers: true,
            matchBrackets: true,
            mode: "text/x-php",
            indentUnit: 4,
            indentWithTabs: true,
            theme: 'blackboard',
            keyMap: "sublime"
        });
        hookAfterEdit.setSize(null, 250);

        hookBeforeDelete = CodeMirror.fromTextArea(document.getElementById('textarea-hookBeforeDelete'), {
            lineNumbers: true,
            matchBrackets: true,
            mode: "text/x-php",
            indentUnit: 4,
            indentWithTabs: true,
            theme: 'blackboard',
            keyMap: "sublime"
        });
        hookBeforeDelete.setSize(null, 250);

        hookAfterDelete = CodeMirror.fromTextArea(document.getElementById('textarea-hookAfterDelete'), {
            lineNumbers: true,
            matchBrackets: true,
            mode: "text/x-php",
            indentUnit: 4,
            indentWithTabs: true,
            theme: 'blackboard',
            keyMap: "sublime"
        });
        hookAfterDelete.setSize(null, 250);

        textareaCallback = CodeMirror.fromTextArea(document.getElementById('textareaCallback'), {
            mode: "text/x-php",
            lineNumbers: true,
            theme: 'blackboard',
            keyMap: "sublime"
        });
        textareaCallback.setSize(null, 100);


        $(document).on('click', '.btn-callback', function () {
            var callbackValue = $(this).parent().find('input').val();
            currentInputCallback = $(this).parent().find('input');
            $('#modal-callback').modal('show');
            $('#modal-callback').on('shown.bs.modal', function (e) {
                textareaCallback.setValue(callbackValue);
                textareaCallback.refresh();
            })
        })
    })

    function saveModalCallback() {
        currentInputCallback.val(textareaCallback.getValue());
        textareaCallback.setValue('');
        $('#modal-callback').modal('hide');
    }
</script>