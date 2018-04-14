<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}' style='{{@$form["style"]}}'>
    <label class='control-label col-sm-2'>{{$form['label']}}
        @if($required)
            <span class='text-danger' title='{!! trans('crudbooster.this_field_is_required') !!}'>*</span>
        @endif
    </label>

    <div class="{{$col_width?:'col-sm-10'}}">

        @if($value=='')
            <div class="input-group">
                <input id="thumbnail-{{$name}}" class="form-control" type="text" readonly value='{{$value}}' name="{{$name}}">
                <span class="input-group-btn">
			        <a id="lfm-{{$name}}" data-input="thumbnail-{{$name}}" data-preview="holder-{{$name}}" class="btn btn-primary">
			          @if(@$form['filemanager_type'] == 'file')
                            <i class="fa fa-file-o"></i> {{trans("crudbooster.chose_an_file")}}
                        @else
                            <i class='fa fa-picture-o'></i> {{trans("crudbooster.chose_an_image")}}
                        @endif
			        </a>
			      </span>

            </div>
        @endif

        @if($value)
            <input id="thumbnail-{{$name}}" class="form-control" type="hidden" value='{{$value}}' name="{{$name}}">
            @if(@$form['filemanager_type'] == 'file')
                @if($value)
                    <div style='margin-top:15px'><a id='holder-{{$name}}' href='{{asset($value)}}' target='_blank'
                                                    title=' {{trans("crudbooster.button_download_file")}} {{ basename($value)}}'><i
                                    class='fa fa-download'></i> {{trans("crudbooster.button_download_file")}}  {{ basename($value)}}</a>
                        &nbsp;<a class='btn btn-danger btn-delete btn-xs'
                                 onclick='swal({   title: "{{trans("crudbooster.delete_title_confirm")}}",   text: "{{trans("crudbooster.delete_description_confirm")}}",   type: "warning",   showCancelButton: true,   confirmButtonColor: "#DD6B55",   confirmButtonText: "{{trans("crudbooster.confirmation_yes")}}",cancelButtonText: "{{trans('crudbooster.button_cancel')}}",   closeOnConfirm: false }, function(){  location.href="{{url($mainpath."/delete-filemanager?file=".$row->{$name}."&id=".$row->id."&column=".$name)}}" });'
                                 href='javascript:void(0)' title='{{trans('crudbooster.text_delete')}}'><i class='fa fa-ban'></i></a>
                    </div>@endif
            @else
                <p><a data-lightbox="roadtrip" href="{{ ($value)?asset($value):'' }}"><img id='holder-{{$name}}'
                                                                                           {{ ($value)?'src='.asset($value):'' }} style="margin-top:15px;max-height:100px;"></a>
                </p>
            @endif

            @if(!$readonly || !$disabled)
                <p><a class='btn btn-danger btn-delete btn-sm'
                      onclick='swal({   title: "{{trans("crudbooster.delete_title_confirm")}}",   text: "{{trans("crudbooster.delete_description_confirm")}}",   type: "warning",   showCancelButton: true,   confirmButtonColor: "#DD6B55",   confirmButtonText: "{{trans("crudbooster.confirmation_yes")}}", cancelButtonText: "{{trans('crudbooster.button_cancel')}}",   closeOnConfirm: false }, function(){  location.href="{{url(CRUDBooster::mainpath("update-single?table=$table&column=$name&value=&id=$id"))}}" });'><i
                                class='fa fa-ban'></i> {{trans('crudbooster.text_delete')}} </a></p>
            @endif
        @endif


        <div class='help-block'>{{@$form['help']}}</div>
        <div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
    </div>
</div>
@if(@$form['filemanager_type'])
    @push('bottom')
        <script type="text/javascript">$('#lfm-{{$name}}').filemanager('file', {prefix: "{{url(config('lfm.prefix'))}}"});</script>
    @endpush
@else
    @push('bottom')
        <script type="text/javascript">$('#lfm-{{$name}}').filemanager('images', {prefix: "{{url(config('lfm.prefix'))}}"});</script>
    @endpush
@endif
