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
                        title: "{{cbLang("confirmation_title")}}",
                        text: "{{cbLang("alert_bulk_action_button")}} " + title + " 
			",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#008D4C",
                        confirmButtonText: "{{cbLang('confirmation_yes')}}",
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
            <?php if($button_bulk_action):?>
            <th width='3%'><input type='checkbox' id='checkall'/></th>
            <?php endif;?>
            <?php if($show_numbering):?>
            <th width="1%">{{ cbLang('no') }}</th>
            <?php endif;?>
            <?php
            foreach ($columns as $col) {
                if ($col['visible'] === FALSE) continue;

                $sort_column = Request::get('filter_column');
                $colname = $col['label'];
                $name = $col['name'];
                $field = $col['field_with'];
                $width = (isset($col['width'])) ?$col['width']: "auto";
		$style = (isset($col['style'])) ?$col['style']: "";
                $mainpath = trim(CRUDBooster::mainpath(), '/').$build_query;
                echo "<th width='$width' $style>";
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
                @if(CRUDBooster::isUpdate() || CRUDBooster::isDelete() || CRUDBooster::isRead())
                    <th width='{{ isset($button_action_width)? $button_action_width :"auto"}}' style="text-align:right">{{cbLang("action_label")}}</th>
                @endif
            @endif
        </tr>
        </thead>
        <tbody>
        @if(count($result)==0)
            <tr class='warning'>
                <?php if($button_bulk_action && $show_numbering):?>
                <td colspan='{{count($columns)+3}}' align="center">
                <?php elseif( ($button_bulk_action && ! $show_numbering) || (! $button_bulk_action && $show_numbering) ):?>
                <td colspan='{{count($columns)+2}}' align="center">
                <?php else:?>
                <td colspan='{{count($columns)+1}}' align="center">
                    <?php endif;?>

                    <i class='fa fa-search'></i> {{cbLang("table_data_not_found")}}
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
                <?php echo "<tr class='$tr_color'>";?>
            @else
                <tr>
                    @endif

                    @foreach($hc as $j=>$h)
                        <td {{ $columns[$j]['style'] or ''}}>{!! $h !!}</td>
                    @endforeach
                </tr>
                @endforeach
        </tbody>


        <tfoot>
        <tr>
            <?php if($button_bulk_action):?>
            <th>&nbsp;</th>
            <?php endif;?>

            <?php if($show_numbering):?>
            <th>&nbsp;</th>
            <?php endif;?>

            <?php
            foreach ($columns as $col) {
                if ($col['visible'] === FALSE) continue;
                $colname = $col['label'];
                $width = (isset($col['width'])) ?$col['width']: "auto";
		$style = (isset($col['style'])) ? $col['style']: "";
                echo "<th width='$width' $style>$colname</th>";
            }
            ?>

            @if($button_table_action)
                @if(CRUDBooster::isUpdate() || CRUDBooster::isDelete() || CRUDBooster::isRead())
                    <th> -</th>
                @endif
            @endif
        </tr>
        </tfoot>
    </table>

</form><!--END FORM TABLE-->

<div class="col-md-8">{!! urldecode(str_replace("/?","?",$result->appends(Request::all())->render())) !!}</div>
<?php
$from = $result->count() ? ($result->perPage() * $result->currentPage() - $result->perPage() + 1) : 0;
$to = $result->perPage() * $result->currentPage() - $result->perPage() + $result->count();
$total = $result->total();
?>
<div class="col-md-4"><span class="pull-right">{{ cbLang("filter_rows_total") }}
        : {{ $from }} {{ cbLang("filter_rows_to") }} {{ $to }} {{ cbLang("filter_rows_of") }} {{ $total }}</span></div>

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
                        $(this).html("<i class='fa fa-minus-square-o'></i> {{cbLang('export_dialog_show_advanced')}}");
                        toggle_advanced_report_boolean = 0;
                    } else {
                        $("#advanced_export").slideUp();
                        $(this).html("<i class='fa fa-plus-square-o'></i> {{cbLang('export_dialog_show_advanced')}}");
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
                            filter_value.attr('placeholder', '{{cbLang("filter_eg")}} : {{cbLang("filter_lorem_ipsum")}}').prop('disabled', false);
                            break;
                        case 'asc':
                            filter_value.prop('disabled', true).attr('placeholder', '{{cbLang("filter_sort_ascending")}}');
                            break;
                        case 'desc':
                            filter_value.prop('disabled', true).attr('placeholder', '{{cbLang("filter_sort_descending")}}');
                            break;
                        case '=':
                            filter_value.prop('disabled', false).attr('placeholder', '{{cbLang("filter_eg")}} : {{cbLang("filter_lorem_ipsum")}}');
                            break;
                        case '>=':
                            filter_value.prop('disabled', false).attr('placeholder', '{{cbLang("filter_eg")}} : 1000');
                            break;
                        case '<=':
                            filter_value.prop('disabled', false).attr('placeholder', '{{cbLang("filter_eg")}} : 1000');
                            break;
                        case '>':
                            filter_value.prop('disabled', false).attr('placeholder', '{{cbLang("filter_eg")}} : 1000');
                            break;
                        case '<':
                            filter_value.prop('disabled', false).attr('placeholder', '{{cbLang("filter_eg")}} : 1000');
                            break;
                        case '!=':
                            filter_value.prop('disabled', false).attr('placeholder', '{{cbLang("filter_eg")}} : {{cbLang("filter_lorem_ipsum")}}');
                            break;
                        case 'in':
                            filter_value.prop('disabled', false).attr('placeholder', '{{cbLang("filter_eg")}} : {{cbLang("filter_lorem_ipsum_dolor_sit")}}');
                            break;
                        case 'not in':
                            filter_value.prop('disabled', false).attr('placeholder', '{{cbLang("filter_eg")}} : {{cbLang("filter_lorem_ipsum_dolor_sit")}}');
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
        <div class="modal fade" tabindex="-1" role="dialog" id='advanced_filter_modal'>
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" aria-label="Close" type="button" data-dismiss="modal">
                            <span aria-hidden="true">×</span></button>
                        <h4 class="modal-title"><i class='fa fa-filter'></i> {{cbLang("filter_dialog_title")}}</h4>
                    </div>
                    <form method='get' action=''>
                        <div class="modal-body">
                            <?php foreach($columns as $key => $col):?>
                            <?php if (isset($col['image']) || isset($col['download']) || $col['visible'] === FALSE) continue;?>

                            <div class='form-group'>

                                <div class='row-filter-combo row'>

                                    <div class="col-sm-2">
                                        <strong>{{$col['label']}}</strong>
                                    </div>

                                    <div class='col-sm-3'>
                                        <select name='filter_column[{{$col["field_with"]}}][type]' data-type='{{$col["type_data"]}}'
                                                class="filter-combo form-control">
                                            <option value=''>** {{cbLang("filter_select_operator_type")}}</option>
                                            @if(in_array($col['type_data'],['string','varchar','text','char']))
                                                <option {{ (CRUDBooster::getTypeFilter($col["field_with"]) == 'like')?"selected":"" }} value='like'>{{cbLang("filter_like")}}</option> @endif
                                            @if(in_array($col['type_data'],['string','varchar','text','char']))
                                                <option {{ (CRUDBooster::getTypeFilter($col["field_with"]) == 'not like')?"selected":"" }} value='not like'>{{cbLang("filter_not_like")}}</option>@endif

                                            <option typeallow='all'
                                                    {{ (CRUDBooster::getTypeFilter($col["field_with"]) == '=')?"selected":"" }} value='='>{{cbLang("filter_equal_to")}}</option>
                                            @if(in_array($col['type_data'],['int','integer','smallint','tinyint','mediumint','bigint','double','float','decimal','time']))
                                                <option {{ (CRUDBooster::getTypeFilter($col["field_with"]) == '>=')?"selected":"" }} value='>='>{{cbLang("filter_greater_than_or_equal")}}</option>@endif
                                            @if(in_array($col['type_data'],['int','integer','smallint','tinyint','mediumint','bigint','double','float','decimal','time']))
                                                <option {{ (CRUDBooster::getTypeFilter($col["field_with"]) == '<=')?"selected":"" }} value='<='>{{cbLang("filter_less_than_or_equal")}}</option>@endif
                                            @if(in_array($col['type_data'],['int','integer','smallint','tinyint','mediumint','bigint','double','float','decimal','time']))
                                                <option {{ (CRUDBooster::getTypeFilter($col["field_with"]) == '<')?"selected":"" }} value='<'>{{cbLang("filter_less_than")}}</option>@endif
                                            @if(in_array($col['type_data'],['int','integer','smallint','tinyint','mediumint','bigint','double','float','decimal','time']))
                                                <option {{ (CRUDBooster::getTypeFilter($col["field_with"]) == '>')?"selected":"" }} value='>'>{{cbLang("filter_greater_than")}}</option>@endif
                                            <option typeallow='all'
                                                    {{ (CRUDBooster::getTypeFilter($col["field_with"]) == '!=')?"selected":"" }} value='!='>{{cbLang("filter_not_equal_to")}}</option>
                                            <option typeallow='all'
                                                    {{ (CRUDBooster::getTypeFilter($col["field_with"]) == 'in')?"selected":"" }} value='in'>{{cbLang("filter_in")}}</option>
                                            <option typeallow='all'
                                                    {{ (CRUDBooster::getTypeFilter($col["field_with"]) == 'not in')?"selected":"" }} value='not in'>{{cbLang("filter_not_in")}}</option>
                                            @if(in_array($col['type_data'],['date','time','datetime','int','integer','smallint','tinyint','mediumint','bigint','double','float','decimal','timestamp']))
                                                <option {{ (CRUDBooster::getTypeFilter($col["field_with"]) == 'between')?"selected":"" }} value='between'>{{cbLang("filter_between")}}</option>@endif
                                            <option {{ (CRUDBooster::getTypeFilter($col["field_with"]) == 'empty')?"selected":"" }} value='empty'>{{cbLang("filter_empty_or_null")}}</option>
                                        </select>
                                    </div><!--END COL_SM_4-->


                                    <div class='col-sm-5'>
                                        <input type='text' class='filter-value form-control'
                                               style="{{ (CRUDBooster::getTypeFilter($col["field_with"]) == 'between')?"display:none":"display:block"}}"
                                               disabled name='filter_column[{{$col["field_with"]}}][value]'
                                               value='{{ (!is_array(CRUDBooster::getValueFilter($col["field_with"])))?CRUDBooster::getValueFilter($col["field_with"]):"" }}'>

                                        <div class='row between-group'
                                             style="{{ (CRUDBooster::getTypeFilter($col["field_with"]) == 'between')?"display:block":"display:none" }}">
                                            <div class='col-sm-6'>
                                                <div class='input-group {{ ($col["type_data"] == "time")?"bootstrap-timepicker":"" }}'>
                                                    <span class="input-group-addon">{{cbLang("filter_from")}}:</span>
                                                    <input
                                                            {{ (CRUDBooster::getTypeFilter($col["field_with"]) != 'between')?"disabled":"" }}
                                                            type='text'
                                                            class='filter-value-between form-control {{ in_array($col["type_data"],["date","datetime","timestamp"]) ? "datepicker" : ((in_array($col["type_data"],["time"])) ? "timepicker" : "") }}'
                                                            {{ in_array($col["type_data"],["date","datetime","timestamp","time"]) ? "readonly" : "" }}
                                                            placeholder='{{$col["label"]}} {{cbLang("filter_from")}}'
                                                            name='filter_column[{{$col["field_with"]}}][value][]' 
                                                            value='<?php
                                                                $value = CRUDBooster::getValueFilter($col["field_with"]);
                                                                echo (CRUDBooster::getTypeFilter($col["field_with"]) == 'between') ? $value[0] : "";
                                                            ?>'>
                                                </div>
                                            </div>
                                            <div class='col-sm-6'>
                                                <div class='input-group {{ ($col["type_data"] == "time")?"bootstrap-timepicker":"" }}'>
                                                    <span class="input-group-addon">{{cbLang("filter_to")}}:</span>
                                                    <input
                                                            {{ (CRUDBooster::getTypeFilter($col["field_with"]) != 'between')?"disabled":"" }}
                                                            type='text'
                                                            class='filter-value-between form-control {{ in_array($col["type_data"],["date","datetime","timestamp"]) ? "datepicker" : (in_array($col["type_data"],["time"]) ? "timepicker" : "" )}}'
                                                            {{ in_array($col["type_data"],["date","datetime","timestamp","time"]) ? "readonly": "" }}
                                                            placeholder='{{$col["label"]}} {{cbLang("filter_to")}}'
                                                            name='filter_column[{{$col["field_with"]}}][value][]'
                                                            value='<?php
                                                                $value = CRUDBooster::getValueFilter($col["field_with"]);
                                                                echo (CRUDBooster::getTypeFilter($col["field_with"]) == 'between') ? $value[1] : "";
                                                            ?>'>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!--END COL_SM_6-->


                                    <div class='col-sm-2'>
                                        <select class='form-control' name='filter_column[{{$col["field_with"]}}][sorting]'>
                                            <option value=''>{{cbLang("filter_sorting")}}</option>
                                            <option {{ (CRUDBooster::getSortingFilter($col["field_with"]) == 'asc')?"selected":"" }} value='asc'>{{cbLang("filter_ascending")}}</option>
                                            <option {{ (CRUDBooster::getSortingFilter($col["field_with"]) == 'desc')?"selected":"" }} value='desc'>{{cbLang("filter_descending")}}</option>
                                        </select>
                                    </div><!--END_COL_SM_2-->

                                </div>

                            </div>
                            <?php endforeach;?>

                        </div>
                        <div class="modal-footer" align="right">
                            <button class="btn btn-default" type="button" data-dismiss="modal">{{cbLang("button_close")}}</button>
                            <button class="btn btn-default btn-reset" type="reset"
                                    onclick='location.href="{{Request::get("lasturl")}}"'>{{cbLang("button_reset")}}</button>
                            <button class="btn btn-primary btn-submit" type="submit">{{cbLang("button_submit")}}</button>
                        </div>
                        {!! CRUDBooster::getUrlParameters(['filter_column','lasturl']) !!}
                        <input type="hidden" name="lasturl" value="{{Request::get('lasturl')?Request::get('lasturl'):Request::fullUrl()}}">
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
        </div>


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
                        $(this).html("<i class='fa fa-minus-square-o'></i> {{cbLang('export_dialog_show_advanced')}}");
                        toggle_advanced_report_boolean = 0;
                    } else {
                        $("#advanced_export").slideUp();
                        $(this).html("<i class='fa fa-plus-square-o'></i> {{cbLang('export_dialog_show_advanced')}}");
                        toggle_advanced_report_boolean = 1;
                    }

                })
            })
        </script>

        <!-- MODAL FOR EXPORT DATA-->
        <div class="modal fade" tabindex="-1" role="dialog" id='export-data'>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" aria-label="Close" type="button" data-dismiss="modal">
                            <span aria-hidden="true">×</span></button>
                        <h4 class="modal-title"><i class='fa fa-download'></i> {{cbLang("export_dialog_title")}}</h4>
                    </div>

                    <form method='post' target="_blank" action='{{ CRUDBooster::mainpath("export-data?t=".time()) }}'>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        {!! CRUDBooster::getUrlParameters() !!}
                        <div class="modal-body">
                            <div class="form-group">
                                <label>{{cbLang("export_dialog_filename")}}</label>
                                <input type='text' name='filename' class='form-control' required value='Report {{ $module_name }} - {{date("d M Y")}}'/>
                                <div class='help-block'>
                                    {{cbLang("export_dialog_help_filename")}}
                                </div>
                            </div>

                            <div class="form-group">
                                <label>{{cbLang("export_dialog_maxdata")}}</label>
                                <input type='number' name='limit' class='form-control' required value='100' max="100000" min="1"/>
                                <div class='help-block'>{{cbLang("export_dialog_help_maxdata")}}</div>
                            </div>

                            <div class='form-group'>
                                <label>{{cbLang("export_dialog_columns")}}</label><br/>
                                @foreach($columns as $col)
                                    <div class='checkbox inline'><label><input type='checkbox' checked name='columns[]'
                                                                               value='{{$col["name"]}}'>{{$col["label"]}}</label></div>
                                @endforeach
                            </div>

                            <div class="form-group">
                                <label>{{cbLang("export_dialog_format_export")}}</label>
                                <select name='fileformat' class='form-control'>
                                    <option value='pdf'>PDF</option>
                                    <option value='xls'>Microsoft Excel (xls)</option>
                                    <option value='csv'>CSV</option>
                                </select>
                            </div>

                            <p><a href='javascript:void(0)' class='toggle_advanced_report'><i
                                            class='fa fa-plus-square-o'></i> {{cbLang("export_dialog_show_advanced")}}</a></p>

                            <div id='advanced_export' style='display: none'>


                                <div class="form-group">
                                    <label>{{cbLang("export_dialog_page_size")}}</label>
                                    <select class='form-control' name='page_size'>
                                        <option <?=($setting->default_paper_size == 'Letter') ? "selected" : ""?> value='Letter'>Letter</option>
                                        <option <?=($setting->default_paper_size == 'Legal') ? "selected" : ""?> value='Legal'>Legal</option>
                                        <option <?=($setting->default_paper_size == 'Ledger') ? "selected" : ""?> value='Ledger'>Ledger</option>
                                        <?php for($i = 0;$i <= 8;$i++):
                                        $select = ($setting->default_paper_size == 'A'.$i) ? "selected" : "";
                                        ?>
                                        <option <?=$select?> value='A{{$i}}'>A{{$i}}</option>
                                        <?php endfor;?>

                                        <?php for($i = 0;$i <= 10;$i++):
                                        $select = ($setting->default_paper_size == 'B'.$i) ? "selected" : "";
                                        ?>
                                        <option <?=$select?> value='B{{$i}}'>B{{$i}}</option>
                                        <?php endfor;?>
                                    </select>
                                    <div class='help-block'><input type='checkbox' name='default_paper_size'
                                                                   value='1'/> {{cbLang("export_dialog_set_default")}}</div>
                                </div>

                                <div class="form-group">
                                    <label>{{cbLang("export_dialog_page_orientation")}}</label>
                                    <select class='form-control' name='page_orientation'>
                                        <option value='potrait'>Potrait</option>
                                        <option value='landscape'>Landscape</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer" align="right">
                            <button class="btn btn-default" type="button" data-dismiss="modal">{{cbLang("button_close")}}</button>
                            <button class="btn btn-primary btn-submit" type="submit">{{cbLang('button_submit')}}</button>
                        </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
        </div>
    @endpush
@endif
