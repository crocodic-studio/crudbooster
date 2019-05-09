@php /** @var \crocodicstudio\crudbooster\models\ColumnModel $column */  @endphp
@if($column->getValue())
    <div align="left">
        <a data-lightbox="preview-image" title="Preview Image" href="{{ ($column->getValue())?asset($column->getValue()):"javascript:;" }}">
            <img src="{{ ($column->getValue())?asset($column->getValue()):cbAsset("images/no_image_100x100.png") }}" style="height: 150px" alt="{{ $column->getLabel() }}">
        </a>
    </div>
@else
    <div align="left">
        <img title="Image Not Available" src="{{ cbAsset("images/no_image_100x100.png") }}" style="height: 150px" alt="Image Not Available">
    </div>
@endif