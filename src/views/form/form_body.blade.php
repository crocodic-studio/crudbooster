<?php
    $header_group_class = "header-group-0";
?>
    @include('crudbooster::default._form_body.component_assets', ['forms' => $forms])
    @foreach($forms as $index => $formInput)
        @php
            $label = $formInput['label'];
            $name = $formInput['name'];
            $join = $formInput['join'];
            $value = $formInput['value'] ?: '';
            $value = (isset($row->{$name}))?$row->{$name}:$value;
            $old = old($name);
            $value = (!empty($old))?$old:$value;

            $validation = makeValidationForHTML($formInput['validation']);

            if(isset($formInput['callback'])) {
                $value = call_user_func($formInput['callback'],$row);
            }

             $required = ($formInput['required'] || @strpos($formInput['validation'], 'required') !== false)? 'required' : '';

            $type = $formInput['type'];
            $readonly = $formInput['readonly'] ? 'readonly' : '';
            $disabled = $formInput['disabled'] ? 'disabled' : '';
            $placeholder  = "placeholder='{$formInput['placeholder']}'";
            $col_width = $formInput['width'];

            if($parent_field == $name) {
                $type = 'hidden';
                $value = $parent_id;
            }

            if($type == 'header') {
                $header_group_class = "header-group-$index";
            }

        @endphp
        @include('crudbooster::default._form_body.component', ['form' => $formInput])

    @endforeach