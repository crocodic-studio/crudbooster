@push('bottom')
    <script>
        function uploadImage(t, target_input_name) {
            $(t).parents('.upload-wrapper').find('.upload-preview').html("<i class='fa fa-spin fa-spinner'></i> Please wait uploading...");
            var formData = new FormData();
            formData.append('userfile', t.files[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                url: "{{ module()->url('upload-file') }}",
                type: "POST",
                processData: false,
                contentType: false,
                data:formData,
                success:function (data) {
                    $(t).parents('.upload-wrapper').find('.upload-preview').html("<a href='" + data.full_url + "' target='_blank' title='"+data.filename+"'>" +
                        "<i class='fa fa-download'></i> Download "+data.filename+"</a>");
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