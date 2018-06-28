<?php

namespace Crocodicstudio\Crudbooster\Modules\ModuleGenerator;

use Crocodicstudio\Crudbooster\Helpers\DbInspector;
use Crocodicstudio\Crudbooster\Modules\ModuleGenerator\ControllerGenerator\FormConfigGenerator;
use Crocodicstudio\Crudbooster\Modules\ModuleGenerator\ControllerGenerator\FieldDetector;
use Schema;

class ControllerGenerator
{
    public static function generateController($table, $name = null)
    {

        $controllerName = self::getControllerName($table, $name);

        $php = self::generateControllerCode($table, $controllerName);
        //create file controller
        FileManipulator::putCtrlContent($controllerName, $php);

        return $controllerName;
    }

    /**
     * @param $table
     * @param $name
     * @return string
     */
    private static function getControllerName($table, $name = null)
    {
        $controllerName = ucwords(str_replace(['_', '-'], ' ', $name ?: $table));
        $controllerName = 'Admin'.str_replace(' ', '', $controllerName).'Controller';

        $countSameFile = count(glob(controllers_dir().$controllerName.'*.php'));

        if ($countSameFile > 0) {
            $controllerName .= $countSameFile;
        }

        return $controllerName;
    }

    /**
     * @param $table
     * @param $controllerName
     * @return string
     * @throws \Exception
     * @throws \Throwable
     */
    private static function generateControllerCode($table, $controllerName)
    {
        $coloms = \Schema::getColumnListing($table);
        $pk = DbInspector::findPk($table);
        $formArrayString = FormConfigGenerator::generateFormConfig($table, $coloms);
        list($cols, $joinList) = self::addCol($table, $coloms, $pk);

        $data = compact('controllerName', 'table', 'pk', 'coloms', 'cols', 'formArrayString', 'joinList');

        return '<?php ' . view('CbModulesGen::controller_stub', $data)->render();
    }

    /**
     * @param $table
     * @param $coloms
     * @param $pk
     * @return array
     */
    private static function addCol($table, $coloms, $pk)
    {
        $coloms_col = array_filter($coloms, function ($field) {
            return (! FieldDetector::isExceptional($field) && ! FieldDetector::isPassword($field));
        });
        $coloms_col = array_slice($coloms_col, 0, 10);

        $joinList = [];
        $cols = [];
        foreach ($coloms_col as $field) {
            $label = str_replace("id_", "", $field);
            $label = ucwords(str_replace("_", " ", $label));
            $label = str_replace('Cms ', '', $label);

            if (FieldDetector::isForeignKey($field)) {
                list($cols, $joinList) = self::handleForeignKey($table.'.'.$pk, $field, $label, $cols, $joinList);
            } else {
                $cols = self::handleOtherFields($field, $label, $cols);
            }
        }

        return [$cols, $joinList];
    }

    /**
     * @param $field2
     * @param $field
     * @param $label
     * @param $cols
     * @param $joinList
     * @return array
     * @throws \Exception
     */
    private static function handleForeignKey($field2, $field, $label, $cols, $joinList)
    {
        $jointable = str_replace(['id_', '_id'], '', $field);

        if (! Schema::hasTable($jointable)) {
            return [$cols, $joinList];
        }
        $joincols = \Schema::getColumnListing($jointable);
        $joinname = DbInspector::colName($joincols);
        $cols[] = ['label' => $label, 'name' => "'".$jointable.$joinname."'"];
        $jointablePK = DbInspector::findPk($jointable);
        $joinList[] = [
            'table' => $jointable,
            'field1' => $jointable.'.'.$jointablePK,
            'field2' => $field2,
        ];

        return [$cols, $joinList];
    }

    /**
     * @param $field
     * @param $label
     * @param $cols
     * @return array
     */
    private static function handleOtherFields($field, $label, $cols)
    {
        $image = '';
        if (FieldDetector::isImage($field)) {
            $image = '"image" => true';
        }
        $cols[] = ['label' => $label, 'name' => "'$field', $image"];

        return $cols;
    }
}