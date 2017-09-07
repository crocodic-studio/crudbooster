<?php $default = !empty($form['placeholder']) ? $form['placeholder'] : cbTrans('text_prefix_option') . " " . $form['label'];?>
<div class='form-group {{$header_group_class}} {{ ($errors->first($name))?"has-error":"" }}' id='form-group-{{$name}}'
     style="{{@$form['style']}}">
    <label class='control-label col-sm-2'>{{$form['label']}} {!!($required)?"<span class='text-danger' title='This field is required'>*</span>":"" !!}</label>

    <div class="{{$col_width?:'col-sm-10'}}">
        <select class='form-control' id="{{$name}}" data-value='{{$value}}'
                {{$required}} {!!$placeholder!!} {{$readonly}} {{$disabled}} name="{{$name}}">
            <option value=''>{{$default}}</option>
            <?php
            $enum = $form['options']['enum'];
            $enumValue = $form['options']['value'];
            foreach ($enum as $i => $e) {
                $v = ($enumValue) ? $enumValue[$i] : $e;
                $select = ($value && $value == $v) ? "selected" : "";
                echo "<option $select value='$v'>$e</option>";
            }
            ?>
        </select>
        <div class="text-danger">{!! $errors->first($name)?"<i class='fa fa-info-circle'></i> ".$errors->first($name):"" !!}</div>
        <p class='help-block'>{{ @$form['help'] }}</p>
    </div>
</div>
