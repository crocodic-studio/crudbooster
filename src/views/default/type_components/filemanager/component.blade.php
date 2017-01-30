<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style='{{@$form["style"]}}'>
				<label class='control-label col-sm-2'>{{$form['label']}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>

				<div class="{{$col_width?:'col-sm-10'}}">
				<div class="input-group">	
			      <span class="input-group-btn">
			        <a id="lfm-{{$name}}" data-input="thumbnail-{{$name}}" data-preview="holder-{{$name}}" class="btn btn-primary">
			          @if(@$form['filemanager_type'])
			          	<i class="fa fa-file-o"></i> Choose a file
			          @else
			          	<i class='fa fa-picture-o'></i> Choose an image
			          @endif
			        </a>
			      </span>
			      <input id="thumbnail-{{$name}}" class="form-control" type="text" readonly value='{{$value}}' name="{{$name}}">
			    </div>

			    @if(@$form['filemanager_type'])
		          	@if($value) <div style='margin-top:15px'><a id='holder-{{$name}}' href='{{asset($value)}}' target='_blank' title='Download File {{ basename($value)}}'><i class='fa fa-download'></i> Download {{ basename($value)}}</a> 
		          	&nbsp;<a class='btn btn-danger btn-delete btn-xs' onclick='swal({   title: "Are you sure?",   text: "You will not be able to undo this action!",   type: "warning",   showCancelButton: true,   confirmButtonColor: "#DD6B55",   confirmButtonText: "Yes, delete !",   closeOnConfirm: false }, function(){  location.href="{{url($mainpath."/delete-filemanager?file=".$row->{$name}."&id=".$row->id."&column=".$name)}}" });' href='javascript:void(0)' title='Delete this file'><i class='fa fa-ban'></i></a>
		          	</div>@endif
		        @else
		          	<img id='holder-{{$name}}' {{ ($value)?'src='.asset($value):'' }} style="margin-top:15px;max-height:100px;">
		        @endif
			    

				<div class='help-block'>{{@$form['help']}}</div>
				<div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
				</div>
			</div>
			@if(@$form['filemanager_type'])
			<script type="text/javascript">$('#lfm-{{$name}}').filemanager('file','{{url("/")}}');</script>
			@else
			<script type="text/javascript">$('#lfm-{{$name}}').filemanager('images','{{url("/")}}');</script>
			@endif