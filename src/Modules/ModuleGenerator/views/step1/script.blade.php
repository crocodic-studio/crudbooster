<script>
    $(function () {
        $('.select2').select2();

    })
    $(function () {
        $('select[name=table]').change(function () {
            var v = $(this).val().replace(".", "_");
            $.get("{{CRUDBooster::mainpath('check-slug')}}/" + v, function (resp) {
                if (resp.total == 0) {
                    $('input[name=path]').val(v);
                } else {
                    v = v + resp.lastid;
                    $('input[name=path]').val(v);
                }
            })

        })
    })
</script>