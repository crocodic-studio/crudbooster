@if($command=='layout')
<div id='{{$componentID}}' class='border-box'>
	                	                		           
	<div class="panel panel-default">
      <div class="panel-heading">
        [name]
      </div>
      <div class="panel-body">
        [sql]
      </div>
    </div>

	<div class='action pull-right'>
    	<a href='javascript:void(0)' data-componentid='{{$componentID}}' data-name='Panel Area' class='btn-edit-component'><i class='fa fa-pencil'></i></a> &nbsp;
    	<a href='javascript:void(0)' data-componentid='{{$componentID}}' class='btn-delete-component'><i class='fa fa-trash'></i></a>
    </div>
</div>
@elseif($command=='configuration')
	<form method='post'>
		<input type='hidden' name='_token' value='{{csrf_token()}}'/>
		<input type='hidden' name='componentid' value='{{$componentID}}'/>
		<div class="form-group">
			<label>Name</label>
			<input class="form-control" required name='config[name]' type='text' value='{{@$config->name}}'/>
		</div>

		<div class="form-group">
			<label>SQL Query Line</label>
			<textarea name='config[sql]' required rows="4" class='form-control'>{{@$config->sql}}</textarea>
			<div class="block-help">
				Use column name 'label' as line chart label. Use name 'value' as value of line chart. Sparate with ; each sql line. Use [SESSION_NAME] to use alias session.  Use [datesconditions] as placehoder to add date filtering.
			</div>
		</div>

		<div class="form-group">
			<label>Bar Area Name</label>
			<input class="form-control" required name='config[area_name]' type='text' value='{{@$config->area_name}}'/>
			<div class="block-help">You can naming each line area. Write name separate with ;</div>
		</div>

		<div class="form-group">
			<label>Goals Value</label>
			<input class="form-control" name='config[goals]' type='number' value='{{@$config->goals}}'/>			
		</div>
	</form>
