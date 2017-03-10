@include('crudbooster::admin_template_plugins')


<form method='get' action="">
{!! CRUDBooster::getUrlParameters(['q']) !!}
<input type="text" placeholder="Search and enter..." name="q" title="Enter to search" value="{{Request::get('q')}}" class="form-control">
</form>

<table id='table_dashboard' class='table table-striped table-bordered table-condensed' style="margin-bottom: 0px">
<thead>	
	@foreach($columns as $col)
	<th>{{ strtoupper($col) }}</th>
	@endforeach
	<th width="5%">SELECT</th>
</thead>
<tbody>
	@foreach($result as $row)
	<tr>	
		@foreach($columns as $col)
		<?php 
			$img_extension = ['jpg','jpeg','png','gif','bmp'];
			$ext = pathinfo($row->$col, PATHINFO_EXTENSION);
			if($ext && in_array($ext, $img_extension)) {
				echo "<td><a href='".asset($row->$col)."' class='fancybox'><img src='".asset($row->$col)."' width='50px' height='30px'/></a></td>";
			}else{
				echo "<td>".str_limit(strip_tags($row->$col),50)."</td>";
			}
		?>		
		@endforeach
		<td><a class='btn btn-primary' href='javascript:void(0)' onclick='parent.selectData{{Request::get("name_column")}}( {{$row->id}}, "{{$row->$columns[1]}}" )'><i class='fa fa-check-circle'></i> Select</a></td>
	</tr>
	@endforeach
</tbody>
</table>
<div align="center">{!! urldecode(str_replace("/?","?",$result->appends(Request::all())->render())) !!}</div>