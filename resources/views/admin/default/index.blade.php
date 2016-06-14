@extends('admin/admin_template')

@section('content')
    
    <div class='row'>
        <div class='col-md-12'> 

        @if($form_tab && (Request::get('is_sub') || @$row))            
        <ul class="nav nav-tabs form-tab">         
          @foreach($form_tab as $ft)
          <?php  
            $url_tab = $ft['route'];
            @$id = $row->id;           
            
            if($ft['filter_field'] && @$_GET['where']) {
              $where = $_GET['where'];
              $field = $ft['filter_field'];
              $id = $where[$field];
            }

            if(!$id) {
              if(strpos($url_tab, '/edit/')===FALSE) {
                continue;
              }              
            }

            $url_tab = str_replace("%id%",$id,$url_tab); 
            $active = '';
            if(strpos($url_tab, Session::get('current_mainpath')) !==FALSE) {
              $active = 'selected';
            }     

          ?> 
              <li role="presentation" class="active"><a style='cursor:pointer' class='{{$active}}' href="{{$url_tab}}"><i class='<?=($ft["icon"])?:"fa fa-bars"?>'></i> {{$ft[label]}}</a></li>
            @endforeach
        </ul>
        @endif

        <script type="text/javascript">
            $(function() {
              $(".form-tab a").each(function() {
                var hrf = $(this).attr('href');
                if(hrf.indexOf("/edit/")==-1 && hrf.indexOf("/add")==-1) {
                  var hr = hrf+'&format=total';
                  var hdl = $(this);
                  hdl.append('&nbsp;<em><i class="fa fa-spinner fa-spin"></i></em>');
                  $.get(hr,function(total) {
                    hdl.find('em').text('('+total+')');
                  }).fail(function() {
                    hdl.find('em').text('(0)');
                  })
                }               
                
              })
            })
          </script>

        <div class='box'>
          <div class='box-body'>
             @include("admin/default/actionmenu")
          </div>
          </div>
    
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Show Data</h3>
                    <div class="box-tools">

                      <form method='get' class="pull-right" action=''>
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

                      <form method='get' action=''>
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
                      <th width='<?=$width?>px'>Aksi</th>
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
        </div><!-- /.col -->


    </div><!-- /.row -->    
@endsection