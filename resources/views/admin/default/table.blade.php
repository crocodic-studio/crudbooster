            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{$table_name?:"Show Data"}}</h3>
                    <div class="box-tools">

                      <form method='get' class="pull-right" action='{{Request::url()}}'>
                        <div class="input-group">
                          <select name='limit' style="width: 56px;"  class='form-control input-sm'>
                              <option {{($limit==5)?'selected':''}} value='5'>5</option> 
                              <option {{($limit==10)?'selected':''}} value='10'>10</option>
                              <option {{($limit==20)?'selected':''}} value='20'>20</option>
                              <option {{($limit==25)?'selected':''}} value='25'>25</option>
                              <option {{($limit==50)?'selected':''}} value='50'>50</option>
                          </select>
                          <button type='submit' class="btn btn-sm btn-default"><i class="fa fa-filter"></i></button>
                        </div>
                      </form>

                      <form method='get' action='{{Request::url()}}'>
                          <div class="input-group">
                            <input type="text" name="q" value="{{ Request::get('q') }}" class="form-control input-sm pull-right" style="width: 150px;" placeholder="Search"/>
                            
                            @if(@$_GET)
                            @foreach(@$_GET as $a=>$b)
                                <?php 
                                  if(is_array($b)) {
                                    $a = $a.'['.key($b).']';
                                    $b = $b[key($b)];
                                  }
                                  echo "<input type='hidden' name='$a' value='$b'/>";
                                ?>                                
                            @endforeach
                            @endif
                            <div class="input-group-btn">
                              @if(Request::get('q'))
                              <?php 
                                $parameters = Request::all();
                                unset($parameters['q']);
                                $build_query = urldecode(http_build_query($parameters));
                                $build_query = (Request::all())?"?".$build_query:"";
                              ?>
                              <button type='button' onclick='location.href="{{url($dashboard).$build_query}}"' title='Reset' class='btn btn-sm btn-warning'><i class='fa fa-ban'></i></button>
                              @endif
                              <button type='submit' class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                            </div>
                          </div>
                      </form>
                  </div>
                </div> 
              
                <div class="box-body table-responsive no-padding">
                  <table id='table_dashboard' class="table table-hover table-striped">
                    <thead>
                    <tr>                      
                      <th width='3%'><input type='checkbox' id='checkall'/></th>
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