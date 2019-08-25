<?php /** @var \crocodicstudio\crudbooster\types\text_area\TextAreaModel $column */  ?>

<input type="email" placeholder="{{ $column->getFilterPlaceholder() }}" name="filter_{{ slug($column->getOrderByColumn(),"_") }}" value="{{ sanitizeXSS(request("filter_".slug($column->getOrderByColumn(),"_"))) }}" class="form-control">
<div class="help-block">{{ $column->getFilterHelp() }}</div>
