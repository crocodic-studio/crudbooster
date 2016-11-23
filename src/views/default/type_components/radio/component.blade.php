<div class='form-group {{$col_width}} {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}'  style="{{@$form['style']}}">
	<label>{{$form['label']}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label><br/>
	<?php foreach($form['dataenum'] as $k=>$d):
		$val = $lab = '';
		if(strpos($d,'|')!==FALSE) {
			$draw = explode("|",$d);
			$val = $draw[0];
			$lab = $draw[1];
		}else{
			$val = $lab = $d;
		}
		$select = ($value == $val)?"checked":"";
	?>	
	<label class='radio-inline'>
		<input type='radio' {{$select}} name='{{$name}}' {{ ($k==0)?$required:'' }} {{$readonly}} {{$disabled}} value='{{$val}}'/> {!!$lab!!} 
	</label>						

	<?php endforeach;?>																
	<div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
	<p class='help-block'>{{ @$form['help'] }}</p>
</div>