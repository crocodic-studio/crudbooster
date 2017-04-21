<div class='form-group' id='form-group-{{$name}}' style="{{@$form['style']}}">

	<label class='control-label col-sm-2'>{{$form['label']}}</label>

	<div class="{{$col_width?:'col-sm-10'}}">
		<textarea id='textarea_{{$name}}' id="{{$name}}" {{$required}} {{$readonly}} {{$disabled}} name="{{$form['name']}}" class='form-control' rows='5'>{{ $value }}</textarea>
		<div class="text-danger">{{ $errors->first($name) }}</div>
		<p class='help-block'>{{ @$form['help'] }}</p>
	</div>

</div>
