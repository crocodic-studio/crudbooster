<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator;

use CRUDBooster;

class Step2Handler
{
    public function showForm($id)
    {
        $module = ModulesRepo::find($id);

        $columns = DbInspector::getTableCols($module->table_name);

        $controllerCode = (FileManipulator::readCtrlContent($module->controller));

        $data = [];
        $data['id'] = $id;
        $data['columns'] = $columns;
        //$data['table_list'] = \CB::listCbTables();
        $data['cols'] = ScaffoldingParser::parse($controllerCode, 'col');


        /*foreach($this->hooks as $hook){
            $data[$hook] = FileManipulator::readMethodContent($controllerCode, $hook);
        }*/

        return view('CbModulesGen::step2', $data);
    }

    public function handleFormSubmit()
    {
        $id = request('id');
        $controller = ModulesRepo::getControllerName($id);

        $newCode = $this->makeColumnPhpCode();
        $code = FileManipulator::readCtrlContent($controller);
        $fileResult = FileManipulator::replaceBetweenMark($code, 'COLUMNS', $newCode);

        /* foreach($this->hooks as $hook){
            $fileResult = FileManipulator::writeMethodContent($fileResult, $hook, request($hook));
        }*/

        FileManipulator::putCtrlContent($controller, $fileResult);

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