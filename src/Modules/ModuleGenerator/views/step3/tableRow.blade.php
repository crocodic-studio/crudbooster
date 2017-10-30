<td>
    <a href="javascript:void(0)" class="btn btn-info btn-plus">
        {!! CB::icon('plus') !!}
    </a>
    <a href="javascript:void(0)" class="btn btn-danger btn-delete">
        {!! CB::icon('trash') !!}
    </a>
    <a href="javascript:void(0)" class="btn btn-success btn-up">
        {!! CB::icon('arrow-up') !!}
    </a>
    <a href="javascript:void(0)" class="btn btn-success btn-down">
        {!! CB::icon('arrow-down') !!}
    </a>
</td>
<td><input type='text' value='{{$form["label"]}}' placeholder="Input field label"
           onclick='showColumnSuggest(this)' onkeyup="showColumnSuggestLike(this)"
           class='form-control labels' name='label[]'/>
</td>
<td><input type='text' value='{{$form["name"]}}' placeholder="Input field name"
           onclick='showNameSuggest(this)' onkeyup="showNameSuggestLike(this)"
           class='form-control name' name='name[]'/>
</td>
<td>

    <div class="input-group">

        <input type='text' value='{{$form["type"]?:"text"}}' placeholder="Input field type"
               onclick='showTypeSuggest(this)' onkeyup="showTypeSuggestLike(this)"
               class='form-control type' name='type[]'/>

        <span class="input-group-btn">
            <button class="btn btn-primary btn-options" title="Options" type="button">
                {!! CB::icon('cog') !!}
            </button>
        </span>

    </div>

    <input type="hidden" class="input-style" name="style[]">
    <input type="hidden" class="input-placeholder" name="placeholder[]">
    <input type="hidden" class="input-help" name="help[]">
    <div class="input-options">
        @foreach($form['options'] as $key=>$val)
            @if(is_array($val))
                <input type="hidden" data-option-name="{{$key}}"
                       class="input-option-hidden input-{{$key}}"
                       name="{{$key}}[{{$form['name']}}]" value="{{implode(';',$val)}}">
            @else
                <input type="hidden" data-option-name="{{$key}}"
                       class="input-option-hidden input-{{$key}}"
                       name="{{$key}}[{{$form['name']}}]" value="{{$val}}">
            @endif
        @endforeach
    </div>
</td>
<td><input type='text' value='{{$form["validation"]}}' class='form-control validation'
           onclick="showValidationSuggest(this)" onkeyup="showValidationSuggestLike(this)"
           name='validation[]' value='required' placeholder='Enter Laravel Validation'/>
</td>
<td>
    <select class='form-control width' name='width[]'>
        @for($i=10;$i>=1;$i--)
            <option {{ ($form['width'] == "col-sm-$i")?"selected":"" }} value='col-sm-{{$i}}'>{{$i}}</option>
        @endfor
    </select>
</td>
                        