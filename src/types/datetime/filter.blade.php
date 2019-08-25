<?php /** @var \crocodicstudio\crudbooster\types\date\DateModel $column */  ?>

<div class="row">
    <div class="col-sm-6">
        <input type="text" autocomplete="off" placeholder="Start" id="filter_datepicker_start_{{ slug($column->getFilterColumn(),"_") }}" name="filter_{{ slug($column->getFilterColumn(),"_") }}[start]" value="{{ sanitizeXSS(request("filter_".slug($column->getFilterColumn(),"_"))["start"]) }}" class="form-control">
        <div class="help-block">E.g: {{ $column->getFormat() }}</div>
    </div>
    <div class="col-sm-6">
        <input type="text" autocomplete="off" placeholder="End" id="filter_datepicker_end_{{ slug($column->getFilterColumn(),"_") }}" name="filter_{{ slug($column->getFilterColumn(),"_") }}[end]" value="{{ sanitizeXSS(request("filter_".slug($column->getFilterColumn(),"_"))["end"]) }}" class="form-control">
        <div class="help-block">E.g: {{ $column->getFormat() }}</div>
    </div>
</div>

<div class="help-block">{{ $column->getFilterHelp() }}</div>

@push("bottom")
    <script>
        $(function() {
            jQuery('#filter_datepicker_start_{{ slug($column->getFilterColumn(),"_") }}').datetimepicker({
                format: '{{ $column->getFormat() }}',
                onShow:function( ct ){
                    this.setOptions({
                        maxDate:jQuery('#filter_datepicker_end_{{ slug($column->getFilterColumn(),"_") }}').val()?jQuery('#filter_datepicker_end_{{ slug($column->getFilterColumn(),"_") }}').val():false
                    })
                },
                timepicker:true
            });
            jQuery('#filter_datepicker_end_{{ slug($column->getFilterColumn(),"_") }}').datetimepicker({
                format: '{{ $column->getFormat() }}',
                onShow:function( ct ){
                    this.setOptions({
                        minDate:jQuery('#filter_datepicker_start_{{ slug($column->getFilterColumn(),"_") }}').val()?jQuery('#filter_datepicker_start_{{ slug($column->getFilterColumn(),"_") }}').val():false
                    })
                },
                timepicker:true
            });
        })
    </script>
@endpush
