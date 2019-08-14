<div class="upload-wrapper">
    <div class="upload-preview" style="padding: 5px">
        @if($value)
            <a href="{{ asset($value) }}" target="_blank" title="Download file"><i class="fa fa-download"></i> Download {{ basename($value) }}</a>
            <a href="javascript:;" class="btn btn-xs btn-danger" onclick="deleteFile{{$name}}(this, '{{ $required?1:0 }}')" title="Delete this file"><i class="fa fa-trash"></i></a>
        @endif
    </div>

    <div class="upload-button">
        <input style="display: none" type='file'
               id="{{ $name }}"
               title="{{ $label }}"
               {{ (!$value && $required)?'required':''}}
               class='form-control' onchange="uploadFile{{$name}}(this, '{{ $name }}', '{{ $required?1:0 }}','{{ $encrypt }}', null, null)"/>

        <div class="input-group" style="width: 350px">
            <input type="text" name="{{ $name }}" value="{{ $value }}" placeholder="Choose a file..." class="form-control" readonly >
            <span class="input-group-btn">
              <button class="btn btn-primary" onclick="showUploadWindow{{$name}}(this)" type="button"><i class="fa fa-upload"></i> Choose File</button>
            </span>
        </div><!-- /input-group -->
        <div class="help-block">File format supported: {{ implode(", ",cbConfig("UPLOAD_FILE_EXTENSION_ALLOWED")) }}</div>
    </div>
</div>

<script>
    function showUploadWindow{{$name}}(t) {
        $(t).parents(".upload-button").find("input[type=file]").click()
    }
    function deleteFile{{$name}}(t,is_required) {
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
    function uploadFile{{$name}}(t, target_input_name, is_required, encrypt, width, height) {
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
            url: "{{ cb()->getDeveloperUrl('upload-file') }}",
            @else
            url: "{{ cb()->getAdminUrl('upload-file') }}",
            @endif
            type: "POST",
            processData: false,
            contentType: false,
            data:formData,
            success:function (data) {

                $(t).parents('.upload-wrapper').find('.upload-preview').html("<a href='" + data.full_url + "' target='_blank' title='"+data.filename+"'>" +
                    "<i class='fa fa-download'></i> Download "+data.filename+"</a> " +
                    "<a href='javascript:;' class='btn btn-xs btn-danger' onclick='deleteFile{{$name}}(this, "+is_required+")' title='Delete this file'><i class='fa fa-trash'></i></a>");
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