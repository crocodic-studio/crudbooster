<?php $name_column = $name.$col['name']; ?>

<div id='{{$name_column}}' class="input-group">
    <input type="hidden" class="input-id">
    <input type="text" class="form-control input-label {{$col['required']?"required":""}}" readonly>
    <span class="input-group-btn">
        <button class="btn btn-primary" id="btn-upload-{{$name_column}}"
                onclick="showFakeUpload{{$name_column}}()" type="button">
            <i class='fa fa-search'></i> {{cbTrans('datamodal_browse_file')}}
        </button>
    </span>
</div><!-- /input-group -->

<div id="loading-{{$name_column}}" class='text-info'
     style="display: none">
    <i class='fa fa-spin fa-spinner'></i> {{cbTrans('text_loading')}}
</div>

<input type="file" id='fake-upload-{{$name_column}}'
       style="display: none">
@push('bottom')
    <script type="text/javascript">
        var file;
        var filename;
        var is_uploading = false;

        function showFakeUpload {{$name_column}}() {
            if (is_uploading) {
                return false;
            }

            $('#fake-upload-{{$name_column}}').click();
        }

        // Add events
        $('#fake-upload-{{$name_column}}').on('change', prepareUpload{{$name_column}});

        // Grab the files and set them to our variable
        function prepareUpload {{$name_column}}(event) {
            var max_size = {{ ($col['max'])?:2000 }};
            file = event.target.files[0];

            var filesize = Math.round(parseInt(file.size) / 1024);

            if (filesize > max_size) {
                sweetAlert('{{cbTrans("alert_warning")}}', '{{cbTrans("your_file_size_is_too_big")}}', 'warning');
                return false;
            }

            filename = $('#fake-upload-{{$name_column}}').val().replace(/C:\\fakepath\\/i, '');
            var extension = filename.split('.').pop().toLowerCase();
            var img_extension = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
            var available_extension = "{{cbConfig('UPLOAD_TYPES')}}".split(",");
            var is_image_only = {{ ($col['upload_type'] == 'image')?"true":"false" }};

            if (is_image_only) {
                if ($.inArray(extension, img_extension) == -1) {
                    sweetAlert('{{cbTrans("alert_warning")}}', '{{cbTrans("your_file_extension_is_not_allowed")}}', 'warning');
                    return false;
                }
            } else {
                if ($.inArray(extension, available_extension) == -1) {
                    sweetAlert('{{cbTrans("alert_warning")}}', '{{cbTrans("your_file_extension_is_not_allowed")}}!', 'warning');
                    return false;
                }
            }


            $('#{{$name_column}} .input-label').val(filename);

            $('#loading-{{$name_column}}').fadeIn();
            $('#btn-add-table-{{$name}}').addClass('disabled');
            $('#btn-upload-{{$name_column}}').addClass('disabled');
            is_uploading = true;

            //Upload File To Server
            uploadFiles{{$name_column}}(event);
        }

        function uploadFiles {{$name_column}}(event) {
            event.stopPropagation(); // Stop stuff happening
            event.preventDefault(); // Totally stop stuff happening

            // START A LOADING SPINNER HERE

            // Create a formdata object and add the files
            var data = new FormData();
            data.append('userfile', file);

            $.ajax({
                url: '{{CRUDBooster::mainpath("upload-file")}}',
                type: 'POST',
                data: data,
                cache: false,
                processData: false, // Don't process the files
                contentType: false, // Set content type to false as jQuery will tell the server its a query string request
                success: function (data, textStatus, jqXHR) {
                    console.log(data);
                    $('#btn-add-table-{{$name}}').removeClass('disabled');
                    $('#loading-{{$name_column}}').hide();
                    $('#btn-upload-{{$name_column}}').removeClass('disabled');
                    is_uploading = false;

                    var basename = data.split('/').reverse()[0];
                    $('#{{$name_column}} .input-label').val(basename);

                    $('#{{$name_column}} .input-id').val(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('#btn-add-table-{{$name}}').removeClass('disabled');
                    $('#btn-upload-{{$name_column}}').removeClass('disabled');
                    is_uploading = false;
                    // Handle errors here
                    console.log('ERRORS: ' + textStatus);
                    // STOP LOADING SPINNER
                    $('#loading-{{$name_column}}').hide();
                }
            });
        }
    </script>
@endpush
