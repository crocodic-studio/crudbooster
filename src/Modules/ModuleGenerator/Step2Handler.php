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

        $controllerCode = file_get_contents(controller_path($module->controller));

        $data = [];
        $data['id'] = $id;
        $data['columns'] = $columns;
        //$data['table_list'] = getTablesList();
        $data['cols'] = parseScaffoldingToArray($controllerCode, 'col');


        $hooks = ['hookQueryIndex', 'hookRowIndex', 'hookBeforeAdd', 'hookAfterAdd',
            'hookBeforeEdit', 'hookAfterEdit', 'hookBeforeDelete', 'hookAfterDelete',];
        foreach($hooks as $hook){
            $data[$hook] = readMethodContent($controllerCode, $hook);
        }

        return view('CbModulesGen::step2', $data);
    }

    public function handleFormSubmit()
    {
        $id = Request::input('id');
        $controller = DB::table('cms_moduls')->where('id', $id)->first()->controller;
        $controller_path = controller_path($controller);

        $code = file_get_contents($controller_path);
        $rawBefore = explode("# START COLUMNS DO NOT REMOVE THIS LINE", $code);
        $rawAfter = explode("# END COLUMNS DO NOT REMOVE THIS LINE", $rawBefore[1]);

        $fileResult = trim($rawBefore[0]);
        $fileResult .= "\n\n            # START COLUMNS DO NOT REMOVE THIS LINE\n";
        $fileResult .= "            \$this->col = [];\n";
        $fileResult .= implode("\n", $this->makeColumnPhpCode());
        $fileResult .= "\n            # END COLUMNS DO NOT REMOVE THIS LINE\n\n            ";
        $fileResult .= trim($rawAfter[1]);

        $fileResult = writeMethodContent($fileResult, 'hookQueryIndex', g('hookQueryIndex'));
        $fileResult = writeMethodContent($fileResult, 'hookRowIndex', g('hookRowIndex'));
        $fileResult = writeMethodContent($fileResult, 'hookBeforeAdd', g('hookBeforeAdd'));
        $fileResult = writeMethodContent($fileResult, 'hookAfterAdd', g('hookAfterAdd'));
        $fileResult = writeMethodContent($fileResult, 'hookBeforeEdit', g('hookBeforeEdit'));
        $fileResult = writeMethodContent($fileResult, 'hookAfterEdit', g('hookAfterEdit'));
        $fileResult = writeMethodContent($fileResult, 'hookBeforeDelete', g('hookBeforeDelete'));
        $fileResult = writeMethodContent($fileResult, 'hookAfterDelete', g('hookAfterDelete'));

        file_put_contents($controller_path, $fileResult);

        return redirect()->route("AdminModulesControllerGetStep3", ["id" => $id]);
    }
    /**
     * @return array
     */
    private function makeColumnPhpCode()
    {
        $labels = Request::input('column');
        $name = Request::input('name');
        $isImage = Request::input('is_image');
        $isDownload = Request::input('is_download');
        $callback = Request::input('callback');
        $width = Request::input('width');

        $columnScript = [];
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

        return $columnScript;
    }
}