@extends('crudbooster::admin_template')

@section('content')

    @push('head')
        <link href="//cdnjs.cloudflare.com/ajax/libs/summernote/0.8.2/summernote.css" rel="stylesheet">
    @endpush
    @push('bottom')
        <script src="//cdnjs.cloudflare.com/ajax/libs/summernote/0.8.2/summernote.js"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                $('.wysiwyg').summernote();
            })
        </script>
    @endpush

    <ul class="nav nav-tabs">
        <li><a href="{{ CRUDBooster::mainpath() }}"><i class='fa fa-file'></i> API Documentation</a></li>
        <li><a href="{{ CRUDBooster::mainpath('screet-key') }}"><i class='fa fa-key'></i> API Secret Key</a></li>
        <li class='active'><a href="{{ CRUDBooster::mainpath('generator') }}"><i class='fa fa-cog'></i> API Generator</a></li>
    </ul>

    <div class='box'>

        <div class='box-body'>
            @push('bottom')
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
                        $.get('{{url(config("crudbooster.ADMIN_PATH"))."/api_generator/column-table"}}/' + t + '/' + type, function (resp) {
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

                        $.get('{{url(config("crudbooster.ADMIN_PATH"))."/api_generator/column-table"}}/' + t + '/' + type, function (resp) {
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
                                if (obj.subquery) {
                                    var subquery = obj.subquery;
                                } else {
                                    var subquery = '';
                                }
                                input_subquery = subquery + "<input type='hidden' name='responses_subquery[]' value='" + subquery + "'/>";
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
            @endpush

            @push('head')
                <style>
                    .selected_text {
                        cursor: pointer;
                    }

                    .selected_text:hover {
                        color: #76a400
                    }

                    tfoot td {
                        background: #eeeeee
                    }

                    .tr-response {
                        cursor: pointer
                    }
                </style>
            @endpush


            <form method='post' action='{{ route("ApiCustomControllerPostSaveApiCustom")}}'>
                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                <input type="hidden" name="id" value="{{$row->id}}">
                <div class='row'>
                    <div class='col-sm-8'>
                        <div class='form-group'>
                            <label>API Name</label>
                            <input type='text' class='form-control' value='{{$row->nama}}' required name='nama' id='input-nama'/>
                        </div>
                    </div>

                    <div class='col-sm-4'>
                        <div class='form-group'>
                            <label>Table</label>
                            <select id='combo_tabel' name='tabel' required class='form-control'>
                                <option value=''>** Choose a Table</option>
                                @foreach($tables as $tab)
                                    <option {{($row->tabel == $tab)?"selected":""}} value='{{$tab}}'>{{$tab}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class='row'>
                    <div class='col-sm-8'>
                        <div class='form-group'>
                            <label>API Slug</label>
                            <div class='input-group'>
                                <span class="input-group-addon" id="basic-addon1" style="background:#eeeeee">{{url("api")}}/</span>
                                <input type='text' class='form-control' value='{{$row->permalink}}' required name='permalink' id='input-permalink'/>
                            </div>
                        </div>
                    </div>
                    <div class='col-sm-2'>
                        <div class='form-group'>
                            <label>Action Type</label>
                            <select id='tipe_action' name='aksi' required class='form-control'>
                                <option value=''>** Select Action</option>
                                <option value='list' {{ ($row->aksi == 'list')?"selected":"" }} >LISTING</option>
                                <option value='detail' {{ ($row->aksi == 'detail')?"selected":"" }}>DETAIL / READ</option>
                                <option value='save_add' {{ ($row->aksi == 'save_add')?"selected":"" }}>CREATE / ADD</option>
                                <option value='save_edit' {{ ($row->aksi == 'save_edit')?"selected":"" }}>UPDATE</option>
                                <option value='delete' {{ ($row->aksi == 'delete')?"selected":"" }}>DELETE</option>
                            </select>
                        </div>
                    </div>
                    <div class='col-sm-2'>
                        <div class='form-group'>
                            <label>Method Type</label>
                            <br/>
                            <label class='radio-inline'>
                                <input type='radio' required class='method_type' {{ ($row->method_type == 'get')?"checked":"" }} name='method_type'
                                       value='get'/> GET
                            </label>
                            <label class='radio-inline'>
                                <input type='radio' class='method_type' {{ ($row->method_type == 'post')?"checked":"" }} name='method_type' value='post'/> POST
                            </label>

                        </div>
                    </div>
                </div>


                <div class='form-group'>
                    <div class="clearfix">
                        <label><i class='fa fa-cog'></i> Parameters</label>
                        <a class='pull-right btn btn-xs btn-primary' href='javascript:void(0)' onclick="load_parameters()"><i class='fa fa-refresh'></i>
                            Reset</a>
                    </div>

                    <table id='table-parameters' class='table table-striped table-bordered'>
                        <thead>
                        <tr>
                            <th width="3%">No</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Laravel Validation / Description / Value</th>
                            <th width="8%" title='is Mandatory ?'>Mandatory</th>
                            <th width="8%" title='is used ?'>Enable</th>
                            <th width="5%">-</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class='row-no-data'>
                            <td colspan='7'>There is no data</td>
                        </tr>
                        </tbody>
                        <tfoot style="display:none">
                        <tr>
                            <td>#</td>
                            <td width="20%"><input class='form-control' name='params_name[]' type='text'/></td>
                            <td width="20%"><select class='form-control' name='params_type[]'>
                                    <optgroup label='Common Validation'>
                                        <option value='string'>String</option>
                                        <option value='integer'>Integer</option>
                                        <option value='email'>Email</option>
                                        <option value='image'>Image (jpeg, png, bmp, gif, or svg)</option>
                                        <option value='file'>File Upload</option>
                                        <option value='exists'>Exists (table,column)</option>
                                        <option value='unique'>Unique (table,column,except)</option>
                                        <option value='password'>Password</option>
                                        <option value='search'>Search</option>
                                        <option value='custom'>Custom (Not In Table)</option>
                                    </optgroup>
                                    <optgroup label='Other Validation'>
                                        <option value='array'>Array</option>
                                        <option value='alpha'>Alpha</option>
                                        <option value='alpha_num'>Alpha Numeric</option>
                                        <option value='alpha_spaces'>Alpha Spaces</option>
                                        <option value='base64_file'>Base64 File</option>
                                        <option value='boolean'>Boolean</option>
                                        <option value='date'>Date (Y-m-d)</option>
                                        <option value='date_format:Y-m-d H:i:s'>DateTime (Y-m-d H:i:s)</option>
                                        <option value='date_format'>Date Format Custom</option>
                                        <option value='digits'>Digits</option>
                                        <option value='digits_between'>Digits Between (Min,Max)</option>
                                        <option value='in'>In (a,b,c)</option>
                                        <option value='json'>Json Valid</option>
                                        <option value='mimes'>Mimes Type</option>
                                        <option value='min'>Min</option>
                                        <option value='max'>Max</option>
                                        <option value='numeric'>Numeric</option>
                                        <option value='not_in'>Not In (a,b,c)</option>
                                        <option value='url'>URL Valid</option>
                                    </optgroup>
                                    <optgroup label='Other'>
                                        <option value='ref'>Child Table References</option>
                                    </optgroup>
                                </select></td>
                            <td><input class='form-control' type='text' name='params_config[]'></td>
                            <td><select class='form-control params_required' name='params_required[]'>
                                    <option value='1'>YES</option>
                                    <option value='0'>NO</option>
                                </select></td>
                            <td><select class='form-control params_used' name='params_used[]'>
                                    <option value='1'>YES</option>
                                    <option value='0'>NO</option>
                                </select></td>
                            <td class='col-delete'><a class='btn btn-primary' href='javascript:void(0)' onclick='addParam()'><i class='fa fa-plus'></i></a></td>
                        </tr>
                        </tfoot>
                    </table>

                    <div class="help-block">
                        To set as comment at description. Add prefix * (asterisk) before description. Unless will be set as default value.
                    </div>
                </div>

                <div class='form-group'>
                    <label>SQL Where Query (Optional)</label>
                    <textarea name='sql_where' rows='3' class='form-control' placeholder="status = [paramStatus]">{{$row->sql_where}}</textarea>
                    <div class='help-block'>Use [paramName] to get the parameter value. e.g : [id] or [email]. Do not use quotes or HTML entities.<br>Password
                        parameters <strong>cannot</strong> be used.
                    </div>
                </div>

                <div class='form-group'>
                    <div class='clearfix'>
                        <label><i class='fa fa-cog'></i> Response</label>
                        <a class='pull-right btn btn-xs btn-primary' href='javascript:void(0)' onclick='load_response()'><i class='fa fa-refresh'></i> Reset</a>
                    </div>
                    <div id='response'>
                        <table id='table-response' class='table table-striped table-bordered'>
                            <thead>
                            <tr>
                                <th width="3%">No</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Sub Query</th>
                                <th width="8%">Enable</th>
                                <th width="3%">-</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class='row-no-data'>
                                <td colspan='7'>There is no data</td>
                            </tr>
                            </tbody>
                            <tfoot style="display:none">
                            <tr class='tr-additional'>
                                <td>#</td>
                                <td width="20%"><input placeholder='E.g : grand_total' name='responses_name[]' class='form-control' type='text'/>
                                    <small>Enter alias name</small>
                                </td>
                                <td>
                                    <select class='form-control' name='responses_type[]'>
                                        <option value='integer'>Integer</option>
                                        <option value='boolean'>Boolean</option>
                                        <option value='string'>String</option>
                                        <option value='file'>File</option>
                                        <option value='date'>Date</option>
                                        <option value='datetime'>DateTime</option>
                                        <option value='double'>Double</option>
                                        <option value='custom'>Custom (Not in Table)</option>
                                    </select>
                                </td>
                                <td><input placeholder="E.g : select sum(total) from order_detail where id_order = order.id" name='responses_subquery[]'
                                           class='form-control' type='text'>
                                    <small>Enter sub query without alias name</small>
                                </td>
                                <td><select class='form-control responses_used' name='responses_used[]'>
                                        <option value='1'>YES</option>
                                        <option value='0'>NO</option>
                                    </select></td>
                                <td class='col-delete'><a class='btn btn-primary' href='javascript:void(0)' onclick='addResponse()'><i
                                                class='fa fa-plus'></i></a></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class='form-group'>
                    <label>API Description</label>
                    <textarea name='keterangan' rows='3' class='form-control wysiwyg' placeholder='Optional'>{{$row->keterangan}}</textarea>
                </div>

                <div class='form-group'>
                    <input type='submit' class='btn btn-success' value='SAVE & GENERATE API'/>
                </div>


        </div><!--END BODY-->
    </div><!--END BOX-->

@endsection
