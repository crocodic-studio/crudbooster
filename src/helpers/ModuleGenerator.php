<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/25/2019
 * Time: 8:14 AM
 */

namespace crocodicstudio\crudbooster\helpers;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class ModuleGenerator
{
    private $table;
    private $icon;
    private $name;
    private $columns;
    private $rebuild;

    public function __construct($table, $name = null, $icon = "fa fa-bars", $columns, $rebuild = null)
    {
        $this->table = $table;
        $this->icon = $icon;
        $this->name = $name;
        $this->columns = $columns;
        $this->rebuild = $rebuild;
    }

    private function makeControllerName($name) {
        $controllerName = ucwords(str_replace('_', ' ', $name));
        $controllerName = str_replace(' ', '', $controllerName).'Controller';
        return "Admin".$controllerName;
    }

    public function make() {
        $name = trim(($this->name)?:ucwords(str_replace("_"," ",$this->table)));

        $template = file_get_contents(__DIR__."/../templates/FooBarController.stub");

        //Replace table
        $template = str_replace("{table}",'"'.$this->table.'"', $template);

        //Replace permalink
        $permalink = strtolower(Str::slug($name,"_"));
        $template = str_replace("{permalink}", '"'.$permalink.'"', $template);

        //Replace Page title
        $template = str_replace("{page_title}", '"'.$name.'"', $template);

        //Replace scaffolding
        $scaffold = "";
        foreach($this->columns as $field) {
            $label = $field['column_label'];
            $column = $field['column_field'];
            $help = isset($field['column_help'])?"->help(\"".$field['column_help']."\")":"";
            $required = ($field['column_mandatory']=="on")?"":"->required(false)";
            $indexShow = ($field['column_browse']=="on")?"":"->showIndex(false)";
            $detailShow = ($field['column_detail']=="on")?"":"->showDetail(false)";
            $editShow = ($field['column_edit']=="on")?"":"->showEdit(false)";
            $addShow = ($field['column_add']=="on")?"":"->showAdd(false)";
            $optionTable = $field['column_option_table'];
            $optionValue = $field['column_option_value'];
            $optionDisplay = $field['column_option_display'];
            $optionSqlCondition = $field['column_option_sql_condition'];
            $optionSqlCondition = str_replace('"',"'", $optionSqlCondition);
            $sqlRawQuery = $field['column_sql_query'];
            $options = $field['column_options'];
            $imageResizeWidth = $field['column_image_width'];
            $imageResizeHeight = $field['column_image_height'];
            $fileEncrypt = $field['column_file_encrypt'];
            $dateFormat = $field['column_date_format'];
            $textDisplayLimit = $field['column_text_display_limit'];
            $maxCharacter = $field['column_text_max'];
            $minCharacter = $field['column_text_min'];
            $moneyPrefix = $field['column_money_prefix'];
            $moneyPrecision = $field['column_money_precision'];
            $moneyThousandSeparator = $field['column_money_thousand_separator'];
            $moneyDecimalSeparator = $field['column_money_decimal_separator'];
            $filterable = ($field['column_filterable']=="on")?"->filterable(true)":"";
            $foreign_key = (isset($field['column_foreign']))?"->foreignKey('".$field['column_foreign']."')":"";

            // Additional Attributes
            $additional = $required . $indexShow . $detailShow . $addShow . $editShow . $help . $filterable . $foreign_key ;

            // Additional money
            $additional .= ($moneyPrefix && $field['column_type']=='money')?"->prefix('".$moneyPrefix."')":"";
            $additional .= ($moneyPrecision && $field['column_type']=='money')?"->precision('".$moneyPrecision."')":"";
            $additional .= ($moneyThousandSeparator && $field['column_type']=='money')?"->thousandSeparator('".$moneyThousandSeparator."')":"";
            $additional .= ($moneyDecimalSeparator && $field['column_type']=='money')?"->decimalSeparator('".$moneyDecimalSeparator."')":"";

            // Additional for image & file type
            $additional .= ($fileEncrypt && in_array($field['column_type'],['file','image']))?"->encrypt(true)":"";
            $additional .= ($imageResizeWidth && in_array($field['column_type'],['file','image']))?"->resize(".$imageResizeWidth.",".$imageResizeHeight.")":"";

            // Additional for date & datetime
            $additional .= ($dateFormat && in_array($field['column_type'],['date','datetime']))?"->format('".$dateFormat."')":"";

            // Additional for text
            $additional .= ($textDisplayLimit!="" && in_array($field['column_type'],['text','text_area','wysiwyg']))?"->strLimit(".$textDisplayLimit.")":"";
            $additional .= ($maxCharacter!="" && in_array($field['column_type'],['text','text_area']))?"->maxLength(".$maxCharacter.")":"";
            $additional .= ($minCharacter!="" && in_array($field['column_type'],['text','text_area']))?"->minLength(".$minCharacter.")":"";

            $methodName = Str::studly($field['column_type']);
            if($label && $column) {
                if(in_array($field['column_type'],['radio','select_option','checkbox'])) {
                    if($options) {
                        $optResult = [];
                        foreach($options as $opt) {
                            $optResult[$opt['key']] = $opt['label'];
                        }
                        $scaffold .= '$this->add' . $methodName . '("' . $label . '","' . $column . '")->options('.min_var_export($optResult).')' . $additional . ';' . "\n\t\t";
                    }
                }elseif (in_array($field['column_type'],['radio_table','select_table'])) {
                    if ($optionTable && $optionValue && $optionDisplay) {
                        $scaffold .= '$this->add' . $methodName . '("' . $label . '","' . $column . '",["table"=>"' . $optionTable . '","value_option"=>"' . $optionValue . '","display_option"=>"' . $optionDisplay . '","sql_condition"=>"' . $optionSqlCondition . '"])' . $additional . ';' . "\n\t\t";
                    }
                }elseif ($field['column_type'] == "select_query") {
                    if($sqlRawQuery && Str::contains($sqlRawQuery,["as `key`","as `label`"])) {
                        $scaffold .= '$this->add' . $methodName . '("' . $label . '","' . $column . '","'.$sqlRawQuery.'")' . $additional . ';' . "\n\t\t";
                    }
                }else{
                    $scaffold .= '$this->add'.$methodName.'("'.$label.'","'.$column.'")'.$additional.';'."\n\t\t";
                }
            }
        }
        $template = str_replace("{scaffolding}", $scaffold, $template);

        $filename = $this->makeControllerName($name);

        //Replace Controller Name
        $template = str_replace("FooBarController", $filename, $template);

        //Create a controller file
        file_put_contents(app_path("Http/Controllers/".$filename.".php"), $template);

        //Save to database
        $module = [];
        $module['name'] = $name;
        $module['icon'] = $this->icon;
        $module['table_name'] = $this->table;
        $module['controller'] = $filename;
        $module['last_column_build'] = json_encode($this->columns);

        if($moduleData = DB::table("cb_modules")->where("name", $name)->where("table_name",$this->table)->first()) {
            DB::table("cb_modules")->where("id",$moduleData->id)->update($module);
            $id_modules = $moduleData->id;
        }else{
            $id_modules = DB::table('cb_modules')->insertGetId($module);
        }

        //Save menu
        $menu = [];
        $menu['name'] = $module['name'];
        $menu['type'] = 'module';
        $menu['cb_modules_id'] = $id_modules;
        if(isset($moduleData)) {
            DB::table("cb_menus")->where("cb_modules_id",$moduleData->id)->update($menu);
        }else{
            DB::table('cb_menus')->insertGetId($menu);
        }

        return $module;
    }

}