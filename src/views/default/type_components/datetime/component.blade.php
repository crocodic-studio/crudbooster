<div class='form-group {{$col_width}} form-datepicker {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
							<label>{{$form['label']}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>
							<div class="input-group">  			
								@if(!$disabled)					
								<span class="input-group-addon"><i class='fa fa-calendar'></i></span>
								@endif
								<input type='text' title="{{$form['label']}}" readonly {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} class='form-control notfocus datetimepicker' name="{{$name}}" id="{{$name}}" value='{{$value}}'/>					
							</div>
							<div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
							<p class='help-block'>{{ @$form['help'] }}</p>
						</div>