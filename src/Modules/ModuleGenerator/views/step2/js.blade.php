<script>
    var columns = {!! json_encode($columns) !!};
    var tables = {!! json_encode($table_list) !!};

    function ucwords(str) {
        return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
            return $1.toUpperCase();
        });
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

    function showTable(t) {
        t = $(t);
        t.next("ul").remove();
        var list = '';
        $.each(tables, function (i, obj) {
            list += "<li>" + obj + "</li>";
        });

        t.after("<ul class='sub'>" + list + "</ul>");
    }

    function showTableLike(t) {
        t = $(t);
        var v = t.val();

        t.next("ul").remove();
        if (!v) return false;

        var list = '';
        $.each(tables, function (i, obj) {
            if (obj.includes(v.toLowerCase())) {
                list += "<li>" + obj + "</li>";
            }
        });

        t.after("<ul class='sub'>" + list + "</ul>");
    }

    function showTableFieldLike(t) {
        t = $(t);
        var table = t.parent().parent().find('.join_table').val();
        var v = t.val();

        t.next("ul").remove();

        if (!table) return false;
        if (!v) return false;

        t.after("<ul class='sub'><li><i class='fa fa-spin fa-spinner'></i> Loading...</li></ul>");

        $.get("{{CRUDBooster::mainpath('table-columns')}}/" + table, function (response) {
            t.next("ul").remove();
            var list = '';
            $.each(response, function (i, obj) {
                if (obj.includes(v.toLowerCase())) {
                    list += "<li>" + obj + "</li>";
                }
            });
            t.after("<ul class='sub'>" + list + "</ul>");
        });
    }

    function showTableField(t) {
        t = $(t);
        var table = t.parent().parent().find('.join_table').val();
        var v = t.val();

        if (!table) return false;

        t.after("<ul class='sub'><li><i class='fa fa-spin fa-spinner'></i> Loading...</li></ul>");

        $.get("{{CRUDBooster::mainpath('table-columns')}}/" + table, function (response) {
            t.next("ul").remove();
            var list = '';
            $.each(response, function (i, obj) {
                list += "<li>" + obj + "</li>";
            });
            t.after("<ul class='sub'>" + list + "</ul>");
        });
    }

    $(function () {


        $(document).on('click', '.btn-plus', function () {
            var tr_parent = $(this).parent().parent('tr');
            var clone = $('#tr-sample').clone();
            clone.removeAttr('id');
            tr_parent.after(clone);
            $('.table-display tr').not('#tr-sample').show();
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
            $(this).parent('ul').prev('input[type=text]').val(v);
            $(this).parent('ul').remove();
        })

        $(document).on('click', '.table-display .btn-delete', function () {
            $(this).parent().parent().remove();
        })

        $(document).on('click', '.table-display .btn-up', function () {
            var tr = $(this).parent().parent();
            var trPrev = tr.prev('tr');
            if (trPrev.length != 0) {

                tr.prev('tr').before(tr.clone());
                tr.remove();
            }
        })

        $(document).on('click', '.table-display .btn-down', function () {
            var tr = $(this).parent().parent();
            var trPrev = tr.next('tr');
            if (trPrev.length != 0) {

                tr.next('tr').after(tr.clone());
                tr.remove();
            }
        })

        $(document).on('change', '.is_image', function () {
            var tr = $(this).parent().parent();
            if ($(this).val() == 1) {
                tr.find('.is_download').val(0);
            }
        })

        $(document).on('change', '.is_download', function () {
            var tr = $(this).parent().parent();
            if ($(this).val() == 1) {
                tr.find('.is_image').val(0);
            }
        })

    })
</script>