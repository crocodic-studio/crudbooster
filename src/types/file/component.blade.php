@include("types::layout_header")
    @php /** @var \crocodicstudio\crudbooster\types\file\FileModel $column */  @endphp
    <div class="upload-wrapper">
        <div class="upload-preview" style="padding: 5px">
            @if($column->getValue())
                <a href="{{ asset($column->getValue()) }}" target="_blank" title="Download file"><i class="fa fa-download"></i> Download {{ basename($column->getValue()) }}</a>
                <a href="javascript:;" class="btn btn-xs btn-danger" onclick="deleteFile(this, '{{ $column->getRequired()?1:0 }}')" title="Delete this file"><i class="fa fa-trash"></i></a>
            @endif
        </div>

        <div class="upload-button">
            <input type='file'
                   id="{{$column->getName()}}"
                   title="{{$column->getLabel()}}"
                   {{ (!$column->getValue() && $column->getRequired())?'required':''}}
                   {{ $column->getReadonly()?'readonly':''}}
                   {{ $column->getDisabled()?'disabled':''}}
                   {!!  $column->getOnchangeJsFunctionName()?"onChange='".$column->getOnchangeJsFunctionName()."'":"" !!}
                   {!! $column->getOnclickJsFunctionName()?"onClick='".$column->getOnclickJsFunctionName()."'":"" !!}
                   {!! $column->getOnblurJsFunctionName()?"onBlur='".$column->getOnblurJsFunctionName()."'":"" !!}
                   class='form-control' onchange="uploadFile(this, '{{ $column->getName() }}', '{{ $column->getRequired()?1:0 }}', {{ $column->getEncrypt() }} )"/>
            <input type="hidden" name="{{ $column->getName() }}" value="{{ $column->getValue() }}">
        </div>
    </div>
@include("types::layout_footer")