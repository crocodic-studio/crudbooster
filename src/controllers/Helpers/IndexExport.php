<?php

namespace crocodicstudio\crudbooster\controllers\Helpers;

use Maatwebsite\Excel\Facades\Excel;

class IndexExport
{
    /**
     * @param $response
     * @param $papersize
     * @param $paperorientation
     * @param $filename
     * @return mixed
     */
    public function pdf($filename, $response, $paperorientation, $papersize)
    {
        $view = view('crudbooster::export', $response)->render();
        $pdf = app('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setPaper($papersize, $paperorientation);

        return $pdf->stream($filename.'.pdf');
    }

    /**
     * @param $filename
     * @param $response
     * @param $orientation
     */
    public function xls($filename, $response, $orientation)
    {
        return Excel::create($filename, function ($excel) use ($response, $orientation) {
            $excel->setTitle($filename)->setCreator("crudbooster.com")->setCompany(CRUDBooster::getSetting('appname'));
            $excel->sheet($filename, function ($sheet) use ($response, $orientation) {
                $sheet->setOrientation($orientation);
                $sheet->loadview('crudbooster::export', $response);
            });
        })->export('xls');
    }

    /**
     * @param $filename
     * @param $response
     * @param $orientation
     */
    public function csv($filename, $response, $orientation)
    {
        return Excel::create($filename, function ($excel) use ($response, $orientation) {
            $excel->setTitle($filename)->setCreator("crudbooster.com")->setCompany(CRUDBooster::getSetting('appname'));
            $excel->sheet($filename, function ($sheet) use ($response, $orientation) {
                $sheet->setOrientation($orientation);
                $sheet->loadview('crudbooster::export', $response);
            });
        })->export('csv');
    }
}