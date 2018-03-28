<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}'
     style="{{@$formInput['style']}}">
    <label class='col-sm-2 control-label'>{{$label}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>

    <div class="{{$col_width?:'col-sm-10'}}">
        @if($value)
            @if(Storage::exists($value))
                <?php
                $url = asset($value);
                $ext = strtolower(pathinfo($value, PATHINFO_EXTENSION));
                $images_type = array('jpg', 'png', 'gif', 'jpeg', 'bmp', 'tiff');
                ?>

                    @if(in_array($ext, $images_type))
                <p>
                    <a data-lightbox='roadtrip' href='{{$url}}'>
                        <img style='max-width:160px' title="Image For {{$label}}" src='{{$url}}'/></a>
                </p>
                @else
                    <p><a href='{{$url}}'>{{cbTrans("button_download_file")}}</a></p>
                @endif

                <input type='hidden' name='_$name' value='{!! $value !!}'/>
            @else
                <p class='text-danger'><i class='fa fa-exclamation-triangle'></i> {!! cbTrans("file_broken") !!}</p>
            @endif

            @if(!$readonly || !$disabled)
                <p><a class='btn btn-danger btn-delete btn-sm'
                      onclick="if(!confirm('{{cbTrans("delete_title_confirm")}}')) return false"
                      href='{{url(CRUDBooster::mainpath("delete-image?image=".$value."&id=".$row->id."&column=".$name))}}'><i
                                class='fa fa-ban'></i> {{cbTrans('text_delete')}} </a></p>
            @endif
        @endif
        @if(!$value)
            <input type='file' id="{{$name}}" title="{{$label}}"
                   {{$required}} {{$readonly}} {{$disabled}} class='form-control' name="{{$name}}"/>
        @else
            <p class='text-muted'><em>{{cbTrans("notice_delete_file_upload")}}</em></p>
        @endif
        @include('crudbooster::default._form_body.underField', ['help' => $formInput['help'], 'error' => $errors->first($name)])

    </div>

</div>
