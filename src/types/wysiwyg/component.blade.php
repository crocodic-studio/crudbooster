@extends('types::layout')
@section('content')
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
                        url: '{{ cb()->url('upload-file') }}',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: data,
                        type: "post",
                        success: function (url) {
                            var image = $('<img>').attr('src', url);
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

    <textarea id='textarea_{{$name}}'
      {{ $column->getRequired()?'required':''}}
      {{ $column->getReadonly()?'readonly':''}}
      {!! $column->getPlaceholder()?"placeholder='".$column->getPlaceholder()."' ":"" !!}
      {{ $column->getDisabled()?'disabled':''}}
        name="{{ $column->getName() }}"
        class='form-control'
        rows='5'>{{ $column->getValue() }}</textarea>
@endsection
