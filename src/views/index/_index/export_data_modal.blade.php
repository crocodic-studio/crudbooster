<div class="modal fade" tabindex="-1" role="dialog" id='export-data'>
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button class="close" aria-label="Close" type="button" data-dismiss="modal">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title"><i class='fa fa-download'></i> {{cbTrans("export_dialog_title")}}</h4>
            </div>

            <form method='post' target="_blank" action='{{ CRUDBooster::mainpath("export-data?t=".time()) }}'>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                {!! CRUDBooster::getUrlParameters() !!}
                <div class="modal-body">
                    <div class="form-group">
                        <label>{{cbTrans("export_dialog_filename")}}</label>
                        <input type='text' name='filename' class='form-control' required
                               value='Report {{ $module_name }} - {{date("d M Y")}}'/>
                        <div class='help-block'>
                            {{cbTrans("export_dialog_help_filename")}}
                        </div>
                    </div>

                    <div class="form-group">
                        <label>{{cbTrans("export_dialog_maxdata")}}</label>
                        <input type='number' name='limit' class='form-control' required value='100' max="100000"
                               min="1"/>
                        <div class='help-block'>{{cbTrans("export_dialog_help_maxdata")}}</div>
                    </div>

                    <div class='form-group'>
                        <label>{{cbTrans("export_dialog_columns")}}</label><br/>
                        @foreach($columns as $col)
                            <div class='checkbox inline'>
                                <label>
                                    <input type='checkbox' checked name='columns[]' value='{{$col["name"]}}'>{{$col["label"]}}
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <div class="form-group">
                        <label>{{cbTrans("export_dialog_format_export")}}</label>
                        <select name='fileformat' class='form-control'>
                            <option value='pdf'>PDF</option>
                            <option value='xls'>Microsoft Excel (xls)</option>
                            <option value='csv'>CSV</option>
                        </select>
                    </div>

                    <p><a href='javascript:void(0)' class='toggle_advanced_report'>
                            <i class='fa fa-plus-square-o'></i> {{cbTrans("export_dialog_show_advanced")}}</a>
                    </p>

                    <div id='advanced_export' style='display: none'>


                        <div class="form-group">
                            <label>{{cbTrans("export_dialog_page_size")}}</label>
                            <select class='form-control' name='page_size'>
                                <option
                                    {!! ($setting->default_paper_size == 'Letter') ? "selected" : ""  !!} value='Letter'>
                                    Letter
                                </option>
                                <option
                                    {!! ($setting->default_paper_size == 'Legal') ? "selected" : "" !!}  value='Legal'>
                                        Legal
                                    </option>
                                    <option
                                  {!! ($setting->default_paper_size == 'Ledger') ? "selected" : ""  !!}  value='Ledger'>
                                    Ledger
                                </option>
                                @for($i = 0;$i <= 8;$i++)
                                    <option  {!! ($setting->default_paper_size == 'A'.$i) ? "selected" : "" !!} value='A{{$i}}'>A{{$i}}</option>
                                @endfor

                                @for($i = 0;$i <= 10;$i++)
                                    <option {!! ($setting->default_paper_size == 'B'.$i) ? "selected" : "" !!} value='B{{$i}}'>B{{$i}}</option>
                                @endfor
                            </select>
                            <div class='help-block'><input type='checkbox' name='default_paper_size'
                                                           value='1'/> {{cbTrans("export_dialog_set_default")}}
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{cbTrans("export_dialog_page_orientation")}}</label>
                            <select class='form-control' name='page_orientation'>
                                <option value='potrait'>Potrait</option>
                                <option value='landscape'>Landscape</option>
                            </select>
                        </div>
                    </div>

                </div>
                <div class="modal-footer" align="right">
                    <button class="btn btn-default" type="button"
                            data-dismiss="modal">{{cbTrans("button_close")}}</button>
                    <button class="btn btn-primary btn-submit" type="submit">{{cbTrans('button_submit')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>