            <div id='table_simple_{{str_slug($table_name)}}' class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Show Data {{$table_name}}</h3>
                    <div class="box-tools">
                      

                      <form method='get' action='{{Request::url()}}'>
                          <div class="input-group">
                            <input type="text" name="q_{{str_slug($table_name)}}" value="{{ Request::get('q_'.str_slug($table_name)) }}" class="form-control input-sm pull-right" style="width: 150px;" placeholder="Search"/>
                            
                            {!! get_input_url_parameters(['q']) !!}
                            
                            <div class="input-group-btn">
                              @if(Request::get('q_'.str_slug($table_name)))
                              <?php 
                                $current_url = Request::url();

                              ?>
                              <button type='button' onclick='location.href="{{$current_url}}"' title='Reset' class='btn btn-sm btn-warning'><i class='fa fa-ban'></i></button>
                              @endif
                              <button type='submit' class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                              <?php 
                                $submodul = ['modul'=>$table_name,'action'=>'add'];
                                $urladd = Request::url();
                                $urladd .= "?submodul=".urlencode(json_encode($submodul))."#form_simple_".str_slug($table_name);
                              ?>
                              @if(Request::segment(3)=='edit')
                              <button type='button' onclick='location.href="{{$urladd}}"' class='btn btn-sm btn-warning'><i class='fa fa-plus'></i></button>
                              @else
                              <button type='button' onclick='swal("Oops","You can not add data {{$table_name}}, Please add new data {{$page_title}} for first","error")' class='btn btn-sm btn-warning'><i class='fa fa-plus'></i></button>
                              @endif
                              
                            </div>
                          </div>
                          
                      </form>


                  </div>
                </div> 
              
                <div class="box-body table-responsive no-padding">
                  <table id='table_dashboard' class="table table-hover table-striped">
                    <thead>
                    <tr>                                            
                      <?php                       
                        foreach($columns as $col) {
                            if($col['visible']===0) continue;
                            $colname = $col['label'];
                            $width = ($col['width'])?:"auto";
                            echo "<th width='$width'>$colname</th>";
                        }
                      ?>   

                      @if($priv->is_edit!=0 || $priv->is_delete!=0 || $priv->is_read!=0)
                      <?php 
                        $width = 0;
                        if($priv->is_edit) {
                          $width += 33;
                        }
                        if($priv->is_delete) {
                          $width += 33;
                        }
                        if($priv->is_read) {
                          $width += 33;
                        }
                        @$width += count($addaction)*33;
                      ?>
                      <th width='<?=$width?>px'>Action</th>
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
                  </table>                                   
                </div><!-- /.box-body -->                

                <div class="box-footer">                    
                     {!! urldecode(str_replace("/?","?",$result->appends(Request::all())->render())) !!}
                </div><!-- /.box-footer-->
            </div><!-- /.box -->