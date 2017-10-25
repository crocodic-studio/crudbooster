<script>
    $(function () {
        $("#is_visible").click(function () {
            var is_ch = $(this).prop('checked');
            console.log('is checked create ' + is_ch);
            $(".is_visible").prop("checked", is_ch);
            console.log('Create all');
        })
        $("#is_create").click(function () {
            var is_ch = $(this).prop('checked');
            console.log('is checked create ' + is_ch);
            $(".is_create").prop("checked", is_ch);
            console.log('Create all');
        })
        $("#is_read").click(function () {
            var is_ch = $(this).is(':checked');
            $(".is_read").prop("checked", is_ch);
        })
        $("#is_edit").click(function () {
            var is_ch = $(this).is(':checked');
            $(".is_edit").prop("checked", is_ch);
        })
        $("#is_delete").click(function () {
            var is_ch = $(this).is(':checked');
            $(".is_delete").prop("checked", is_ch);
        })
        $(".select_horizontal").click(function () {
            var p = $(this).parents('tr');
            var is_ch = $(this).is(':checked');
            p.find("input[type=checkbox]").prop("checked", is_ch);
        })
    })
</script>