<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator;

use crocodicstudio\crudbooster\Modules\ModuleGenerator\ControllerGenerator\FormConfigGenerator;
use crocodicstudio\crudbooster\Modules\ModuleGenerator\ControllerGenerator\FieldDetector;
use Schema;
use CRUDBooster;
use CB;

class ControllerGenerator
{
    public static function generateController($table, $name = null){

        $controllerName = self::getControllerName($table, $name);

        $coloms = DbInspector::getTableCols($table);
        $pk = DbInspector::findPk($table);
        $formArrayString = FormConfigGenerator::generateFormConfig($table, $coloms);
        list($cols, $joinList) = self::addCol($table, $coloms, $pk);
        $php = '<?php '.view('CbModulesGen::controller_stub', compact('controllerName', 'table', 'pk', 'coloms', 'cols', 'formArrayString', 'joinList'))->render();
        //create file controller
        FileManipulator::putCtrlContent('Admin'.$controllerName.'.php', $php);

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

        $countSameFile = count(glob(base_path(controllers_dir()).'Admin'.$controllername.'.php'));

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
     * @param $pk
     * @return array
     */
    private static function addCol($table, $coloms, $pk)
    {
        $coloms_col = array_slice($coloms, 0, 8);
        $joinList = [];
        $cols = [];

        foreach ($coloms_col as $c) {
            $label = str_replace("id_", "", $c);
            $label = ucwords(str_replace("_", " ", $label));
            $label = str_replace('Cms ', '', $label);
            $field = $c;

            if (FieldDetector::isExceptional($field) || FieldDetector::isPassword($field)) {
                continue;
            }

            if (FieldDetector::isForeignKey($field)) {
                $jointable = str_replace(['id_', '_id'], '', $field);

                if (Schema::hasTable($jointable)) {
                    $joincols = CRUDBooster::getTableColumns($jointable);
                    $joinname = DbInspector::colName($joincols);
                    $cols[] = ['label' => $label, 'name' =>  $jointable.$joinname];
                    $jointablePK = CB::pk($jointable);
                    $joinList[] = [
                        'table' => $jointable,
                        'field1' => $jointable.'.'.$jointablePK,
                        'field2' => $table.'.'.$pk,
                    ];
                }
            } else {
                $image = '';
                if (FieldDetector::isImage($field)) {
                    $image = '"image" => true';
                }
                $cols[] = ['label' => $label, 'name' =>  "'$field', $image"];
            }
        }

        return [$cols, $joinList];
    }
}