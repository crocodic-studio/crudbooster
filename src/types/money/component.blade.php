@include("types::layout_header")
    @php /** @var \crocodicstudio\crudbooster\types\money\MoneyModel $column */  @endphp
        <input type='text' title="{{ $column->getLabel() }}"
               placeholder="{{ $column->getPlaceholder() }}"
               {{ $column->getRequired()?'required':''}}
               {{ $column->getReadonly()?'readonly':''}}
               {{ $column->getDisabled()?'disabled':''}}
               {!!  $column->getOnchangeJsFunctionName()?"onChange='".$column->getOnchangeJsFunctionName()."'":"" !!}
               {!! $column->getOnclickJsFunctionName()?"onClick='".$column->getOnclickJsFunctionName()."'":"" !!}
               {!! $column->getOnblurJsFunctionName()?"onBlur='".$column->getOnblurJsFunctionName()."'":"" !!}
               class='form-control input-money'
               name="{{ $column->getName() }}"
               id="money-{{ $column->getName() }}"
               value='{{ old($column->getName())?:($column->getDefaultValue())?:$column->getValue() }}'/>

    @push('bottom')
        <script>
            $(function () {
                $("#money-{{$column->getName()}}").maskMoney({
                    precision: {{ $column->getPrecision()?$column->getPrecision():0 }},
                    thousands: {!! $column->getThousands()?"'".$column->getThousands()."'":"','" !!},
                    decimal: {!! $column->getDecimal()?"'".$column->getDecimal()."'":"'.'" !!}
                });
            })
        </script>
        @endpush
@include("types::layout_footer")