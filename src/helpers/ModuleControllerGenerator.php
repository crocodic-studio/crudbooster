<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/4/2020
 * Time: 6:08 PM
 */

namespace crocodicstudio\crudbooster\helpers;


class ModuleControllerGenerator
{
    public $module_name;
    public $module_icon;
    public $table;
    public $generate_type = "Simple";
    public $global_privilege = false;
    public $limit = 20;
    public $button_table_action = true;
    public $button_action_style = "button_icon";
    public $button_add = true;
    public $button_delete = true;
    public $button_edit = true;
    public $button_detail = true;
    public $button_show = true;
    public $button_filter = true;
    public $button_export = true;
    public $button_import = false;
    public $button_bulk_action = true;
    public $sidebar_mode = "normal";

    /**
     * Make a controller name
     * @return string
     */
    private function getControllerName() {
        $controller_name = ucwords(str_replace('_', ' ', $this->table));
        $controller_name = str_replace(' ', '', $controller_name).'Controller';
        if ($this->module_name) {
            $controller_name = ucwords(str_replace(['_', '-'], ' ', $this->module_name));
            $controller_name = str_replace(' ', '', $controller_name).'Controller';
        }
        return $controller_name;
    }

    private function makeColumnArray($columns) {

        $exception = ['id', 'created_at', 'updated_at', 'deleted_at'];
        $image_candidate = explode(',', config('crudbooster.IMAGE_FIELDS_CANDIDATE'));
        $password_candidate = explode(',', config('crudbooster.PASSWORD_FIELDS_CANDIDATE'));
        $phone_candidate = explode(',', config('crudbooster.PHONE_FIELDS_CANDIDATE'));
        $email_candidate = explode(',', config('crudbooster.EMAIL_FIELDS_CANDIDATE'));
        $name_candidate = explode(',', config('crudbooster.NAME_FIELDS_CANDIDATE'));
        $url_candidate = explode(',', config("crudbooster.URL_FIELDS_CANDIDATE"));

        $column_list = array_slice($columns, 0, 8);
        $php = "\$this->col = [];\n";
        foreach ($column_list as $c) {
            $label = str_replace("id_", "", $c);
            $label = ucwords(str_replace("_", " ", $label));
            $label = str_replace('Cms ', '', $label);
            $field = $c;

            if (in_array($field, $exception)) {
                continue;
            }

            if (array_search($field, $password_candidate) !== false) {
                continue;
            }

            if (substr($field, 0, 3) == 'id_') {
                $jointable = str_replace('id_', '', $field);
                $joincols = cb()->getTableColumns($jointable);
                $joinname = cb()->getNameTable($joincols);
                $php .= "\t\t".'$this->col[] = ["label"=>"'.$label.'","name"=>"'.$field.'","join"=>"'.$jointable.','.$joinname.'"];'."\n";
            } elseif (substr($field, -3) == '_id') {
                $jointable = substr($field, 0, (strlen($field) - 3));
                $joincols = cb()->getTableColumns($jointable);
                $joinname = cb()->getNameTable($joincols);
                $php .= "\t\t".'$this->col[] = ["label"=>"'.$label.'","name"=>"'.$field.'","join"=>"'.$jointable.','.$joinname.'"];'."\n";
            } else {
                $image = '';
                if (in_array($field, $image_candidate)) {
                    $image = ',"image"=>true';
                }
                $php .= "\t\t".'$this->col[] = ["label"=>"'.$label.'","name"=>"'.$field.'" '.$image.'];'."\n";
            }
        }
        return $php;
    }

