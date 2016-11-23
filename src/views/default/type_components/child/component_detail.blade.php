<tr><td colspan='2'>
<div class="panel panel-default">
  <div class="panel-heading">
    <i class='fa fa-bars'></i> {{$form['label']}}      
  </div> 
  <div class="panel-body">
    <table id='table-{{$form['table']}}' class='table table-hover table-striped table-bordered'>
    	<thead>
    		<tr>
    			@foreach($form['columns'] as $column)
    			<th>{{$column['label']}}</th>
    			@endforeach    			
    		</tr>
    	</thead>
    	<tbody>
    					              		
    	</tbody>
    </table>
    <script type="text/javascript">                                                                  
      var datatable_{{$form['table']}} = $("#table-{{$form['table']}}").DataTable({                           
        "columns": [
          @for($i=1;$i<=count($form['columns']);$i++)
            null,
          @endfor                                
          ]
      });                                                           
      load_table_data('{{$form['table']}}','{{$form["foreign_key"]}}');  
    </script>
  </div>
</div>
</td></tr>

