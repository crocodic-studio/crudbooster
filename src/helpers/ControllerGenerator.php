<?php

namespace crocodicstudio\crudbooster\helpers;


class ControllerGenerator
{
    public static function generateController($table, $name = null){

        $controllerName = self::getControllerName($table, $name);

        $coloms = CRUDBooster::getTableColumns($table);
        $pk = CB::pk($table);

        $php = '<?php
 
    namespace App\Http\Controllers;

	use Session;
	use Request;
	use DB;
	use CRUDBooster;
	use CB;

	class Admin'.$controllerName.' extends \crocodicstudio\crudbooster\controllers\CBController {

	    public function cbInit() {
	    	# START CONFIGURATION DO NOT REMOVE THIS LINE
			$this->table 			   = "'.$table.'";	        
			$this->title_field         = "'.CRUDBooster::getNameTable($coloms).'";
			$this->limit               = 20;
			$this->orderby             = "'.$pk.',desc";
			$this->show_numbering      = FALSE;			      
			$this->button_table_action = TRUE;   
			$this->button_action_style = "button_icon";     
			$this->button_add          = TRUE;
			$this->button_delete       = TRUE;
			$this->button_edit         = TRUE;
			$this->button_detail       = TRUE;
			$this->button_show         = TRUE;
			$this->button_filter       = TRUE;        
			$this->button_export       = FALSE;	        
			$this->button_import       = FALSE;
			$this->button_bulk_action  = TRUE;	
			# END CONFIGURATION DO NOT REMOVE THIS LINE						      

			# START COLUMNS DO NOT REMOVE THIS LINE
	        $this->col = [];
';
        list($php, $joinQuery) = self::addCol($table, $coloms, $php, $pk);

        $php .= '
            # END COLUMNS DO NOT REMOVE THIS LINE
        
            # START FORM DO NOT REMOVE THIS LINE
            \$this->form = [];';

        $php = self::addFormToController($table, $coloms, $php);

        $php .= '
            # END FORM DO NOT REMOVE THIS LINE

			/* 
	        | ---------------------------------------------------------------------- 
	        | Sub Module
	        | ----------------------------------------------------------------------     
			| @label          = Label of action 
			| @path           = Path of sub module
			| @foreign_key 	  = foreign key of sub table/module
			| @button_color   = Bootstrap Class (primary,success,warning,danger)
			| @button_icon    = Font Awesome Class  
			| @parent_columns = Sparate with comma, e.g : name,created_at
	        | 
	        */
	        $this->sub_module = [];


	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add More Action Button / Menu
	        | ----------------------------------------------------------------------     
	        | @label       = Label of action 
	        | @url         = Target URL, you can use field alias. e.g : [id], [name], [title], etc
	        | @icon        = Font awesome class icon. e.g : fa fa-bars
	        | @color 	   = Default is primary. (primary, warning, succecss, info)     
	        | @showIf 	   = If condition when action show. Use field alias. e.g : [id] == 1
	        | 
	        */
	        $this->addaction = [];


	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add More Button Selected
	        | ----------------------------------------------------------------------     
	        | @label       = Label of action 
	        | @icon 	   = Icon from fontawesome
	        | @name 	   = Name of button 
	        | Then about the action, you should code at actionButtonSelected method 
	        | 
	        */
	        $this->button_selected = [];

	                
	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add alert message to this module at overheader
	        | ----------------------------------------------------------------------     
	        | @message = Text of message 
	        | @type    = warning,success,danger,info        
	        | 
	        */
	        $this->alert        = [];
	                

	        
	        /* 
	        | ---------------------------------------------------------------------- 
	        | Add more button to header button 
	        | ----------------------------------------------------------------------     
	        | @label = Name of button 
	        | @url   = URL Target
	        | @icon  = Icon from Awesome.
	        | 
	        */
	        $this->index_button = [];



	        /* 
	        | ---------------------------------------------------------------------- 
	        | Customize Table Row Color
	        | ----------------------------------------------------------------------     
	        | @condition = If condition. You may use field alias. E.g : [id] == 1
	        | @color = Default is none. You can use bootstrap success,info,warning,danger,primary.        
	        | 
	        */
	        $this->table_row_color = [];     	          

	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | You may use this bellow array to add statistic at dashboard 
	        | ---------------------------------------------------------------------- 
	        | @label, @count, @icon, @color 
	        |
	        */
	        $this->index_statistic = [];



	        /*
	        | ---------------------------------------------------------------------- 
	        | Add javascript at body 
	        | ---------------------------------------------------------------------- 
	        | javascript code in the variable 
	        | $this->script_js = "function() { ... }";
	        |
	        */
	        $this->script_js = NULL;


            /*
	        | ---------------------------------------------------------------------- 
	        | Include HTML Code before index table 
	        | ---------------------------------------------------------------------- 
	        | html code to display it before index table
	        | $this->pre_index_html = "<p>test</p>";
	        |
	        */
	        $this->pre_index_html = null;
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include HTML Code after index table 
	        | ---------------------------------------------------------------------- 
	        | html code to display it after index table
	        | $this->post_index_html = "<p>test</p>";
	        |
	        */
	        $this->post_index_html = null;
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include Javascript File 
	        | ---------------------------------------------------------------------- 
	        | URL of your javascript each array 
	        | $this->load_js[] = asset("myfile.js");
	        |
	        */
	        $this->load_js = [];
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Add css style at body 
	        | ---------------------------------------------------------------------- 
	        | css code in the variable 
	        | $this->style_css = ".style{....}";
	        |
	        */
	        $this->style_css = NULL;
	        
	        
	        
	        /*
	        | ---------------------------------------------------------------------- 
	        | Include css File 
	        | ---------------------------------------------------------------------- 
	        | URL of your css each array 
	        | $this->load_css[] = asset("myfile.css");
	        |
	        */
	        $this->load_css = [];
	        
	        
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for button selected
	    | ---------------------------------------------------------------------- 
	    | @id_selected = the id selected
	    | @button_name = the name of button
	    |
	    */
	    public function actionButtonSelected($id_selected,$button_name) {
	        //Your code here
	            
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate query of index result 
	    | ---------------------------------------------------------------------- 
	    | @query = current sql query 
	    |
	    */
	    public function hookQueryIndex(&$query) {
	        //Your code here
	        '.$joinQuery.'
	    }  

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate row of index table html 
	    | ---------------------------------------------------------------------- 
	    |
	    */    
	    public function hookRowIndex($column_index,&$column_value) {	        
	    	//Your code here
	    }

	    /*
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate data input before add data is execute
	    | ---------------------------------------------------------------------- 
	    | @arr
	    |
	    */
	    public function hookBeforeAdd(&$postdata) {        
	        //Your code here

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after add public static function called 
	    | ---------------------------------------------------------------------- 
	    | @id = last insert id
	    | 
	    */
	    public function hookAfterAdd($id) {        
	        //Your code here

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for manipulate data input before update data is execute
	    | ---------------------------------------------------------------------- 
	    | @postdata = input post data 
	    | @id       = current id 
	    | 
	    */
	    public function hookBeforeEdit(&$postdata,$id) {        
	        //Your code here

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after edit public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hookAfterEdit($id) {
	        //Your code here 

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command before delete public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hookBeforeDelete($id) {
	        //Your code here

	    }

	    /* 
	    | ---------------------------------------------------------------------- 
	    | Hook for execute command after delete public static function called
	    | ----------------------------------------------------------------------     
	    | @id       = current id 
	    | 
	    */
	    public function hookAfterDelete($id) {
	        //Your code here

	    }

	    //By the way, you can still create your own method in here... :) 

	}
';
        //create file controller
        file_put_contents(base_path("app/Http/Controllers/").'Admin'.$controllerName.'.php', $php);

        return 'Admin'.$controllerName;
    }
    /**
     * @param $table
     * @param $name
     * @return string
     */
    private static function getControllerName($table, $name)
    {
        $controllername = ucwords(str_replace('_', ' ', $table));
        $controllername = str_replace(' ', '', $controllername).'Controller';
        if ($name) {
            $controllername = ucwords(str_replace(['_', '-'], ' ', $name));
            $controllername = str_replace(' ', '', $controllername).'Controller';
        }

        $countSameFile = count(glob(base_path("app/Http/Controllers/").'Admin'.$controllername.'.php'));

        if ($countSameFile != 0) {
            $suffix = $countSameFile;
            $controllername = ucwords(str_replace(['_', '-'], ' ', $name)).$suffix;
            $controllername = str_replace(' ', '', $controllername).'Controller';
        }

        return $controllername;
    }

    /**
     * @param $table
     * @param $coloms
     * @return string
     */
    private static function addFormToController($table, $coloms)
    {
        foreach ($coloms as $i => $c) {
            //$attribute = [];
            $validation = [];
            $validation[] = 'required';
            $placeholder = '';
            $help = '';
            $options = [];

            $label = str_replace("id_", "", $c);
            $label = ucwords(str_replace("_", " ", $label));
            $field = $c;

            if (self::isExceptional($field)) {
                continue;
            }

            $typedata = CRUDBooster::getFieldType($table, $field);

            switch ($typedata) {
                default:
                case 'varchar':
                case 'char':
                    $type = "text";
                    $validation[] = "min:1|max:255";
                    break;
                case 'text':
                case 'longtext':
                    $type = 'textarea';
                    $validation[] = "string|min:5";
                    break;
                case 'date':
                    $type = 'date';
                    $validation[] = "date";
                    $options = [
                        'php_format' => 'M, d Y',
                        'datepicker_format' => 'M, dd YYYY',
                    ];
                    break;
                case 'datetime':
                case 'timestamp':
                    $type = 'datetime';
                    $validation[] = "date_format:Y-m-d H:i:s";
                    $options = [
                        'php_format' => 'M, d Y H:i',
                    ];
                    break;
                case 'time':
                    $type = 'time';
                    $validation[] = 'date_format:H:i:s';
                    break;
                case 'double':
                    $type = 'money';
                    $validation[] = "integer|min:0";
                    break;
                case 'int':
                case 'integer':
                    $type = 'number';
                    $validation[] = 'integer|min:0';
                    break;
            }

            if (self::isForeignKey($field)) {
                $jointable = str_replace(['id_', '_id'], '', $field);
                if (Schema::hasTable($jointable)) {
                    $joincols = CRUDBooster::getTableColumns($jointable);
                    $joinname = CRUDBooster::getNameTable($joincols);
                    $jointablePK = CB::pk($jointable);
                    $type = 'select2_datatable';
                    $options = [
                        "table" => $jointable,
                        "field_label" => $joinname,
                        "field_value" => $jointablePK,
                    ];
                }
            }

            if (substr($field, 0, 3) == 'is_') {
                $type = 'radio_dataenum';
                $label_field = ucwords(substr($field, 3));
                $validation = ['required|integer'];
                $options = [
                    "enum" => ["In ".$label_field, $label_field],
                    "value" => [0, 1],
                ];
            }

            if (self::isPassword($field)) {
                $type = 'password';
                $validation = ['min:3', 'max:32'];
                $help = trans("crudbooster.text_default_help_password");
            }

            if (self::isImage($field)) {
                $type = 'upload';
                $help = trans('crudbooster.text_default_help_upload');
                $validation = ['required|image'];
            }

            if (self::isGeographical($field)) {
                $type = 'hidden';
            }

            if (self::isPhone($field)) {
                $type = 'number';
                $validation = ['required', 'numeric'];
                $placeholder = trans('crudbooster.text_default_help_number');
            }

            if (self::isEmail($field)) {
                $type = 'email';
                $validation[] = 'email|unique:'.$table;
                $placeholder = trans('crudbooster.text_default_help_email');
            }

            if ($type == 'text' && in_array($field, explode(',', cbConfig('NAME_FIELDS_CANDIDATE')))) {
                $placeholder = trans('crudbooster.text_default_help_text');
                $validation = ['required', 'string', 'min:3', 'max:70'];
            }

            if ($type == 'text' && in_array($field, explode(',', cbConfig("URL_FIELDS_CANDIDATE")))) {
                $validation = ['required', 'url'];
                $placeholder = trans('crudbooster.text_default_help_url');
            }

            $validation = implode('|', $validation);

            $formArray = [];
            $formArray['label'] = $label;
            $formArray['name'] = $field;
            $formArray['type'] = $type;
            $formArray['options'] = $options;
            $formArray['required'] = true;
            $formArray['validation'] = $validation;
            $formArray['help'] = $help;
            $formArray['placeholder'] = $placeholder;
            $formArrayString = min_var_export($formArray, "            ");
            $php = "
            ";
            $php .= '$this->form[] = '.$formArrayString.';';
        }

        return $php;
    }

    /**
     * @param $table
     * @param $coloms
     * @param $php
     * @param $pk
     * @return array
     */
    private static function addCol($table, $coloms, $php, $pk)
    {
        $coloms_col = array_slice($coloms, 0, 8);
        $joinList = [];

        foreach ($coloms_col as $c) {
            $label = str_replace("id_", "", $c);
            $label = ucwords(str_replace("_", " ", $label));
            $label = str_replace('Cms ', '', $label);
            $field = $c;

            if (self::isExceptional($field) || self::isPassword($field)) {
                continue;
            }

            if (self::isForeignKey($field)) {
                $jointable = str_replace(['id_', '_id'], '', $field);

                if (Schema::hasTable($jointable)) {
                    $joincols = CRUDBooster::getTableColumns($jointable);
                    $joinname = CRUDBooster::getNameTable($joincols);
                    $php .= "            \$this->col[] = ['label'=>$label,'name'=>'$jointable.$joinname'];"."\n";
                    $jointablePK = CB::pk($jointable);
                    $joinList[] = [
                        'table' => $jointable,
                        'field1' => $jointable.'.'.$jointablePK,
                        'field2' => $table.'.'.$pk,
                    ];
                }
            } else {
                $image = '';
                if (self::isImage($field)) {
                    $image = ',"image"=>true';
                }
                $php .= "            ".'$this->col[] = ["label"=>"'.$label.'","name"=>"'.$field.'" '.$image.'];'."\n";
            }
        }
        $joinQuery = '';
        if (count($joinList)) {
            foreach ($joinList as $j) {
                $joinQuery .= '$query->join("'.$j['table'].'","'.$j['field1'].'","=","'.$j['field2'].'");'."\n";
            }
        }

        return [$php, $joinQuery];
    }

    /**
     * @param $field
     * @return bool
     */
    private static function isPassword($field)
    {
        return in_array($field, explode(',', cbConfig('PASSWORD_FIELDS_CANDIDATE')));
    }

    /**
     * @param $field
     * @return bool
     */
    private static function isEmail($field)
    {
        return in_array($field, explode(',', cbConfig('EMAIL_FIELDS_CANDIDATE')));
    }

    /**
     * @param $field
     * @return bool
     */
    private static function isPhone($field)
    {
        return in_array($field, explode(',', cbConfig('PHONE_FIELDS_CANDIDATE')));
    }

    /**
     * @param $field
     * @return bool
     */
    private static function isImage($field)
    {
        return in_array($field, explode(',', cbConfig('IMAGE_FIELDS_CANDIDATE')));
    }

    /**
     * @param $field
     * @return bool
     */
    private static function isExceptional($field)
    {
        return in_array($field, ['id', 'created_at', 'updated_at', 'deleted_at']);
    }

    /**
     * @param $field
     * @return bool
     */
    private static function isForeignKey($field)
    {
        return substr($field, 0, 3) == 'id_' || substr($field, -3) == '_id';
    }

    /**
     * @param $field
     * @return bool
     */
    private static function isGeographical($field)
    {
        return $field == 'latitude' || $field == 'longitude';
    }
}