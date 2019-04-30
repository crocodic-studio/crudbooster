@include("types::layout_header")
    @php /** @var \crocodicstudio\crudbooster\types\file\FileModel $column */  @endphp
    <div class="upload-wrapper">
        <div class="upload-preview">
            @if($column->getValue())
                <a href="{{ asset($column->getValue()) }}" target="_blank" title="Download file"><i class="fa fa-download"></i> Download {{ basename($column->getValue()) }}</a>
            @endif
        </div>

        <div class="upload-button">
            <input type='file'
                   id="{{$column->getName()}}"
                   title="{{$column->getLabel()}}"
                   {{ $column->getRequired()?'required':''}}
                   {{ $column->getReadonly()?'readonly':''}}
                   {{ $column->getDisabled()?'disabled':''}}
                   class='form-control' onchange="uploadFile(this, '{{ $column->getName() }}')"/>
            <input type="hidden" name="{{ $column->getName() }}" value="{{ $column->getValue() }}">
        </div>
    </div>
@include("types::layout_footer")