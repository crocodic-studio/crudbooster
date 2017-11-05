<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}'
     style="{{@$form['style']}}">
    <label class='control-label col-sm-2'>{{$form['label']}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>
    <div class="{{$col_width?:'col-sm-10'}}">

        <?php
        $options = ['value' => [], 'label' => []];
        $enums = @$form['options']['enum'] ?: [];

        //$options['label'] = $form['options']['enum'];

        if ($form['options']['value']) {
            $options['value'] = $form['options']['value'];
        } else {
            $options['value'] = $form['options']['enum'];
        }
        ?>
    @foreach($enums as $i => $enum)
        <div data-val='{!! $val !!}' class='checkbox {{$disabled}}'>
            <label>
                <input type='checkbox'  {{$disabled}} {!! is_checked($form['options']['result_format'], $value, $options['value'][$i]) !!}
                 name='{!! $name !!}[]'
                 value='{!! $options['value'][$i] !!}'>
                 {!! $enum !!}
            </label>
        </div>
    @endforeach
        <div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
        <p class='help-block'>{{ @$form['help'] }}</p>
    </div>
</div>