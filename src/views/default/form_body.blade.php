<form class='form-horizontal' method='post' id="form" enctype="multipart/form-data"
	  action='{{CRUDBooster::mainpath("add-save")}}'>
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	<input type='hidden' name='return_url' value='{{Request::fullUrl()}}'/>
	@php

		//Loading Assets
        $asset_already = [];
        foreach($forms as $form) {
            $type = @$form['type']?:'text';
            $name = $form['name'];

            if(in_array($type, $asset_already)) {continue;}
	@endphp
	@if(file_exists(base_path('/vendor/crocodicstudio/crudbooster/src/views/default/type_components/'.$type.'/asset.blade.php')))
		@include('crudbooster::default.type_components.'.$type.'.asset')
	@elseif(file_exists(resource_path('views/vendor/crudbooster/type_components/'.$type.'/asset.blade.php')))
		@include('vendor.crudbooster.type_components.'.$type.'.asset')
	@endif
	@php
		$asset_already[] = $type;
    }


        //Loading input components
        $header_group_class = "";
        foreach($forms as $index=>$form) {

            $name 		= $form['name'];
            @$join 		= $form['join'];
            @$value		= (isset($form['value']))?$form['value']:'';
            @$value		= (isset($row->{$name}))?$row->{$name}:$value;

            $old 		= old($name);
            $value 		= (!empty($old))?$old:$value;

            $validation = array();
            $validation_raw = isset($form['validation'])?explode('|',$form['validation']):array();
            if($validation_raw) {
                foreach($validation_raw as $vr) {
                    $vr_a = explode(':',$vr);
                    if($vr_a[1]) {
                        $key = $vr_a[0];
                        $validation[$key] = $vr_a[1];
                    }else{
                        $validation[$vr] = true;
                    }
                }
            }

            if(isset($form['callback'])) {
                $value = call_user_func($form['callback'],$row);
            }

            $form['type'] = ($form['type'])?:'text';
            $type         = @$form['type'];
            $required     = (@$form['required'])?"required":"";
            $required  	  = (@strpos($form['validation'], 'required')!==false)?"required":$required;
            $readonly     = (@$form['readonly'])?"readonly":"";
            $disabled     = (@$form['disabled'])?"disabled":"";
            $placeholder  = (@$form['placeholder'])?"placeholder='".$form['placeholder']."'":"";
            $col_width    = @$form['width']?:"col-sm-9";

            if($parent_field == $name) {
                $type = 'hidden';
                $value = $parent_id;
            }

            if($type=='header') {
                $header_group_class = "header-group-$index";
            }else{
                $header_group_class = ($header_group_class)?:"header-group-$index";
            }

	@endphp
	@if(file_exists(base_path('/vendor/crocodicstudio/crudbooster/src/views/default/type_components/'.$type.'/component.blade.php')))
		@include('crudbooster::default.type_components.'.$type.'.component')
	@elseif(file_exists(resource_path('views/vendor/crudbooster/type_components/'.$type.'/component.blade.php')))
		@include('vendor.crudbooster.type_components.'.$type.'.component')
	@else
		<p class='text-danger'>{{$type}} is not found in type component system</p><br/>
	@endif
    <?php
    } ?>

	<p align="right"><input type='submit' class='btn btn-primary' value='Add Menu'/></p>
</form>