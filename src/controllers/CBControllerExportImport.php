<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/7/2020
 * Time: 5:48 PM
 */

namespace crocodicstudio\crudbooster\controllers;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

trait CBControllerExportImport
{
    public function getExportData()
    {
        return redirect(cb()->mainpath());
    }

    public function postExportData()
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(180);

        $this->limit = request()->input('limit');
        $this->index_return = true;
        $filetype = request()->input('fileformat');
        $filename = request()->input('filename');
        $papersize = request()->input('page_size');
        $paperorientation = request()->input('page_orientation');
        $response = $this->getIndex();

        if (request()->input('default_paper_size')) {
            DB::table('cms_settings')->where('name', 'default_paper_size')->update(['content' => $papersize]);
        }

        switch ($filetype) {
            case "pdf":
                $view = view('crudbooster::export', $response)->render();
                $pdf = App::make('dompdf.wrapper');
                $pdf->loadHTML($view);
                $pdf->setPaper($papersize, $paperorientation);

                return $pdf->stream($filename.'.pdf');
                break;
            case 'xls':
                Excel::create($filename, function ($excel) use ($response) {
                    $excel->setTitle($filename)->setCreator("crudbooster.com")->setCompany(cb()->getSetting('appname'));
                    $excel->sheet($filename, function ($sheet) use ($response) {
                        $sheet->setOrientation($paperorientation);
                        $sheet->loadview('crudbooster::export', $response);
                    });
                })->export('xls');
                break;
            case 'csv':
                Excel::create($filename, function ($excel) use ($response) {
                    $excel->setTitle($filename)->setCreator("crudbooster.com")->setCompany(cb()->getSetting('appname'));
                    $excel->sheet($filename, function ($sheet) use ($response) {
                        $sheet->setOrientation($paperorientation);
                        $sheet->loadview('crudbooster::export', $response);
                    });
                })->export('csv');
                break;
        }
    }

    public function getImportData()
    {
        $this->cbLoader();
        $data['page_menu'] = Route::getCurrentRoute()->getActionName();
        $data['page_title'] = 'Import Data '.$module->name;

        if (request()->get('file') && ! request()->get('import')) {
            $file = base64_decode(request()->get('file'));
            $file = storage_path('app/'.$file);
            $rows = Excel::load($file, function ($reader) {
            })->get();

            $countRows = ($rows)?count($rows):0;

            Session::put('total_data_import', $countRows);

            $data_import_column = [];
            foreach ($rows as $value) {
                $a = [];
                foreach ($value as $k => $v) {
                    $a[] = $k;
                }
                if ($a && count($a)) {
                    $data_import_column = $a;
                }
                break;
            }

            $table_columns = DB::getSchemaBuilder()->getColumnListing($this->table);

            $data['table_columns'] = $table_columns;
            $data['data_import_column'] = $data_import_column;
        }

        return view('crudbooster::import', $data);
    }

    public function postDoneImport()
    {
        $this->cbLoader();
        $data['page_menu'] = Route::getCurrentRoute()->getActionName();
        $data['page_title'] = trans('crudbooster.import_page_title', ['module' => $module->name]);
        Session::put('select_column', request()->get('select_column'));

        return view('crudbooster::import', $data);
    }

    public function postDoImportChunk()
    {
        $this->cbLoader();
        $file_md5 = md5(request()->get('file'));

        if (request()->get('file') && request()->get('resume') == 1) {
            $total = Session::get('total_data_import');
            $prog = intval(Cache::get('success_'.$file_md5)) / $total * 100;
            $prog = round($prog, 2);
            if ($prog >= 100) {
                Cache::forget('success_'.$file_md5);
            }

            return response()->json(['progress' => $prog, 'last_error' => Cache::get('error_'.$file_md5)]);
        }

        $select_column = Session::get('select_column');
        $select_column = array_filter($select_column);
        $table_columns = DB::getSchemaBuilder()->getColumnListing($this->table);

        $file = base64_decode(request()->get('file'));
        $file = storage_path('app/'.$file);

        $rows = Excel::load($file, function ($reader) {
        })->get();

        $has_created_at = false;
        if (cb()->isColumnExists($this->table, 'created_at')) {
            $has_created_at = true;
        }

        $data_import_column = [];
        foreach ($rows as $value) {
            $a = [];
            foreach ($select_column as $sk => $s) {
                $colname = $table_columns[$sk];

                if (cb()->isForeignKey($colname)) {

                    //Skip if value is empty
                    if ($value->$s == '') {
                        continue;
                    }

                    if (intval($value->$s)) {
                        $a[$colname] = $value->$s;
                    } else {
                        $relation_table = cb()->getTableForeignKey($colname);
                        $relation_moduls = DB::table('cms_moduls')->where('table_name', $relation_table)->first();

                        $relation_class = __NAMESPACE__.'\\'.$relation_moduls->controller;
                        if (! class_exists($relation_class)) {
                            $relation_class = '\App\Http\Controllers\\'.$relation_moduls->controller;
                        }
                        $relation_class = new $relation_class;
                        $relation_class->cbLoader();

                        $title_field = $relation_class->title_field;

                        $relation_insert_data = [];
                        $relation_insert_data[$title_field] = $value->$s;

                        if (cb()->isColumnExists($relation_table, 'created_at')) {
                            $relation_insert_data['created_at'] = date('Y-m-d H:i:s');
                        }

                        try {
                            $relation_exists = DB::table($relation_table)->where($title_field, $value->$s)->first();
                            if ($relation_exists) {
                                $relation_primary_key = $relation_class->primary_key;
                                $relation_id = $relation_exists->$relation_primary_key;
                            } else {
                                $relation_id = DB::table($relation_table)->insertGetId($relation_insert_data);
                            }

                            $a[$colname] = $relation_id;
                        } catch (\Exception $e) {
                            exit($e);
                        }
                    } //END IS INT

                } else {
                    $a[$colname] = $value->$s;
                }
            }

            $has_title_field = true;
            foreach ($a as $k => $v) {
                if ($k == $this->title_field && $v == '') {
                    $has_title_field = false;
                    break;
                }
            }

            if ($has_title_field == false) {
                continue;
            }

            try {

                if ($has_created_at) {
                    $a['created_at'] = date('Y-m-d H:i:s');
                }

                DB::table($this->table)->insert($a);
                Cache::increment('success_'.$file_md5);
            } catch (\Exception $e) {
                $e = (string) $e;
                Cache::put('error_'.$file_md5, $e, 500);
            }
        }

        return response()->json(['status' => true]);
    }

    public function postDoUploadImportData()
    {
        $this->cbLoader();
        if (request()->hasFile('userfile')) {
            $file = request()->file('userfile');
            $ext = $file->getClientOriginalExtension();

            $validator = Validator::make([
                'extension' => $ext,
            ], [
                'extension' => 'in:xls,xlsx,csv',
            ]);

            if ($validator->fails()) {
                $message = $validator->errors()->all();

                return redirect()->back()->with(['message' => implode('<br/>', $message), 'type' => 'warning']);
            }

            //Create Directory Monthly
            $filePath = 'uploads/'.cb()->myId().'/'.date('Y-m');
            Storage::makeDirectory($filePath);

            //Move file to storage
            $filename = md5(str_random(5)).'.'.$ext;
            $url_filename = '';
            if (Storage::putFileAs($filePath, $file, $filename)) {
                $url_filename = $filePath.'/'.$filename;
            }
            $url = cb()->mainpath('import-data').'?file='.base64_encode($url_filename);

            return redirect($url);
        } else {
            return redirect()->back();
        }
    }

}