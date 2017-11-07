@push('bottom')
    <script type="text/javascript">
        $(document).ready(function () {
            var $window = $(window);

            function checkWidth() {
                var windowsize = $window.width();
                if (windowsize > 500) {
                    console.log(windowsize);
                    $('#box-body-table').removeClass('table-responsive');
                } else {
                    console.log(windowsize);
                    $('#box-body-table').addClass('table-responsive');
                }
            }

            checkWidth();
            $(window).resize(checkWidth);

            $('.selected-action ul li a').click(function () {
                var name = $(this).data('name');
                $('#form-table input[name="button_name"]').val(name);
                var title = $(this).attr('title');

                swal({
                        title: "{{cbTrans("confirmation_title")}}",
                        text: "{{cbTrans("alert_bulk_action_button")}} " + title + " ?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#008D4C",
                        confirmButtonText: "{{cbTrans('confirmation_yes')}}",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    },
                    function () {
                        $('#form-table').submit();
                    });

            })

            $('table tbody tr .button_action a').click(function (e) {
                e.stopPropagation();
            })
        });
    </script>
@endpush

<form id='form-table' method='post' action='{{CRUDBooster::mainpath("action-selected")}}'>
    <input type='hidden' name='button_name' value=''/>
    <input type='hidden' name='_token' value='{{csrf_token()}}'/>
    <table id='table_dashboard' class="table table-hover table-striped table-bordered">
        <thead>
        <tr class="active">
            @if($button_bulk_action)
                <th width='3%'><input type='checkbox' id='checkall'/></th>
            @endif
            @if($show_numbering)
                <th width="1%">{{ cbTrans('no') }}</th>
            @endif

            <?php
            foreach ($columns as $col) {
                if ($col['visible'] === FALSE) continue;

                $sort_column = Request::get('filter_column');
                $colname = $col['label'];
                $name = $col['name'];
                $field = $col['field_with'];
                $width = ($col['width']) ?: "auto";
                $mainpath = trim(CRUDBooster::mainpath(), '/').$build_query;
                echo "<th width='$width'>";
                if (isset($sort_column[$field])) {
                    switch ($sort_column[$field]['sorting']) {
                        case 'asc':
                            $url = CRUDBooster::urlFilterColumn($field, 'sorting', 'desc');
                            echo "<a href='$url' title='Click to sort descending'>$colname &nbsp; <i class='fa fa-sort-desc'></i></a>";
                            break;
                        case 'desc':
                            $url = CRUDBooster::urlFilterColumn($field, 'sorting', 'asc');
                            echo "<a href='$url' title='Click to sort ascending'>$colname &nbsp; <i class='fa fa-sort-asc'></i></a>";
                            break;
                        default:
                            $url = CRUDBooster::urlFilterColumn($field, 'sorting', 'asc');
                            echo "<a href='$url' title='Click to sort ascending'>$colname &nbsp; <i class='fa fa-sort'></i></a>";
                            break;
                    }
                } else {
                    $url = CRUDBooster::urlFilterColumn($field, 'sorting', 'asc');
                    echo "<a href='$url' title='Click to sort ascending'>$colname &nbsp; <i class='fa fa-sort'></i></a>";
                }

                echo "</th>";
            }
            ?>

            @if($button_table_action)
                @if(CRUDBooster::canUpdate() || CRUDBooster::canDelete() || CRUDBooster::canRead())
                    <th width='{{$button_action_width?:"auto"}}'
                        style="text-align:right">{{cbTrans("action_label")}}</th>
                @endif
            @endif
        </tr>
        </thead>
        <tbody>
        @if(count($result)==0)
            <tr class='warning'>
                @if($button_bulk_action && $show_numbering)
                    <td colspan='{{count($columns)+3}}' align="center">
                @elseif( ($button_bulk_action && ! $show_numbering) || (! $button_bulk_action && $show_numbering) )
                    <td colspan='{{count($columns)+2}}' align="center">
                @else
                    <td colspan='{{count($columns)+1}}' align="center">
                @endif
                    <i class='fa fa-search'></i> {{cbTrans("table_data_not_found")}}
                </td>
            </tr>
        @endif

        @foreach($html_contents['html'] as $i=>$hc)

            @if($table_row_color)
                <?php $tr_color = NULL;?>
                @foreach($table_row_color as $trc)
                    <?php
                    $query = $trc['condition'];
                    $color = $trc['color'];
                    $row = $html_contents['data'][$i];
                    foreach ($row as $key => $val) {
                        $query = str_replace("[".$key."]", '"'.$val.'"', $query);
                    }

                    @eval("if($query) {
                        \$tr_color = \$color;
                    }");
                    ?>
                @endforeach
               <tr class='{!! $tr_color !!}'>
            @else
                <tr>
                    @endif

                    @foreach($hc as $h)
                        <td>{!! $h !!}</td>
                    @endforeach
                </tr>
                @endforeach
        </tbody>


        <tfoot>
        <tr>
            @if($button_bulk_action)
                <th>&nbsp;</th>
            @endif

            @if($show_numbering)
                <th>&nbsp;</th>
            @endif

            <?php
            foreach ($columns as $col) {
                if ($col['visible'] === FALSE) continue;
                $colname = $col['label'];
                $width = ($col['width']) ?: "auto";
                echo "<th width='$width'>$colname</th>";
            }
            ?>

            @if($button_table_action)
                @if(CRUDBooster::canUpdate() || CRUDBooster::canDelete() || CRUDBooster::canRead())
                    <th> -</th>
                @endif
            @endif
        </tr>
        </tfoot>
    </table>

</form><!--END FORM TABLE-->

