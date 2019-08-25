<?php
    /** @var \crocodicstudio\crudbooster\types\text\TextModel $column */
    $filterName = "filter_".slug($column->getFilterColumn(),"_");
    $filterValue = sanitizeXSS(request($filterName));
?>
<select name="filter_{{ slug($column->getFilterColumn(),"_") }}" style="width: 100%" id="filter_{{ $column->getName()  }}" class="form-control select2">
    <option value="">** All Data</option>
    @if(!$column->getForeignKey())
        @foreach($column->getOptions() as $key=>$value)
            <option {{ $filterValue==$key?"selected":"" }} value="{{ $key }}">{{ $value }}</option>
        @endforeach
    @endif
</select>
<div class="help-block">{{ $column->getFilterHelp() }}</div>
