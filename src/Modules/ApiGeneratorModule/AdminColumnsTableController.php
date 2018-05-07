<?php

namespace crocodicstudio\crudbooster\Modules\ApiGeneratorModule;

use crocodicstudio\crudbooster\controllers\CBController;
use crocodicstudio\crudbooster\Modules\ModuleGenerator\ControllerGenerator\FieldDetector;
use Illuminate\Support\Facades\DB;

class AdminColumnsTableController extends CBController
{
    public function cbInit()
    {
        $this->table = 'cms_apicustom';
        $this->primaryKey = "id";
        $this->titleField = "nama";
        $this->buttonShow = false;
        $this->deleteBtn = false;
        $this->buttonAdd = false;
        $this->buttonImport = false;
        $this->buttonExport = false;
    }

    public function getColumnTable($table, $type = 'list')
    {
        $this->cbLoader();

        $columns = array_filter(\Schema::getColumnListing($table), function ($colName) {
            return ! (in_array($colName, ['created_at', 'deleted_at', 'updated_at']));
        });

        $result = [];
        foreach ($columns as $colName) {
            $result[] = ['name' => $colName, 'type' => $this->getFieldType($colName, $table)];

            if (! in_array($type, ['list', 'detail']) || ! starts_with($colName, 'id_')) {
                continue;
            }
            $relatedTable = str_after($colName, 'id_');
            $result = $this->addRelatedTableColTypes($relatedTable, $result);
        }

        return response()->json($result);
    }

    /**
     * @param $colName string
     * @param $table
     * @return string
     */
    private function getFieldType($colName, $table)
    {
        $MAP = [
            'isEmail' => "email",
            'isImage' => "image",
            'isPassword' => "password",
            'isForeignKey' => "integer",
        ];

        foreach ($MAP as $methodName => $type) {
            if (FieldDetector::$methodName($colName)) {
                return $type;
            }
        }

        return \Schema::getColumnType($table, $colName);
    }

    /**
     * @param $table
     * @param $result
     * @return array
     */
    private function addRelatedTableColTypes($table, $result)
    {
        $columns = DB::getSchemaBuilder()->getColumnListing($table);
        $columns = array_filter($columns, function ($col) {
            return ! FieldDetector::isExceptional($col) && !starts_with($col, 'id_');
        });

        foreach ($columns as $col) {
            $col = str_replace("_$table", "", $col);
            $result[] = ['name' => $table.'_'.$col, 'type' => \Schema::getColumnType($table, $col)];
        }

        return $result;
    }
}
