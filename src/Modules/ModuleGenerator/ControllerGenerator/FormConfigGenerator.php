<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator\ControllerGenerator;

use CB;

class FormConfigGenerator
{
    /**
     * @param $table
     * @param $coloms
     * @return array
     */
    static function generateFormConfig($table, $coloms)
    {
        $formArrayString = [];
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

            if (FieldDetector::isExceptional($field)) {
                continue;
            }

            $typedata = CB::getFieldType($table, $field);

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

            if (FieldDetector::isForeignKey($field)) {
                list($type, $options) = self::handleForeignKey($field);
            }

            if (substr($field, 0, 3) == 'is_') {
                $type = 'radio_dataenum';
                $label_field = ucwords(substr($field, 3));
                $validation = ['required|integer'];
                $options = [
                    'enum' => ['In '.$label_field, $label_field],
                    'value' => [0, 1],
                ];
            }

            if (FieldDetector::isPassword($field)) {
                $type = 'password';
                $validation = ['min:3', 'max:32'];
                $help = cbTrans("text_default_help_password");
            }

            if (FieldDetector::isImage($field)) {
                $type = 'upload';
                $validation = ['required|image'];
                $help = cbTrans('text_default_help_upload');
            }

            if (FieldDetector::isGeographical($field)) {
                $type = 'hidden';
            }

            if (FieldDetector::isPhone($field)) {
                $type = 'number';
                $validation = ['required', 'numeric'];
                $placeholder = cbTrans('text_default_help_number');
            }

            if (FieldDetector::isEmail($field)) {
                $type = 'email';
                $validation[] = 'email|unique:'.$table;
                $placeholder = cbTrans('text_default_help_email');
            }

            if ($type == 'text' && FieldDetector::isNameField($field)) {
                $placeholder = cbTrans('text_default_help_text');
                $validation = ['required', 'string', 'min:3', 'max:70'];
            }

            if ($type == 'text' && FieldDetector::isUrlField($field)) {
                $validation = ['required', 'url'];
                $placeholder = cbTrans('text_default_help_url');
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
            $formArrayString[] = min_var_export($formArray, "            ");
        }

        return $formArrayString;
    }

    /**
     * @param $field
     * @return array
     */
    private static function handleForeignKey($field)
    {
        $jointable = str_replace(['id_', '_id'], '', $field);
        if (Schema::hasTable($jointable)) {
            $joincols = CB::getTableColumns($jointable);
            $joinname = CB::getNameTable($joincols);
            $jointablePK = CB::pk($jointable);
            $type = 'select2_datatable';
            $options = [
                'table' => $jointable,
                'field_label' => $joinname,
                'field_value' => $jointablePK,
            ];
        }

        return [$type, $options];
    }
}