@include("types::layout_header")
    @php /** @var \crocodicstudio\crudbooster\types\number\NumberModel $column */  @endphp
        <input type='number' title="{{ $column->getLabel() }}"
               placeholder="{{ $column->getPlaceholder() }}"
               {{ $column->getRequired()?'required':''}}
               {{ $column->getReadonly()?'readonly':''}}
               {{ $column->getDisabled()?'disabled':''}}
               {!!  $column->getOnchangeJsFunctionName()?"onChange='".$column->getOnchangeJsFunctionName()."'":"" !!}
               {!! $column->getOnclickJsFunctionName()?"onClick='".$column->getOnclickJsFunctionName()."'":"" !!}
               {!! $column->getOnblurJsFunctionName()?"onBlur='".$column->getOnblurJsFunctionName()."'":"" !!}
               max="{{ $column->getMax() }}"
               min="{{ $column->getMin() }}"
               @if($column->getStep()) step="{{ $column->getStep() }}" @endif
               class='form-control'
               name="{{ $column->getName() }}"
               id="{{ $column->getName() }}"
               value='{{ old($column->getName())?:($column->getDefaultValue())?:$column->getValue() }}'/>
@include("types::layout_footer")