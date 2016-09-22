@extends('crudbooster::admin_template')

@section('content')

    @if($index_additional_view && ($index_additional_view['position']=='top' || !$index_additional_view['position']))
        @include($index_additional_view['view'],$index_additional_view['data'])
    @endif
    
    <div class='row'>
        <div class='col-md-12'> 

          <div  id='box_main' >

            @if($index_statistic)
              <div id='box-statistic' class='row'>
              @foreach($index_statistic as $stat)
                  <div  class="col-md-{{ $stat['width'] }}">
                      <div class="small-box bg-{{ $stat['color'] }}">
                        <div class="inner">
                          <h3>{{ $stat['count'] }}</h3>
                          <p>{{ $stat['label'] }}</p>
                        </div>
                        <div class="icon">
                          <i class="{{ $stat['icon'] }}"></i>
                        </div>                    
                      </div>
                  </div>
              @endforeach
              </div>
            @endif

            @if(Request::segment(3) == 'sub-module')
            <?php 
                $parent_module = DB::table('cms_moduls')->where('path',Request::segment(2))->first();
                
                $parent_module_class = '\crocodicstudio\crudbooster\controllers\\' . $parent_module->controller;                
                if(!class_exists($parent_module_class)) {
                  $parent_module_class = '\App\Http\Controllers\\'.$parent_module->controller;
                }
                $parent_module_class = new $parent_module_class;

                $parent_module_class->index_array = TRUE;
                $parent_module_class->index_only_id = intval(Request::segment(4));
                $index_single = $parent_module_class->getIndex();
            ?>
            <div class='box box-primary' id='box-header-module'>
              <div class="box-header with-border">
                <h3 class="box-title">{{ $parent_module->name }}</h3>

                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button>
                </div>
                <!-- /.box-tools -->
              </div>
              <div class='box-body'>
                  <table class='table table-striped'>
                    <thead>
                    <tr>                                            
                      <?php                       
                        $parent_columns = $parent_module_class->columns_table();

                        foreach($parent_columns as $col) {                            
                            if($col['visible']===FALSE) continue;                            
                            $sort_column = Request::get('filter_column');
                            $colname = $col['label'];
                            $name = $col['name'];
                            $field = $col['field_with'];
                            $width = ($col['width'])?:"auto";
                            $mainpath = trim(mainpath(),'/').$build_query;
                            echo "<th width='$width'>$colname</th>";                            
                        }
                      ?>   

                      @if($priv->is_edit!=0 || $priv->is_delete!=0 || $priv->is_read!=0)                      
                      <th width='100px'>Action</th>
                      @endif                                                               
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($index_single as $column)
                        <tr>
                            @foreach($column as $k=>$col)
                              <?php if($k==0) continue;?>
                              <td>{!! $col !!}</td>
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                  </table>
              </div>
            </div>


            <ul class="nav nav-tabs sub-module-tab"> 
                <?php 
                  $subs = $parent_module_class->sub_module();
                  $parent_path = $parent_module_class->mainpath();
                  foreach($subs as $sub):
                    $active = (Request::segment(5) == $sub['path'])?"active":"";
                ?>
                  <li role="presentation" class='{{ $active }}' title="{{$mainpath}}">
                      <a href='{{ $parent_path."/sub-module/".Request::segment(4)."/".$sub["path"] }}'><i class='{{$sub["icon"]}}'></i> {{ $sub['label'] }}</a>
                  </li>
                <?php endforeach;?>
            </ul>

            @endif



            <div class='box'>
              <div class='box-body'>
                 @include("crudbooster::default.actionmenu")
              </div>
            </div>
    
            <!-- Box -->
            @include("crudbooster::default.table")


          </div><!--END BOX MAIN-->

        </div><!-- /.col -->


    </div><!-- /.row -->  


    @if($index_additional_view && $index_additional_view['position']=='bottom')
        @include($index_additional_view['view'],$index_additional_view['data'])
    @endif

@endsection
