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
               class='form-control datetimepicker'
               name="{{ $column->getName() }}"
               id="{{ $column->getName() }}"
               data-format="{{ $column->getFormat()?convertPHPToMomentFormat($column->getFormat()):"YYYY-MM-DD HH:mm:ss" }}"
               value='{{ old($column->getName())?:($column->getDefaultValue())?:$column->getValue() }}'/>
@include("types::layout_footer")