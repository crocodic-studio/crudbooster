@extends('crudbooster::admin_template')
@section('content')
        
        <div  style="width:750px;margin:0 auto ">
        @if(CRUDBooster::getCurrentMethod() != 'getProfile' && $button_cancel)
          @if(g('return_url'))
          <p><a href='{{g("return_url")}}'>{{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>       
          @else
          <p><a href='{{CRUDBooster::mainpath()}}'>{{trans("crudbooster.form_back_to_list",['module'=>CRUDBooster::getCurrentModule()->name])}}</a></p>       
          @endif
        @endif
        <div class="panel panel-default">
           <div class="panel-heading">
             <strong><i class='{{CRUDBooster::getCurrentModule()->icon}}'></i> {!! $page_title or "Page Title" !!}</strong>
           </div> 

           <div class="panel-body">
                <?php                               
                  $action = (@$row)?CRUDBooster::mainpath("edit-save/$row->id"):CRUDBooster::mainpath("add-save");          
                ?>
                <form method='post' id="form" enctype="multipart/form-data" action='{{$action}}'>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">    
                <input type='hidden' name='return_url' value='{{ @$return_url }}'/>      
                <input type='hidden' name='ref_mainpath' value='{{ CRUDBooster::mainpath() }}'/>      
                <input type='hidden' name='ref_parameter' value='{{urldecode(http_build_query(@$_GET))}}'/>
                        <div class="box-body">

                          @if($command == 'detail')
                             @include("crudbooster::default.form_detail")  
                          @else
                             @include("crudbooster::default.form_body")         
                          @endif
                        </div><!-- /.box-body -->
                
                        <div class="box-footer">  
                          <div class='pull-right'>                            
                              @if($button_cancel)                       
                                @if(g('return_url'))
                                <a href='{{g("return_url")}}' class='btn btn-default'>{{trans("crudbooster.button_back")}}</a>
                                @else 
                                <a href='{{CRUDBooster::mainpath("?".http_build_query(@$_GET)) }}' class='btn btn-default'>{{trans("crudbooster.button_back")}}</a>
                                @endif
                              @endif
                              @if(CRUDBooster::isCreate() || CRUDBooster::isUpdate())

                                 @if(CRUDBooster::isCreate() && $button_addmore==TRUE && $command == 'add')                             
                                    <input type='submit' name='submit' value='{{trans("crudbooster.button_save_more")}}' class='btn btn-success'/>
                                 @endif

                                 @if($button_save && $command != 'detail')
                                    <input type='submit' name='submit' value='{{trans("crudbooster.button_save")}}' class='btn btn-success'/>
                                 @endif
                                 
                              @endif
                          </div>
                        </div><!-- /.box-footer-->

                        </form>
        
            </div>
        </div>
        </div><!--END AUTO MARGIN-->

@endsection