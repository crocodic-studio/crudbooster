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

            if(strpos($url_tab, Session::get('current_mainpath').'/') !==FALSE || strpos($url_tab, Session::get('current_mainpath').'?')!==FALSE) {
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

          <div  id='box_main' >

            <div class='box'>
              <div class='box-body'>
                 @include("admin/default/actionmenu")
              </div>
            </div>
    
            <!-- Box -->
            @include("admin/default/table")


          </div><!--END BOX MAIN-->

        </div><!-- /.col -->


    </div><!-- /.row -->    
@endsection