@include("types::layout_header")
    @php /** @var \crocodicstudio\crudbooster\models\ColumnModel $column */  @endphp
    <div class="upload-wrapper">
        <div class="upload-preview">
            @if($column->getValue())
                <a href="{{ asset($column->getValue()) }}" data-lightbox="thumbnail">
                    <img src="{{ asset($column->getValue()) }}" style="max-width: 250px" alt="Preview Image">
                </a>
            @endif
        </div>

        <div class="upload-button">
            <input type='file'
                   accept="image/*"
                   id="{{$column->getName()}}"
                   title="{{$column->getLabel()}}"
                   {{ $column->getRequired()?'required':''}}
                   {{ $column->getReadonly()?'readonly':''}}
                   {{ $column->getDisabled()?'disabled':''}}
                   class='form-control' onchange="uploadImage(this, '{{ $column->getName() }}')"/>
            <input type="hidden" name="{{ $column->getName() }}" value="{{ $column->getValue() }}">
        </div>
    </div>
@include("types::layout_footer")