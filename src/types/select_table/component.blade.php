@include("types::layout_header")
    <?php /** @var \crocodicstudio\crudbooster\types\select_table\SelectTableModel $column */?>
    <select style="width: 100%" id="select-{{ $column->getName() }}" class="form-control select2"
            {{ $column->getRequired()?'required':''}}
            {{ $column->getReadonly()?'readonly':''}}
            {{ $column->getDisabled()?'disabled':''}}
            {!!  $column->getOnchangeJsFunctionName()?"onChange='".$column->getOnchangeJsFunctionName()."'":"" !!}
            {!! $column->getOnclickJsFunctionName()?"onClick='".$column->getOnclickJsFunctionName()."'":"" !!}
            {!! $column->getOnblurJsFunctionName()?"onBlur='".$column->getOnblurJsFunctionName()."'":"" !!}
            name="{{ $column->getName() }}" id="{{ $column->getName() }}">
        <option value="">** Select a {{ $column->getLabel() }}</option>
        @if(!$column->getForeignKey())
            <?php
                $columnValue = old($column->getName())?:($column->getDefaultValue())?:$column->getValue();
            ?>
            @foreach($column->getOptions() as $key=>$value)
                <option {{ $columnValue==$key?'selected':'' }} value="{{ $key }}">{{ $value }}</option>
            @endforeach
        @endif
    </select>

    @if($column->getForeignKey())
        @push('bottom')
            <script>
                $(function () {
                    let value = "{{ old($column->getName())?:($column->getDefaultValue())?:$column->getValue() }}";
                    $("#select-{{ $column->getForeignKey() }}").change(function () {
                        let foreign_value = $(this).val();
                        $.post("{{ cb()->getAdminUrl("select-table-lookup") }}",{
                            _token:"{{csrf_token()}}",
                            foreign_key:"{{ encrypt($column->getForeignKey()) }}",
                            foreign_value: foreign_value,
                            table:"{{ encrypt($column->getOptionsFromTable()['table']) }}",
                            sql_condition:"{{ encrypt($column->getOptionsFromTable()['sql_condition']) }}"
                        },function (resp) {
                            if(resp.status) {
                                var opt = "<option value=''>** Select a {{ $column->getLabel() }}</option>";
                                $.each(resp.data,function (i,obj) {
                                    opt += "<option value='"+obj.{{ $column->getOptionsFromTable()['key_field'] }}+"'>"+obj.{{ $column->getOptionsFromTable()['display_field'] }}+"</option>";
                                })
                                $("#select-{{ $column->getName() }}").select2("val","");
                                $("#select-{{ $column->getName() }}").val("");
                                $("#select-{{ $column->getName() }}").html(opt);

                                if(value) {
                                    $("#select-{{ $column->getName() }}").val(value);
                                    $("#select-{{ $column->getName() }}").select2({
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
@include("types::layout_footer")