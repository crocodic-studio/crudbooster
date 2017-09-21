<?php

namespace crocodicstudio\crudbooster\controllers\Helpers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class IndexImport
{
    /**
     * @return mixed
     */
    public function doneImport()
    {
        $data = [];
        $data['page_menu'] = Route::getCurrentRoute()->getActionName();
        $data['page_title'] = trans('crudbooster.import_page_title', ['module' => \CRUDBooster::getCurrentModule()->name]);
        session()->put('select_column', request('select_column'));

        return view('crudbooster::import', $data);
    }


    /**
     * @param $file_md5
     * @return mixed
     */
    function handleImportProgress($file_md5)
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
    function uploadImportData($file)
    {
        $dir = 'uploads/'.date('Y-m');
        $filename = md5(str_random(5)).'.'. $file->getClientOriginalExtension();
        //Create Directory Monthly
        Storage::makeDirectory($dir);

        //Move file to storage
        Storage::putFileAs($dir, $file, $filename);

        return CRUDBooster::mainpath('import-data').'?file='.base64_encode($dir.'/'.$filename);
    }
}