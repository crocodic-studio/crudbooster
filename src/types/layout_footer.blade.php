<?php /** @var \crocodicstudio\crudbooster\models\ColumnModel $column */ ?>
    </div>
</div>

<div class="text-danger">{!! $errors->first( $column->getName() )?"<i class='fa fa-info-circle'></i> ".$errors->first( $column->getName() ):"" !!}</div>
<p class='help-block'>{{ $column->getHelp() }}</p>
</div>

@if(is_callable($column->getOnchangeJsFunctionCallback()))
    <script>
        {!! call_user_func($column->getOnchangeJsFunctionCallback()) !!}
    </script>
@endif

@if(is_callable($column->getOnclickJsFunctionCallback()))
    <script>
        {!! call_user_func($column->getOnclickJsFunctionCallback()) !!}
    </script>
@endif

@if(is_callable($column->getOnblurJsFunctionCallback()))
    <script>
        {!! call_user_func($column->getOnblurJsFunctionCallback()) !!}
    </script>
@endif