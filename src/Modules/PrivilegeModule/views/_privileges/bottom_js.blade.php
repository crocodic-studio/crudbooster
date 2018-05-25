<script>
    $(function () {
        $("#is_visible").click(function () {
            var is_ch = $(this).prop('checked');
            console.log('is checked create ' + is_ch);
            $(".is_visible").prop("checked", is_ch);
            console.log('Create all');
        })
        $("#can_create").click(function () {
            var is_ch = $(this).prop('checked');
            console.log('is checked create ' + is_ch);
            $(".can_create").prop("checked", is_ch);
            console.log('Create all');
        })
        $("#can_read").click(function () {
            var is_ch = $(this).is(':checked');
            $(".can_read").prop("checked", is_ch);
        })
        $("#can_edit").click(function () {
            var is_ch = $(this).is(':checked');
            $(".can_edit").prop("checked", is_ch);
        })
        $("#can_delete").click(function () {
            var is_ch = $(this).is(':checked');
            $(".can_delete").prop("checked", is_ch);
        })
        $(".select_horizontal").click(function () {
            var p = $(this).parents('tr');
            var is_ch = $(this).is(':checked');
            p.find("input[type=checkbox]").prop("checked", is_ch);
        })
    })
</script>