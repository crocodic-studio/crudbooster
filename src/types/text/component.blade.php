@include("types::layout_header")
        <?php /** @var \crocodicstudio\crudbooster\types\text\TextModel $column */  ?>
        <input type='text'
               title="{{ $column->getLabel() }}"
               placeholder="{{ $column->getPlaceholder() }}"
               {{ $column->getRequired()?'required':''}}
               {{ $column->getReadonly()?'readonly':''}}
               {{ $column->getDisabled()?'disabled':''}}
               {{ $column->getMaxLength()?"maxlength=".$column->getMaxLength():""}}
               {{ $column->getMinLength()?"minlength=".$column->getMinLength():""}}
               {!!  $column->getOnchangeJsFunctionName()?"onChange='".$column->getOnchangeJsFunctionName()."'":"" !!}
               {!! $column->getOnclickJsFunctionName()?"onClick='".$column->getOnclickJsFunctionName()."'":"" !!}
               {!! $column->getOnblurJsFunctionName()?"onBlur='".$column->getOnblurJsFunctionName()."'":"" !!}
               class='form-control'
               name="{{ $column->getName() }}"
               id="{{ $column->getName() }}"
               value='{{ old($column->getName())?:($column->getDefaultValue())?:$column->getValue() }}'/>
@include("types::layout_footer")
