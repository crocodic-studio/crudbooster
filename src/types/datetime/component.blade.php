@include("types::layout_header")
    @php /** @var \crocodicstudio\crudbooster\types\datetime\DatetimeModel $column */  @endphp
        <input type='text' title="{{ $column->getLabel() }}"
               placeholder="{{ $column->getPlaceholder()?:$column->getFormat() }}"
               {{ $column->getRequired()?'required':''}}
               readonly
               {{ $column->getDisabled()?'disabled':''}}
               class='form-control datetimepicker'
               name="{{ $column->getName() }}"
               id="{{ $column->getName() }}"
               data-format="{{ $column->getFormat()?convertPHPToMomentFormat($column->getFormat()):"YYYY-MM-DD HH:mm:ss" }}"
               value='{{ old($column->getName())?:$column->getValue() }}'/>
@include("types::layout_footer")