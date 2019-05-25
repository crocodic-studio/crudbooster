@include("types::layout_header")
    <?php /** @var \crocodicstudio\crudbooster\types\text\TextAreaModel $column */ ?>
    <textarea name="{{$column->getName()}}" id="{{$column->getName()}}"
              {{ $column->getRequired()?'required':''}}
              {{ $column->getReadonly()?'readonly':''}}
              placeholder="{{ $column->getPlaceholder() }}"
              {{ $column->getDisabled()?'disabled':''}}
              {!!  $column->getOnchangeJsFunctionName()?"onChange='".$column->getOnchangeJsFunctionName()."'":"" !!}
              {!! $column->getOnclickJsFunctionName()?"onClick='".$column->getOnclickJsFunctionName()."'":"" !!}
              {!! $column->getOnblurJsFunctionName()?"onBlur='".$column->getOnblurJsFunctionName()."'":"" !!}
              class='form-control'
              rows='5'>{{ old($column->getName())?:($column->getDefaultValue())?:$column->getValue() }}</textarea>
@include("types::layout_footer")