    private function makeFormArray($columns) {
        $exception = ['id', 'created_at', 'updated_at', 'deleted_at'];
        $image_candidate = explode(',', config('crudbooster.IMAGE_FIELDS_CANDIDATE'));
        $password_candidate = explode(',', config('crudbooster.PASSWORD_FIELDS_CANDIDATE'));
        $phone_candidate = explode(',', config('crudbooster.PHONE_FIELDS_CANDIDATE'));
        $email_candidate = explode(',', config('crudbooster.EMAIL_FIELDS_CANDIDATE'));
        $name_candidate = explode(',', config('crudbooster.NAME_FIELDS_CANDIDATE'));
        $url_candidate = explode(',', config("crudbooster.URL_FIELDS_CANDIDATE"));

        $php = "\$this->form = [];\n";
        foreach ($columns as $c) {
            $attribute = [];
            $validation = [];
            $validation[] = 'required';
            $placeholder = '';
            $help = '';

            $label = str_replace("id_", "", $c);
            $label = ucwords(str_replace("_", " ", $label));
            $field = $c;

            if (in_array($field, $exception)) {
                continue;
            }

            $typedata = cb()->getFieldType($table, $field);

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
                    $validation[] = "string|min:5|max:5000";
                    break;
                case 'date':
                    $type = 'date';
                    $validation[] = "date";
                    break;
                case 'datetime':
                case 'timestamp':
                    $type = 'datetime';
                    $validation[] = "date_format:Y-m-d H:i:s";
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

            if (substr($field, 0, 3) == 'id_') {
                $jointable = str_replace('id_', '', $field);
                $joincols = cb()->getTableColumns($jointable);
                $joinname = cb()->getNameTable($joincols);
                $attribute['datatable'] = $jointable.','.$joinname;
                $type = 'select2';
            }

            if (substr($field, -3) == '_id') {
                $jointable = str_replace('_id', '', $field);
                $joincols = cb()->getTableColumns($jointable);
                $joinname = cb()->getNameTable($joincols);
                $attribute['datatable'] = $jointable.','.$joinname;
                $type = 'select2';
            }

            if (substr($field, 0, 3) == 'is_') {
                $type = 'radio';
                $label_field = ucwords(substr($field, 3));
                $validation = ['required|integer'];
                $attribute['dataenum'] = ['1|'.$label_field, '0|Un-'.$label_field];
            }

            if (in_array($field, $password_candidate)) {
                $type = 'password';
                $validation = ['min:3', 'max:32'];
                $attribute['help'] = trans("crudbooster.text_default_help_password");
            }

            if (in_array($field, $image_candidate)) {
                $type = 'upload';
                $attribute['help'] = trans('crudbooster.text_default_help_upload');
                $validation = ['required|image|max:3000'];
            }

            if ($field == 'latitude') {
                $type = 'hidden';
            }
            if ($field == 'longitude') {
                $type = 'hidden';
            }

            if (in_array($field, $phone_candidate)) {
                $type = 'number';
                $validation = ['required', 'numeric'];
                $attribute['placeholder'] = trans('crudbooster.text_default_help_number');
            }

            if (in_array($field, $email_candidate)) {
                $type = 'email';
                $validation[] = 'email|unique:'.$table;
                $attribute['placeholder'] = trans('crudbooster.text_default_help_email');
            }

            if ($type == 'text' && in_array($field, $name_candidate)) {
                $attribute['placeholder'] = trans('crudbooster.text_default_help_text');
                $validation = ['required', 'string', 'min:3', 'max:70'];
            }

            if ($type == 'text' && in_array($field, $url_candidate)) {
                $validation = ['required', 'url'];
                $attribute['placeholder'] = trans('crudbooster.text_default_help_url');
            }

            $validation = implode('|', $validation);

            $php .= "\t\t";
            $php .= '$this->form[] = ["label"=>"'.$label.'","name"=>"'.$field.'","type"=>"'.$type.'","required"=>TRUE';

            if ($validation) {
                $php .= ',"validation"=>"'.$validation.'"';
            }

            if ($attribute) {
                foreach ($attribute as $key => $val) {
                    if (is_bool($val)) {
                        $val = ($val) ? "TRUE" : "FALSE";
                    } else {
                        $val = '"'.$val.'"';
                    }
                    $php .= ',"'.$key.'"=>'.$val;
                }
            }

            $php .= "];\n";
        }
        return $php;
    }

