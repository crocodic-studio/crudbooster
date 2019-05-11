@include("types::layout_header")
    <?php /** @var \crocodicstudio\crudbooster\types\select\SelectModel $column */?>
    <select style="width: 100%" class="form-control select2"
            {{ $column->getRequired()?'required':''}}
            {{ $column->getReadonly()?'readonly':''}}
            {{ $column->getDisabled()?'disabled':''}}
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