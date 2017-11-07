<script type="text/javascript">
    var columns = {!! json_encode($columns) !!};
    var types = {!! json_encode($types) !!};
    var validation_rules = ['required', 'string', 'integer', 'double', 'image', 'date', 'numeric', 'alpha_spaces'];

    function ucwords(str) {
        return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
            return $1.toUpperCase();
        });
    }

    function showTypeSuggest(t) {
        t = $(t);

        t.next("ul").remove();
        var list = '';
        $.each(types, function (i, obj) {
            list += "<li>" + obj + "</li>";
        });

        t.after("<ul class='sub' style='margin-top:40px'>" + list + "</ul>");
    }

    function showTypeSuggestLike(t) {
        t = $(t);

        var v = t.val();
        t.next("ul").remove();
        if (!v) return false;

        var list = '';
        $.each(types, function (i, obj) {
            if (obj.includes(v.toLowerCase())) {
                list += "<li>" + obj + "</li>";
            }
        });

        t.after("<ul class='sub'>" + list + "</ul>");
    }

    function showNameSuggest(t) {
        t = $(t);

        t.next("ul").remove();
        var list = '';
        $.each(columns, function (i, obj) {
            list += "<li>" + obj + "</li>";
        });

        t.after("<ul class='sub'>" + list + "</ul>");
    }

    function showNameSuggestLike(t) {
        t = $(t);

        var v = t.val();
        t.next("ul").remove();
        if (!v) return false;

        var list = '';
        $.each(columns, function (i, obj) {
            if (obj.includes(v.toLowerCase())) {
                list += "<li>" + obj + "</li>";
            }
        });

        t.after("<ul class='sub'>" + list + "</ul>");
    }

    function showColumnSuggest(t) {
        t = $(t);
        t.next("ul").remove();

        var list = '';
        $.each(columns, function (i, obj) {
            obj = obj.replace('id_', '');
            obj = ucwords(obj.replace('_', ' '));
            list += "<li>" + obj + "</li>";
        });

        t.after("<ul class='sub'>" + list + "</ul>");
    }

    function showColumnSuggestLike(t) {
        t = $(t);
        var v = t.val();

        t.next("ul").remove();
        if (!v) return false;

        var list = '';
        $.each(columns, function (i, obj) {

            if (obj.includes(v.toLowerCase())) {
                obj = obj.replace('id_', '');
                obj = ucwords(obj.replace('_', ' '));

                list += "<li>" + obj + "</li>";
            }
        });

        t.after("<ul class='sub'>" + list + "</ul>");
    }

    function showValidationSuggest(t) {
        t = $(t);
        t.next("ul").remove();

        var list = '';
        $.each(validation_rules, function (i, obj) {
            list += "<li>" + obj + "</li>";
        });

        t.after("<ul class='sub'>" + list + "</ul>");
    }

    function showValidationSuggestLike(t) {
        t = $(t);
        var v = t.val();

        t.next("ul").remove();
        if (!v) return false;

        var list = '';
        $.each(validation_rules, function (i, obj) {
            if (obj.includes(v.toLowerCase())) {
                list += "<li>" + obj + "</li>";
            }
        });

        t.after("<ul class='sub'>" + list + "</ul>");
    }

    $(function () {


        $(document).on('click', '.btn-plus', function () {
            var tr_parent = $(this).parent().parent('tr');
            var clone = $('#tr-sample').clone();
            clone.removeAttr('id');
            tr_parent.after(clone);
            $('.table-form tr').not('#tr-sample').show();
        })

//init row
        $('.btn-plus').last().click();

        $(document).mouseup(function (e) {
            var container = $(".sub");
            if (!container.is(e.target)
                && container.has(e.target).length === 0) {
                container.hide();
            }
        });

        $(document).on('click', '.sub li', function () {
            var v = $(this).text();
            var t = $(this).parent('ul').parent('td');
            var tr_index = parseInt(t.parent().index());

            var input_name = $(this).parent().parent('td').find('input[type=text]').attr('name');

            if (input_name == 'type[]') {
                $(this).parent('ul').prev('input[type=text]').val(v).trigger('change');
                $(this).parent('ul').remove();

            } else if (input_name == 'validation[]') {
                var currentVal = $(this).parent('ul').prev('input[type=text]').val();
                if (currentVal != '') {
                    v = currentVal + '|' + v;
                }
                $(this).parent('ul').prev('input[type=text]').val(v).trigger('change');
                $(this).parent('ul').remove();
            } else {
                $(this).parent('ul').prev('input[type=text]').val(v).trigger('change');
                $(this).parent('ul').remove();
            }
        })

        $(document).on('click', '.table-form .btn-delete', function () {
            $(this).parent().parent().remove();
        })

        $(document).on('click', '.table-form .btn-up', function () {
            var tr = $(this).parent().parent();
            var trPrev = tr.prev('tr');
            if (trPrev.length != 0) {

                tr.prev('tr').before(tr.clone());
                tr.remove();
            }
        })

        $(document).on('click', '.table-form .btn-down', function () {
            var tr = $(this).parent().parent();
            var trPrev = tr.next('tr');
            if (trPrev.length != 0) {

                tr.next('tr').after(tr.clone());
                tr.remove();
            }
        })

        var handleOptions = null;

        $('#modal-options .btn-save-option').click(function () {

            var name = handleOptions.parent().parent().parent().parent().find('input.name').val();
            var close = true;
            $('#modal-options .modal-body').find('input,select,textarea').each(function () {
                if ($(this).prop('required')) {
                    if ($(this).val() == '') {
                        $(this).parent().addClass('has-error');
                        $(this).focus();
                        $(this).parent().find('.help-error').show();
                        swal("Oops", "There are some inputs need to filled", "warning");
                        close = false;
                    }
                }
            })
            if (close == true) {
                var style = $('#modal-options input[name=style]').val();
                var placeholder = $('#modal-options input[name=placeholder]').val();
                var help = $('#modal-options input[name=help]').val();

                handleOptions.parent().parent().parent().find('.input-style').val(style);
                handleOptions.parent().parent().parent().find('.input-placeholder').val(placeholder);
                handleOptions.parent().parent().parent().find('.input-help').val(help);

                if ($('#modal-options .input-options-item').length > 0) {
                    handleOptions.parent().parent().parent().find('.input-options').empty();
                    $('#modal-options .input-options-item').each(function () {
                        var v = $(this).val();
                        var n = $(this).attr('name');
                        handleOptions.parent().parent().parent().find('.input-options').append("<input type='hidden' data-option-name='" + n + "' class='input-option-hidden input-" + n + "' name='" + n + "[" + name + "]' value='" + v + "'/>");
                    })
                }

                $('#modal-options').modal('hide');
            }
        })


        $(document).on('click', '.btn-options', function () {
            handleOptions = $(this);
            var input = handleOptions.parent().parent().find('input');
            var type = input.val();

            var style = handleOptions.parent().parent().parent().find('.input-style').val();
            var placeholder = handleOptions.parent().parent().parent().find('.input-placeholder').val();
            var help = handleOptions.parent().parent().parent().find('.input-help').val();

            $('#modal-options input[name=style]').val(style);
            $('#modal-options input[name=placeholder]').val(placeholder);
            $('#modal-options input[name=help]').val(help);

            $.get("{{route('AdminModulesControllerGetTypeInfo')}}/" + type, function (r) {
                r = JSON.parse(r);

                if (r.options) {
                    $('#modal-options .form-group-options').empty();
                    $.each(r.options, function (i, obj) {
                        var required = (obj.required) ? "required" : "";
                        var defaultValue = (obj.default) ? obj.default : "";

                        var v = handleOptions.parent().parent().parent().find('.input-' + obj.name).val();
                        defaultValue = (v) ? v : defaultValue;

                        var html = "<div class='form-group'>";
                        html += "<label>" + obj.label + "</label>";
                        switch (obj.type) {
                            default:
                            case 'text':
                                html += "<input type='text' name='" + obj.name + "' class='input-options-item form-control' value='" + defaultValue + "' " + required + "/>";
                                break;
                            case 'textarea':
                                html += "<textarea class='input-options-item form-control' name='" + obj.name + "' " + required + ">" + defaultValue + "</textarea>";
                                break;
                            case 'array':
                                html += "<input type='text' placeholder='Sparate with semicolon' name='" + obj.name + "' class='input-options-item form-control' value='" + defaultValue + "' " + required + "/>";
                                break;
                            case 'boolean':
                                html += "<select name='" + obj.name + "' class='input-options-item form-control'>";
                                html += "<option value='0'> No</option>";
                                html += "<option value='1'> Yes</option>";
                                html += "</select>";
                                break;
                            case 'radio':
                            case 'select':
                                if (obj.enum) {
                                    html += "<select name='" + obj.name + "' class='input-options-item form-control'>";
                                    $.each(obj.enum, function (e, b) {
                                        html += "<option value='" + b + "'>" + b + "</option>";
                                    })
                                    html += "</select>";
                                }
                                break;
                        }
                        if (obj.help) {
                            html += "<div class='help-block'>" + obj.help + "</div>";
                        }
                        html += "<div class='help-block help-error' style='display:none'>Please fill out this field</div>";
                        html += "</div>";
                        $('#modal-options .form-group-options').append(html);
                    })
                }
            })

            $('#modal-options').modal('show');
        })


        $('.name').change(function () {
            var name = $(this).val();
            console.log(name + ' changed');
            $(this).parent().parent().find('.input-option-hidden').each(function () {
                var optionName = $(this).data('option-name');
                $(this).attr('name', name + "[" + optionName + "]");
            })
        })

    })
</script>
    