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

@if($column->getForeignKey())
    @push('bottom')
        <script>
            $(function () {
                let value = "{{ old($filterName)?:$filterValue }}";
                $("#filter_{{ $column->getForeignKey() }}").change(function () {
                    let foreign_value = $(this).val();
                    $.post("{{ cb()->getAdminUrl("select-table-lookup") }}",{
                        _token:"{{csrf_token()}}",
                        foreign_key:"{{ encrypt($column->getForeignKey()) }}",
                        foreign_value: foreign_value,
                        table:"{{ encrypt($column->getOptionsFromTable()['table']) }}",
                        sql_condition:"{{ encrypt($column->getOptionsFromTable()['sql_condition']) }}"
                    },function (resp) {
                        if(resp.status) {
                            var opt = "<option value=''>** All Data {{ $column->getLabel() }}</option>";
                            $.each(resp.data,function (i,obj) {
                                opt += "<option value='"+obj.{{ $column->getOptionsFromTable()['key_field'] }}+"'>"+obj.{{ $column->getOptionsFromTable()['display_field'] }}+"</option>";
                            })
                            $("#filter_{{ $column->getName() }}").select2("val","");
                            $("#filter_{{ $column->getName() }}").val("");
                            $("#filter_{{ $column->getName() }}").html(opt);

                            if(value) {
                                $("#filter_{{ $column->getName() }}").val(value);
                                $("#filter_{{ $column->getName() }}").select2({
                                    placeholder:"** Select a {{ $column->getLabel() }}"
                                })
                            }
                        }
                    })
                }).trigger("change");
            })
        </script>
    @endpush
@endif
