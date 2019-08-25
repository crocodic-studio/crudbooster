@include("types::layout_header")
    @php /** @var \crocodicstudio\crudbooster\types\datetime\DatetimeModel $column */  @endphp
        <input type='text' title="{{ $column->getLabel() }}"
               placeholder="{{ $column->getPlaceholder()?:$column->getFormat() }}"
               {{ $column->getRequired()?'required':''}}
               readonly
               {{ $column->getDisabled()?'disabled':''}}
               {!!  $column->getOnchangeJsFunctionName()?"onChange='".$column->getOnchangeJsFunctionName()."'":"" !!}
               {!! $column->getOnclickJsFunctionName()?"onClick='".$column->getOnclickJsFunctionName()."'":"" !!}
               {!! $column->getOnblurJsFunctionName()?"onBlur='".$column->getOnblurJsFunctionName()."'":"" !!}
               class='form-control timepicker'
               name="{{ $column->getName() }}"
               id="{{ $column->getName() }}"
               data-format="{{ $column->getFormat()?$column->getFormat():"H:i" }}"
               value='{{ old($column->getName())?:($column->getDefaultValue())?:$column->getValue() }}'/>
@include("types::layout_footer")