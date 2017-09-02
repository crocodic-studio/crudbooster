
			@push('bottom')
			<script>				
				$(function() {
					$('#{{$name}}').select2({
						@if($form['options']['multiple']==true)
						  multiple: true,
						@endif
						placeholder: "{{ ($form['placeholder'])?:cbTrans('text_prefix_option')." ".$form['label'] }}",
						allowClear: {{$form['options']['allow_clear']?'true':'false'}}
					});					  
				})
			</script>
			@endpush
			

		<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style="{{@$form['style']}}">
			<label class='control-label col-sm-2'>{{$form['label']}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>

			<div class="{{$col_width?:'col-sm-10'}}">								
			<select style='width:100%' class='form-control' id="{{$name}}" {{$required}} {{$readonly}} {!!$placeholder!!} {{$disabled}} name="{{$name}}{{($form['options']['multiple']==true)?'[]':''}}" {{ ($form['options']['multiple'])?'multiple="multiple"':'' }} >					
					
					@if($form['options']['multiple']==false)
						<option value=''>{{cbTrans('text_prefix_option')}} {{$form['label']}}</option>
					@endif

					@php 
						@$enum = $form['options']['enum'];						
						@$enumValue = $form['options']['value'];
					@endphp								
					@foreach($enum as $i=>$e)	
						@if($enumValue)
							@php
								$selected = ($enumValue[$i]==$value)?"selected":"";
							@endphp
							<option value="{{$enumValue[$i]}}" {{$selected}} >{{$e}}</option>
						@else	
							@php
								$selected = ($e==$value)?"selected":"";
							@endphp	
							<option value="{{$e}}" {{$selected}} >{{$e}}</option>
						@endif
					@endforeach							
			</select>
			<div class="text-danger">
				{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}
			</div><!--end-text-danger-->
			<p class='help-block'>{{ @$form['help'] }}</p>

			</div>
		</div>