<p>{!! urldecode(str_replace("/?","?",$result->appends(Request::all())->render())) !!}</p>





@if($columns)
    @push('bottom')
        <script>
            $(function () {
                $('.btn-filter-data').click(function () {
                    $('#filter-data').modal('show');
                })

                $('.btn-export-data').click(function () {
                    $('#export-data').modal('show');
                })

                var toggle_advanced_report_boolean = 1;
                $(".toggle_advanced_report").click(function () {

                    if (toggle_advanced_report_boolean == 1) {
                        $("#advanced_export").slideDown();
                        $(this).html("<i class='fa fa-minus-square-o'></i> {{cbTrans('export_dialog_show_advanced')}}");
                        toggle_advanced_report_boolean = 0;
                    } else {
                        $("#advanced_export").slideUp();
                        $(this).html("<i class='fa fa-plus-square-o'></i> {{cbTrans('export_dialog_show_advanced')}}");
                        toggle_advanced_report_boolean = 1;
                    }

                })


                $("#table_dashboard .checkbox").click(function () {
                    var is_any_checked = $("#table_dashboard .checkbox:checked").length;
                    if (is_any_checked) {
                        $(".btn-delete-selected").removeClass("disabled");
                    } else {
                        $(".btn-delete-selected").addClass("disabled");
                    }
                })

                $("#table_dashboard #checkall").click(function () {
                    var is_checked = $(this).is(":checked");
                    $("#table_dashboard .checkbox").prop("checked", !is_checked).trigger("click");
                })

                $('#btn_advanced_filter').click(function () {
                    $('#advanced_filter_modal').modal('show');
                })

                $(".filter-combo").change(function () {
                    var n = $(this).val();
                    var p = $(this).parents('.row-filter-combo');
                    var type_data = $(this).attr('data-type');
                    var filter_value = p.find('.filter-value');

                    p.find('.between-group').hide();
                    p.find('.between-group').find('input').prop('disabled', true);
                    filter_value.val('').show().focus();
                    switch (n) {
                        default:
                            filter_value.removeAttr('placeholder').val('').prop('disabled', true);
                            p.find('.between-group').find('input').prop('disabled', true);
                            break;
                        case 'like':
                        case 'not like':
                            filter_value.attr('placeholder', '{{cbTrans("filter_eg")}} : {{cbTrans("filter_lorem_ipsum")}}').prop('disabled', false);
                            break;
                        case 'asc':
                            filter_value.prop('disabled', true).attr('placeholder', '{{cbTrans("filter_sort_ascending")}}');
                            break;
                        case 'desc':
                            filter_value.prop('disabled', true).attr('placeholder', '{{cbTrans("filter_sort_descending")}}');
                            break;
                        case '=':
                            filter_value.prop('disabled', false).attr('placeholder', '{{cbTrans("filter_eg")}} : {{cbTrans("filter_lorem_ipsum")}}');
                            break;
                        case '>=':
                            filter_value.prop('disabled', false).attr('placeholder', '{{cbTrans("filter_eg")}} : 1000');
                            break;
                        case '<=':
                            filter_value.prop('disabled', false).attr('placeholder', '{{cbTrans("filter_eg")}} : 1000');
                            break;
                        case '>':
                            filter_value.prop('disabled', false).attr('placeholder', '{{cbTrans("filter_eg")}} : 1000');
                            break;
                        case '<':
                            filter_value.prop('disabled', false).attr('placeholder', '{{cbTrans("filter_eg")}} : 1000');
                            break;
                        case '!=':
                            filter_value.prop('disabled', false).attr('placeholder', '{{cbTrans("filter_eg")}} : {{cbTrans("filter_lorem_ipsum")}}');
                            break;
                        case 'in':
                            filter_value.prop('disabled', false).attr('placeholder', '{{cbTrans("filter_eg")}} : {{cbTrans("filter_lorem_ipsum_dolor_sit")}}');
                            break;
                        case 'not in':
                            filter_value.prop('disabled', false).attr('placeholder', '{{cbTrans("filter_eg")}} : {{cbTrans("filter_lorem_ipsum_dolor_sit")}}');
                            break;
                        case 'between':
                            filter_value.val('').hide();
                            p.find('.between-group input').prop('disabled', false);
                            p.find('.between-group').show().focus();
                            p.find('.filter-value-between').prop('disabled', false);
                            break;
                    }
                })

                /* Remove disabled when reload page and input value is filled */
                $(".filter-value").each(function () {
                    var v = $(this).val();
                    if (v != '') $(this).prop('disabled', false);
                })

            })
        </script>

        <!-- MODAL FOR SORTING DATA-->
        @include('crudbooster::default._index.advanced_filter_modal', ['columns' => $columns])


        <script>
            $(function () {
                $('.btn-filter-data').click(function () {
                    $('#filter-data').modal('show');
                })

                $('.btn-export-data').click(function () {
                    $('#export-data').modal('show');
                })

                var toggle_advanced_report_boolean = 1;
                $(".toggle_advanced_report").click(function () {

                    if (toggle_advanced_report_boolean == 1) {
                        $("#advanced_export").slideDown();
                        $(this).html("<i class='fa fa-minus-square-o'></i> {{cbTrans('export_dialog_show_advanced')}}");
                        toggle_advanced_report_boolean = 0;
                    } else {
                        $("#advanced_export").slideUp();
                        $(this).html("<i class='fa fa-plus-square-o'></i> {{cbTrans('export_dialog_show_advanced')}}");
                        toggle_advanced_report_boolean = 1;
                    }

                })
            })
        </script>

        <!-- MODAL FOR EXPORT DATA-->
        @include('crudbooster::default._index.export_data_modal')
    @endpush
@endif
