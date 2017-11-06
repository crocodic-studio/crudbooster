@push('bottom')
    <script type="text/javascript">
        var currentRow = null;

        function resetForm {{$name}}() {
            $('#panel-form-{{$name}}').find("input[type=text],input[type=number],select,textarea").val('');
            $('#panel-form-{{$name}}').find(".select2").val('').trigger('change');
        }

        function deleteRow {{$name}}(t) {

            if (confirm("{{cbTrans('delete_title_confirm')}}")) {
                $(t).parent().parent().remove();
                if ($('#table-{{$name}} tbody tr').length == 0) {
                    var colspan = $('#table-{{$name}} thead tr th').length;
                    $('#table-{{$name}} tbody').html("<tr class='trNull'><td colspan='" + colspan + "' align='center'>{{cbTrans('table_data_not_found')}}</td></tr>");
                }
            }
        }

        function editRow {{$name}}(t) {
            var p = $(t).parent().parent(); //parentTR
            currentRow = p;
            p.addClass('warning');
            $('#btn-add-table-{{$name}}').val('{{cbTrans("save_changes")}}');
            @foreach($formInput['columns'] as $c)
            @if($c['type']=='select')
            $('#{{$name.$c["name"]}}').val(p.find(".{{$c['name']}} input").val()).trigger("change");
                    @elseif($c['type']=='radio')
            var v = p.find(".{{$c['name']}} input").val();
            $('.{{$name.$c["name"]}}[value=' + v + ']').prop('checked', true);
            @elseif($c['type']=='datamodal')
            $('#{{$name.$c["name"]}} .input-label').val(p.find(".{{$c['name']}} .td-label").text());
            $('#{{$name.$c["name"]}} .input-id').val(p.find(".{{$c['name']}} input").val());
            @elseif($c['type']=='upload')
            @if($c['upload_type']=='image')
            $('#{{$name.$c["name"]}} .input-label').val(p.find(".{{$c['name']}} img").data('label'));
            @else
            $('#{{$name.$c["name"]}} .input-label').val(p.find(".{{$c['name']}} a").data('label'));
            @endif
            $('#{{$name.$c["name"]}} .input-id').val(p.find(".{{$c['name']}} input").val());
            @else
            $('#{{$name.$c["name"]}}').val(p.find(".{{$c['name']}} input").val());
            @endif
            @endforeach
        }

        function validateForm {{$name}}() {
            var is_false = 0;
            $('#panel-form-{{$name}} .required').each(function () {
                var v = $(this).val();
                if (v == '') {
                    sweetAlert("{{cbTrans('alert_warning')}}", "{{cbTrans('please_complete_the_form')}}", "warning");
                    is_false += 1;
                }
            })

            if (is_false == 0) {
                return true;
            } else {
                return false;
            }
        }

        function addToTable {{$name}}() {

            if (validateForm{{$name}}() == false) {
                return false;
            }

            var trRow = '<tr>';
            @foreach($formInput['columns'] as $c)
                    @if($c['type']=='select')
                trRow += "<td class='{{$c['name']}}'>" + $('#{{$name.$c["name"]}} option:selected').text() +
                "<input type='hidden' name='{{$name}}-{{$c['name']}}[]' value='" + $('#{{$name.$c["name"]}}').val() + "'/>" +
                "</td>";
            @elseif($c['type']=='radio')
                trRow += "<td class='{{$c['name']}}'><span class='td-label'>" + $('.{{$name.$c["name"]}}:checked').val() + "</span>" +
                "<input type='hidden' name='{{$name}}-{{$c['name']}}[]' value='" + $('.{{$name.$c["name"]}}:checked').val() + "'/>" +
                "</td>";
            @elseif($c['type']=='datamodal')
                trRow += "<td class='{{$c['name']}}'><span class='td-label'>" + $('#{{$name.$c["name"]}} .input-label').val() + "</span>" +
                "<input type='hidden' name='{{$name}}-{{$c['name']}}[]' value='" + $('#{{$name.$c["name"]}} .input-id').val() + "'/>" +
                "</td>";
            @elseif($c['type']=='upload')
                    @if($c['upload_type']=='image')
                trRow += "<td class='{{$c['name']}}'>" +
                "<a data-lightbox='roadtrip' href='{{asset('/')}}" + $('#{{$name.$c["name"]}} .input-id').val() + "'><img data-label='" + $('#{{$name.$c["name"]}} .input-label').val() + "' src='{{asset('/')}}" + $('#{{$name.$c["name"]}} .input-id').val() + "' width='50px' height='50px'/></a>" +
                "<input type='hidden' name='{{$name}}-{{$c['name']}}[]' value='" + $('#{{$name.$c["name"]}} .input-id').val() + "'/>" +
                "</td>";
            @else
                trRow += "<td class='{{$c['name']}}'><a data-label='" + $('#{{$name.$c["name"]}} .input-label').val() + "' href='{{asset('/')}}" + $('#{{$name.$c["name"]}} .input-id').val() + "'>" + $('#{{$name.$c["name"]}} .input-label').val() + "</a>" +
                "<input type='hidden' name='{{$name}}-{{$c['name']}}[]' value='" + $('#{{$name.$c["name"]}} .input-id').val() + "'/>" +
                "</td>";
            @endif
                    @else
                trRow += "<td class='{{$c['name']}}'>" + $('#{{$name.$c["name"]}}').val() +
                "<input type='hidden' name='{{$name}}-{{$c['name']}}[]' value='" + $('#{{$name.$c["name"]}}').val() + "'/>" +
                "</td>";
            @endif
                    @endforeach
                trRow += "<td>" +
                "<a href='#panel-form-{{$name}}' onclick='editRow{{$name}}(this)' class='btn btn-warning btn-xs'><i class='fa fa-pencil'></i></a> " +
                "<a href='javascript:void(0)' onclick='deleteRow{{$name}}(this)' class='btn btn-danger btn-xs'><i class='fa fa-trash'></i></a></td>";
            trRow += '</tr>';
            $('#table-{{$name}} tbody .trNull').remove();
            if (currentRow == null) {
                $("#table-{{$name}} tbody").prepend(trRow);
            } else {
                currentRow.removeClass('warning');
                currentRow.replaceWith(trRow);
                currentRow = null;
            }
            $('#btn-add-table-{{$name}}').val('{{cbTrans("button_add_to_table")}}');
            $('#btn-reset-form-{{$name}}').click();
        }
    </script>
@endpush