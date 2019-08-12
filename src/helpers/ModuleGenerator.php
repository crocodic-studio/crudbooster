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

            $methodName = Str::studly($field['column_type']);
            if($label && $column) {
                if(in_array($field['column_type'],['radio','select_option','checkbox'])) {
                    if($options) {
                        $optResult = [];
                        foreach($options as $opt) {
                            $optResult[$opt['key']] = $opt['label'];
                        }
                        $scaffold .= '$this->add' . $methodName . '("' . $label . '","' . $column . '")->options('.min_var_export($optResult).')' . $required . $indexShow . $detailShow . $addShow . $editShow . $help . ';' . "\n\t\t";
                    }
                }elseif (in_array($field['column_type'],['radio_table','select_table'])) {
                    if ($optionTable && $optionValue && $optionDisplay) {
                        $scaffold .= '$this->add' . $methodName . '("' . $label . '","' . $column . '",["table"=>"' . $optionTable . '","value_option"=>"' . $optionValue . '","display_option"=>"' . $optionDisplay . '","sql_condition"=>"' . $optionSqlCondition . '"])' . $required . $indexShow . $detailShow . $addShow . $editShow . $help . ';' . "\n\t\t";
                    }
                }elseif ($field['column_type'] == "select_query") {
                    if($sqlRawQuery && Str::contains($sqlRawQuery,["as `key`","as `label`"])) {
                        $scaffold .= '$this->add' . $methodName . '("' . $label . '","' . $column . '","'.$sqlRawQuery.'")' . $required . $indexShow . $detailShow . $addShow . $editShow . $help . ';' . "\n\t\t";
                    }
                }else{
                    $scaffold .= '$this->add'.$methodName.'("'.$label.'","'.$column.'")'.$required.$indexShow.$detailShow.$addShow.$editShow.$help.';'."\n\t\t";
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