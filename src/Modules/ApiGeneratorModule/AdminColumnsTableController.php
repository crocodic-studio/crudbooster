<?php

namespace crocodicstudio\crudbooster\Modules\ApiGeneratorModule;

use crocodicstudio\crudbooster\controllers\CBController;
use crocodicstudio\crudbooster\Modules\ModuleGenerator\ControllerGenerator\FieldDetector;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class AdminColumnsTableController extends CBController
{
    public function cbInit()
    {
        $this->table = 'cms_apicustom';
        $this->primaryKey = "id";
        $this->title_field = "nama";
        $this->buttonShow = false;
        $this->button_new = false;
        $this->deleteBtn = false;
        $this->buttonAdd = false;
        $this->button_import = false;
        $this->buttonExport = false;
    }

    public function getColumnTable($table, $type = 'list')
    {
        $this->cbLoader();
        $except = ['created_at', 'deleted_at', 'updated_at'];

        $result = \Schema::getColumnListing($table);
        $newResult = [];
        foreach ($result as $row) {

            if (in_array($row, $except)) {
                continue;
            }
            $type_field = \Schema::getColumnType($table, $row);
            $newResult[] = ['name' => $row, 'type' => $this->getFieldType($row, $type_field)];

            if (! in_array($type, ['list', 'detail']) || ! starts_with($row, 'id_')) {
                continue;
            }

            $newResult = $this->prepareResults($row, $newResult);
        }

        return response()->json($newResult);
    }

    /**
     * @param $ro string
     * @param $default string
     * @return string
     */
    private function getFieldType($ro, $default)
    {
        $MAP = [
            'isEmail' => "email",
            'isImage' => "image",
            'isPassword' => "password",
            'isForeignKey' => "integer",
        ];

        foreach ($MAP as $methodName => $type) {
            if (FieldDetector::$methodName($ro)) {
                return $type;
            }
        }

        return $default;
    }

    /**
     * @param $table2
     * @param $ro
     * @param $new_result
     * @return array
     */
    private function prepareResults($ro, $new_result)
    {
        if (starts_with($ro, 'id_')) {
            return $new_result;
        }
        $table2 = substr($ro, 3);
        $columns = DB::getSchemaBuilder()->getColumnListing($table2);
        $columns = array_filter($columns, function ($col) {
            return ! FieldDetector::isExceptional($col);
        });

        foreach ($columns as $col) {
            $col = str_replace("_$table2", "", $col);
            $new_result[] = ['name' => $table2.'_'.$col, 'type' => \Schema::getColumnType($table2, $col)];
        }

        return $new_result;
    }
}
