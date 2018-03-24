<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator\ControllerGenerator;
use Illuminate\Support\Facades\Schema;
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

            $label = str_replace("id_", "", $c);
            $label = ucwords(str_replace("_", " ", $label));
            $field = $c;

            if (FieldDetector::isExceptional($field)) {
                continue;
            }

            $typedata = CB::getFieldType($table, $field);

            list($type, $validation, $options) = self::parseFieldType($typedata, $validation);

            if (FieldDetector::isForeignKey($field)) {
                list($type, $options) = self::handleForeignKey($field);
            }

            if (substr($field, 0, 3) == 'is_') {
                $type = 'radio_dataenum';
                $label = ucwords(substr($field, 3));
                $validation = ['required|integer'];
                $options = [
                    'enum' => ['In '.$label, $label],
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

    /**
     * @param $typeData
     * @param $validation
     * @return array
     */
    private static function parseFieldType($typeData, $validation)
    {
        $options = [];
        if ($typeData == 'text' || $typeData == 'longtext') {
            $type = 'textarea';
            $validation[] = "string|min:5";
        } elseif ($typeData == 'date') {
            $type = 'date';
            $validation[] = "date";
            $options = ['php_format' => 'M, d Y', 'datepicker_format' => 'M, dd YYYY',];
        } elseif ($typeData == 'datetime' || $typeData == 'timestamp') {
            $type = 'datetime';
            $validation[] = "date_format:Y-m-d H:i:s";
            $options = ['php_format' => 'M, d Y H:i',];
        } elseif ($typeData == 'time') {
            $type = 'time';
            $validation[] = 'date_format:H:i:s';
        } elseif ($typeData == 'double') {
            $type = 'money';
            $validation[] = "integer|min:0";
        } elseif ($typeData == 'int' || $typeData == 'integer') {
            $type = 'number';
            $validation[] = 'integer|min:0';
        } else {
            $type = "text";
            $validation[] = "min:1|max:255";
        }

        return [$type, $validation, $options];
    }
}