    public function generate()
    {
        $columns = cb()->getTableColumns($this->table);

        /**
         * Find the similarity of name in the columns
         */
        $name_candidate_column = cb()->getNameTable($columns);

        /**
         * Find the primary key of table
         */
        $pk = cb()->pk($this->table);

        if(request('include_controller_doc')) $template = file_get_contents(__DIR__.'/../stubs/AdvancedModuleController.php.stub');
        else $template = file_get_contents(__DIR__.'/../stubs/SimpleModuleController.php.stub');

        // Replace class name
        $template = str_replace('FooBarController', $this->getControllerName(), $template);

        // Replace module name
        $template = str_replace('# module_name', "public \$module_name\t\t\t\t= \"".$this->module_name."\";", $template);

        // Replace table
        $template = str_replace("# table","public \$table\t\t\t\t\t= \"".$this->table."\";", $template);

        // Replace limit
        $template = str_replace("# limit","public \$limit\t\t\t\t\t= ".$this->limit.";", $template);

        // Replace title_field
        $template = str_replace("# title_field","public \$title_field\t\t\t\t= \"".$name_candidate_column."\";", $template);

        // Replace orderby
        $template = str_replace("# orderby","public \$order_by\t\t\t\t= ['".$pk."'=>'desc'];", $template);

        // Replace show_numbering
        $template = str_replace("# show_numbering","public \$show_numbering\t\t\t= false;", $template);

        // Replace global_privilege
        $this->global_privilege = ($this->global_privilege)?"true":"false";
        $template = str_replace("# global_privilege","public \$global_privilege\t\t= ".$this->global_privilege.";", $template);

        // Replace button_table_action
        $this->button_table_action = ($this->button_table_action)?"true":"false";
        $template = str_replace("# button_table_action","public \$button_table_action\t\t= ".$this->button_table_action.";", $template);

        // Replace button_action_style
        $template = str_replace("# button_action_style","public \$button_action_style\t\t= \"".$this->button_action_style."\";", $template);

        // Replace button_add
        $this->button_add = ($this->button_add)?"true":"false";
        $template = str_replace("# button_add","public \$button_add\t\t\t\t= ".$this->button_add.";", $template);

        // Replace button_edit
        $this->button_edit = ($this->button_edit)?"true":"false";
        $template = str_replace("# button_edit","public \$button_edit\t\t\t\t= ".$this->button_edit.";", $template);

        // Replace button_delete
        $this->button_delete = ($this->button_delete)?"true":"false";
        $template = str_replace("# button_delete","public \$button_delete\t\t\t= ".$this->button_delete.";", $template);

        // Replace button_detail
        $this->button_detail = ($this->button_detail)?"true":"false";
        $template = str_replace("# button_detail","public \$button_detail\t\t\t= ".$this->button_detail.";", $template);

        // Replace button_show
        $this->button_show = ($this->button_show)?"true":"false";
        $template = str_replace("# button_show","public \$button_show\t\t\t\t= ".$this->button_show.";", $template);

        // Replace button_filter
        $this->button_filter = ($this->button_filter)?"true":"false";
        $template = str_replace("# button_filter","public \$button_filter\t\t\t= ".$this->button_filter.";", $template);

        // Replace button_export
        $this->button_export = ($this->button_export)?"true":"false";
        $template = str_replace("# button_export","public \$button_export\t\t\t= ".$this->button_export.";", $template);

        // Replace button_import
        $this->button_import = ($this->button_import)?"true":"false";
        $template = str_replace("# button_import","public \$button_import\t\t\t= ".$this->button_import.";", $template);

        // Replace button_bulk_action
        $this->button_bulk_action = ($this->button_bulk_action)?"true":"false";
        $template = str_replace("# button_bulk_action","public \$button_bulk_action\t\t= ".$this->button_bulk_action.";", $template);

        // Replace button_add
        $template = str_replace("# sidebar_mode","public \$sidebar_mode\t\t\t= \"".$this->sidebar_mode."\";", $template);

        // Replace columns
        $template = str_replace("# columns",$this->makeColumnArray($columns), $template);

        // Replace forms
        $template = str_replace("# forms",$this->makeFormArray($columns), $template);


        // Make sure there is no space after and before
        $template = trim($template);

        // Create a controller file
        file_put_contents(app_path('Http/Controllers/Admin'.$this->getControllerName().'.php'), $template);

        return 'Admin'.$this->getControllerName();
    }

}