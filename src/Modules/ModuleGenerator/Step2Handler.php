<?php

namespace Crocodicstudio\Crudbooster\Modules\ModuleGenerator;

class Step2Handler
{
    public function showForm($id)
    {
        $module = ModulesRepo::find($id);

        $columns = \Schema::getColumnListing($module->table_name);

        $controllerCode = (FileManipulator::readCtrlContent($module->controller));

        $data = [
            'id' => $id,
            'columns' => $columns,
            'cols' => ScaffoldingParser::parse($controllerCode, 'col')
        ];
        $data['table_list'] = \Crocodicstudio\Crudbooster\helpers\CRUDBooster::listCbTables();

        return view('CbModulesGen::step2', $data);
    }

    public function handleFormSubmit()
    {
        $id = request('id');
        $controller = ModulesRepo::getControllerName($id);
        $newCode = view('CbModulesGen::templates.col')->render();
        $code = FileManipulator::readCtrlContent($controller);
        $fileResult = FileManipulator::replaceBetweenMark($code, 'COLUMNS', $newCode);

        FileManipulator::putCtrlContent($controller, $fileResult);

        return redirect()->route("AdminModulesControllerGetStep3", ["id" => $id]);
    }
}