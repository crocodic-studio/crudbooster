<?php /** @var \crocodicstudio\crudbooster\types\number\NumberModel $column */  ?>

<div class="row">
    <div class="col-sm-6">
        <input type="number" placeholder="From" name="filter_{{ slug($column->getFilterColumn(),"_") }}[start]" value="{{ sanitizeXSS(request("filter_".slug($column->getFilterColumn(),"_"))['start']) }}" class="form-control">
    </div>
    <div class="col-sm-6">
        <input type="number" placeholder="To" name="filter_{{ slug($column->getFilterColumn(),"_") }}[end]" value="{{ sanitizeXSS(request("filter_".slug($column->getFilterColumn(),"_"))['end']) }}" class="form-control">
    </div>
</div>

<div class="help-block">{{ $column->getFilterHelp()?:" " }}</div>
