@include("types::layout_header")
    <?php /** @var \crocodicstudio\crudbooster\types\wysiwyg\WysiwygModel $column */ ?>
    @push('bottom')
        <script type="text/javascript">
            $(document).ready(function () {
                $('#textarea_{{ $column->getName() }}').summernote({
                    height: ($(window).height() - 300),
                    callbacks: {
                        onImageUpload: function (image) {
                            uploadImageSummernote{{$column->getName()}}(image[0]);
                        }
                    }
                });

                function uploadImageSummernote{{$column->getName()}}(image) {
                    var data = new FormData();
                    data.append("userfile", image);
                    $.ajax({
                        headers: {
                          "X-CSRF-TOKEN":"{{ csrf_token() }}"
                        },
                        url: '{{ cb()->getAdminUrl('upload-image') }}',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: data,
                        type: "post",
                        success: function (resp) {
                            var image = $('<img>').attr('src', resp.full_url);
                            $('#textarea_{{ $column->getName() }}').summernote("insertNode", image[0]);
                        },
                        error: function (data) {
                            console.log(data);
                            swal('Oops', 'Upload image failed!','warning');
                        }
                    });
                }
            })
        </script>
    @endpush

    <textarea id='textarea_{{ $column->getName() }}'
      {{ $column->getRequired()?'required':''}}
      {{ $column->getReadonly()?'readonly':''}}
      {!! $column->getPlaceholder()?"placeholder='".$column->getPlaceholder()."' ":"" !!}
      {{ $column->getDisabled()?'disabled':''}}
        name="{{ $column->getName() }}"
        class='form-control'
        rows='5'>{{ old($column->getName())?:($column->getDefaultValue())?:$column->getValue() }}</textarea>
@include("types::layout_footer")
