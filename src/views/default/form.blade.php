@extends('crudbooster::admin_template')
@section('content')

	<style type="text/css">
	@if(Request::get('detail'))
		input[readonly],textarea[readonly],select[readonly],input[disabled],textarea[disabled],select[disabled] {
			background: #ffffff !important;
			border-top: 0px !important;
			border-left: 0px !important;
			border-right: 0px !important;
			cursor: default !important;
		}

		.btn-form-save, .btn-delete, .btn-browse {
			display: none;
		}
	@endif 
	</style>

	<!-- DataTables -->
	<script src="{{ asset("vendor/crudbooster/assets/adminlte/plugins/datatables/jquery.dataTables.min.js")}}"></script>
	<script src="{{ asset("vendor/crudbooster/assets/adminlte/plugins/datatables/dataTables.bootstrap.min.js")}}"></script>

	<!-- JQUERY QRCODE-->
	<script src="{{asset("vendor/crudbooster/assets/jquery-qrcode/jquery.qrcode-0.12.0.min.js")}}"></script>	

	<!--LOAD SELECT2-->	
	<link rel='stylesheet' href='<?php echo asset("vendor/crudbooster/assets/select2/dist/css/select2.min.css")?>'/>
	<script src='<?php echo asset("vendor/crudbooster/assets/select2/dist/js/select2.full.min.js")?>'></script>
	<style>.select2-container .select2-selection--single {height: 35px}</style>
    <div class='row'>
        <div class='col-md-12'> 			
			 @if($form_tab && (Request::get('is_sub') || @$row))            
        <ul class="nav nav-tabs form-tab">         
          @foreach($form_tab as $ft)
          <?php  
            $url_tab = $ft['route'];
            @$id = $row->id;           
            
            if($ft['foreign_key'] && @$_GET['where']) {
              $where = $_GET['where'];
              $field = $ft['foreign_key'];
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

            @if($button_show_data || $button_reload_data || $button_new_data || $button_delete_data || $index_button || $columns)
    			  <div id='box-actionmenu' class='box'>
      				<div class='box-body'>
      					 @include("crudbooster::default.actionmenu")
      				</div>
    			  </div>
            @endif

            <!-- Box -->
            <div id='box_main' class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ (Request::get('detail'))?str_replace(array("Add Data","Edit Data"),"Detail Data",$page_title):$page_title }}</h3>
                    <div class="box-tools">
                    	@if($button_cancel)<a href='{{ mainpath("?".urldecode(http_build_query(@$_GET))) }}' class='btn btn-default'>Cancel</a>@endif
                    	
                    </div>
                </div>
        
        <?php           
          if($data_sub_module) {
            $action_path = Route($data_sub_module->controller."GetIndex");
          }else{
            $action_path = mainpath();
          }            

          if(@!$row) {
              $action = $action_path."/add-save";
          }else{
              $action = $action_path."/edit-save/$row->id";
          }
        ?>
				<form method='post' id="form" enctype="multipart/form-data" action='{{$action}}'>
				<input type="hidden" name="_token" value="{{ csrf_token() }}">	  
        <input type='hidden' name='ref_mainpath' value='{{ mainpath() }}'/>      
				<input type='hidden' name='ref_parameter' value='{{urldecode(http_build_query(@$_GET))}}'/>
                <div class="box-body">
                	@include("crudbooster::default.form_body")					
                </div><!-- /.box-body -->
				
                <div class="box-footer">	
                	<div class='pull-right'>														
            					@if($button_cancel)<a href='{{mainpath("?".urldecode(http_build_query(@$_GET))) }}' class='btn btn-default'>Cancel</a>@endif
                      @if( ($priv->is_create || $priv->is_edit) && count($forms)!=0)

                         @if($priv->is_create && $button_addmore==TRUE)                             
                            <input type='submit' name='submit' value='Save & Add More' class='btn btn-success'/>
                         @endif
                         @if($button_save)
                            <input type='submit' name='submit' value='Save' class='btn btn-success'/>
                         @endif
                         
                      @endif
        					</div>
                </div><!-- /.box-footer-->

                </form>
				
            </div><!-- /.box -->
        </div><!-- /.col -->


    </div><!-- /.row -->

    <?php 
    if($form_add) {
    	echo implode("\n",$form_add);
    }
	
	?>
@endsection