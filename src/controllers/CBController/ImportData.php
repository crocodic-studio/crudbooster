<?php

namespace Crocodicstudio\Crudbooster\Controllers\CBController;

use Crocodicstudio\Crudbooster\Controllers\Helpers\IndexImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use CRUDBooster;

trait ImportData
{
    public function getImportData()
    {
        $this->genericLoader();

        $data = [];
        $data['page_menu'] = Route::getCurrentRoute()->getActionName();
        $data['page_title'] = 'Import Data '.CRUDBooster::getCurrentModule()->name;

        if (! request('file') || request('import')) {
            return $this->cbView('crudbooster::import', $data);
        }

        $file = 'storage'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.base64_decode(request('file'));
        $rows = Excel::load($file, function ($reader) {
        })->get();

        session()->put('total_data_import', count($rows));

        $import_column = [];
        foreach ($rows as $value) {
            $a = [];
            foreach ($value as $k => $v) {
                $a[] = $k;
            }
            if (count($a)) {
                $import_column = $a;
            }
            break;
        }

        $data['table_columns'] = DB::getSchemaBuilder()->getColumnListing($this->table);
        $data['data_import_column'] = $import_column;

        return $this->cbView('crudbooster::import', $data);
    }

    public function postDoImportChunk()
    {
        $import = app(IndexImport::class);
        $this->genericLoader();
        $fileMD5 = md5(request('file'));

        if (request('file') && request('resume') == 1) {
            return $import->handleImportProgress($fileMD5);
        }

        $import->InsertToDB($fileMD5, $this->table, $this->titleField);

        return response()->json(['status' => true]);
    }


}