
    </div>
</div>

<div class="text-danger">{!! $errors->first( $column->getName() )?"<i class='fa fa-info-circle'></i> ".$errors->first( $column->getName() ):"" !!}</div>
<p class='help-block'>{{ $column->getHelp() }}</p>
</div>