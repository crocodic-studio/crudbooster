@extends('admin/admin_template')

@section('content')
    <script>
    $(function() {
      jQuery.fn.selectText = function(){
         var doc = document;
         var element = this[0];
         console.log(this, element);
         if (doc.body.createTextRange) {
             var range = document.body.createTextRange();
             range.moveToElementText(element);
             range.select();
         } else if (window.getSelection) {
             var selection = window.getSelection();        
             var range = document.createRange();
             range.selectNodeContents(element);
             selection.removeAllRanges();
             selection.addRange(range);
         }
      };

    $(document).on("click",".selected_text",function() {
          console.log("clicked");
          $(this).selectText();
     });

    })
    </script> 
    <style>.selected_text {cursor:pointer;} .selected_text:hover {color:#76a400}</style>
    <div class='row'>
      <div class='col-sm-12'>
            <div class="box box-primary collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title">API GENERATOR</h3>
                    <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                  </div><!-- /.box-tools -->
                </div>
                <div class="box-body">
                <form method='post' action='{{url("admin/api_generator/save-api-custom")}}'>
                  <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <div class='form-group'>
                            <label>API Name</label>
                            <input type='text' class='form-control' placeholder='Opsional' name='nama'/>
                        </div>

                        <div class='form-group'>
                            <label>Action Type</label>
                            <select id='tipe_action' name='aksi' class='form-control'>
                                <option value=''>** Choose Action</option>
                                <optgroup label="GENERAL">
                                  <option value='list'>LISTING</option>
                                  <option value='detail'>DETAIL</option>
                                  <option value='save_add'>CREATE</option>
                                  <option value='save_edit'>UPDATE</option>                                
                                  <option value='delete'>DELETE</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class='form-group'>
                            <label>Table</label>
                            <select id='combo_tabel' name='tabel' class='form-control'>
                                  <option value=''>** Choose a Table</option>
                                  @foreach($tables as $tab)
                                  <option value='{{$tab}}'>{{$tab}}</option>
                                  @endforeach
                            </select>
                        </div>
                        <script>
                            $(function() {
                              $('#combo_tabel,#tipe_action').change(function() {
                                  var t = $('#combo_tabel').val();
                                  var type = $('#tipe_action').val();
                                  $.get('{{url("admin/api_generator/column-table")}}/'+t+'/'+type,function(resp) {
                                      $('#koloms').empty();
                                      $.each(resp,function(i,obj) {
                                          $('#koloms').append('<input type="checkbox" name="kolom[]" class="kolom_cek" value="'+obj+'"/> '+obj+' &nbsp;');
                                      })
                                  })
                                  if(type=='list' || type=='detail') {
                                      $(".sub_query_input").prop("readonly",false);
                                  }else{
                                      $(".sub_query_input").prop("readonly",true);
                                  }
                              })

                              $(".uncheck_all_kolom").click(function() {
                                var p = $(this).prop("checked");
                                $(".kolom_cek").prop("checked",p);
                              })
                            })
                           
                        </script>
                        <div class='form-group'>
                            <label>Column <input type='checkbox' class='uncheck_all_kolom' value='1'/></label>
                            <div id='koloms'><em>Please select a table for first</em></div>
                        </div>

                        <div class='form-group'>
                            <label>Parameters</label>
                            <input type='text' class='form-control' placeholder='Optional, ex : name_column1,name_column2,name_column3' name='parameter'/>
                        </div>

                        <div class='form-group'>
                            <label>Sub Query</label>                            
                            <textarea name='sub_query_1' rows='6' class='form-control sub_query_input' readonly placeholder='Optional, only for Listing & Detail'></textarea>
                            <div class='help-block'>Use %%name_parameter%% to call parameter value in sub query</div>
                        </div>

                        <div class='form-group'>
                            <label>SQL Where Query</label>
                            <textarea name='sql_where' rows='6' class='form-control' placeholder='Optional'></textarea>
                            <div class='help-block'>name_column = '1' <span class='text-danger'>or</span> name_column = '1' and name_column_b != '0' . Use %%name_parameter%% to call parameter value</div>
                        </div>                     

                        

                        <div class='form-group'>
                            <label>API Description</label>
                            <textarea name='keterangan' rows='6' class='form-control' placeholder='Opsional'></textarea>
                        </div>
                      
                        <div class='form-group'>
                            <input type='submit' class='btn btn-success' value='SAVE & GENERATE API'/>
                        </div> 


                </div><!-- /.box-body -->
            </div><!-- /.box -->

            <style>
            .table-api tbody tr td a {
              color: #db0e00;
              font-family: arial;
            }
            </style> 
            <script>
                function generate_screet_key() {
                    if(confirm("Are you sure want to change this SCREETKEY ?")) {
                      $.get("<?php echo action('ApiGeneratorController@getGenerateScreetKey')?>",function(resp) {                        
                            alert("SCREETKEY GENERATED SUCCESS !");
                            $("#screetkey").text(resp.key);
                            $("#screetkey").attr("title",resp.url);
                      })  
                    }
                    
                }
            </script>
            <div class="box box-warning">
                <div class="box-header with-border">
                    <h3 class="box-title">API LIST</h3>
                    <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                  </div><!-- /.box-tools -->
                </div>
                <div class="box-body">
                    <div class='form-group'>
                        <label>API BASE URL</label>
                        <input type='text' readonly class='form-control' title='Hanya klik dan otomatis copy to clipboard (kecuali Safari)' onClick="this.setSelectionRange(0, this.value.length); document.execCommand('copy');" value='{{url('api')}}'/>
                    </div>
                    <div class='form-group'>
                        <label>API Key</label><br/>
                        SCREETKEY : <span id='screetkey' style='font-weight: bold'><?php echo $screetkey?></span> <a href='javascript:void(0)' onclick="generate_screet_key()">RE-GENERATE</a><br/>                          
                        <label>Header :</label><br/>                          
                          X-Authorization-Token : md5( SCREETKEY + UNIX CURRENT TIME + USER_AGENT )<br/>
                          X-Authorization-Time  : UNIX CURRENT TIME                          
                    </div>
                    <table class='table table-striped table-api table-bordered'>
                        <thead>
                            <tr class='info'><th width='2%'>No</th><th>API Name</th></tr>
                        </thead> 
                        <tbody>
                              <?php 
                                  
                                  $acts = array('list','detail','save_add','save_edit','delete');
                                  $int_candidate = array('id','is','berat','weight','stok','height','width');
                                  $double_candidate = array('price','total','subtotal','tax','nominal','harga','biaya','jumlah');
                                  $date_candidate = array('date','tanggal','tgl','waktu');

                                  
                                  $no=1;
                                  foreach($tables as $tb):
                                    
                                    $tb_raw = $tb;
                                    $tb = str_replace('_',' ',$tb);
                                    $tb = ucwords(strtolower($tb));


                                    $apicustom = DB::table('cms_apicustom')->where('tabel',$tb_raw)->orderby('nama','asc')->get();
                                    if(count($apicustom)==0) continue;

                                    echo "<tr><td colspan='2'><strong>$tb</strong></td></tr>";
                                    
                              
                                    foreach($apicustom as $ac) {
                                            $param = $keterangan = '';
                                            switch($ac->aksi) {
                                              case "list": 

                                                if($ac->parameter) {
                                                  $params_array = explode(',',$ac->parameter);
                                                  foreach($params_array as &$p) {
                                                      $p = trim($p);                                                    
                                                      $type = '';
                                                      foreach($int_candidate as $ic) {
                                                          $ic_length = strlen($ic)+1;
                                                          if(substr($p,0,$ic_length)==$ic.'_' || $p == $ic) {
                                                              $type = '(int)';
                                                              break;
                                                          }
                                                      }

                                                      foreach($double_candidate as $d) {
                                                          if(strpos(strtolower($p), $d)!==FALSE) {
                                                            $type = '(double)';
                                                            break;
                                                          }
                                                      }

                                                      foreach($date_candidate as $d) {
                                                          if(strpos($p, $d)!==FALSE) {
                                                            $type = '(Y-m-d)';
                                                          }
                                                      }

                                                      $p = ($type)?$p.$type:$p.'(string)';
                                                  }

                                                  $param = implode(',',$params_array);
                                                }


                                                $columns = @explode(',',$ac->kolom);                                                
                                                foreach($columns as &$col) {
                                                    $col = trim($col);
                                                      $type = '';
                                                      foreach($int_candidate as $ic) {
                                                          $ic_length = strlen($ic)+1;
                                                          if(substr($col,0,$ic_length)==$ic.'_' || $col == $ic) {
                                                              $type = 'int';
                                                              break;
                                                          }
                                                      }

                                                      foreach($double_candidate as $d) {
                                                          if(strpos(strtolower($col), $d)!==FALSE) {
                                                            $type = 'double';
                                                            break;
                                                          }
                                                      }

                                                      foreach($date_candidate as $d) {
                                                          if(strpos($col, $d)!==FALSE) {
                                                            $type = 'Y-m-d';
                                                          }
                                                      }
                                                      //$col = ($type)?$col.$type:$col.'(string)';
                                                      $type = ($type)?:"string";
                                                      $col = array("field"=>$col,"type"=>$type);
                                                }                                                                                              

                                                $response = array();
                                                $response[] = array("field"=>"api_status","type"=>"int");
                                                $response[] = array("field"=>"api_message","type"=>"string");
                                                $response[] = array("field"=>"api_total_data","type"=>"int");
                                                $response[] = array("field"=>"api_offset","type"=>"int");
                                                $response[] = array("field"=>"api_limit","type"=>"int");
                                                $response[] = array("field"=>"data","child"=>$columns);                                              

                                                if($ac->keterangan) $keterangan = '* '.nl2br($ac->keterangan).'<br/>';
                                                $keterangan .= "* For pagination, add URL Params `limit` and `offset`.<br/>                                                        
                                                        * The Param prefix  `search_` is for searching.";
                                              break;
                                              default:
                                              case "detail":
                                                $param = $ac->parameter;                                                
                                                $response = array();
                                                $response[] = array("field"=>"api_status","type"=>"int");
                                                $response[] = array("field"=>"api_message","type"=>"string");
                                                $koloms = explode(",",$ac->kolom);
                                                foreach($koloms as $kol) {
                                                  $response[] = array("field"=>$kol,"type"=>"string");
                                                }

                                              break;
                                              case "save_add":
                                                $param = ($ac->parameter)?:$ac->kolom;
                                                $response = array();
                                                $response[] = array("field"=>"api_status","type"=>"int");
                                                $response[] = array("field"=>"api_message","type"=>"string");
                                                $response[] = array("field"=>"id","type"=>"int");
      

                                                $keterangan = ($ac->keterangan)?"* $ac->keterangan<br/>":"";
                                              break;
                                              case "save_edit":
                                                $param = ($ac->parameter)?:$ac->kolom;
                                                $keterangan = ($ac->keterangan)?"* $ac->keterangan<br/>":"";
                                                $response = array();
                                                $response[] = array("field"=>"api_status","type"=>"int");
                                                $response[] = array("field"=>"api_message","type"=>"string");
              
                                              break;
                                              case "delete":
                                                $param = ($ac->parameter)?:'id';
                                                $keterangan = ($ac->keterangan)?"* $ac->keterangan<br/>":"";
                                                $response = array();
                                                $response[] = array("field"=>"api_status","type"=>"int");
                                                $response[] = array("field"=>"api_message","type"=>"string");
                                              break;
                                            }

                                            $keterangan = ($keterangan)?:"<em class='text-muted'>There is no description</em>";
                                            
                                            $param = str_replace(",","<br/>",$param);

                                            $url = '/'.$ac->permalink;
                                        ?>
                                          <tr>
                                              <td><?=$no++;?></td>
                                              <td> 
                                                <a href='javascript:void(0)' title='Api Custom' style='color:#009fe3' class='link_name_api'><?=$ac->nama;?></a> &nbsp; 
                                                <sup>
                                                  <a title='Delete this API' onclick="if(!confirm('Are you sure want to delete ?')) return false" href="{{url('admin/api_generator/delete-api/'.$ac->id)}}"><i class='fa fa-trash'></i></a>
                                                  &nbsp; <a title='Edit This API' href='{{action("ApiCustomController@getEdit")."/$ac->id"}}'><i class='fa fa-pencil'></i></a> 
                                                </sup>
                                                <div class='detail_api' style='display:none'>
                                                    <table class='table table-bordered'>
                                                    <tr><td width='12%'><strong>URL (POST)</strong></td><td><input title='Click and copied !' type='text' class='form-control' readonly onClick="this.setSelectionRange(0, this.value.length); document.execCommand('copy');" value="{{$url}}"/></td></tr>
                                                    <tr><td><strong>PARAMETER</strong></td><td>{!!$param!!}</td></tr>
                                                    <tr><td><strong>RESPONSE</strong></td><td>
                                                    <div class='row'>
                                                    <?php                                                       
                                                      foreach($response as $resp):                                                        
                                                    ?>    

                                                        @if(!$resp['child'])
                                                        <div class='col-sm-4'><span class='selected_text'>{{$resp['field']}}</span> ({{$resp['type']}})</div>
                                                        @else
                                                        <div class='col-sm-12' style='margin-top:5px'><strong><u>{{$resp['field']}}</u></strong> (array)</div>
                                                            <?php foreach($resp['child'] as $child):?>
                                                                  <div class='col-sm-4'><div style='padding-left:5px'><span class='selected_text'>{{$child['field']}}</span> ({{$child['type']}})</div></div>

                                                              <?php endforeach;?>
                                                        @endif
                                                    <?php endforeach;?>
                                                    </div>
                                                    </td></tr>
                                                    <tr><td><strong>DESCRIPTION</strong></td><td><em>{!!$keterangan!!}</em></td></tr>
                                                    </table>
                                                </div>
                                              </td>
                                          </tr>
                                        <?php
                                    }

                                  endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>

      </div>    
    </div>

    <script>
    $(function() {
      $(".link_name_api").click(function() {
          $(".detail_api").slideUp();
          $(this).parent("td").find(".detail_api").slideDown();
      })
      $(".selected_text").each(function() {
            var n = $(this).text();
            if(n.indexOf('api_')==0) {
                $(this).attr('class','selected_text text-danger');
            }            
      })
    })
    </script> 
  
@endsection