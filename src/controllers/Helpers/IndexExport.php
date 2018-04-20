<?php

namespace crocodicstudio\crudbooster\controllers\Helpers;

use Maatwebsite\Excel\Facades\Excel;

class IndexExport
{
    /**
     * @param $response
     * @param $paperSize
     * @param $paperOrientation
     * @param $fileName
     * @return mixed
     * @throws \Throwable
     */
    public function pdf($fileName, $response, $paperOrientation, $paperSize)
    {
        $view = view('crudbooster::export', $response)->render();
        $pdf = app('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setPaper($paperSize, $paperOrientation);

        return $pdf->stream($fileName.'.pdf');
    }

    /**
     * @param $filename
     * @param $response
     * @param $orientation
     */
    public function xls($filename, $response, $orientation)
    {
        return $this->exportExcelAs($filename, $response, $orientation, 'xls');
    }

    /**
     * @param $filename
     * @param $response
     * @param $orientation
     */
    public function csv($filename, $response, $orientation)
    {
        return $this->exportExcelAs($filename, $response, $orientation, 'csv');
    }

    /**
     * @param $filename
     * @param $response
     * @param $orientation
     * @param $fmt
     * @return mixed
     */
    private function exportExcelAs($filename, $response, $orientation, $fmt)
    {
        return Excel::create($filename, function ($excel) use ($response, $orientation, $filename) {
            $excel->setTitle($filename)->setCreator("crudbooster.com")->setCompany(cbGetsetting('appname'));
            $excel->sheet($filename, function ($sheet) use ($response, $orientation) {
                $sheet->setOrientation($orientation);
                $sheet->loadview('crudbooster::export', $response);
            });
        })->export($fmt);
    }
}