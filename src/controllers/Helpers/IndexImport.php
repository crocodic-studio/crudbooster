<?php

namespace crocodicstudio\crudbooster\controllers\Helpers;

use crocodicstudio\crudbooster\helpers\CRUDBooster;
use crocodicstudio\crudbooster\helpers\DbInspector;
use crocodicstudio\crudbooster\Modules\ModuleGenerator\ModulesRepo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class IndexImport
{
    /**
     * @return mixed
     */
    public function doneImport()
    {
        $data = [];
        $data['page_title'] = trans('crudbooster.import_page_title', ['module' => \CRUDBooster::getCurrentModule()->name]);
        session()->put('select_column', request('select_column'));

        return view('crudbooster::import', $data);
    }

    /**
     * @param $file_md5
     * @return mixed
     * @throws \Exception
     */
    public function handleImportProgress($file_md5)
    {
        $total = session('total_data_import') * 100;
        $prog = intval(cache('success_'.$file_md5)) / $total;
        $prog = round($prog, 2);

        if ($prog >= 100) {
            cache()->forget('success_'.$file_md5);
        }

        return response()->json(['progress' => $prog, 'last_error' => cache('error_'.$file_md5)]);
    }

    /**
     * @param $file
     * @return string
     */
    public function uploadImportData($file)
    {
        $dir = 'uploads/'.date('Y-m');
        $filename = md5(str_random(5)).'.'.$file->getClientOriginalExtension();

        //Create Directory Monthly
        Storage::makeDirectory($dir);

        //Move file to storage
        Storage::putFileAs($dir, $file, $filename);

        return CRUDBooster::mainpath('import-data').'?file='.base64_encode($dir.'/'.$filename);
    }

    /**
     * @param $file
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validateForImport($file)
    {
        return Validator::make(['extension' => $file->getClientOriginalExtension(),], ['extension' => 'in:xls,xlsx,csv']);
    }

    /**
     * @param $file_md5
     * @param $table
     * @param $titleField
     */
    public function InsertToDB($file_md5, $table, $titleField)
    {
        $select_column = array_filter(session('select_column'));
        $table_columns = DB::getSchemaBuilder()->getColumnListing($table);

        $file = base64_decode(request('file'));
        $file = 'storage'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.$file;
        $rows = Excel::load($file, function ($reader) {
        })->get();

        //$data_import_column = [];
        foreach ($rows as $value) {
            $a = $this->readAndInsert($select_column, $table_columns, $value);

            $has_title_field = true;
            foreach ($a as $k => $v) {
                if ($k == $titleField && $v == '') {
                    $has_title_field = false;
                    break;
                }
            }

            if ($has_title_field == false) {
                continue;
            }

            try {
                if (\Schema::hasColumn($table, 'created_at')) {
                    $a['created_at'] = YmdHis();
                }

                \DB::table($table)->insert($a);
                Cache::increment('success_'.$file_md5);
            } catch (\Exception $e) {
                $e = (string) $e;
                Cache::put('error_'.$file_md5, $e, 500);
            }
        }
    }

    /**
     * @param $selectColumn
     * @param $table_columns
     * @param $value
     * @return array
     */
    private function readAndInsert($selectColumn, $table_columns, $value)
    {
        $a = [];
        foreach ($selectColumn as $sk => $s) {
            $colname = $table_columns[$sk];

            if (! DbInspector::isForeignKey($colname) || intval($value->$s)) {
                $a[$colname] = $value->$s;
                continue;
            }

            //Skip if value is empty
            if ($value->$s == '') {
                continue;
            }

            $relation_table = DbInspector::getRelatedTableName($colname);
            $relation_class = $this->resolveController($relation_table);
            $relation_class->genericLoader();

            $titleField = $relation_class->titleField;

            $relation_insert_data = [];
            $relation_insert_data[$titleField] = $value->$s;

            if (\Schema::hasColumn($relation_table, 'created_at')) {
                $relation_insert_data['created_at'] = YmdHis();
            }

            try {
                $relation_exists = DB::table($relation_table)->where($titleField, $value->$s)->first();
                if ($relation_exists) {
                    $relation_primary_key = $relation_class->primaryKey;
                    $relation_id = $relation_exists->$relation_primary_key;
                } else {
                    $relation_id = DB::table($relation_table)->insertGetId($relation_insert_data);
                }

                $a[$colname] = $relation_id;
            } catch (\Exception $e) {
                //exit($e);
            }
        }

        return $a;
    }

    /**
     * @param $table
     */
    private function resolveController($table)
    {
        $module = ModulesRepo::getByTableName($table);
        if (is_null($module)) {
            return ;
        }
        $relation_class = __NAMESPACE__.'\\'.$module->controller;
        if (! class_exists($relation_class)) {
            $relation_class = ctrlNamespace().'\\'.$module->controller;
        }

        return new $relation_class;
    }
}