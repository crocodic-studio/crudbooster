<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator;

use CRUDBooster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class Step2Handler
{
    public function showForm($id)
    {
        $module = DB::table('cms_moduls')->where('id', $id)->first();

        $columns = CRUDBooster::getTableColumns($module->table_name);

        $controllerCode = (readCtrlContent($module->controller));

        $data = [];
        $data['id'] = $id;
        $data['columns'] = $columns;
        //$data['table_list'] = \CB::listCbTables();
        $data['cols'] = parseScaffoldingToArray($controllerCode, 'col');


        $hooks = ['hookQueryIndex', 'hookRowIndex', 'hookBeforeAdd', 'hookAfterAdd',
            'hookBeforeEdit', 'hookAfterEdit', 'hookBeforeDelete', 'hookAfterDelete',];
        foreach($hooks as $hook){
            $data[$hook] = FileManipulator::readMethodContent($controllerCode, $hook);
        }

        return view('CbModulesGen::step2', $data);
    }

    public function handleFormSubmit()
    {
        $id = Request::input('id');
        $controller = DB::table('cms_moduls')->where('id', $id)->first()->controller;

        $newCode = $this->makeColumnPhpCode();
        $code = readCtrlContent($controller);
        $fileResult = \CB::replaceBetweenMark($code, 'COLUMNS', $newCode);

        $fileResult = FileManipulator::writeMethodContent($fileResult, 'hookQueryIndex', g('hookQueryIndex'));
        $fileResult = FileManipulator::writeMethodContent($fileResult, 'hookRowIndex', g('hookRowIndex'));
        $fileResult = FileManipulator::writeMethodContent($fileResult, 'hookBeforeAdd', g('hookBeforeAdd'));
        $fileResult = FileManipulator::writeMethodContent($fileResult, 'hookAfterAdd', g('hookAfterAdd'));
        $fileResult = FileManipulator::writeMethodContent($fileResult, 'hookBeforeEdit', g('hookBeforeEdit'));
        $fileResult = FileManipulator::writeMethodContent($fileResult, 'hookAfterEdit', g('hookAfterEdit'));
        $fileResult = FileManipulator::writeMethodContent($fileResult, 'hookBeforeDelete', g('hookBeforeDelete'));
        $fileResult = FileManipulator::writeMethodContent($fileResult, 'hookAfterDelete', g('hookAfterDelete'));

        file_put_contents(controller_path($controller), $fileResult);

        return redirect()->route("AdminModulesControllerGetStep3", ["id" => $id]);
    }
    /**
     * @return array
     */
    private function makeColumnPhpCode()
    {
        $labels = request('column');
        $name = request('name');
        $isImage = request('is_image');
        $isDownload = request('is_download');
        $callback = request('callback');
        $width = request('width');

        $columnScript = [];
        $columnScript[] = '            $this->col[] = [];';
        foreach ($labels as $i => $label) {

            if (! $name[$i]) {
                continue;
            }

            $colProperties = ["'label' => '$label'", "'name' => '{$name[$i]}'"];
            if ($isImage[$i]) {
                $colProperties[] = '"image" => true ';
            }
            if ($isDownload[$i]) {
                $colProperties[] = '"download" => true';
            }
            if ($callback[$i]) {
                $colProperties[] = '"callback" => function($row) {'.$callback[$i].'}';
            }
            if ($width[$i]) {
                $colProperties[] = "'width' => '$width[$i]'";
            }

            $columnScript[] = '            $this->col[] = ['.implode(", ", $colProperties).'];';
        }
        return implode("\n", $columnScript);
    }
}