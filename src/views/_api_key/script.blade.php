<script>
    var lastno = {!! $no + 1 !!};

    function generate_screet_key() {
        $.get("{!! route('AdminApiGeneratorControllerGetGenerateScreetKey') !!}", function (resp) {
            lastno += 1;
            $('#table-apikey').append("<tr><td>" + lastno + "</td><td>" + resp.key + "</td><td>0</td><td><span class='label label-success'>Active</span></td><td>" +
                "<a class='btn btn-xs btn-default' href='{{CRUDBooster::mainpath("status-apikey")}}?id=" + resp.id + "&status=0'>Non Active</a> <a class='btn btn-xs btn-danger' href='javascript:void(0)' onclick='deleteApi(" + resp.id + ")'>Delete</a> </td></tr>"
            );
            $('.no-screetkey').remove();
            swal("Success!", "Your new screet key has been generated successfully", "success");
        })
    }

    function deleteApi(id) {
        swal({
            title: "Are you sure ?",
            text: "You will not be able to recover this data!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: false
        }, function () {
            $.get("{{CRUDBooster::mainpath('delete-api-key')}}?id=" + id, function (resp) {
                if (resp.status == 1) {
                    swal("Success!", "The screet key has been deleted !", "success");
                } else {
                    swal("Upps!", "The screet key can't delete !", "warning");
                }
                location.href = document.location.href;
            })
        })
    }
</script>