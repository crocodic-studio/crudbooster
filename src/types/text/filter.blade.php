<?php /** @var \crocodicstudio\crudbooster\types\text\TextModel $column */  ?>

<input type="text" placeholder="{{ $column->getFilterPlaceholder() }}" name="filter_{{ slug($column->getFilterColumn(),"_") }}" value="{{ sanitizeXSS(request("filter_".slug($column->getFilterColumn(),"_"))) }}" class="form-control">
<div class="help-block">{{ $column->getFilterHelp() }}</div>
