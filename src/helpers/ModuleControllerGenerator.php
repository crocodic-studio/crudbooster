<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/4/2020
 * Time: 6:08 PM
 */

namespace crocodicstudio\crudbooster\helpers;


use Illuminate\Support\Facades\Schema;

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

    private $columns = [];
    private $forms = [];
    private $properties = [];

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

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param array $properties
     */
    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    public function getColumns() {
        return $this->columns;
    }

    public function getForms() {
        return $this->forms;
    }

    public function setColumns($columns) {
        $this->columns = $columns;
    }

    public function setForms($forms) {
        $this->forms = $forms;
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

        if(!$this->columns) {
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
                    if(Schema::hasTable($jointable)) {
                        $joincols = cb()->getTableColumns($jointable);
                        $joinname = cb()->getNameTable($joincols);
                        $this->columns[] = ['label'=>$label,'name'=>$field,'join'=>$jointable.",".$joinname];
                    } else {
                        $this->columns[] = ['label'=>$label,'name'=>$field];
                    }

                } elseif (substr($field, -3) == '_id') {
                    $jointable = substr($field, 0, (strlen($field) - 3));
                    if(Schema::hasTable($jointable)) {
                        $joincols = cb()->getTableColumns($jointable);
                        $joinname = cb()->getNameTable($joincols);
                        $this->columns[] = ['label'=>$label,'name'=>$field,'join'=>$jointable.",".$joinname];
                    } else {
                        $this->columns[] = ['label'=>$label,'name'=>$field];
                    }
                } else {
                    if (in_array($field, $image_candidate)) {
                        $this->columns[] = ['label'=>$label,'name'=>$field,'image'=>true];
                    } else {
                        $this->columns[] = ['label'=>$label,'name'=>$field];
                    }
                }
            }
        }

        $php = "\$this->col = [];\n";
        foreach($this->columns as $column) {
            $php .= "\t\t".'$this->col[] = '.min_var_export($column).';'."\n";
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

        if(!$this->forms) {
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

                    if(Schema::hasTable($jointable)) {
                        $joincols = cb()->getTableColumns($jointable);
                        $joinname = cb()->getNameTable($joincols);
                        $attribute['datatable'] = $jointable.','.$joinname;
                        $type = 'select2';
                    }
                }

                if (substr($field, -3) == '_id') {
                    $jointable = str_replace('_id', '', $field);
                    if(Schema::hasTable($jointable)) {
                        $joincols = cb()->getTableColumns($jointable);
                        $joinname = cb()->getNameTable($joincols);
                        $attribute['datatable'] = $jointable.','.$joinname;
                        $type = 'select2';
                    }
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

                if ($attribute) {
                    $form = ['label'=>$label,'name'=>$field,'type'=>$type,'required'=>true,'validation'=>$validation];
                    $attribute_array = [];
                    foreach ($attribute as $key => $val) {

                        if (is_bool($val)) {
                            $val = ($val) ? "TRUE" : "FALSE";
                        }

                        $attribute_array[$key] = $val;
                    }

                    $form = array_merge($form, $attribute_array);
                    $this->forms[] = $form;
                } else {
                    $this->forms[] = ['label'=>$label,'name'=>$field,'type'=>$type,'required'=>true,'validation'=>$validation];
                }
            }
        }

        $php = "\$this->form = [];\n";
        foreach($this->forms as $form) {
            $php .= "\t\t".'$this->form[] = '.min_var_export($form).';'."\n";
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

        if(!$this->properties) {
            // Replace module name
            $template = str_replace('# module_name', "public \$module_name = \"".$this->module_name."\";", $template);
            $this->properties['module_name'] = $this->module_name;

            // Replace table
            $template = str_replace("# table","public \$table = \"".$this->table."\";", $template);
            $this->properties['table'] = $this->table;

            // Replace limit
            $template = str_replace("# limit","public \$limit = ".$this->limit.";", $template);
            $this->properties['limit'] = $this->limit;

            // Replace title_field
            $template = str_replace("# title_field","public \$title_field = \"".$name_candidate_column."\";", $template);
            $this->properties['title_field'] = $name_candidate_column;

            // Replace orderby
            $template = str_replace("# order_by","public \$order_by = ['".$pk."'=>'desc'];", $template);
            $this->properties['order_by'] = $pk.",desc";

            // Replace show_numbering
            $template = str_replace("# show_numbering","public \$show_numbering = false;", $template);
            $this->properties['show_numbering'] = false;

            // Replace global_privilege
            $this->global_privilege = ($this->global_privilege)?"true":"false";
            $template = str_replace("# global_privilege","public \$global_privilege = ".$this->global_privilege.";", $template);
            $this->properties['global_privilege'] = $this->global_privilege;

            // Replace button_table_action
            $this->button_table_action = ($this->button_table_action)?"true":"false";
            $template = str_replace("# button_table_action","public \$button_table_action = ".$this->button_table_action.";", $template);
            $this->properties['button_table_action'] = $this->button_table_action;

            // Replace button_action_style
            $template = str_replace("# button_action_style","public \$button_action_style = \"".$this->button_action_style."\";", $template);
            $this->properties['$this->button_action_style'] = $this->button_action_style;

            // Replace button_add
            $this->button_add = ($this->button_add)?"true":"false";
            $template = str_replace("# button_add","public \$button_add = ".$this->button_add.";", $template);
            $this->properties['button_add'] = $this->button_add;

            // Replace button_edit
            $this->button_edit = ($this->button_edit)?"true":"false";
            $template = str_replace("# button_edit","public \$button_edit = ".$this->button_edit.";", $template);
            $this->properties['button_edit'] = $this->button_edit;

            // Replace button_delete
            $this->button_delete = ($this->button_delete)?"true":"false";
            $template = str_replace("# button_delete","public \$button_delete = ".$this->button_delete.";", $template);
            $this->properties['button_delete'] = $this->button_delete;

            // Replace button_detail
            $this->button_detail = ($this->button_detail)?"true":"false";
            $template = str_replace("# button_detail","public \$button_detail = ".$this->button_detail.";", $template);
            $this->properties['button_detail'] = $this->button_detail;

            // Replace button_show
            $this->button_show = ($this->button_show)?"true":"false";
            $template = str_replace("# button_show","public \$button_show = ".$this->button_show.";", $template);
            $this->properties['button_show'] = $this->button_show;

            // Replace button_filter
            $this->button_filter = ($this->button_filter)?"true":"false";
            $template = str_replace("# button_filter","public \$button_filter = ".$this->button_filter.";", $template);
            $this->properties['button_filter'] = $this->button_filter;

            // Replace button_export
            $this->button_export = ($this->button_export)?"true":"false";
            $template = str_replace("# button_export","public \$button_export = ".$this->button_export.";", $template);
            $this->properties['button_export'] = $this->button_export;

            // Replace button_import
            $this->button_import = ($this->button_import)?"true":"false";
            $template = str_replace("# button_import","public \$button_import = ".$this->button_import.";", $template);
            $this->properties['button_import'] = $this->button_import;

            // Replace button_bulk_action
            $this->button_bulk_action = ($this->button_bulk_action)?"true":"false";
            $template = str_replace("# button_bulk_action","public \$button_bulk_action = ".$this->button_bulk_action.";", $template);
            $this->properties['$this->button_bulk_action'] = $this->button_bulk_action;

            // Replace button_add
            $template = str_replace("# sidebar_mode","public \$sidebar_mode = \"".$this->sidebar_mode."\";", $template);
            $this->properties['$this->sidebar_mode'] = $this->sidebar_mode;
        } else {
            foreach($this->properties as $key => $value) {
                if(is_int($value)) {
                    $template = str_replace("# ".$key,"public \$".$key." = ".$value.";", $template);
                } elseif (is_bool($value)) {
                    $value = ($value===true)?"true":"false";
                    $template = str_replace("# ".$key,"public \$".$key." = ".$value.";", $template);
                } else {
                    $template = str_replace("# ".$key,"public \$".$key." = \"".$value."\";", $template);
                }
            }
        }

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