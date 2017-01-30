<?php 
  $classname = 'App\Http\Controllers\\'.$form['controller'];
  $sub       = new $classname();        
  $subtable  = $sub->table;
  $columns   = $sub->columns_table;     
  $fk        = CRUDBooster::getForeignKey($table,$subtable);  
  $fk_id     = ($row)?$row->id:0; 
  $subquery  = DB::table($subtable)->where($fk,$fk_id)->get();   
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
            <?php if($col['name'] == $fk) continue;?>
            <th>{{$col['label']}}</th>
          @endforeach               
        </tr>
      </thead>
      <tbody>
        @foreach($subquery as $s)
          <tr>
            @foreach($columns as $col)
              <?php if($col['name'] == $fk) continue;?>

              @if($col['image'])
                @if($s->$col['name'])
                  <td><a rel='img-{{$name}}' class='fancybox' href='{{ asset($s->$col['name']) }}'><img class='thumbnail' src="{{ asset($s->$col['name']) }}" width='40px' height='40px'/></a></td>
                @else
                  <td>-</td>
                @endif
              @elseif($col['download'])
                @if($s->$col['name'])
                  <td><a target="_blank" href='{{ asset($s->$col['name']) }}'>Download File</a></td>
                @else
                  <td>
                    -
                  </td>
                @endif
              @else
                <td>{{ $s->$col['name'] }}</td>
              @endif
            @endforeach
          </tr>
        @endforeach
      </tbody>
      </table>
  </div>
</div>


</td></tr>

