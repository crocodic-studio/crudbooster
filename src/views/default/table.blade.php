            <script type="text/javascript">
                  $(document).ready(function() {                      
                      var $window = $(window);                      
                      function checkWidth() {
                          var windowsize = $window.width();
                          if (windowsize > 500) {
                              console.log(windowsize);
                              $('#box-body-table').removeClass('table-responsive');
                          }else{
                            console.log(windowsize);
                              $('#box-body-table').addClass('table-responsive'); 
                          }
                      }                      
                      checkWidth();                      
                      $(window).resize(checkWidth);

                      $('.selected-action ul li a').click(function() {
                        var name = $(this).data('name');
                        $('#form-table input[name="button_name"]').val(name);
                        if(confirm("Are you sure want to do this action ?")) {                            
                          $('#form-table').submit();
                        }
                      })
                  });
                </script>



                  <div class='row'>
                    <div class='col-sm-8'>
                          
                            

                            <div class="selected-action" style="display:inline-block;position:relative">
                              <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">{{trans("crudbooster.button_selected_action")}}
                                <span class="fa fa-caret-down"></span></button>                              
                              <ul class="dropdown-menu">    
                                @if($button_delete && CRUDBooster::isDelete())                                                                                                                                                         
                                <li><a href="javascript:void(0)" data-name='delete' title='{{trans('crudbooster.action_delete_selected')}}'><i class="fa fa-trash"></i> {{trans('crudbooster.action_delete_selected')}}</a></li>
                                @endif

                                @if($button_selected)
                                  @foreach($button_selected as $button)
                                    <li><a href="javascript:void(0)" data-name='{{$button["name"]}}' title='{{$button["label"]}}''><i class="fa fa-{{$button['icon']}}"></i> {{$button['label']}}</a></li>
                                  @endforeach
                                @endif

                              </ul>
                            </div>                          

                          @if($button_show)
                          <a href="{{ CRUDBooster::mainpath() }}" id='btn_show_data' class="btn btn-sm btn-primary" title="{{trans('crudbooster.action_show_data')}}">
                            <i class="fa fa-bars"></i> {{trans('crudbooster.action_show_data')}}
                          </a>
                          @endif

                          @if($button_add && CRUDBooster::isCreate())
                          <a href="{{ CRUDBooster::mainpath('add') }}" id='btn_add_new_data' class="btn btn-sm btn-primary" title="{{trans('crudbooster.action_add_data')}}">
                            <i class="fa fa-plus"></i> {{trans('crudbooster.action_add_data')}}
                          </a>
                          @endif                          

                          @if($button_filter)
                          <a href="javascript:void(0)" id='btn_advanced_filter' data-url-parameter='{{$build_query}}' title='Advanced Filter Data' class="btn btn-sm btn-primary {{(Request::get('filter_column'))?'active':''}}">                               
                            <i class="fa fa-filter"></i> {{trans("crudbooster.button_filter")}}
                          </a>
                          @endif  

                          @if($button_export)
                          <a href="javascript:void(0)" id='btn_export_data' data-url-parameter='{{$build_query}}' title='Export Data' class="btn btn-sm btn-primary btn-export-data">
                            <i class="fa fa-upload"></i> {{trans("crudbooster.button_export")}}
                          </a>
                          @endif

                          @if($button_import)
                          <a href="{{ CRUDBooster::mainpath('import-data') }}" id='btn_import_data' data-url-parameter='{{$build_query}}' title='Import Data' class="btn btn-sm btn-primary btn-import-data">
                            <i class="fa fa-download"></i> {{trans("crudbooster.button_import")}}
                          </a>
                          @endif

                          <!--ADD ACTIon-->
                           @if(count($index_button))
                          <div class="more-action" style="display:inline-block;position:relative">
                            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">More Action
                              <span class="fa fa-caret-down"></span></button>
                            <ul class="dropdown-menu">
                                
                             
                                  @foreach($index_button as $ib)
                                   <li> <a href='{{$ib["url"]}}' id='{{str_slug($ib["label"])}}' class='btn btn-primary btn-xs' 
                                    @if($ib['onClick']) onClick='return {{$ib["onClick"]}}' @endif
                                    @if($ib['onMouseOever']) onMouseOever='return {{$ib["onMouseOever"]}}' @endif
                                    @if($ib['onMoueseOut']) onMoueseOut='return {{$ib["onMoueseOut"]}}' @endif
                                    @if($ib['onKeyDown']) onKeyDown='return {{$ib["onKeyDown"]}}' @endif
                                    @if($ib['onLoad']) onLoad='return {{$ib["onLoad"]}}' @endif
                                    >
                                      <i class='{{$ib["icon"]}}'></i> {{$ib["label"]}}
                                    </a></li>
                                  @endforeach                                                          
                              

                            </ul>
                          </div>
                          @endif
                          
                    </div>
                    <div class='col-sm-4' align="right">

                        <form method='get' style="display:inline-block;width: 260px;" action='{{Request::url()}}'>
                              <div class="input-group">
                                <input type="text" name="q" value="{{ Request::get('q') }}" class="form-control input-sm pull-right" placeholder="Search"/>                            
                                {!! CRUDBooster::getUrlParameters(['q']) !!}
                                <div class="input-group-btn">
                                  @if(Request::get('q'))
                                  <?php 
                                    $parameters = Request::all();
                                    unset($parameters['q']);
                                    $build_query = urldecode(http_build_query($parameters));
                                    $build_query = ($build_query)?"?".$build_query:"";
                                    $build_query = (Request::all())?$build_query:"";
                                  ?>
                                  <button type='button' onclick='location.href="{{ CRUDBooster::mainpath($build_query) }}"' title='Reset' class='btn btn-sm btn-warning'><i class='fa fa-ban'></i></button>
                                  @endif
                                  <button type='submit' class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                                </div>
                              </div>
                          </form>


                        <form method='get' id='form-limit-paging' style="display:inline-block" action='{{Request::url()}}'>                        
                            {!! CRUDBooster::getUrlParameters(['limit']) !!}
                            <div class="input-group">
                              <select onchange="$('#form-limit-paging').submit()" name='limit' style="width: 56px;"  class='form-control input-sm'>
                                  <option {{($limit==5)?'selected':''}} value='5'>5</option> 
                                  <option {{($limit==10)?'selected':''}} value='10'>10</option>
                                  <option {{($limit==20)?'selected':''}} value='20'>20</option>
                                  <option {{($limit==25)?'selected':''}} value='25'>25</option>
                                  <option {{($limit==50)?'selected':''}} value='50'>50</option>
                                  <option {{($limit==100)?'selected':''}} value='100'>100</option>
                                  <option {{($limit==200)?'selected':''}} value='200'>200</option>
                              </select>                              
                            </div>
                          </form>

                          
                    </div>
                  </div>
                                            
                  <form id='form-table' method='post' action='{{CRUDBooster::mainpath("action-selected")}}'>
                  <input type='hidden' name='button_name' value=''/>
                  <input type='hidden' name='_token' value='{{csrf_token()}}'/>
                  <table id='table_dashboard' class="table table-hover table-striped table-bordered">
                    <thead>
                    <tr>                      
                      <th width='3%'><input type='checkbox' id='checkall'/></th>
                      <?php                       
                        foreach($columns as $col) {
                            if($col['visible']===FALSE) continue;
                            
                            $sort_column = Request::get('filter_column');
                            $colname = $col['label'];
                            $name = $col['name'];
                            $field = $col['field_with'];
                            $width = ($col['width'])?:"auto";
                            $mainpath = trim(CRUDBooster::mainpath(),'/').$build_query;
                            echo "<th width='$width'>";
                            if(isset($sort_column[$field])) {
                              switch($sort_column[$field]['type']) {                                
                                case 'asc': 
                                  $url = CRUDBooster::urlFilterColumn($field,'desc');
                                  echo "<a href='$url' title='Click to sort descending'>$colname &nbsp; <i class='fa fa-sort-desc'></i></a>";
                                  break;
                                case 'desc':
                                  $url = CRUDBooster::urlFilterColumn($field,'asc');
                                  echo "<a href='$url' title='Click to sort ascending'>$colname &nbsp; <i class='fa fa-sort-asc'></i></a>";
                                  break;
                                default:
                                  $url = CRUDBooster::urlFilterColumn($field,'asc');
                                  echo "<a href='$url' title='Click to sort ascending'>$colname &nbsp; <i class='fa fa-sort'></i></a>";
                                  break;      
                              }
                            }else{     
                                  $url = CRUDBooster::urlFilterColumn($field,'asc');                         
                                  echo "<a href='$url' title='Click to sort ascending'>$colname &nbsp; <i class='fa fa-sort'></i></a>";                                  
                            }
                            
                            
                            echo "</th>";
                        }
                      ?>   

                      @if($button_table_action)
                        @if(CRUDBooster::isUpdate() || CRUDBooster::isDelete() || CRUDBooster::isRead())                     
                            <th width='{{$button_action_width?:"auto"}}' style="text-align:right">{{trans("crudbooster.action_label")}}</th>
                        @endif                   
                      @endif                                            
                    </tr>
                    </thead>
                    <tbody>
                      @if(count($result)==0)
                      <tr class='warning'>
                          <td colspan='{{count($columns)+2}}' align="center"><i class='fa fa-search'></i> {{trans("crudbooster.table_data_not_found")}}</td>
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
                                  foreach($row as $key=>$val) {
                                    $query = str_replace("[".$key."]",'"'.$val.'"',$query);
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
                          
                              @foreach($hc as $h)
                                <td>{!! $h !!}</td>
                              @endforeach
                          </tr>
                      @endforeach
                    </tbody>  


                    <tfoot>
                    <tr>                      
                      <th>&nbsp;</th>
                      <?php                       
                        foreach($columns as $col) {
                            if($col['visible']===FALSE) continue;
                            $colname = $col['label'];
                            $width = ($col['width'])?:"auto";
                            echo "<th width='$width'>$colname</th>";
                        }
                      ?>   

                      @if($button_table_action)
                        @if(CRUDBooster::isUpdate() || CRUDBooster::isDelete() || CRUDBooster::isRead())
                        <th> - </th>
                        @endif                   
                      @endif                                            
                    </tr>
                    </tfoot>               
                  </table>                                   

                  </form><!--END FORM TABLE-->

            <p>{!! urldecode(str_replace("/?","?",$result->appends(Request::all())->render())) !!}</p>





            @if($columns)
            <script>
            $(function(){
              $('.btn-filter-data').click(function() {
                $('#filter-data').modal('show');
              })

              $('.btn-export-data').click(function() {
                $('#export-data').modal('show');
              })

              var toggle_advanced_report_boolean = 1;
              $(".toggle_advanced_report").click(function() {
                
                if(toggle_advanced_report_boolean==1) {
                  $("#advanced_export").slideDown();
                  $(this).html("<i class='fa fa-minus-square-o'></i> Show Advanced Export");
                  toggle_advanced_report_boolean = 0;
                }else{
                  $("#advanced_export").slideUp();
                  $(this).html("<i class='fa fa-plus-square-o'></i> Show Advanced Export");
                  toggle_advanced_report_boolean = 1;
                }   
                
              })


              $("#table_dashboard .checkbox").click(function() {
                var is_any_checked = $("#table_dashboard .checkbox:checked").length;
                if(is_any_checked) {
                  $(".btn-delete-selected").removeClass("disabled");
                }else{
                  $(".btn-delete-selected").addClass("disabled");
                }
              })

              $("#table_dashboard #checkall").click(function() {
                var is_checked = $(this).is(":checked");
                $("#table_dashboard .checkbox").prop("checked",!is_checked).trigger("click");
              })

              $('#btn_advanced_filter').click(function() {
                $('#advanced_filter_modal').modal('show');
              })

              $(".filter-combo").change(function() {
                console.log('Filter combo detected');
                var n = $(this).val();
                var p = $(this).parents('.row-filter-combo');
                var type_data = $(this).attr('data-type');
                var filter_value = p.find('.filter-value');

                switch(n) {
                  default:
                    filter_value.removeAttr('placeholder').val('').prop('disabled',true);
                    
                    filter_value.val('').show().focus();
                    p.find('.between-group').hide();

                    p.find('.filter-value-between').val('').prop('disabled',true);
                  break;
                  case 'like':
                  case 'not like':
                    filter_value.val('').show().focus();  
                    p.find('.between-group').hide();
                    
                    filter_value.attr('placeholder','e.g : Lorem Ipsum').prop('disabled',false);
                  break;
                  case 'asc':
                    filter_value.val('').show().focus();
                    p.find('.between-group').hide();

                    filter_value.prop('disabled',true).attr('placeholder','Sort ascending');
                  break;
                  case 'desc':
                    filter_value.val('').show().focus();
                    p.find('.between-group').hide();

                    filter_value.prop('disabled',true).attr('placeholder','Sort descending');
                  break;
                  case '=':
                    filter_value.val('').show().focus();
                    p.find('.between-group').hide();

                    filter_value.prop('disabled',false).attr('placeholder','e.g : Lorem Ipsum');
                  break;
                  case '>=':        
                    filter_value.val('').show().focus();
                    p.find('.between-group').hide();

                    filter_value.prop('disabled',false).attr('placeholder','e.g : 1000');
                  break;
                  case '<=':        
                    filter_value.val('').show().focus();
                    p.find('.between-group').hide();

                    filter_value.prop('disabled',false).attr('placeholder','e.g : 1000');
                  break;
                  case '>':       
                    filter_value.val('').show().focus();
                    p.find('.between-group').hide();

                    filter_value.prop('disabled',false).attr('placeholder','e.g : 1000');
                  break;
                  case '<':       
                    filter_value.val('').show().focus();
                    p.find('.between-group').hide();

                    filter_value.prop('disabled',false).attr('placeholder','e.g : 1000'); 
                  break; 
                  case '!=':
                    filter_value.val('').show().focus();
                    p.find('.between-group').hide();

                    filter_value.prop('disabled',false).attr('placeholder','e.g : Lorem Ipsum');
                  break;
                  case 'in':
                    filter_value.val('').show().focus();
                    p.find('.between-group').hide();

                    filter_value.prop('disabled',false).attr('placeholder','e.g : Lorem,Ipsum,Dolor Sit');
                  break;
                  case 'not in':
                    filter_value.val('').show().focus();
                    p.find('.between-group').hide();

                    filter_value.prop('disabled',false).attr('placeholder','e.g : Lorem,Ipsum,Dolor Sit');
                  break;
                  case 'between':       
                    filter_value.val('').hide();
                    p.find('.between-group').show().focus();
                    p.find('.filter-value-between').prop('disabled',false);
                    
                  break;
                }
              })

              /* Remove disabled when reload page and input value is filled */
              $(".filter-value").each(function() {
                var v = $(this).val();
                if(v != '') $(this).prop('disabled',false);
              })
              $(".filter-value-between").each(function() {
                var v = $(this).val();
                if(v != '') {
                  // $(this).parents('.row-filter-combo').find('.filter-value').hide();
                  $(this).prop('disabled',false);
                }
              })
            })
            </script>
            <!-- MODAL FOR SORTING DATA-->
            <div class="modal fade" tabindex="-1" role="dialog" id='advanced_filter_modal'>
              <div class="modal-dialog">
                <div class="modal-content" >
                  <div class="modal-header">
                    <button class="close" aria-label="Close" type="button" data-dismiss="modal">
                    <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title"><i class='fa fa-filter'></i> {{trans("crudbooster.filter_dialog_title")}}</h4>
                  </div>
                  <form method='get' action=''>
                    <div class="modal-body">
                      
                      <?php foreach($columns as $key => $col):?>
                        <?php if( isset($col['image']) || isset($col['download']) || $col['visible']===FALSE) continue;?>   
                      <div class='form-group'>
                        <label>{{ $col['label'] }}</label>
                        <div class='row-filter-combo row'>

                          <div class='col-sm-6'>
                            <select name='filter_column[{{$col["field_with"]}}][type]' data-type='{{$col["type_data"]}}' class="filter-combo form-control">
                              <option value=''>** Select Operator Type</option>             
                              @if(in_array($col['type_data'],['string','varcar','text']))<option {{ (CRUDBooster::getTypeFilter($col["field_with"]) == 'like')?"selected":"" }} value='like'>LIKE</option> @endif
                              @if(in_array($col['type_data'],['string','varcar','text']))<option {{ (CRUDBooster::getTypeFilter($col["field_with"]) == 'not like')?"selected":"" }} value='not like'>NOT LIKE</option>@endif
                              <option typeallow='all' {{ (CRUDBooster::getTypeFilter($col["field_with"]) == 'asc')?"selected":"" }} value='asc'>ASCENDING</option>
                              <option typeallow='all' {{ (CRUDBooster::getTypeFilter($col["field_with"]) == 'desc')?"selected":"" }} value='desc'>DESCENDING</option>
                              <option typeallow='all' {{ (CRUDBooster::getTypeFilter($col["field_with"]) == '=')?"selected":"" }} value='='>= (Equal To)</option>
                              @if(in_array($col['type_data'],['int','integer','double','float','decimal']))<option {{ (CRUDBooster::getTypeFilter($col["field_with"]) == '>=')?"selected":"" }} value='>='>>= (Greater Than or Equal)</option>@endif
                              @if(in_array($col['type_data'],['int','integer','double','float','decimal']))<option {{ (CRUDBooster::getTypeFilter($col["field_with"]) == '<=')?"selected":"" }} value='<='><= (Less Than or Equal)</option>@endif
                              @if(in_array($col['type_data'],['int','integer','double','float','decimal']))<option {{ (CRUDBooster::getTypeFilter($col["field_with"]) == '<')?"selected":"" }} value='<'>< (Less Than)</option>@endif
                              @if(in_array($col['type_data'],['int','integer','double','float','decimal']))<option {{ (CRUDBooster::getTypeFilter($col["field_with"]) == '>')?"selected":"" }} value='>'>> (Greater Than)</option>@endif
                              <option typeallow='all' {{ (CRUDBooster::getTypeFilter($col["field_with"]) == '!=')?"selected":"" }} value='!='>!= (Not Equal To)</option>
                              <option typeallow='all' {{ (CRUDBooster::getTypeFilter($col["field_with"]) == 'in')?"selected":"" }} value='in'>IN</option>
                              <option typeallow='all' {{ (CRUDBooster::getTypeFilter($col["field_with"]) == 'not in')?"selected":"" }} value='not in'>NOT IN</option>
                              @if(in_array($col['type_data'],['date','time','datetime','int','integer','double','float','decimal']))<option {{ (CRUDBooster::getTypeFilter($col["field_with"]) == 'between')?"selected":"" }} value='between'>BETWEEN</option>@endif                         
                            </select>
                          </div>
                          <div class='col-sm-6'>
                            <input type='text' class='filter-value form-control' {{ (CRUDBooster::getTypeFilter($col["field_with"]) == 'between')?"style=\"display:none\"":""}} disabled name='filter_column[{{$col["field_with"]}}][value]' value='{{ (!is_array(CRUDBooster::getValueFilter($col["field_with"])))?CRUDBooster::getValueFilter($col["field_with"]):"" }}'>

                            <div class='row between-group' style="{{ (CRUDBooster::getTypeFilter($col["field_with"]) == 'between')?"display:block":"display:none" }}">
                              <div class='col-sm-6'>
                                <input type='text' class='filter-value-between form-control {{ (in_array($col["type_data"],["date","time","datetime"]))?"datepicker":"" }}' disabled placeholder='{{$col["label"]}} from' name='filter_column[{{$col["field_with"]}}][value][]' value='<?php 
                                $value = CRUDBooster::getValueFilter($col["field_with"]); 
                                echo (CRUDBooster::getTypeFilter($col["field_with"])=='between')?$value[0]:"";
                                ?>'>
                              </div>
                              <div class='col-sm-6'>
                                <input type='text' class='filter-value-between form-control {{ (in_array($col["type_data"],["date","time","datetime"]))?"datepicker":"" }}' disabled placeholder='{{$col["label"]}} to' name='filter_column[{{$col["field_with"]}}][value][]' value='<?php 
                                $value = CRUDBooster::getValueFilter($col["field_with"]); 
                                echo (CRUDBooster::getTypeFilter($col["field_with"])=='between')?$value[1]:"";
                                ?>'>
                              </div>
                            </div>

                          </div>
                        </div>

                      </div>
                      <?php endforeach;?>       
                      
                    </div>
                    <div class="modal-footer" align="right">
                      <button class="btn btn-default" type="button" data-dismiss="modal">{{trans("crudbooster.button_close")}}</button>
                      <button class="btn btn-default btn-reset" type="reset" onclick='location.href="{{CRUDBooster::mainpath()}}"' >Reset</button>
                      <button class="btn btn-primary btn-submit" type="submit">{{trans("crudbooster.button_submit")}}</button>
                    </div>
                  </form>
                </div>
                <!-- /.modal-content -->
              </div>
            </div>


            <script>
            $(function(){
              $('.btn-filter-data').click(function() {
                $('#filter-data').modal('show');
              })

              $('.btn-export-data').click(function() {
                $('#export-data').modal('show');
              })

              var toggle_advanced_report_boolean = 1;
              $(".toggle_advanced_report").click(function() {
                
                if(toggle_advanced_report_boolean==1) {
                  $("#advanced_export").slideDown();
                  $(this).html("<i class='fa fa-minus-square-o'></i> {{trans('crudbooster.export_dialog_show_advanced')}}");
                  toggle_advanced_report_boolean = 0;
                }else{
                  $("#advanced_export").slideUp();
                  $(this).html("<i class='fa fa-plus-square-o'></i> {{trans('crudbooster.export_dialog_show_advanced')}}");
                  toggle_advanced_report_boolean = 1;
                }   
                
              })
            })
            </script>

            <!-- MODAL FOR EXPORT DATA-->
            <div class="modal fade" tabindex="-1" role="dialog" id='export-data'>
              <div class="modal-dialog">
                <div class="modal-content" >
                  <div class="modal-header">
                    <button class="close" aria-label="Close" type="button" data-dismiss="modal">
                    <span aria-hidden="true">×</span></button>
                    <h4 class="modal-title"><i class='fa fa-download'></i> {{trans("crudbooster.export_dialog_title")}}</h4>
                  </div>
                  
                  <form method='post' target="_blank" action='{{ CRUDBooster::mainpath("export-data?t=".time()) }}'> 
                  <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  {!! CRUDBooster::getUrlParameters() !!}
                    <div class="modal-body">                    
                      <div class="form-group">
                        <label>{{trans("crudbooster.export_dialog_filename")}}</label>
                        <input type='text' name='filename' class='form-control' required value='Report {{ $module_name }} - {{date("d M Y")}}'/>
                        <div class='help-block'>
                        {{trans("crudbooster.export_dialog_help_filename")}}
                        </div>
                      </div>

                      <div class="form-group">
                        <label>{{trans("crudbooster.export_dialog_maxdata")}}</label>
                        <input type='number' name='limit' class='form-control' required value='100' max="100000" min="1" /> 
                        <div class='help-block'>{{trans("crudbooster.export_dialog_help_maxdata")}}</div>         
                      </div>  

                      <div class='form-group'>
                        <label>{{trans("crudbooster.export_dialog_columns")}}</label><br/>
                        @foreach($columns as $col)
                          <div class='checkbox inline'><label><input type='checkbox' checked name='columns[]' value='{{$col["name"]}}'>{{$col["label"]}}</label></div>
                        @endforeach
                      </div>

                      <div class="form-group">
                        <label>{{trans("crudbooster.export_dialog_format_export")}}</label>
                        <select name='fileformat' class='form-control'>
                          <option value='pdf'>PDF</option>
                          <option value='xls'>Microsoft Excel (xls)</option>              
                          <option value='csv'>CSV</option>
                        </select>
                      </div>

                      <p><a href='javascript:void(0)' class='toggle_advanced_report'><i class='fa fa-plus-square-o'></i> {{trans("crudbooster.export_dialog_show_advanced")}}</a></p>

                      <div id='advanced_export' style='display: none'>
                                    

                      <div class="form-group">
                        <label>{{trans("crudbooster.export_dialog_page_size")}}</label>
                        <select class='form-control' name='page_size'>
                          <option <?=($setting->default_paper_size=='Letter')?"selected":""?> value='Letter'>Letter</option>
                          <option <?=($setting->default_paper_size=='Legal')?"selected":""?> value='Legal'>Legal</option>
                          <option <?=($setting->default_paper_size=='Ledger')?"selected":""?> value='Ledger'>Ledger</option>
                          <?php for($i=0;$i<=8;$i++):
                            $select = ($setting->default_paper_size == 'A'.$i)?"selected":"";
                          ?>
                          <option <?=$select?> value='A{{$i}}'>A{{$i}}</option>
                          <?php endfor;?>

                          <?php for($i=0;$i<=10;$i++):
                            $select = ($setting->default_paper_size == 'B'.$i)?"selected":"";
                          ?>
                          <option <?=$select?> value='B{{$i}}'>B{{$i}}</option>
                          <?php endfor;?>
                        </select>   
                        <div class='help-block'><input type='checkbox' name='default_paper_size' value='1'/> {{trans("crudbooster.export_dialog_set_default")}}</div>       
                      </div>

                      <div class="form-group">
                        <label>{{trans("crudbooster.export_dialog_page_orientation")}}</label>
                        <select class='form-control' name='page_orientation'>
                          <option value='potrait'>Potrait</option>
                          <option value='landscape'>Landscape</option>
                        </select>           
                      </div>
                      </div>

                    </div>
                    <div class="modal-footer" align="right">
                      <button class="btn btn-default" type="button" data-dismiss="modal">{{trans("crudbooster.button_close")}}</button>         
                      <button class="btn btn-primary btn-submit" type="submit">{{trans('crudbooster.button_submit')}}</button>
                    </div>
                  </form>
                </div>
                <!-- /.modal-content -->
              </div>
            </div>

            @endif