<?php 
	$name = str_slug($form['label'],'');
?>
<script type="text/javascript">
	$(function() {
		$('#form-group-{{$name}} .select2').select2();		
	})
</script>
<div class='form-group {{$header_group_class}}' id='form-group-{{$name}}'>								
	
	@if($form['columns'])						
	<div class="col-sm-12">

	<div id='panel-form-{{$name}}' class="panel panel-default">
		<div class="panel-heading">
		  	<i class='fa fa-bars'></i> {{$form['label']}}
		</div>
		<div class="panel-body">
			
			<div class="panel panel-default">
				<div class="panel-heading">
					<i class='fa fa-table'></i> Table Detail
				</div>
				<div class="panel-body no-padding table-responsive"  style="max-height: 400px;overflow: auto;">
					<table id='table-{{$name}}' class='table table-striped table-bordered'>
					<thead>
						<tr>
							@foreach($form['columns'] as $col)						
								<th>{{$col['label']}}</th>
							@endforeach		
								<th width="90px">{{trans('crudbooster.action_label')}}</th>
						</tr>
					</thead>
					<tbody>

						<?php 
							$columns_tbody = [];
							$data_child = DB::table($form['table'])
							->where($form['foreign_key'],$id);
							foreach($form['columns'] as $i=>$c) {
								$data_child->addselect($form['table'].'.'.$c['name']);

								if($c['type'] == 'datamodal') {
									$datamodal_title = explode(',',$c['datamodal_columns'])[0];
									$datamodal_table = $c['datamodal_table'];
									$data_child->join($c['datamodal_table'],$c['datamodal_table'].'.id','=',$c['name']);
									$data_child->addselect($c['datamodal_table'].'.'.$datamodal_title.' as '.$datamodal_table.'_'.$datamodal_title);
								}elseif ($c['type'] == 'select') {
									if($c['datatable']) {
										$join_table = explode(',',$c['datatable'])[0];
										$join_field = explode(',',$c['datatable'])[1];
										$data_child->join($join_table,$join_table.'.id','=',$c['name']);
										$data_child->addselect($join_table.'.'.$join_field.' as '.$join_table.'_'.$join_field);										
									}
								}								
								
							}

							$data_child = $data_child->orderby($form['table'].'.id','desc')->get();
							foreach($data_child as $d):							
						?>
						<tr>
							@foreach($form['columns'] as $col)
								<td class="{{$col['name']}}">
								<?php 
									if($col['type'] == 'select') {
										if($col['datatable']) {
											$join_table = explode(',',$col['datatable'])[0];
											$join_field = explode(',',$col['datatable'])[1];
											echo "<span class='td-label'>";
											echo $d->{$join_table.'_'.$join_field};
											echo "</span>";
											echo "<input type='hidden' name='".$name."-".$col['name']."[]' value='".$d->{$col['name']}."'/>";
										}
										if($col['dataenum']) {
											echo "<span class='td-label'>";
											echo $d->{$col['name']};
											echo "</span>";
											echo "<input type='hidden' name='".$name."-".$col['name']."[]' value='".$d->{$col['name']}."'/>";
										}
									}elseif ($col['type']=='datamodal') {
										$datamodal_title = explode(',',$col['datamodal_columns'])[0];
										$datamodal_table = $col['datamodal_table'];
										echo "<span class='td-label'>";
										echo $d->{$datamodal_table.'_'.$datamodal_title};
										echo "</span>";
										echo "<input type='hidden' name='".$name."-".$col['name']."[]' value='".$d->{$col['name']}."'/>";
									}elseif ($col['type']=='upload') {
										$filename = basename( $d->{$col['name']} );
										if($col['upload_type']=='image') {
											echo "<a href='".asset( $d->{$col['name']} )."' data-lightbox='roadtrip'><img data-label='$filename' src='".asset( $d->{$col['name']} )."' width='50px' height='50px'/></a>";
											echo "<input type='hidden' name='".$name."-".$col['name']."[]' value='".$d->{ $col['name'] }."'/>";
										}else{
											echo "<a data-label='$filename' href='".asset( $d->{$col['name']} )."'>$filename</a>";
											echo "<input type='hidden' name='".$name."-".$col['name']."[]' value='".$d->{ $col['name'] }."'/>";
										}									
									}else{
										echo "<span class='td-label'>";
										echo $d->{$col['name']};
										echo "</span>";
										echo "<input type='hidden' name='".$name."-".$col['name']."[]' value='".$d->{$col['name']}."'/>";
									}
								?>
								</td>
							@endforeach		
							<td>
								<a href='#panel-form-{{$name}}' onclick='editRow{{$name}}(this)' class='btn btn-warning btn-xs'><i class='fa fa-pencil'></i></a>
								<a href='javascript:void(0)' onclick='deleteRow{{$name}}(this)' class='btn btn-danger btn-xs'><i class='fa fa-trash'></i></a>
							</td>					
						</tr>

						<?php endforeach;?>

						@if(count($data_child)==0)
						<tr class="trNull">
							<td colspan="{{count($form['columns'])+1}}" align="center">{{trans('crudbooster.table_data_not_found')}}</td>
						</tr>
						@endif
					</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- /.box-body -->
	</div> 
	</div>


	@else

		<div style="border:1px dashed #c41300;padding:20px;margin:20px">
			<span style="background: yellow;color: black;font-weight: bold">CHILD {{$name}} : COLUMNS ATTRIBUTE IS MISSING !</span>
			<p>You need to set the "columns" attribute manually</p>
		</div>
	@endif
</div>
