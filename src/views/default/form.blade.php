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
                    	@if($button_cancel)<a href='{{url($dashboard)."?".urldecode(http_build_query(@$_GET)) }}' class='btn btn-default'>Cancel</a>@endif
                    	@if( ($priv->is_create || $priv->is_edit) && count($forms)!=0)
                    	   @if($priv->is_create && $button_addmore == TRUE) <button title='Save Data' class='btn btn-success addmore' type='button'><i class='fa fa-save'></i> Save & Add More</button>@endif
                    	   @if($button_save)<button title='Save Data' class='btn btn-success btn-form-save' onclick="$('#form').submit()" type='button'><i class='fa fa-save'></i> Save</button>@endif
                    	@endif
                    </div>
                </div>


				<form method='post' id="form" enctype="multipart/form-data" action='@if (@!$row->id) {{url($mainpath."/add-save")}} @else {{url($mainpath."/edit-save")."/$row->id" }}@endif'>
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<input type="hidden" name="addmore" value="0"/>
				<input type='hidden' name='ref_parameter' value='{{urldecode(http_build_query(@$_GET))}}'/>
                <div class="box-body">
                	@include("crudbooster::default.form_body")					
                </div><!-- /.box-body -->
				
                <div class="box-footer">	
                	<div class='pull-right'>														
            					@if($button_cancel)<a href='{{url($dashboard)."?".urldecode(http_build_query(@$_GET)) }}' class='btn btn-default'>Cancel</a>@endif
                      @if( ($priv->is_create || $priv->is_edit) && count($forms)!=0)
                         @if($priv->is_create && $button_addmore==TRUE) <button title='Save Data' class='btn btn-success addmore' type='button'><i class='fa fa-save'></i> Save & Add More</button>@endif
                         @if($button_save)<button title='Save Data' class='btn btn-success btn-form-save' onclick="$('#form').submit()" type='button'><i class='fa fa-save'></i> Save</button>@endif
                      @endif
        					</div>
                </div><!-- /.box-footer-->

                </form>
				
            </div><!-- /.box -->
        </div><!-- /.col -->


    </div><!-- /.row -->

    <script type="text/javascript">
    	$('.addmore').click(function() {
    		$("#form input[name=addmore]").val(1);
        	$("#form").submit();
    	})
    </script>

    <?php 
    //FORM SUB 
    if($form_sub) {
    	foreach($form_sub as $fs) {
      $table               = str_replace("_view", "", $table);
			$c                   = new $fs['classname'];
			$c->parent_id        = $row->id;
			$c->parent_field     = !empty($fs['foreign_key']) ? $fs['foreign_key'] : 'id_'.$table;
			$c->controller_name  = $fs['controller'];
			$c->index_table_only = true;
			$c->table_name       = $fs['label'];
			$c->is_sub           = true;	
			$c->referal			     = Request::url();		
				
			if(Request::get('submodul')) {
				$submodul = Request::get('submodul');
				$submodul = urldecode($submodul);
				$submodul = json_decode($submodul);
				

				if($submodul->modul == $fs['label']) {
					switch($submodul->action) {
						case 'detail':
              echo $c->getDetail($submodul->id);
            break;
						case 'edit':
							echo $c->getEdit($submodul->id);
						break;
						case 'add':
							echo $c->getAdd();
						break;
					}
				}
			}

			echo $c->getIndex();			
		}
    }

    if($form_add) {
    	echo implode("\n",$form_add);
    }
	
	?>
@endsection