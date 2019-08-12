@push('bottom')
    <script>
        function deleteFile(t,is_required) {
            let h = $(t);

            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this file!\n(You need to save the form to take the action)",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) {
                        if(is_required == 1) {
                            h.parents(".upload-wrapper").find("input[type=file]").prop("required", true);
                        }
                        h.parents(".upload-wrapper").find("input[type=file]").val(null);

                        h.parents(".upload-wrapper").find("input[type=hidden]").val(null);
                        h.parents(".upload-preview").empty();
                    }
                });

        }
        function uploadFile(t, target_input_name, is_required, encrypt) {
            if(!t.files[0]) return false;

            $(t).parents('.upload-wrapper').find('.upload-preview').html("<i class='fa fa-spin fa-spinner'></i> Please wait uploading...");
            var formData = new FormData();
            formData.append('userfile', t.files[0]);
            formData.append("encrypt", encrypt);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                url: "{{ cb()->getAdminUrl('upload-file') }}",
                type: "POST",
                processData: false,
                contentType: false,
                data:formData,
                success:function (data) {
                    $(t).parents('.upload-wrapper').find('.upload-preview').html("<a href='" + data.full_url + "' target='_blank' title='"+data.filename+"'>" +
                        "<i class='fa fa-download'></i> Download "+data.filename+"</a> " +
                        "<a href='javascript:;' class='btn btn-xs btn-danger' onclick='deleteFile(this, "+is_required+")' title='Delete this file'><i class='fa fa-trash'></i></a>");
                    $('input[name=' + target_input_name + ']').val(data.url);
                },
                error:function (jqXHR, textStatus, errorThrown) {
                    swal("Oops", errorThrown, "warning");
                },
                done: function () {
                    $(t).val(null);
                    $(t).parents('.upload-wrapper').find('.upload-preview').empty();
                }
            });
        }
    </script>
@endpush