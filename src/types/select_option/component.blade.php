@include("types::layout_header")
    <?php /** @var \crocodicstudio\crudbooster\types\select_option\SelectOptionModel $column */?>
    <select style="width: 100%" id="select-{{ $column->getName() }}" class="form-control select2"
            {{ $column->getRequired()?'required':''}}
            {{ $column->getReadonly()?'readonly':''}}
            {{ $column->getDisabled()?'disabled':''}}
            {!!  $column->getOnchangeJsFunctionName()?"onChange='".$column->getOnchangeJsFunctionName()."'":"" !!}
            {!! $column->getOnclickJsFunctionName()?"onClick='".$column->getOnclickJsFunctionName()."'":"" !!}
            {!! $column->getOnblurJsFunctionName()?"onBlur='".$column->getOnblurJsFunctionName()."'":"" !!}
            name="{{ $column->getName() }}" id="{{ $column->getName() }}">
        <option value="">** Select a {{ $column->getLabel() }}</option>
        <?php
            $columnValue = old($column->getName())?:($column->getDefaultValue())?:$column->getValue();
        ?>
        @foreach($column->getOptions() as $key=>$value)
            <option {{ $columnValue==$key?'selected':'' }} value="{{ $key }}">{{ $value }}</option>
        @endforeach
    </select>
@include("types::layout_footer")