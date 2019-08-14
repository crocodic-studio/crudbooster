<div class="upload-wrapper">
    <div class="upload-preview" style="margin-bottom: 10px">
        @if($value)
            <a href="{{ asset($value) }}" target="_blank" data-lightbox="thumbnail">
                <img class="img-thumbnail" src="{{ asset($value) }}" style="max-width: 250px" alt="Preview Image">
            </a> &nbsp;
            <a href="javascript:;" class="btn btn-danger" onclick="deleteImage{{$name}}(this, '{{ $required?1:0 }}')" title="Delete this image"><i class="fa fa-trash"></i></a>
        @endif
    </div>

    <div class="upload-button">
        <input style="display: none" type='file'
               accept="image/*"
               id="{{ $name }}"
               title="{{ $label }}"
               {{ (!$value && $required)?'required':''}}
               class='form-control' onchange="uploadImage{{$name}}(this, '{{ $name }}', '{{ $required?1:0 }}','{{ $encrypt }}', '{{ $resizeWidth }}','{{ $resizeHeight }}')"/>

        <div class="input-group" style="width: 350px">
            <input type="text" name="{{ $name }}" value="{{ $value }}" placeholder="Choose an image file..." class="form-control" readonly >
            <span class="input-group-btn">
              <button class="btn btn-primary" onclick="showUploadWindow{{$name}}(this)" type="button"><i class="fa fa-upload"></i> Choose Image</button>
            </span>
        </div><!-- /input-group -->
        <div class="help-block">File format supported: {{ implode(", ",cbConfig("UPLOAD_IMAGE_EXTENSION_ALLOWED")) }}</div>
    </div>
</div>

<script>
    function showUploadWindow{{$name}}(t) {
        $(t).parents(".upload-button").find("input[type=file]").click()
    }
    function deleteImage{{$name}}(t,is_required) {
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
                    h.parents(".upload-wrapper").find("input[name={{$name}}]").val(null);
                    h.parents(".upload-preview").empty();
                }
            });
    }
    function uploadImage{{$name}}(t, target_input_name, is_required, encrypt, width, height) {
        if(!t.files[0]) return false;

        $(t).parents('.upload-wrapper').find('.upload-preview').html("<p><i class='fa fa-spin fa-spinner'></i> Please wait uploading...</p>");
        var formData = new FormData();
        formData.append('userfile', t.files[0]);
        formData.append("encrypt", encrypt);
        formData.append("resize_width", width);
        formData.append("resize_height", height);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            @if(request()->segment(1)=="developer")
            url: "{{ cb()->getDeveloperUrl('upload-image') }}",
            @else
            url: "{{ cb()->getAdminUrl('upload-image') }}",
            @endif
            type: "POST",
            processData: false,
            contentType: false,
            data:formData,
            success:function (data) {

                $(t).parents('.upload-wrapper').find('.upload-preview').html("<a href='" + data.full_url + "' target='_blank' data-lightbox='preview-image' title='"+data.filename+"'>" +
                    "<img class='img-thumbnail' style='max-width:250px' src='"+data.full_url+"' title='Preview Image'/></a>" +
                    "<a href='javascript:;' class='btn btn-danger' onclick='deleteImage{{$name}}(this, "+is_required+")' title='Delete this image'><i class='fa fa-trash'></i></a>");
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