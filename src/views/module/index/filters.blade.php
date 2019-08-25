@if($columns = columnSingleton()->getFilterableColumns())
    <div id="box-filter" class="box box-default">
        <div class="box-header with-border">
            <h1 class="box-title" style="margin-top: 5px"><a href="javascript:;" onclick="openBoxFilter(this)"><i class="fa fa-filter"></i> {{ cbLang("filter") }}</a></h1>
            <div class="pull-right">
                <a href="javascript:;" onclick="openBoxFilter(this)" id="btn-open-filter"  class="btn btn-sm"><i class="fa fa-plus"></i></a>
                <a href="javascript:;" onclick="closeBoxFilter(this)" id="btn-close-filter" style="display: none" class="btn btn-sm"><i class="fa fa-minus"></i></a>
            </div>
        </div>
        <form autocomplete="off" class="form-horizontal" method="get">
        <div class="box-body" style="display: none">

            @foreach($columns as $column)
                @if(file_exists(base_path('vendor/crocodicstudio/crudbooster/src/types/'.$column->getType().'/filter.blade.php')))
                @php /* @var \crocodicstudio\crudbooster\models\ColumnModel $column */ @endphp
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="">{{ $column->getLabel() }}</label>
                    <div class="col-sm-8">
                        @include('types::'.$column->getType().'.filter')
                    </div>
                </div>
                @endif
            @endforeach

        </div>
        <div class="box-footer" style="display: none;">
            <div align="center">
                <button class="btn btn-primary" type="submit"><i class="fa fa-filter"></i> {{ cbLang("submit")." ".cbLang("filter") }}</button>
            </div>
        </div>
        </form>
    </div>
    @push("bottom")
        <script>
            function openBoxFilter(t) {
                $("#btn-open-filter").hide();
                $("#btn-close-filter").show();
                $("#box-filter .box-body").slideDown();
                $("#box-filter .box-footer").slideDown();
            }
            function closeBoxFilter(t) {
                $("#btn-close-filter").hide();
                $("#btn-open-filter").show();
                $("#box-filter .box-body").slideUp();
                $("#box-filter .box-footer").slideUp();
            }
        </script>
    @endpush
@endif
