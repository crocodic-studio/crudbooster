<?php 
  $classname = 'App\Http\Controllers\\'.$form['controller'];
  $sub       = new $classname();
  $sub->cbLoader();        
  $subtable  = $sub->table;
  $columns   = $sub->columns_table;     
  $fk        = CRUDBooster::getForeignKey($table,$subtable);  
  $fk_id     = ($row)?$row->id:0; 
  if($row) {
          
    $subquery = DB::table($subtable)->select($subtable.'.id');
    $subquery->where($subtable.'.'.$fk,$fk_id);
    foreach ($columns as $key => $value) {
      if(strpos($value['name'], ' as ')!==FALSE) {
        $subquery->addselect(DB::raw($value['name']));
        continue;
      }

      if($value['join']) {
        $join_exp     = explode(',', $value['join']);
        $join_table  = $join_exp[0];
        $join_column = $join_exp[1];            
        $join_alias  = str_replace(".", "_", $join_table);
        $subquery->leftjoin($join_table.' as '.$join_alias,$join_alias.'.id','=',$subtable.'.'.$value['name']);
        $subquery->addselect($join_alias.'.'.$join_column.' as '.$value['name'].'_label');

      }else{            
        $subquery->addselect($subtable.'.'.$value['name']);
      }
    }
    
    $subquery = $subquery->get();           
    }else{
    $subquery          = CRUDBooster::getTemporary($subtable,[$fk=>$fk_id]);                  
    }
?>

<tr><td colspan='2'>

<div class="panel panel-default">
  <div class="panel-heading">
    <i class='fa fa-bars'></i> {{$form['label']}}      
  </div> 
  <div class="panel-body">
    <table id='table-{{$form["name"]}}' class='table table-striped datatables-simple'>
      <thead>
        <tr>
          @foreach($columns as $col)
            <?php if($col['name'] == $fk || $col['visible']===FALSE) continue;?>
            <th>{{$col['label']}}</th>
          @endforeach               
        </tr>
      </thead>
      <tbody>
        @foreach($subquery as $s)
          <tr>
            @foreach($columns as $col)
              <?php 
                if($col['name'] == $fk || $col['visible']===FALSE) continue;
                
                $value = $s->$col['name'];
                if(strpos($col['name'], ' as ')!==FALSE) {
                  $field = substr($col['name'], strpos($col['name'], ' as ')+4);                  
                  $value = $s->$field;
                } 
                if($col['join']) {
                  $value = $s->{$col['name'].'_label'};
                }
              ?>

              @if($col['image'])
                @if($value)
                  <td><a rel='img-{{$name}}' class='fancybox' href='{{ asset($value) }}'><img class='thumbnail' src="{{ asset($value) }}" width='40px' height='40px'/></a></td>
                @else
                  <td>-</td>
                @endif
              @elseif($col['download'])
                @if($value)
                  <td><a target="_blank" href='{{ asset($value) }}'>Download File</a></td>
                @else
                  <td>
                    -
                  </td>
                @endif
              @else
                @if($col['callback_php'])
                          <td>
                            <?php 
                            if($col['callback_php']) {
                              foreach($s as $k=>$v) {
                                  $col['callback_php'] = str_replace('$row->'.$k,$v,$col['callback_php']);
                              }
                              @eval("echo ".$col['callback_php'].";");
                            }
                            ?>
                          </td>
                        @else
                        <td>{{ trim(strip_tags($value)) }}</td>
                        @endif                
              @endif
            @endforeach
          </tr>
        @endforeach
      </tbody>
      </table>
  </div>
</div>


</td></tr>

