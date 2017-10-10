<script>
    $(function () {
        jQuery.fn.selectText = function () {
            var doc = document;
            var element = this[0];
            console.log(this, element);
            if (doc.body.createTextRange) {
                var range = document.body.createTextRange();
                range.moveToElementText(element);
                range.select();
            } else if (window.getSelection) {
                var selection = window.getSelection();
                var range = document.createRange();
                range.selectNodeContents(element);
                selection.removeAllRanges();
                selection.addRange(range);
            }
        };

        $(document).on("click", ".selected_text", function () {
            console.log("clicked");
            $(this).selectText();
        });


        $('#input-nama').on('input', function () {
            var v = $(this).val();
            if (v) {
                v = v.replace(/[^0-9a-z]/gi, '_').toLowerCase();
                $('#input-permalink').val(v);
            } else {
                $('#input-permalink').val('');
            }
        })

        $('#input-permalink').on('input', function () {
            var v = $(this).val();
            v = v.replace(/[^0-9a-z]/gi, '_').toLowerCase();
            $('#input-permalink').val(v);
        })

        $('#tipe_action').change(function () {
            var v = $(this).val();
            $('.method_type').prop('checked', false);
            switch (v) {
                case 'list':
                case 'detail':
                case 'delete':
                    $('.method_type[value=get]').prop('checked', true);
                    break;
                case 'save_add':
                case 'save_edit':
                    $('.method_type[value=post]').prop('checked', true);
                    break;
            }
        })

        $(document).on('click', '.tr-response', function () {
            console.log('tr response clicked');
            var is_check = $(this).find('select').val();
            if (is_check == '1') {
                $(this).find('select').val(0);
                $(this).removeClass('success');
            } else {
                $(this).find('select').val(1);
                $(this).addClass('success');
            }
        })
    })

    function load_response() {
        var t = $('#combo_tabel').val();
        var type = 'list';
        var tipe_action = $('#tipe_action').val();
        var no_params = 0;
        $('#table-response tbody').empty();

        if (t == '') return false;
        if (tipe_action == '') return false;

        no_params += 1;
        $('#table-response tbody').append("<tr><td>" + no_params + "</td><td>api_status</td><td>boolean</td><td>-</td><td><select class='form-control' disabled><option>YES</option></select></td><td>-</td></tr>");
        no_params += 1;
        $('#table-response tbody').append("<tr><td>" + no_params + "</td><td>api_message</td><td>string</td><td>-</td><td><select class='form-control' disabled><option>YES</option></select></td><td>-</td></tr>");

        if (tipe_action == 'list') {
            $('#table-response tbody').append("<tr class='info' style='font-weight'><td>#</td><td>data</td><td>&nbsp;</td><td>-</td><td>-</td><td>-</td></tr>");
        }

        if (tipe_action == 'detail') {
            $('#table-response tbody').append("<tr class='info' style='font-weight'><td>#</td><td>data</td><td>&nbsp;</td><td>-</td><td>-</td><td>-</td></tr>");
        }

        no_params = 0;
        $.get('{{url(cbConfig("ADMIN_PATH"))."/api-generator/column-table"}}/' + t + '/' + type, function (resp) {
            $.each(resp, function (i, obj) {

                switch (obj.type) {
                    default:
                        var obj_type = obj.type;
                        break;
                    case 'varchar':
                    case 'nvarchar':
                    case 'char':
                    case 'text':
                        var obj_type = 'string';
                        break;
                    case 'integer':
                        var obj_type = 'integer';
                        break;
                    case 'double':
                    case 'float':
                    case 'decimal':
                        var obj_type = 'numeric';
                        break;
                    case 'date':
                        var obj_type = 'date';
                        break;
                    case 'datetime':
                    case 'timestamp':
                        var obj_type = 'date_format:Y-m-d H:i:s';
                        break;
                    case 'email':
                        var obj_type = 'email';
                        break;
                    case 'image':
                        var obj_type = 'image';
                        break;
                    case 'password':
                        var obj_type = 'password';
                        break;
                }

                no_params += 1;
                $('#table-response tbody').append("<tr class='success tr-response'><td>" + no_params + "</td><td>&nbsp;&nbsp;- " + obj.name + "<input type='hidden' name='responses_name[]' value='" + obj.name + "'/></td><td>" + obj_type + "<input type='hidden' name='responses_type[]' value='" + obj_type + "'/></td><td>-<input type='hidden' name='responses_subquery[]' value=''/></td><td><select class='form-control responses_used' name='responses_used[]'><option value='1'>YES</option><option value='0'>NO</option></select></td><td><a class='btn btn-danger' href='javascript:void(0)' onclick='deleteResponse(this)'><i class='fa fa-ban'></i></a></td></tr>");
            })
        })

        $('#table-response tfoot').show();
    }

    function load_parameters() {
        var t = $('#combo_tabel').val();
        var type = 'save_add';
        var tipe_action = $('#tipe_action').val();

        if (t == '') return false;
        if (tipe_action == '') return false;

        if (tipe_action == 'list' || tipe_action == 'detail') {
            $('textarea[name=sub_query_1]').prop('readonly', false);
        } else {
            $('textarea[name=sub_query_1]').prop('readonly', true);
        }

        $.get('{{url(cbConfig("ADMIN_PATH"))."/api-generator/column-table"}}/' + t + '/' + type, function (resp) {
            var no_params = 0;
            $('#table-parameters tbody').empty();
            $.each(resp, function (i, obj) {

                var param_html = $('#table-parameters tfoot tr').clone();
                $('#table-parameters tbody').append(param_html);
            })

            var i = 0;
            $('#table-parameters tbody tr').each(function () {

                var field_type = resp[i].type;
                var field_name = resp[i].name;

                if (tipe_action == 'save_add' && field_name == 'id') {
                    $(this).remove();
                } else {
                    no_params += 1;
                }


                switch (field_type) {
                    default:
                    case 'varchar':
                    case 'nvarchar':
                    case 'char':
                    case 'text':
                        var type = 'string';
                        break;
                    case 'integer':
                        var type = 'integer';
                        break;
                    case 'double':
                    case 'float':
                    case 'decimal':
                        var type = 'numeric';
                        break;
                    case 'date':
                        var type = 'date';
                        break;
                    case 'datetime':
                    case 'timestamp':
                        var type = 'date_format:Y-m-d H:i:s';
                        break;
                    case 'email':
                        var type = 'email';
                        break;
                    case 'image':
                        var type = 'image';
                        break;
                    case 'password':
                        var type = 'password';
                        break;
                }


                $(this).find('td:nth-child(1)').text(no_params);
                $(this).find('td:nth-child(2) input').val(field_name);
                $(this).find('td:nth-child(3) select').val(type);

                if (tipe_action == 'list' || tipe_action == 'detail' || tipe_action == 'delete') {
                    $(this).find('td:nth-child(5) select').val('0');
                    $(this).find('td:nth-child(6) select').val('0');
                }
                if (tipe_action == 'detail' && field_name == 'id') {
                    $(this).find('td:nth-child(5) select').val('1');
                    $(this).find('td:nth-child(6) select').val('1');
                }

                if (tipe_action == 'delete' && field_name == 'id') {
                    $(this).find('td:nth-child(5) select').val('1');
                    $(this).find('td:nth-child(6) select').val('1');
                }


                $(this).find('.col-delete').html("<a class='btn btn-danger' href='javascript:void(0)' onclick='deleteParam(this)'><i class='fa fa-ban'></i></a>");
                i += 1;
            })
        })

        $('#table-parameters tfoot').show();
    }

    function init_data_parameters() {
                @if($parameters)

        var resp = {!!$parameters!!};
        var tipe_action = $('#tipe_action').val();

        var no_params = 0;
        $('#table-parameters tbody').empty();
        $.each(resp, function (i, obj) {
            var param_html = $('#table-parameters tfoot tr').clone();
            $('#table-parameters tbody').append(param_html);
        })

        var i = 0;
        $('#table-parameters tbody tr').each(function () {

            var field_type = resp[i].type;
            var field_name = resp[i].name;
            var required = resp[i].required;
            var used = resp[i].used;
            var config = resp[i].config;

            console.log(field_name + ' - ' + field_type);

            if (tipe_action == 'save_add' && field_name == 'id') {
                $(this).remove();
            } else {
                no_params += 1;
            }


            switch (field_type) {
                default:
                    var type = field_type;
                    break;
                case 'varchar':
                case 'nvarchar':
                case 'char':
                case 'text':
                    var type = 'string';
                    break;
                case 'integer':
                    var type = 'integer';
                    break;
                case 'double':
                case 'float':
                case 'decimal':
                    var type = 'numeric';
                    break;
                case 'date':
                    var type = 'date';
                    break;
                case 'datetime':
                case 'timestamp':
                    var type = 'date_format:Y-m-d H:i:s';
                    break;
                case 'email':
                    var type = 'email';
                    break;
                case 'image':
                    var type = 'image';
                    break;
                case 'password':
                    var type = 'password';
                    break;
            }

            $(this).find('td:nth-child(1)').text(no_params);
            $(this).find('td:nth-child(2) input').val(field_name);
            $(this).find('td:nth-child(3) select').val(type);
            $(this).find('td:nth-child(4) input').val(config);
            $(this).find('td:nth-child(5) select').val(required);
            $(this).find('td:nth-child(6) select').val(used);

            $(this).find('.col-delete').html("<a class='btn btn-danger' href='javascript:void(0)' onclick='deleteParam(this)'><i class='fa fa-ban'></i></a>");
            i += 1;
        })

        $('#table-parameters tfoot').show();

        @endif
    } //end function init_data_parameter


    function init_data_responses() {
                @if($responses)

        var t = $('#combo_tabel').val();
        var type = 'list';
        var tipe_action = $('#tipe_action').val();
        var no_params = 0;
        $('#table-response tbody').empty();

        if (t == '') return false;
        if (tipe_action == '') return false;

        no_params += 1;
        $('#table-response tbody').append("<tr><td>" + no_params + "</td><td>api_status</td><td>boolean</td><td>-</td><td><select class='form-control' disabled><option>YES</option></select></td><td>-</td></tr>");
        no_params += 1;
        $('#table-response tbody').append("<tr><td>" + no_params + "</td><td>api_message</td><td>string</td><td>-</td><td><select class='form-control' disabled><option>YES</option></select></td><td>-</td></tr>");

        if (tipe_action == 'list') {
            $('#table-response tbody').append("<tr class='info' style='font-weight'><td>#</td><td>data</td><td>&nbsp;</td><td>-</td><td>-</td><td>-</td></tr>");
        }

        no_params = 0;
        var responses_data = {!! $responses !!};

        $.each(responses_data, function (i, obj) {
            no_params += 1;
            var used_yes = (obj.used == '1') ? "selected" : "";
            var used_no = (obj.used == '0') ? "selected" : "";
            var tr_success = (obj.used == '1') ? "success" : "";


            switch (obj.type) {
                default:
                    var obj_type = obj.type;
                    break;
                case 'varchar':
                case 'nvarchar':
                case 'char':
                case 'text':
                    var obj_type = 'string';
                    break;
                case 'integer':
                    var obj_type = 'integer';
                    break;
                case 'double':
                case 'float':
                case 'decimal':
                    var obj_type = 'numeric';
                    break;
                case 'date':
                    var obj_type = 'date';
                    break;
                case 'datetime':
                case 'timestamp':
                    var obj_type = 'dateTime';
                    break;
                case 'email':
                    var obj_type = 'email';
                    break;
                case 'image':
                    var obj_type = 'image';
                    break;
                case 'password':
                    var obj_type = 'password';
                    break;
            }

            var input_subquery = '';
            var delete_btn = '';

            if (obj.subquery == '') {
                input_subquery = "-<input type='hidden' name='responses_subquery[]' value='" + obj.subquery + "'/>";
                delete_btn = "<a class='btn btn-danger' href='javascript:void(0)' onclick='deleteResponse(this)'><i class='fa fa-ban'></i></a>";
            } else {
                input_subquery = obj.subquery + "<input type='hidden' name='responses_subquery[]' value='" + obj.subquery + "'/>";
                delete_btn = "<a class='btn btn-danger' href='javascript:void(0)' onclick='deleteResponse(this)'><i class='fa fa-ban'></i></a>";
            }

            $('#table-response tbody').append("<tr class='" + tr_success + " tr-response'><td>" + no_params + "</td><td>&nbsp;&nbsp;- " + obj.name + "<input type='hidden' name='responses_name[]' value='" + obj.name + "'/></td><td>" + obj_type + "<input type='hidden' name='responses_type[]' value='" + obj_type + "'/></td><td>" + input_subquery + "</td><td><select class='form-control responses_used' name='responses_used[]'><option " + used_yes + " value='1'>YES</option><option " + used_no + " value='0'>NO</option></select></td><td>" + delete_btn + "</td></tr>");
        })

        $('#table-response tfoot').show();

        @endif
    } //end function init_data_responses

    $(function () {

        @if($row)
        init_data_parameters();
        init_data_responses();
        @endif

        $('#combo_tabel,#tipe_action').change(function () {

            load_response();

            load_parameters();
        })

        $('#table-parameters tfoot tr td:nth-child(2) input').on('input', function () {
            var v = $(this).val();
            v = v.replace(/[^0-9a-z]/gi, '_').toLowerCase();
            $(this).val(v);
        })


    })

    function deleteParam(t) {
        $(t).parent().parent().remove();
        var no_params = 0;
        $('#table-parameters tbody tr').each(function () {
            no_params += 1;
            $(this).find('td:nth-child(1)').text(no_params);
        });
    }

    function addParam() {

        var htm = $('#table-parameters tfoot tr').clone();

        var val = $('#table-parameters tfoot tr td:nth-child(2) input').val();
        var validation = $('#table-parameters tfoot tr td:nth-child(3) select').val();
        var config = $('#table-parameters tfoot tr td:nth-child(4) select').val();
        var m = $('#table-parameters tfoot tr td:nth-child(5) select').val();
        var u = $('#table-parameters tfoot tr td:nth-child(6) select').val();

        htm.find('td:nth-child(3)').find('select').val(validation);
        htm.find('td:nth-child(4)').find('select').val(config);
        htm.find('td:nth-child(5)').find('select').val(m);
        htm.find('td:nth-child(6)').find('select').val(u);

        if (val == '') return false;

        $('#table-parameters .row-no-data').remove();
        $('#table-parameters tbody').append(htm);

        $('#table-parameters tfoot tr').find('input[type=text]').val('');
        $('#table-parameters tfoot tr').find('option').removeAttr('selected');

        var no_params = 0;
        $('#table-parameters tbody tr').each(function () {
            no_params += 1;
            $(this).find('td:nth-child(1)').text(no_params);
            $(this).find('.col-delete').html("<a class='btn btn-danger' href='javascript:void(0)' onclick='deleteParam(this)'><i class='fa fa-ban'></i></a>");
        });
    }

    function addResponse() {
        console.log('addResponse');

        var val = $('#table-response tfoot tr td:nth-child(2) input').val();
        var validation = $('#table-response tfoot tr td:nth-child(3) select').val();
        var subquery = $('#table-response tfoot tr td:nth-child(4) input').val();
        var is_check = $('#table-response tfoot tr td:nth-child(5) select').val();

        var check_yes, check_no;

        if (is_check == '1') {
            check_yes = 'selected';
        } else {
            check_no = 'selected';
        }

        var htm = "<tr class='tr-response tr-additional'>";
        htm += "<td>#</td>";
        htm += "<td>&nbsp;&nbsp;- " + val + "<input type='hidden' name='responses_name[]' value='" + val + "'/></td><td>" + validation + "<input type='hidden' name='responses_type[]' value='" + validation + "'/></td><td>" + subquery + "<input type='hidden' name='responses_subquery[]' value='" + subquery + "'/></td>";
        htm += "<td><select class='form-control responses_used' name='responses_used[]'><option " + check_yes + " value='1'>YES</option><option " + check_no + " value='0'>NO</option></select></td>";
        htm += "<td><a class='btn btn-danger' href='javascript:void(0)' onclick='deleteResponse(this)'><i class='fa fa-ban'></i></a></td></tr>";

        if (val == '') return false;
        // if(subquery == '') return false;

        $('#table-response .row-no-data').remove();
        $('#table-response tbody').append(htm);

        $('#table-response tfoot tr').find('input[type=text]').val('');
        $('#table-response tfoot tr').find('option').removeAttr('selected');

        var no_params = 0;
        $('#table-response tbody tr').each(function () {
            no_params += 1;

            $(this).addClass('tr-response');

            if ($(this).hasClass('tr-additional')) {
                if ($(this).find('select').val() == '1') {
                    $(this).addClass('success');
                }
            }

            $(this).find('td:nth-child(1)').text(no_params);
            $(this).find('.col-delete').html("<a class='btn btn-danger' href='javascript:void(0)' onclick='deleteResponse(this)'><i class='fa fa-ban'></i></a>");
        });
    }

    function deleteResponse(t) {
        $(t).parent().parent().remove();
        var no_params = 0;
        $('#table-response tbody tr').each(function () {
            no_params += 1;
            $(this).find('td:nth-child(1)').text(no_params);
        });
    }

</script>