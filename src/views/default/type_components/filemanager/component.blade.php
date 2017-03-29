<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style='{{@$form["style"]}}'>
				<label class='control-label col-sm-2'>{{$form['label']}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>

				<div class="{{$col_width?:'col-sm-10'}}">

				@if($value=='')
				<div class="input-group">	
			      <span class="input-group-btn">
			        <a id="lfm-{{$name}}" data-input="thumbnail-{{$name}}" data-preview="holder-{{$name}}" class="btn btn-primary">
			          @if(@$form['filemanager_type'] == 'file')
			          	<i class="fa fa-file-o"></i> {{trans("crudbooster.chose_an_file")}}
			          @else
			          	<i class='fa fa-picture-o'></i>{{trans("crudbooster.chose_an_image")}}
			          @endif
			        </a>
			      </span>
			      <input id="thumbnail-{{$name}}" class="form-control" type="text" readonly value='{{$value}}' name="{{$name}}">
			    </div>
			    @endif
			    
			    @if($value)
			    	<input id="thumbnail-{{$name}}" class="form-control" type="hidden" value='{{$value}}' name="{{$name}}">
				    @if(@$form['filemanager_type'] == 'file')
			          	@if($value) <div style='margin-top:15px'><a id='holder-{{$name}}' href='{{asset($value)}}' target='_blank' title='Download File {{ basename($value)}}'><i class='fa fa-download'></i> Download {{ basename($value)}}</a> 
			          	&nbsp;<a class='btn btn-danger btn-delete btn-xs' onclick='swal({   title: "Are you sure?",   text: "You will not be able to undo this action!",   type: "warning",   showCancelButton: true,   confirmButtonColor: "#DD6B55",   confirmButtonText: "Yes, delete !",   closeOnConfirm: false }, function(){  location.href="{{url($mainpath."/delete-filemanager?file=".$row->{$name}."&id=".$row->id."&column=".$name)}}" });' href='javascript:void(0)' title='Delete this file'><i class='fa fa-ban'></i></a>
			          	</div>@endif
			        @else
			          	<p><a class="fancybox" href="{{ ($value)?asset($value):'' }}"><img id='holder-{{$name}}' {{ ($value)?'src='.asset($value):'' }} style="margin-top:15px;max-height:100px;"></a></p>
			        @endif

			        @if(!$readonly || !$disabled)
					<p><a class='btn btn-danger btn-delete btn-sm' onclick="if(!confirm('{{trans("crudbooster.delete_title_confirm")}}')) return false" href='{{url(CRUDBooster::mainpath("update-single?table=$table&column=$name&value=&id=$id"))}}'><i class='fa fa-ban'></i> Delete </a></p>
					@endif
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