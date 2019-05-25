@include("types::layout_header")
    @php /** @var \crocodicstudio\crudbooster\types\image\ImageModel $column */  @endphp
    <div class="upload-wrapper">
        <div class="upload-preview" style="padding: 5px">
            @if($column->getValue())
                <a href="{{ asset($column->getValue()) }}" data-lightbox="thumbnail">
                    <img class="img-thumbnail" src="{{ asset($column->getValue()) }}" style="max-width: 250px" alt="Preview Image">
                </a>
                <a href="javascript:;" class="btn btn-danger" onclick="deleteImage(this, '{{ $column->getRequired()?1:0 }}')" title="Delete this image"><i class="fa fa-trash"></i></a>
            @endif
        </div>

        <div class="upload-button">
            <input type='file'
                   accept="image/*"
                   id="{{$column->getName()}}"
                   title="{{$column->getLabel()}}"
                   {{ (!$column->getValue() && $column->getRequired())?'required':''}}
                   {{ $column->getReadonly()?'readonly':''}}
                   {{ $column->getDisabled()?'disabled':''}}
                   {!! $column->getOnchangeJsFunctionName()?"onChange='".$column->getOnchangeJsFunctionName()."'":"" !!}
                   {!! $column->getOnclickJsFunctionName()?"onClick='".$column->getOnclickJsFunctionName()."'":"" !!}
                   {!! $column->getOnblurJsFunctionName()?"onBlur='".$column->getOnblurJsFunctionName()."'":"" !!}
                   class='form-control' onchange="uploadImage(this, '{{ $column->getName() }}', '{{ $column->getRequired()?1:0 }}','{{ $column->getEncrypt() }}', '{{ $column->getResizeWidth() }}','{{ $column->getResizeHeight() }}')"/>
            <input type="hidden" name="{{ $column->getName() }}" value="{{ $column->getValue() }}">
        </div>
    </div>
@include("types::layout_footer")