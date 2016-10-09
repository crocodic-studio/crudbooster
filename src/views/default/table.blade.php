            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{$data_sub_module->name ?:"Show Data"}}</h3>
                    <div class="box-tools pull-right">

                      <form method='get' class='pull-right' action='{{Request::url()}}'>                        
                        {!! get_input_url_parameters(['limit']) !!}
                        <div class="input-group">
                          <select name='limit' style="width: 56px;"  class='form-control input-sm'>
                              <option {{($limit==5)?'selected':''}} value='5'>5</option> 
                              <option {{($limit==10)?'selected':''}} value='10'>10</option>
                              <option {{($limit==20)?'selected':''}} value='20'>20</option>
                              <option {{($limit==25)?'selected':''}} value='25'>25</option>
                              <option {{($limit==50)?'selected':''}} value='50'>50</option>
                          </select>
                          <button type='submit' class="btn btn-sm btn-default">Go</button>
                        </div>
                      </form>


                      <form method='get' action='{{Request::url()}}'>
                          <div class="input-group">
                            <input type="text" name="q" value="{{ Request::get('q') }}" class="form-control input-sm pull-right" style="width: 190px;" placeholder="Search"/>                            
                            {!! get_input_url_parameters(['q']) !!}
                            <div class="input-group-btn">
                              @if(Request::get('q'))
                              <?php 
                                $parameters = Request::all();
                                unset($parameters['q']);
                                $build_query = urldecode(http_build_query($parameters));
                                $build_query = ($build_query)?"?".$build_query:"";
                                $build_query = (Request::all())?$build_query:"";
                              ?>
                              <button type='button' onclick='location.href="{{ mainpath($build_query) }}"' title='Reset' class='btn btn-sm btn-warning'><i class='fa fa-ban'></i></button>
                              @endif
                              <button type='submit' class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                            </div>
                          </div>
                      </form>
                                            
                  </div>
                </div> 
              
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
                  });
                </script>
              
                <div id='box-body-table' class="box-body table-responsive no-padding">
                  <table id='table_dashboard' class="table table-hover table-striped">
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
                            $mainpath = trim(mainpath(),'/').$build_query;
                            echo "<th width='$width'>";
                            if(isset($sort_column[$field])) {
                              switch($sort_column[$field]['type']) {                                
                                case 'asc': 
                                  $url = url_filter_column($field,'desc');
                                  echo "<a href='$url' title='Click to sort descending'>$colname &nbsp; <i class='fa fa-sort-desc'></i></a>";
                                  break;
                                case 'desc':
                                  $url = url_filter_column($field,'asc');
                                  echo "<a href='$url' title='Click to sort ascending'>$colname &nbsp; <i class='fa fa-sort-asc'></i></a>";
                                  break;
                                default:
                                  $url = url_filter_column($field,'asc');
                                  echo "<a href='$url' title='Click to sort ascending'>$colname &nbsp; <i class='fa fa-sort'></i></a>";
                                  break;      
                              }
                            }else{     
                                  $url = url_filter_column($field,'asc');                         
                                  echo "<a href='$url' title='Click to sort ascending'>$colname &nbsp; <i class='fa fa-sort'></i></a>";                                  
                            }
                            
                            
                            echo "</th>";
                        }
                      ?>   

                      @if($button_table_action)
                        @if($priv->is_edit!=0 || $priv->is_delete!=0 || $priv->is_read!=0)                     
                            <th width='100px'>Action</th>
                        @endif                   
                      @endif                                            
                    </tr>
                    </thead>
                    <tbody>
                      @if(count($result)==0)
                      <tr class='warning'>
                          <td colspan='{{count($columns)+2}}' align="center"><i class='fa fa-search'></i> No Data Avaliable</td>
                      </tr>
                      @endif

                      @foreach($html_contents as $hc)
                          {!! '<tr><td>'.implode('</td><td>',$hc).'</td></tr>' !!}
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
                        @if($priv->is_edit!=0 || $priv->is_delete!=0 || $priv->is_read!=0)
                        <th> - </th>
                        @endif                   
                      @endif                                            
                    </tr>
                    </tfoot>               
                  </table>                                   
                </div><!-- /.box-body -->                

                <div class="box-footer">                    
                     {!! urldecode(str_replace("/?","?",$result->appends(Request::all())->render())) !!}
                </div><!-- /.box-footer-->
            </div><!-- /.box -->