@elseif($command=='showFunction')
	
	@if($key == 'sql')
		<?php 
			$sqls = explode(';',$value);
			$dataPoints = array();
			$datax = array();

			foreach($sqls as $i=>$sql) {

				$datamerger = array();

				$sessions = Session::all();
			    foreach($sessions as $key=>$val) {
			      $sql = str_replace("[".$key."]", $val, $sql);
			    }

			    try{
			    	$query = DB::select(DB::raw($sql));				  	
				  	foreach($query as $r) {
				  		$datax[] = $r->label;
				  		$datamerger[] = $r->value;
				  	}
			    }catch(\Exception $e) {

			    }

			    $dataPoints[$i] = $datamerger;
			}			

			$datax = array_unique($datax);

			$area_name = explode(';',$config->area_name);
			$area_name_safe = $area_name;
			foreach($area_name_safe as &$a) {
				$a = str_slug($a,'_');
			}

			$dataPointsJS = json_encode($dataPoints);

			$data_result = array();
			foreach($datax as $i=>$d) {				
				$dr = array();
				$dr['y'] = $d;
				foreach($area_name as $e=>$name) {
					$name = str_slug($name,'_');
					$dr[$name] = $dataPoints[$e][$i];
				}
				$data_result[] = $dr;
			}			

			$data_result = json_encode($data_result);

			$data_labels = array();
			$data_resultcjs = array();
			foreach($datax as $i=>$d) {				
				$dr = array();
				$dr['y'] = $d;
				foreach($area_name as $e=>$name) {
					$name = str_slug($name,'_');
					$dr[$name] = $dataPoints[$e][$i];
					//$data_labels[] = $name;
				}
				$data_labels[] = $d;
				$data_resultcjs[] = $dr;
			}	
			$data_resultcjs = json_encode($data_resultcjs);
			$data_labelsjs = json_encode($data_labels);

			// $data_result = preg_replace('/"([a-zA-Z_]+[a-zA-Z0-9_]*)":/','$1:',$data_result);

		?>
		<!--<div id="chartContainer-{{$componentID}}" style="height: 250px;"></div>-->
		<div id="container-{{$componentID}}" style="width: 75%;margin-left:auto;margin-right:auto;">
        	<canvas id="canvas-{{$componentID}}"></canvas>
    	</div>
		
		<script type="text/javascript">
        var color = Chart.helpers.color;
        window.chartColors = {
            red: '#FF4136',
            orange: '#FF851B',
            yellow: '#FFDC00',
            green: '#2ECC40',
            blue: '#0074D9',
            purple: '#B10DC9',
            grey: '#AAAAAA'
        }; 
        var fillColorsData = [
                color(window.chartColors.blue).alpha(0.5).rgbString(),
                color(window.chartColors.green).alpha(0.5).rgbString(),
                color(window.chartColors.red).alpha(0.5).rgbString(),
                color(window.chartColors.orange).alpha(0.5).rgbString(),
                color(window.chartColors.purple).alpha(0.5).rgbString(),
                color(window.chartColors.grey).alpha(0.5).rgbString(),
                window.chartColors.blue,
                window.chartColors.green,
                window.chartColors.red,
                window.chartColors.orange,
                window.chartColors.purple,
                window.chartColors.grey,
                color(window.chartColors.blue).alpha(0.5).rgbString(),
                color(window.chartColors.green).alpha(0.5).rgbString(),
                color(window.chartColors.red).alpha(0.5).rgbString(),
                color(window.chartColors.orange).alpha(0.5).rgbString(),
                color(window.chartColors.purple).alpha(0.5).rgbString(),
                color(window.chartColors.grey).alpha(0.5).rgbString(),
                window.chartColors.blue,
                window.chartColors.green,
                window.chartColors.red,
                window.chartColors.orange,
                window.chartColors.purple,
                window.chartColors.grey,
                color(window.chartColors.blue).alpha(0.5).rgbString(),
                color(window.chartColors.green).alpha(0.5).rgbString(),
                color(window.chartColors.red).alpha(0.5).rgbString(),
                color(window.chartColors.orange).alpha(0.5).rgbString(),
                color(window.chartColors.purple).alpha(0.5).rgbString(),
                color(window.chartColors.grey).alpha(0.5).rgbString(),
                window.chartColors.blue,
                window.chartColors.green,
                window.chartColors.red,
                window.chartColors.orange,
                window.chartColors.purple,
                window.chartColors.grey,
            ];

        var borderColorsData =
            [
                color(window.chartColors.blue).rgbString(),
                color(window.chartColors.green).rgbString(),
                color(window.chartColors.red).rgbString(),
                color(window.chartColors.orange).rgbString(),
                color(window.chartColors.purple).rgbString(),
                color(window.chartColors.grey).rgbString(),
                color(window.chartColors.blue).rgbString(),
                color(window.chartColors.green).rgbString(),
                color(window.chartColors.red).rgbString(),
                color(window.chartColors.orange).rgbString(),
                color(window.chartColors.purple).rgbString(),
                color(window.chartColors.grey).rgbString(),
                color(window.chartColors.blue).rgbString(),
                color(window.chartColors.green).rgbString(),
                color(window.chartColors.red).rgbString(),
                color(window.chartColors.orange).rgbString(),
                color(window.chartColors.purple).rgbString(),
                color(window.chartColors.grey).rgbString(),
                color(window.chartColors.blue).rgbString(),
                color(window.chartColors.green).rgbString(),
                color(window.chartColors.red).rgbString(),
                color(window.chartColors.orange).rgbString(),
                color(window.chartColors.purple).rgbString(),
                color(window.chartColors.grey).rgbString(),
                color(window.chartColors.blue).rgbString(),
                color(window.chartColors.green).rgbString(),
                color(window.chartColors.red).rgbString(),
                color(window.chartColors.orange).rgbString(),
                color(window.chartColors.purple).rgbString(),
                color(window.chartColors.grey).rgbString(),
                color(window.chartColors.blue).rgbString(),
                color(window.chartColors.green).rgbString(),
                color(window.chartColors.red).rgbString(),
                color(window.chartColors.orange).rgbString(),
                color(window.chartColors.purple).rgbString(),
                color(window.chartColors.grey).rgbString(),
            ];
            
		var arr_datapts = $.parseJSON('{!! $dataPointsJS !!}')[0];
		var arr_labels = {!! $data_labelsjs !!};
        var barChartData = {
            labels: arr_labels,
            datasets: [{
                backgroundColor: fillColorsData,
                borderColor: borderColorsData,
                borderWidth: 1,
                data: arr_datapts
            }]

        };

		$(function() {
					
			var ctx = document.getElementById("canvas-{{$componentID}}").getContext("2d");
            window.myBar = new Chart(ctx, {
                type: 'pie',
                data: barChartData,
                options: {
                    responsive: true,
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false,
                        //text: 'Chart.js Bar Chart'
                    },
                    tooltipEvents: [],
                    showTooltips: true,
                    onAnimationComplete: function() {
                        this.showTooltip(this.segments, true);
                    },
                   tooltips: {
                        callbacks: {
                            label: function (tooltipItem, data) {
                                return 'label';
                            }
                        }
                    }
                }
            });	
		})				
		</script>
	@else

		{!! $value !!}
	@endif
@endif	