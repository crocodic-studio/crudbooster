@include("types::layout_header")
        <?php /** @var \crocodicstudio\crudbooster\types\custom\CustomModel $column */?>
        {!! $column->getHtml() !!}
@include("types::layout_footer")