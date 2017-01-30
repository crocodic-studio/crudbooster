<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}'  style="{{@$form['style']}}">
	<label class='control-label col-sm-2'>{{$form['label']}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>

	<div class="{{$col_width?:'col-sm-10'}}">
	<?php 
	$dataenum = $form['dataenum'];
	$dataenum = (is_array($dataenum))?$dataenum:explode(";",$dataenum);

	if($dataenum):
	foreach($dataenum as $k=>$d):
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

	<?php endforeach; endif;?>																
	<div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
	<p class='help-block'>{{ @$form['help'] }}</p>

	</div>
</div>