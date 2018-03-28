@push('bottom')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#textarea_{{$name}}').summernote({
                height: {{ ($formInput['options']['height'])?:"($(window).height() - 300)"}},
                callbacks: {
                    onImageUpload: function (image) {
                        uploadImage{{$name}}(image[0]);
                    }
                }
            });

            function uploadImage {{$name}}(image) {
                var data = new FormData();
                data.append("userfile", image);
                $.ajax({
                    url: '{{CRUDBooster::mainpath("upload-summernote")}}',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: data,
                    type: "post",
                    success: function (url) {
                        var image = $('<img>').attr('src', url);
                        $('#textarea_{{$name}}').summernote("insertNode", image[0]);
                    },
                    error: function (data) {
                        console.log(data);
                    }
                });
            }
        })
    </script>
@endpush

<div class='form-group' id='form-group-{{$name}}' style="{{@$formInput['style']}}">
    <label class='control-label col-sm-2'>{{$label}}</label>

    <div class="{{$col_width?:'col-sm-10'}}">
        <textarea id='textarea_{{$name}}' id="{{$name}}"
                  {{$required}} {{$readonly}} {{$disabled}} name="{{$formInput['name']}}" class='form-control'
                  rows='5'>{{ $value }}</textarea>
        @include('crudbooster::default._form_body.underField', ['help' => $formInput['help'], 'error' => $errors->first($name)])
    </div>
</div>
