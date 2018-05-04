<?php

namespace crocodicstudio\crudbooster\controllers\CBController;

use crocodicstudio\crudbooster\controllers\Helpers\IndexExport;

trait ExportData
{
    public function postExportData()
    {
        $this->limit = request('limit');
        $this->index_return = true;
        $filename = request('filename');
        $papersize = request('page_size');
        $paperorientation = request('page_orientation');
        $indexContent = $this->getIndex();

        if (request('default_paper_size')) {
            $this->table('cms_settings')->where('name', 'default_paper_size')->update(['content' => $papersize]);
        }
        $format = request('fileformat');
        if(in_array($format, ['pdf', 'xls', 'csv']))
        {
            return app(IndexExport::class)->{$format}($filename, $indexContent, $paperorientation, $papersize);
        }
    }

    public function getExportData()
    {
        return redirect(CRUDBooster::mainpath());
    }

}